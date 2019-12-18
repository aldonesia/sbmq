<?php

namespace App\Http\Controllers;

use App\block_type;
use Illuminate\Http\Request;


use App\progress;

use App\Http\Controllers\ppController;
use App\Helpers\Helpme;
use App\material_type;
use App\panel_position;
use App\panel_type;
use App\piecepart;
use App\process;
use App\project;
use App\panel;
use App\block;

class TrackController extends Controller
{
	public function index()
	{
		return view('track.index');
	}

	/**
	 * split_code function
	 * split code into array of parameter
	 *
	 * @param string $code : string codee
	 * @param integer $max : max index of code
	 * @param integer $proc_idx : process index
	 * @param integer $bt_idx ; block type index
	 * @param integer $blockId_idx ; block no index
	 * @param integer $ppos_idx : panel position index
	 * @param integer $pt_idx ; panel type index
	 * @param integer $panIDx : panel no index
	 * @param integer $ppId_idx : piecepart no index
	 * @param integer $mtParid_idx : material type parant index
	 * @param integer $mt_idx : material type index
	 * @param integer $Proj_idx : project index
	 * @return array
	 */
	private function split_code(string $code, array $dats)
	{
		// get parameter
		$code =				trim($code);
		$max =					intval($dats['max']);
		$proc_idx =		intval($dats['proc_idx']);
		$bt_idx =			intval($dats['bt_idx']);
		$blockId_idx =	intval($dats['blockId_idx']);
		$ppos_idx =		intval($dats['ppos_idx']);
		$pt_idx =			intval($dats['pt_idx']);
		$panIDx =			intval($dats['panIDx']);
		$ppId_idx =		intval($dats['ppId_idx']);
		$mtParid_idx =	intval($dats['mtParid_idx']);
		$mt_idx =			intval($dats['mt_idx']);
		$proj_idx =		intval($dats['proj_idx']);

		if (strlen($code) < $max) return array();

		$return = array();
		$return[] =	substr($code, $proc_idx, 		$bt_idx - $proc_idx);
		$return[] =	substr($code, $bt_idx, 			$blockId_idx - $bt_idx);
		$return[] =	substr($code, $blockId_idx, $ppos_idx - $blockId_idx);
		$return[] =	substr($code, $ppos_idx, 		$pt_idx - $ppos_idx);
		$return[] =	substr($code, $pt_idx, 			$panIDx - $pt_idx);
		$return[] =	substr($code, $panIDx, 			$ppId_idx - $panIDx);
		$return[] =	substr($code, $ppId_idx, 		$mtParid_idx - $ppId_idx);
		$return[] =	substr($code, $mtParid_idx, $mt_idx - $mtParid_idx);
		$return[] =	substr($code, $mt_idx, 			$proj_idx - $mt_idx);
		$return[] =	substr($code, $proj_idx, 		$max - $proj_idx);

		// Helpme::print_rdie(array($code, $dats, $return));
		return $return;
	}

	public function search(Request $request)
	{
		$code = trim($request->code);

		// rules
		$rules = array(
			'max'					=> 	19,
			'proc_idx'		=>	0,
			'bt_idx'			=>	2,
			'blockId_idx'	=>	4,
			'ppos_idx'		=>	6,
			'pt_idx'			=>	7,
			'panIDx'			=>	9,
			'ppId_idx'		=>	11,
			'mtParid_idx'	=>	13,
			'mt_idx'			=>	14,
			'proj_idx'		=>	16,
		);

		$dats = $this->split_code($code, $rules);
		if (empty($dats)) return view('track.show')->withMessage("code: $code is invalid. Please Input Correct code !");

		list(
			$proc_shortname, $bt_id, $block_id, $ppos_id, $pt_id, $pan_id,
			$pp_id, $mt_parid, $mt_id, $proj_id
		) = $dats;

		if (!strcmp($proc_shortname, 'PP')) list($arr, $logs, $message) = $this->query_pp($code, $dats);
		else if (!strcmp($proc_shortname, 'PR')) list($arr, $logs, $message) = $this->query_pr($code, $dats);
		else if (!strcmp($proc_shortname, 'FA')) list($arr, $logs, $message) = $this->query_fa($code, $dats);
		else if (!strcmp($proc_shortname, 'SA')) list($arr, $logs, $message) = $this->query_sa($code, $dats);
		else if (!strcmp($proc_shortname, 'AS')) list($arr, $logs, $message) = $this->query_as($code, $dats);
		else if (!strcmp($proc_shortname, 'ER')) list($arr, $logs, $message) = $this->query_er($code, $dats);
		else {
			$arr=	array();
			$logs=	array();
			$message= "No Details found for $code. Try to search again !";
		}
		//helpme::print_rdie(array($arr, $logs, $message));
		return view('track.show', compact('arr', 'logs', 'message'));
	}

