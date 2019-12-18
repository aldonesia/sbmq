<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\process;
use App\status;
use App\block;
use App\block_type;
use App\Helpers\Helpme;
use App\panel;
use App\piecepart;
use App\progress;
use App\code;
use PDF;

use Illuminate\Support\Facades\Auth;

class AsController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index($proj_id)
	{
		$proj_id = intval($proj_id);

		// get process details
		$AsProcDats=process::select('proc_id','proc_name')
												->where([['proc_mne', 'LIKE', '5.%'], ['proc_lvl', 2]])
												->get();

		$_AsProcDats=['proc_id','proc_name'];
		foreach($AsProcDats as $adats){
			$_AsProcDats['proc_id'][]=	$adats->proc_id;
			$_AsProcDats['proc_name'][]=	$adats->proc_name;
		}

		if (request()->ajax()) {
			$_AsProcIds=	$_AsProcDats['proc_id'];

			return datatables()->of(
				block::join('m_block_type', 'tb_block.bt_id', '=', 'm_block_type.bt_id')
				->join('m_stat', 'tb_block.stat_id', '=', 'm_stat.stat_id')
				->join('m_process', 'tb_block.proc_id', '=', 'm_process.proc_id')
				->select('tb_block.*', 'm_stat.stat_name', 'm_process.proc_name', 'm_process.proc_parid', 'm_block_type.bt_name', 'm_block_type.bt_shortname')
				->where('tb_block.proj_id', $proj_id)
				->whereIn('tb_block.proc_id', $_AsProcIds)
				->orderBy('tb_block.proc_id', 'ASC')
				->orderBy('tb_block.stat_id', 'ASC')
				->orderBy('tb_block.bt_id', 'ASC')
				->orderBy('tb_block.block_no', 'ASC')->get())
				->addColumn('proc_shortname', function ($data) {
					return trim(process::where('proc_id', $data->proc_parid)->get('proc_shortname')->first()->proc_shortname);
				})
				->addColumn('panel', function($data){
					$UlFormat=<<<ulf
					<ul class="list-group-flush" style="padding-inline-start:0px">%s</ul>
					ulf;
					$LiFormat=<<<lif
					<li class="list-group-item" style="background-color: transparent;border:none;"><a href="%s" data-remote="false" data-toggle="modal" data-target="#formModal">%s (%s) - %s</a></li>
					lif;
					$PanDats= panel::join('m_panel_type', 'tb_panel.pt_id','=','m_panel_type.pt_id')
										->join('m_panel_position', 'tb_panel.ppos_id','=','m_panel_position.ppos_id')
										->select('pan_id', 'pt_name', 'pt_shortname', 'ppos_name')
										->where('block_id', $data->block_id)->get();
					$str= array();
					foreach($PanDats as $aDat){
						$str[]= sprintf($LiFormat, route('sa.show', [$data->proj_id, $aDat->pan_id]), $aDat->pt_shortname,$aDat->pt_name, $aDat->ppos_name);
					}
					return sprintf($UlFormat, implode('',$str));
				})
				->addColumn('action', function ($data) {
					$format_modal = <<<frm
					<a href="%s" data-remote="false" data-toggle="modal" data-target="#formModal">
						<button type="button" title="%s" name="%s" id=%d class="%s btn %s btn-sm"><i class="fa %s"></i></button>
					</a>
					frm;
					$button = sprintf($format_modal, route('as.show', [$data->proj_id, $data->block_id]), "View progress Block $data->bt_name", 'progress', $data->bt_name, 'edit', 'btn-info', 'fa-info');
					if($data->stat_id < 3 && (Auth::user()->roles->first()->name == 'Production Planning Control' || Auth::user()->roles->first()->name == 'Super Admin')) $button .= sprintf($format_modal, route('as.edit', [$data->proj_id, $data->block_id]), "Update Block $data->bt_name", 'update', $data->bt_name, 'update', 'btn-primary', 'fa-edit');
					// if($data->stat_id < 2) $button .= sprintf($format_modal, route('as.delete', [$data->proj_id, $data->block_id]), "Delete Block $data->bt_name", 'delete', $data->bt_name, 'delete', 'btn-danger', 'fa-trash');
					if($data->stat_id > 1) $button .= sprintf($format_modal, route('as.qrcode', [$data->proj_id, $data->block_id]), "View QR Code $data->bt_name", 'qrcode', $data->bt_name, 'qrcode', 'btn-secondary', 'fa-qrcode');
					return $button;
				})
				->rawColumns(['action', 'proc_shortname', 'panel'])
				->toJson();

		}

		$stat_dats = status::get();
		return view('progress.as.index', compact(['proj_id', 'stat_dats', '_AsProcDats']));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create($proj_id)
	{
		$proj_id=		intval($proj_id);
		$btDats= 		block_type::get();
		$panDats=		panel::join('m_panel_position', 'tb_panel.ppos_id', '=', 'm_panel_position.ppos_id')
								->join('m_panel_type', 'tb_panel.pt_id', '=', 'm_panel_type.pt_id')
								->select('tb_panel.*', 'm_panel_type.pt_name', 'm_panel_position.ppos_name')
								->where([['tb_panel.block_id', 0], ['tb_panel.proj_id', $proj_id], ['tb_panel.stat_id', 2]])
								->get();

		return view('progress.as.add', compact(['btDats', 'panDats', 'proj_id']));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request, $proj_id)
	{
		$counter=	intval($request->pan_count);
		$proj_id=	intval($proj_id);
		$rules= array(
			'bt_id'	=> 'required|integer',
		);
		$PanIds= array();
		for ($i=1; $i < $counter + 1; $i++){
			$rules["pan_id$i"]= 'required|integer';
			$PanIds[] = intval($request->{'pan_id'.$i});
		}

		$error = Validator::make($request->all(), $rules);
		if ($error->fails()) {
			$return = str_replace("must be an integer", "", $error->errors()->all());
			$return = str_replace("The pan id", "Please select panel ", $return);
			$return = str_replace("The bt id", "Please select block type ", $return);
			return response()->json(['errors' => $return]);
		}

		// declare request variabel
		$bt_id= 	intval($request->bt_id);
		$remark=	trim($request->remark);

		// insert block
		$prev_block=		block::where('proj_id', $proj_id)->get('block_no')->last();
		$prev_block_no=	isset($prev_block->block_no) ? intval($prev_block->block_no) : 0;
		$block_no=			$prev_block_no + 1;
		$form_data = array(
			'proj_id'	=>  $proj_id,
			'bt_id'		=>	$bt_id,
			'proc_id'	=>  19, // cutting to fitting, first sub process
			'stat_id'	=>  1,	// waiting for next sub process
			'block_no'=>	$block_no,
			'remark'	=>  $remark,
		);
		$block_id = block::create($form_data)->block_id;
		if($block_id < 1) return response()->json(['errors' => ['Data errors']]);

		// update panel
		panel::where('proj_id', $proj_id)->whereIn('pan_id', $PanIds)->update(array('stat_id'=> 3, 'block_id'=>$block_id));

		// insert progress
		$username= 	auth()->user()->name;
		$bt_name= 	block_type::where('bt_id', $bt_id)->get()->last()->bt_name;
		$progress_data = array(
			'proj_id'			=>  $proj_id,
			'user_id'			=>  intval(auth()->user()->id),
			'proc_id'			=>  18,	// assembly
			'block_id'		=>  $block_id,
			'prog_remark'	=>  "Block $bt_name no $block_no was added to the Assembly Process, made from $counter panel by $username",
		);
		progress::create($progress_data);

		$progress_data = array(
			'proj_id'			=>  $proj_id,
			'user_id'			=>  intval(auth()->user()->id),
			'proc_id'			=>  19,	// cutting to fitting
			'block_id'		=>  $block_id,
			'prog_remark'	=>  "Block $bt_name no $block_no has moved to the Cutting to Fitting Sub Process created by $username",
		);
		progress::create($progress_data);

		return response()->json(['success' => 'Data Added successfully.']);
	}

	public function allqrcode($proj_id)
    {
		$proj_id= intval($proj_id);

		// get process details
		$AsProcDats=process::select('proc_id','proc_name')
												->where([['proc_mne', 'LIKE', '5.%'], ['proc_lvl', 2]])
												->get();

		$_AsProcDats=['proc_id','proc_name'];
		foreach($AsProcDats as $adats){
			$_AsProcDats['proc_id'][]=	$adats->proc_id;
			$_AsProcDats['proc_name'][]=	$adats->proc_name;
		}
		$_AsProcIds=	$_AsProcDats['proc_id'];

		$block = block::join('m_block_type', 'tb_block.bt_id', '=', 'm_block_type.bt_id')
						->join('m_stat', 'tb_block.stat_id', '=', 'm_stat.stat_id')
						->select('tb_block.*','m_block_type.bt_shortname','m_block_type.bt_name')
						->where('tb_block.proj_id', $proj_id)
						->where('tb_block.stat_id', '>', '1')
						->whereIn('tb_block.proc_id', $_AsProcIds)
						->get();
		
		$block_count = block::select('tb_block.proj_id')
							->where('tb_block.proj_id', $proj_id)
							->where('tb_block.stat_id', '>', '1')
							->count();
		// Helpme::print_rdie($block);
		
		foreach($block as $key => $bl){
			$block_id[] = $bl->block_id;
			$name[] = $bl->bt_shortname." - ".$bl->bt_name;

			$block_no[$key] = sprintf('%02d', $bl->block_no);
			if($block_no[$key] >= '100'){
				$block_no[$key] = '99';
			}
			if($proj_id >= '1000'){
				$no_proj = '999';
			}else{
				$no_proj = sprintf('%03d', $proj_id);
			}

			$qrcode[$key] = "AS".$bl->bt_shortname.$block_no[$key]."0XX0000000".$no_proj; 

			$qrcode_data = array(
				'code_name' =>  $qrcode[$key],
			);
			if (code::where('code_name', '=', $qrcode_data)->exists()) {
				//return
			}else{
				$code_id= code::create($qrcode_data)->code_id;
				$prog_data= array(
					'code_id' => $code_id,
				);
				progress::where([ ['block_id', $bl->block_id], ['proc_id', 21] ])->update($prog_data);
			}
		}
			        
        view()->share('progress',$qrcode);
        $pdf = PDF::loadView('progress.allqrcode', compact('qrcode','name'));

        return $pdf->stream('qr-code-allqrcode-'.$proj_id.'.pdf');
    }

	public function qrcode($proj_id, $as_id)
    {
		$as_id= intval($as_id);
		
        $block = block::join('m_block_type', 'tb_block.bt_id', '=', 'm_block_type.bt_id')
						->join('m_stat', 'tb_block.stat_id', '=', 'm_stat.stat_id')
						->select('tb_block.*','m_block_type.bt_shortname')
						->where('tb_block.block_id', $as_id)
						->where('tb_block.stat_id', '>', '1')
						->first();
						
		$block_count = block::select('tb_block.proj_id')
							->where('tb_block.proj_id', $proj_id)
							->where('tb_block.stat_id', '>', '1')
							->count();

		$block_no = sprintf('%02d', $block->block_no);
		if($block_no >= '100'){
			$block_no = '99';
		}
		if($proj_id >= '1000'){
			$no_proj = '999';
		}else{
			$no_proj = sprintf('%03d', $proj_id);
		}

		$qrcode= "AS".$block->bt_shortname.$block_no."0XX0000000".$no_proj; 

		$qrcode_data = array(
			'code_name' =>  $qrcode,
		);
		if (code::where('code_name', '=', $qrcode_data)->exists()) {
			//return
		}else{
			$code_id= code::create($qrcode_data)->code_id;
			$prog_data= array(
				'code_id' => $code_id,
			);
			progress::where([ ['block_id', $block->block_id], ['proc_id', 21] ])->update($prog_data);
		}   

        return view('progress.qrcode', compact('qrcode'));
    }

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($proj_id, $as_id)
	{
		$dats= progress::showAs($proj_id, $as_id);
		// Helpme::print_rdie($SaDats);
		return view('progress.as.show', compact('dats'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($proj_id, $as_id)
	{
		$AsProcDats=process::select('proc_id','proc_name')
												->where([['proc_mne', 'LIKE', '5.%'], ['proc_lvl', 2]])
												->get();

		$_AsProcDats=['proc_id','proc_name'];
		foreach($AsProcDats as $adats){
			$_AsProcDats['proc_id'][]=	$adats->proc_id;
			$_AsProcDats['proc_name'][]=	$adats->proc_name;
		}

		$btDats= 		block_type::get();

		$blockDats=	block::where('block_id', $as_id)->get()->last();
		$panDats=		panel::join('m_panel_position', 'tb_panel.ppos_id', '=', 'm_panel_position.ppos_id')
								->join('m_panel_type', 'tb_panel.pt_id', '=', 'm_panel_type.pt_id')
								->select('tb_panel.*', 'm_panel_type.pt_name', 'm_panel_position.ppos_name')
								->where('tb_panel.proj_id', $proj_id)
								->whereIn('tb_panel.block_id', array(0,$as_id))
								->get();
		$panSelected=array();
		$panSelectedIds=array();
		foreach($panDats as $aDat){
			if($aDat->block_id != $as_id) continue;
			$panSelected[]= $aDat;
			$panSelectedIds[]= $aDat->pan_id;
		}
		$panSelectedIdsStr= json_encode($panSelectedIds);
		// helpme::print_rdie($panSelected);
		return view('progress.as.edit', compact(['_AsProcDats','panDats','panSelected', 'panSelectedIds','panSelectedIdsStr','btDats', 'blockDats', 'proj_id']));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $proj_id, $block_id)
	{
		$counter=		intval($request->pan_count);
		$proj_id=		intval($proj_id);
		$block_id=	intval($request->hidden_id);
		$rules= array(
			'bt_id'	=> 'required|integer',
		);
		$PanIds= array();
		for ($i=1; $i < $counter + 1; $i++){
			$rules["pan_id$i"]= 'required|integer';
			$PanIds[] = intval($request->{'pan_id'.$i});
		}

		$error = Validator::make($request->all(), $rules);
		if ($error->fails()) {
			$return = str_replace("must be an integer", "", $error->errors()->all());
			$return = str_replace("The pan id", "Please select panel ", $return);
			$return = str_replace("The bt id", "Please select block type ", $return);
			return response()->json(['errors' => $return]);
		}

		// declare parameter
		$proc_old=	intval($request->proc_old);
		$proc_id= 	intval($request->proc_id);
		$bt_id=			intval($request->bt_id);
		$block_no=	intval($request->block_no);
		$remark=		trim($request->remark);
		$username= 	auth()->user()->name;
		$bt_name= 	block_type::where('bt_id', $bt_id)->get()->last()->bt_name;
		$proc_name= process::where('proc_id', $proc_id)->get('proc_name')->last()->proc_name;

		// are there panels changed?
		$old_counter= intval($request->old_pan_count);
		$old_pan_ids=	json_decode($request->old_pan_ids);
		if ($old_counter != $counter || !empty(array_diff($old_pan_ids,$PanIds))){
			// get all old pp_names
			$old_pan_dats= panel::join('m_panel_type', 'tb_panel.pt_id', 'm_panel_type.pt_id')
										->join('m_panel_position', 'tb_panel.ppos_id', 'm_panel_position.ppos_id')
										->select('pt_name', 'ppos_name')->whereIn('pan_id', $old_pan_ids)->get();
			$old_pan_names= array();
			foreach($old_pan_dats as $adat){
				$old_pan_names[]= $adat->pt_name .'-'. $adat->ppos_name;
			}
			$old_pan_names= implode(',', $old_pan_names);

			// get all new pp_names
			$new_pan_dats= panel::join('m_panel_type', 'tb_panel.pt_id', 'm_panel_type.pt_id')
									->join('m_panel_position', 'tb_panel.ppos_id', 'm_panel_position.ppos_id')
									->select('pt_name', 'ppos_name')->whereIn('pan_id', $PanIds)->get();
			$new_pan_names= array();
			foreach($new_pan_dats as $adat){
				$new_pan_names[]= $adat->pt_name .'-'. $adat->ppos_name;
			}
			$new_pan_names= implode(',', $new_pan_names);

			// remove block_id attribute on selected pan_id
			panel::where('proj_id', $proj_id)->whereIn('pan_id', $old_pan_ids)->update(array('stat_id'=> 2, 'block_id'=>0));
			// update panel
			panel::where('proj_id', $proj_id)->whereIn('pan_id', $PanIds)->update(array('stat_id'=> 3, 'block_id'=>$block_id));

			// update progress
			$prog_remark=	"Block $bt_name no $block_no has changed the composition of panel from $old_pan_names to $new_pan_names by $username";
			$progress_data = array(
				'proj_id'			=>  $proj_id,
				'user_id'			=>  intval(auth()->user()->id),
				'proc_id'			=>  $proc_id,
				'block_id'		=>  $block_id,
				'prog_remark'	=>  $prog_remark,
			);
			progress::create($progress_data);
		}

		// update block
		$data = block::findOrFail($block_id);
		if ($data == null) return response()->json(['errors' => ['Data Not Found']]);
		$form_data = array(
			'proc_id'		=>	$proc_id,
			'bt_id'			=>  $bt_id,
			'remark'		=>  $remark,
		);
		if($proc_id == 21 ){
			$form_data['stat_id']= 2; // welding sub process, no next sub process
		}
		else {
			$form_data['stat_id']= 1;
		}
		$data->where('block_id', $block_id)->update($form_data);

		// progress
		$prog_remark=	$proc_id != $proc_old ? "Block $bt_name no $block_no has moved to the $proc_name sub process by $username" : "Block $bt_name no $block_no has been updated by $username ";
		$progress_data = array(
			'proj_id'			=>  $proj_id,
			'user_id'			=>  intval(auth()->user()->id),
			'proc_id'			=>  $proc_id,
			'block_id'		=>  $block_id,
			'prog_remark'	=>  $prog_remark,
		);
		progress::create($progress_data);

		return response()->json(['success' => 'Data is successfully updated']);
	}

	/**
	 * Show the form for delete confirmations the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function delete($id)
	{
		return view('progress.as.delete');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}
}