	private function query_pp(string $code, array $dats)
	{
		list(
			$proc_shortname, $bt_sname, $block_id, $ppos_id, $pt_sname, $pan_id,
			$pp_id, $mt_parid, $mt_id, $proj_id
		) = $dats;

		$proc_shortname	=	trim($proc_shortname);
		$bt_sname					=	trim($bt_sname);
		$block_id				=	intval($block_id);
		$ppos_id				=	intval($ppos_id);
		$pt_sname					=	trim($pt_sname);
		$pan_id					=	intval($pan_id);
		$pp_id					=	intval($pp_id);
		$mt_parid				=	intval($mt_parid);
		$mt_id					=	intval($mt_id);
		$proj_id				=	intval($proj_id);

		// return variable
		$arr=		array();
		$logs=		array();
		$message=	'';

		// get logs
		$lookup =	progress::join('m_code', 'tb_progress.code_id', 'm_code.code_id')
			->select('tb_progress.mat_id')
			->where('m_code.code_name', 'LIKE', $code)
			->get()->last();

		if (isset($lookup->mat_id)) {
			$mat_id = intval($lookup->mat_id);
			$logs = progress::showPp($proj_id, $mat_id);
		} else {
			$message= "No Details found for $code. Try to search again !";
			return array($arr, $logs, $message);
		}

		// Helpme::print_rdie($dats);
		$process 			= process::where('proc_shortname', $proc_shortname)->get('proc_name')->last()->proc_name;
		$block_type 	= !empty($bt_sname) && strcmp($bt_sname, 'XX') ? block_type::where('bt_shortname', $bt_sname)->get('bt_name')->last()->bt_name : '-';
		$block_no 		= $block_id > 0 ? $block_id : '-';
		$pan_position	= $ppos_id > 0 ? panel_position::where('ppos_id', $ppos_id)->get('ppos_name')->last()->ppos_name : '-';
		$pan_type 		= !empty($pt_sname) && strcmp($pt_sname, 'XX') ? panel_type::where('pt_shortname', $pt_sname)->get('pt_name')->last()->pt_name : '-';
		$pan_no 			= $pan_id > 0 ? $pan_id : '-';
		$piece_part 	= $pp_id > 0 ? piecepart::where('pp_id', $pp_id)->get('pp_name')->last()->pp_name : '-';
		$general_mat	= $mt_parid > 0 ? material_type::where('mt_id', $mt_parid)->get('mt_name')->last()->mt_name : '-';
		$mat_type 		= $mt_id > 0 ? material_type::where('mt_id', $mt_id)->get('mt_name')->last()->mt_name : '-';
		$proj_name	 	= $proj_id > 0 ? project::where('proj_id', $proj_id)->get('proj_name')->last()->proj_name : '-';

		$arr =	array(
			'code'					=> $code,
			'process' 			=> $process,
			'block_type' 		=> $block_type,
			'block_no' 			=> $block_no,
			'pan_position'	=> $pan_position,
			'pan_type' 			=> $pan_type,
			'pan_no' 				=> $pan_no,
			'piece_part' 		=> $piece_part,
			'general_mat' 	=> $general_mat,
			'mat_type' 			=> $mat_type,
			'proj_name' 		=> $proj_name,
		);

		return array($arr, $logs, $message);
	}

	private function query_pr(string $code, array $dats)
	{
		list(
			$proc_shortname, $bt_sname, $block_id, $ppos_id, $pt_sname, $pan_id,
			$pp_id, $mt_parid, $mt_id, $proj_id
		) = $dats;

		$proc_shortname	=	trim($proc_shortname);
		$bt_sname					=	trim($bt_sname);
		$block_id				=	intval($block_id);
		$ppos_id				=	intval($ppos_id);
		$pt_sname					=	trim($pt_sname);
		$pan_id					=	intval($pan_id);
		$pp_id					=	intval($pp_id);
		$mt_parid				=	intval($mt_parid);
		$mt_id					=	intval($mt_id);
		$proj_id				=	intval($proj_id);

		// return variable
		$arr=		array();
		$logs=		array();
		$message=	'';

		// get logs
		$lookup =	progress::join('m_code', 'tb_progress.code_id', 'm_code.code_id')
			->select('tb_progress.mat_id')
			->where('m_code.code_name', 'LIKE', $code)
			->get()->last();

		if (isset($lookup->mat_id)) {
			$mat_id = intval($lookup->mat_id);
			$logs = progress::showPr($proj_id, $mat_id);
		} else {
			$message= "No Details found for $code. Try to search again !";
			return array($arr, $logs, $message);
		}

		// Helpme::print_rdie($proj_id);
		$process 			= process::where('proc_shortname', $proc_shortname)->get('proc_name')->last()->proc_name;
		$block_type 	= !empty($bt_sname) && strcmp($bt_sname, 'XX') ? block_type::where('bt_shortname', $bt_sname)->get('bt_name')->last()->bt_name : '-';
		$block_no 		= $block_id > 0 ? $block_id : '-';
		$pan_position	= $ppos_id > 0 ? panel_position::where('ppos_id', $ppos_id)->get('ppos_name')->last()->ppos_name : '-';
		$pan_type 		= !empty($pt_sname) && strcmp($pt_sname, 'XX') ? panel_type::where('pt_shortname', $pt_sname)->get('pt_name')->last()->pt_name : '-';
		$pan_no 			= $pan_id > 0 ? $pan_id : '-';
		$piece_part 	= $pp_id > 0 ? piecepart::where('pp_id', $pp_id)->get('pp_name')->last()->pp_name : '-';
		$general_mat	= $mt_parid > 0 ? material_type::where('mt_id', $mt_parid)->get('mt_name')->last()->mt_name : '-';
		$mat_type 		= $mt_id > 0 ? material_type::where('mt_id', $mt_id)->get('mt_name')->last()->mt_name : '-';
		$proj_name	 	= $proj_id > 0 ? project::where('proj_id', $proj_id)->get('proj_name')->last()->proj_name : '-';

		$arr =	array(
			'code'					=> $code,
			'process' 			=> $process,
			'block_type' 		=> $block_type,
			'block_no' 			=> $block_no,
			'pan_position'	=> $pan_position,
			'pan_type' 			=> $pan_type,
			'pan_no' 				=> $pan_no,
			'piece_part' 		=> $piece_part,
			'general_mat' 	=> $general_mat,
			'mat_type' 			=> $mat_type,
			'proj_name' 		=> $proj_name,
		);

		return array($arr, $logs, $message);
	}

	private function query_fa(string $code, array $dats)
	{
		list(
			$proc_shortname, $bt_sname, $block_id, $ppos_id, $pt_sname, $pan_id,
			$pp_id, $mt_parid, $mt_id, $proj_id
		) = $dats;

		$proc_shortname	=	trim($proc_shortname);
		$bt_sname					=	trim($bt_sname);
		$block_id				=	intval($block_id);
		$ppos_id				=	intval($ppos_id);
		$pt_sname					=	trim($pt_sname);
		$pan_id					=	intval($pan_id);
		$pp_id					=	intval($pp_id);
		$mt_parid				=	intval($mt_parid);
		$mt_id					=	intval($mt_id);
		$proj_id				=	intval($proj_id);

		// return variable
		$arr=		array();
		$logs=		array();
		$message=	'';

		// get logs
		$lookup =	progress::join('m_code', 'tb_progress.code_id', 'm_code.code_id')
			->select('tb_progress.pp_id')
			->where('m_code.code_name', 'LIKE', $code)
			->get()->last();

		if (isset($lookup->pp_id)) {
			$pp_id = intval($lookup->pp_id);
			$logs = 	progress::showFa($proj_id, $pp_id);
		} else {
			$message= "No Details found for $code. Try to search again !";
			return array($arr, $logs, $message);
		}

		// Helpme::print_rdie($proj_id);
		$process 			= process::where('proc_shortname', $proc_shortname)->get('proc_name')->last()->proc_name;
		$block_type 	= !empty($bt_sname) && strcmp($bt_sname, 'XX') ? block_type::where('bt_shortname', $bt_sname)->get('bt_name')->last()->bt_name : '-';
		$block_no 		= $block_id > 0 ? $block_id : '-';
		$pan_position	= $ppos_id > 0 ? panel_position::where('ppos_id', $ppos_id)->get('ppos_name')->last()->ppos_name : '-';
		$pan_type 		= !empty($pt_sname) && strcmp($pt_sname, 'XX') ? panel_type::where('pt_shortname', $pt_sname)->get('pt_name')->last()->pt_name : '-';
		$pan_no 			= $pan_id > 0 ? $pan_id : '-';
		$piece_part 	= $pp_id > 0 ? piecepart::where('pp_id', $pp_id)->get('pp_name')->last()->pp_name : '-';
		$general_mat	= $mt_parid > 0 ? material_type::where('mt_id', $mt_parid)->get('mt_name')->last()->mt_name : '-';
		$mat_type 		= $mt_id > 0 ? material_type::where('mt_id', $mt_id)->get('mt_name')->last()->mt_name : '-';
		$proj_name	 	= $proj_id > 0 ? project::where('proj_id', $proj_id)->get('proj_name')->last()->proj_name : '-';

		$arr =	array(
			'code'					=> $code,
			'process' 			=> $process,
			'block_type' 		=> $block_type,
			'block_no' 			=> $block_no,
			'pan_position'	=> $pan_position,
			'pan_type' 			=> $pan_type,
			'pan_no' 				=> $pan_no,
			'piece_part' 		=> $piece_part,
			'general_mat' 	=> $general_mat,
			'mat_type' 			=> $mat_type,
			'proj_name' 		=> $proj_name,
		);

		return array($arr, $logs, $message);
	}

	private function query_sa(string $code, array $dats)
	{
		list(
			$proc_shortname, $bt_sname, $block_id, $ppos_id, $pt_sname, $pan_id,
			$pp_id, $mt_parid, $mt_id, $proj_id
		) = $dats;

		$proc_shortname	=	trim($proc_shortname);
		$bt_sname					=	trim($bt_sname);
		$block_id				=	intval($block_id);
		$ppos_id				=	intval($ppos_id);
		$pt_sname					=	trim($pt_sname);
		$pan_id					=	intval($pan_id);
		$pp_id					=	intval($pp_id);
		$mt_parid				=	intval($mt_parid);
		$mt_id					=	intval($mt_id);
		$proj_id				=	intval($proj_id);

		// return variable
		$arr=		array();
		$logs=		array();
		$message=	'';

		// get logs
		$lookup =	progress::join('m_code', 'tb_progress.code_id', 'm_code.code_id')
			->select('tb_progress.pan_id')
			->where('m_code.code_name', 'LIKE', $code)
			->get()->last();

		if (isset($lookup->pan_id)) {
			$pan_id = intval($lookup->pan_id);
			$logs = 	progress::showSa($proj_id, $pan_id);
		} else {
			$message= "No Details found for $code. Try to search again !";
			return array($arr, $logs, $message);
		}

		// Helpme::print_rdie($proj_id);
		$process 			= process::where('proc_shortname', $proc_shortname)->get('proc_name')->last()->proc_name;
		$block_type 	= !empty($bt_sname) && strcmp($bt_sname, 'XX') ? block_type::where('bt_shortname', $bt_sname)->get('bt_name')->last()->bt_name : '-';
		$block_no 		= $block_id > 0 ? $block_id : '-';
		$pan_position	= $ppos_id > 0 ? panel_position::where('ppos_id', $ppos_id)->get('ppos_name')->last()->ppos_name : '-';
		$pan_type 		= !empty($pt_sname) && strcmp($pt_sname, 'XX') ? panel_type::where('pt_shortname', $pt_sname)->get('pt_name')->last()->pt_name : '-';
		$pan_no 			= $pan_id > 0 ? $pan_id : '-';
		$piece_part 	= $pan_id > 0 && $proj_id > 0 ? $this->get_piecepart_list($proj_id, $pan_id) : '-';
		$general_mat	= $mt_parid > 0 ? material_type::where('mt_id', $mt_parid)->get('mt_name')->last()->mt_name : '-';
		$mat_type 		= $mt_id > 0 ? material_type::where('mt_id', $mt_id)->get('mt_name')->last()->mt_name : '-';
		$proj_name	 	= $proj_id > 0 ? project::where('proj_id', $proj_id)->get('proj_name')->last()->proj_name : '-';

		$arr =	array(
			'code'					=> $code,
			'process' 			=> $process,
			'block_type' 		=> $block_type,
			'block_no' 			=> $block_no,
			'pan_position'	=> $pan_position,
			'pan_type' 			=> $pan_type,
			'pan_no' 				=> $pan_no,
			'piece_part' 		=> $piece_part,
			'general_mat' 	=> $general_mat,
			'mat_type' 			=> $mat_type,
			'proj_name' 		=> $proj_name,
		);

		return array($arr, $logs, $message);
	}

	private function query_as(string $code, array $dats)
	{
		list(
			$proc_shortname, $bt_sname, $block_id, $ppos_id, $pt_sname, $pan_id,
			$pp_id, $mt_parid, $mt_id, $proj_id
		) = $dats;

		$proc_shortname	=	trim($proc_shortname);
		$bt_sname					=	trim($bt_sname);
		$block_id				=	intval($block_id);
		$ppos_id				=	intval($ppos_id);
		$pt_sname					=	trim($pt_sname);
		$pan_id					=	intval($pan_id);
		$pp_id					=	intval($pp_id);
		$mt_parid				=	intval($mt_parid);
		$mt_id					=	intval($mt_id);
		$proj_id				=	intval($proj_id);

		// return variable
		$arr=		array();
		$logs=		array();
		$message=	'';

		// get logs
		$lookup =	progress::join('m_code', 'tb_progress.code_id', 'm_code.code_id')
			->select('tb_progress.block_id')
			->where('m_code.code_name', 'LIKE', $code)
			->get()->last();

		if (isset($lookup->block_id)) {
			$block_id = intval($lookup->block_id);
			$logs = 	progress::showAs($proj_id, $block_id);
		} else {
			$message= "No Details found for $code. Try to search again !";
			return array($arr, $logs, $message);
		}

		// Helpme::print_rdie($proj_id);
		$process 			= process::where('proc_shortname', $proc_shortname)->get('proc_name')->last()->proc_name;
		$block_type 	= !empty($bt_sname) && strcmp($bt_sname, 'XX') ? block_type::where('bt_shortname', $bt_sname)->get('bt_name')->last()->bt_name : '-';
		$block_no 		= $block_id > 0 ? $block_id : '-';
		$pan_position	= $ppos_id > 0 ? panel_position::where('ppos_id', $ppos_id)->get('ppos_name')->last()->ppos_name : '-';
		$pan_type 		= !empty($pt_sname) && strcmp($pt_sname, 'XX') ? panel_type::where('pt_shortname', $pt_sname)->get('pt_name')->last()->pt_name : '-';
		$pan_no 			= $proj_id > 0 && $block_id > 0 ? $this->get_panel_list($proj_id, $block_id) : '-';
		$piece_part 	= $pp_id > 0 ? piecepart::where('pp_id', $pp_id)->get('pp_name')->last()->pp_name : '-';
		$general_mat	= $mt_parid > 0 ? material_type::where('mt_id', $mt_parid)->get('mt_name')->last()->mt_name : '-';
		$mat_type 		= $mt_id > 0 ? material_type::where('mt_id', $mt_id)->get('mt_name')->last()->mt_name : '-';
		$proj_name	 	= $proj_id > 0 ? project::where('proj_id', $proj_id)->get('proj_name')->last()->proj_name : '-';

		$arr =	array(
			'code'					=> $code,
			'process' 			=> $process,
			'block_type' 		=> $block_type,
			'block_no' 			=> $block_no,
			'pan_position'	=> $pan_position,
			'pan_type' 			=> $pan_type,
			'pan_no' 				=> $pan_no,
			'piece_part' 		=> $piece_part,
			'general_mat' 	=> $general_mat,
			'mat_type' 			=> $mat_type,
			'proj_name' 		=> $proj_name,
		);

		return array($arr, $logs, $message);
	}

	private function query_er(string $code, array $dats)
	{
		list(
			$proc_shortname, $bt_sname, $block_id, $ppos_id, $pt_sname, $pan_id,
			$pp_id, $mt_parid, $mt_id, $proj_id
		) = $dats;

		$proc_shortname	=	trim($proc_shortname);
		$bt_sname					=	trim($bt_sname);
		$block_id				=	intval($block_id);
		$ppos_id				=	intval($ppos_id);
		$pt_sname					=	trim($pt_sname);
		$pan_id					=	intval($pan_id);
		$pp_id					=	intval($pp_id);
		$mt_parid				=	intval($mt_parid);
		$mt_id					=	intval($mt_id);
		$proj_id				=	intval($proj_id);

		// return variable
		$arr=		array();
		$logs=		array();
		$message=	'';

		// get logs
		$lookup =	progress::join('m_code', 'tb_progress.code_id', 'm_code.code_id')
			->select('tb_progress.ship_id')
			->where('m_code.code_name', 'LIKE', $code)
			->get()->last();

		if (isset($lookup->ship_id)) {
			$ship_id = intval($lookup->ship_id);
			$logs = 	progress::showEr($proj_id, $ship_id);
		} else {
			$message= "No Details found for $code. Try to search again !";
			return array($arr, $logs, $message);
		}

		// Helpme::print_rdie($proj_id);
		$process 			= process::where('proc_shortname', $proc_shortname)->get('proc_name')->last()->proc_name;
		$block_type 	= !empty($bt_sname) && strcmp($bt_sname, 'XX') ? block_type::where('bt_shortname', $bt_sname)->get('bt_name')->last()->bt_name : '-';
		$block_no 		= $proj_id > 0 && $ship_id > 0 ? $this->get_block_list($proj_id, $ship_id) : '-';
		$pan_position	= $ppos_id > 0 ? panel_position::where('ppos_id', $ppos_id)->get('ppos_name')->last()->ppos_name : '-';
		$pan_type 		= !empty($pt_sname) && strcmp($pt_sname, 'XX') ? panel_type::where('pt_shortname', $pt_sname)->get('pt_name')->last()->pt_name : '-';
		$pan_no 			= $pan_id > 0 ? $pan_id : '-';
		$piece_part 	= $pp_id > 0 ? piecepart::where('pp_id', $pp_id)->get('pp_name')->last()->pp_name : '-';
		$general_mat	= $mt_parid > 0 ? material_type::where('mt_id', $mt_parid)->get('mt_name')->last()->mt_name : '-';
		$mat_type 		= $mt_id > 0 ? material_type::where('mt_id', $mt_id)->get('mt_name')->last()->mt_name : '-';
		$proj_name	 	= $proj_id > 0 ? project::where('proj_id', $proj_id)->get('proj_name')->last()->proj_name : '-';

		$arr =	array(
			'code'					=> $code,
			'process' 			=> $process,
			'block_type' 		=> $block_type,
			'block_no' 			=> $block_no,
			'pan_position'	=> $pan_position,
			'pan_type' 			=> $pan_type,
			'pan_no' 				=> $pan_no,
			'piece_part' 		=> $piece_part,
			'general_mat' 	=> $general_mat,
			'mat_type' 			=> $mat_type,
			'proj_name' 		=> $proj_name,
		);

		return array($arr, $logs, $message);
	}

	private function get_piecepart_list(int $proj_id, int $pan_id)
	{
		$UlFormat = <<<ulf
		<ul class="list-group-flush" style="padding-inline-start:0px">%s</ul>
		ulf;
		$LiFormat = <<<lif
		<li class="list-group-item" style="background-color: transparent;border:none;"><a href="%s" data-remote="false" data-toggle="modal" data-target="#formModal">%s - %s - no %d</a></li>
		lif;
		$PpDats = piecepart::join('tb_material', 'tb_piecepart.mat_id', '=', 'tb_material.mat_id')
			->join('m_material_type', 'tb_material.mt_id', '=', 'm_material_type.mt_id')
			->select('pp_name', 'pp_no', 'pp_id', 'm_material_type.mt_name')
			->where('pan_id', $pan_id)->get();
		$str = array();
		foreach ($PpDats as $pp) {
			$str[] = sprintf($LiFormat, route('fa.show', [$proj_id, $pp->pp_id]), $pp->mt_name, $pp->pp_name, $pp->pp_no);
		}
		return sprintf($UlFormat, implode('', $str));
	}

	private function get_panel_list(int $proj_id, int $block_id)
	{
		$UlFormat = <<<ulf
		<ul class="list-group-flush" style="padding-inline-start:0px">%s</ul>
		ulf;
		$LiFormat = <<<lif
		<li class="list-group-item" style="background-color: transparent;border:none;"><a href="%s" data-remote="false" data-toggle="modal" data-target="#formModal">%s (%s) - %s - no %d </a></li>
		lif;
		$PanDats = panel::join('m_panel_type', 'tb_panel.pt_id', '=', 'm_panel_type.pt_id')
			->join('m_panel_position', 'tb_panel.ppos_id', '=', 'm_panel_position.ppos_id')
			->select('pan_id', 'pan_no', 'pt_name', 'pt_shortname', 'ppos_name')
			->where('block_id', $block_id)->get();
		$str = array();
		foreach ($PanDats as $aDat) {
			$str[] = sprintf($LiFormat, route('sa.show', [$proj_id, $aDat->pan_id]), $aDat->pt_shortname, $aDat->pt_name, $aDat->ppos_name, $aDat->pan_no);
		}
		return sprintf($UlFormat, implode('', $str));
	}

	private function get_block_list(int $proj_id, int $ship_id)
	{
		$UlFormat = <<<ulf
		<ul class="list-group-flush" style="padding-inline-start:0px">%s</ul>
		ulf;
		$LiFormat = <<<lif
		<li class="list-group-item" style="background-color: transparent;border:none;"><a href="%s" data-remote="false" data-toggle="modal" data-target="#formModal">%s (%s) - no %d</a></li>
		lif;
		$blockDats = block::join('m_block_type', 'tb_block.bt_id', '=', 'm_block_type.bt_id')
			->select('block_id', 'block_no', 'bt_name', 'bt_shortname')
			->where('ship_id', $ship_id)->get();
		$str = array();
		foreach ($blockDats as $aDat) {
			$str[] = sprintf($LiFormat, route('as.show', [$proj_id, $aDat->block_id]), $aDat->bt_shortname, $aDat->bt_name, $aDat->block_no);
		}
		return sprintf($UlFormat, implode('', $str));
	}
}
