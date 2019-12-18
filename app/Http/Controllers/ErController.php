<?php

namespace App\Http\Controllers;

use App\Helpers\Helpme;
use Illuminate\Http\Request;
use Validator;

use App\process;
use App\ship;
use App\block;
use App\project;
use App\status;
use App\panel;
use App\piecepart;
use App\material;
use App\progress;
use App\code;
use PDF;

use Illuminate\Support\Facades\Auth;

class ErController extends Controller
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
		$ErProcDats=process::select('proc_id','proc_name')
												->where([['proc_mne', 'LIKE', '6.%'], ['proc_lvl', 2]])
												->orWhere('proc_mne', 'LIKE', '7%')
												->get();

		$_ErProcDats=['proc_id','proc_name'];
		foreach($ErProcDats as $adats){
			$_ErProcDats['proc_id'][]=	$adats->proc_id;
			$_ErProcDats['proc_name'][]=	$adats->proc_name;
		}

		if (request()->ajax()) {
			$_ErProcIds=	$_ErProcDats['proc_id'];

			return datatables()->of(
				ship::join('m_stat', 'tb_ship.stat_id', '=', 'm_stat.stat_id')
				->join('m_process', 'tb_ship.proc_id', '=', 'm_process.proc_id')
				->select('tb_ship.*', 'm_stat.stat_name', 'm_process.proc_name', 'm_process.proc_parid', 'm_process.proc_shortname')
				->where('tb_ship.proj_id', $proj_id)
				->whereIn('tb_ship.proc_id', $_ErProcIds)
				->orderBy('tb_ship.proc_id', 'ASC')
				->orderBy('tb_ship.stat_id', 'ASC')->get())
				->addColumn('proc_shortname', function ($data) {
					if($data->proc_parid > 0){
						return trim(process::where('proc_id', $data->proc_parid)->get('proc_shortname')->first()->proc_shortname);
					}
					else {
						return $data->proc_shortname;
					}
				})
				->addColumn('block', function($data){
					$UlFormat=<<<ulf
					<ul class="list-group-flush" style="padding-inline-start:0px">%s</ul>
					ulf;
					$LiFormat=<<<lif
					<li class="list-group-item" style="background-color: transparent;border:none;"><a href="%s" data-remote="false" data-toggle="modal" data-target="#formModal">%s (%s)</a></li>
					lif;
					$blockDats= block::join('m_block_type', 'tb_block.bt_id','=','m_block_type.bt_id')
										->select('block_id', 'bt_name', 'bt_shortname')
										->where('ship_id', $data->ship_id)->get();
					$str= array();
					foreach($blockDats as $aDat){
						$str[]= sprintf($LiFormat, route('as.show', [$data->proj_id, $aDat->block_id]),$aDat->bt_shortname,$aDat->bt_name);
					}
					return sprintf($UlFormat, implode('',$str));
				})
				->addColumn('action', function ($data) {
					$format_modal = <<<frm
					<a href="%s" data-remote="false" data-toggle="modal" data-target="#formModal">
						<button type="button" title="%s" name="%s" id=%d class="%s btn %s btn-sm"><i class="fa %s"></i></button>
					</a>
					frm;
					$proj_name= project::where('proj_id', $data->proj_id)->get('proj_name')->first()->proj_name;
					$button = sprintf($format_modal, route('er.show', [$data->proj_id, $data->ship_id]), "View progress Ship $proj_name", 'progress', $proj_name, 'edit', 'btn-info', 'fa-info');
					if($data->stat_id < 3 && (Auth::user()->roles->first()->name == 'Production Planning Control' || Auth::user()->roles->first()->name == 'Super Admin')) $button .= sprintf($format_modal, route('er.edit', [$data->proj_id, $data->ship_id]), "Update Ship $proj_name", 'update', $proj_name, 'update', 'btn-primary', 'fa-edit');
					// if($data->stat_id < 2) $button .= sprintf($format_modal, route('er.delete', [$data->proj_id, $data->ship_id]), "Delete Ship $proj_name", 'delete', $proj_name, 'delete', 'btn-danger', 'fa-trash');
					if($data->stat_id > 1) $button .= sprintf($format_modal, route('er.qrcode', [$data->proj_id, $data->ship_id]), "View QR Code $proj_name", 'qrcode', $proj_name, 'qrcode', 'btn-secondary', 'fa-qrcode');
					return $button;
				})
				->rawColumns(['action', 'proc_shortname', 'block'])
				->toJson();

		}

		$stat_dats = status::get();
		return view('progress.er.index', compact(['proj_id', 'stat_dats', '_ErProcDats']));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create($proj_id)
	{
		$proj_id=		intval($proj_id);
		$blockDats=	block::join('m_block_type', 'tb_block.bt_id', '=', 'm_block_type.bt_id')
								->select('tb_block.*', 'm_block_type.bt_name')
								->where([['tb_block.ship_id', 0], ['tb_block.proj_id', $proj_id], ['tb_block.stat_id', 2]])
								->get();

		return view('progress.er.add', compact(['blockDats', 'proj_id']));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request, $proj_id)
	{
		$counter=	intval($request->block_count);
		$proj_id=	intval($proj_id);
		$rules= array();
		$blockIds= array();
		for ($i=1; $i < $counter + 1; $i++){
			$rules["block_id$i"]= 'required|integer';
			$blockIds[] = intval($request->{'block_id'.$i});
		}

		$error = Validator::make($request->all(), $rules);
		if ($error->fails()) {
			$return = str_replace("must be an integer", "", $error->errors()->all());
			$return = str_replace("The block id", "Please select block ", $return);
			return response()->json(['errors' => $return]);
		}

		// declare request variabel
		$remark=	trim($request->remark);

		// insert ship
		$form_data = array(
			'proj_id'	=>  $proj_id,
			'proc_id'	=>  23, // cutting to fitting, first sub process
			'stat_id'	=>  1,	// waiting for next sub process
			'remark'	=>  $remark,
		);
		$ship_id = ship::create($form_data)->ship_id;
		if($ship_id < 1) return response()->json(['errors' => ['Data errors']]);

		// update block
		block::where('proj_id', $proj_id)->whereIn('block_id', $blockIds)->update(array('stat_id'=> 3, 'ship_id'=>$ship_id));

		// insert progress
		$username= 	auth()->user()->name;
		$proj_name= project::where('proj_id', $proj_id)->get('proj_name')->first()->proj_name;
		$progress_data = array(
			'proj_id'			=>  $proj_id,
			'user_id'			=>  intval(auth()->user()->id),
			'proc_id'			=>  22,	// Erection
			'ship_id'			=>  $ship_id,
			'prog_remark'	=>  "Ship $proj_name was added to the Erection Process, made from $counter block by $username",
		);
		progress::create($progress_data);

		$progress_data = array(
			'proj_id'			=>  $proj_id,
			'user_id'			=>  intval(auth()->user()->id),
			'proc_id'			=>  23,	// cutting to fitting
			'ship_id'			=>  $ship_id,
			'prog_remark'	=>  "Ship $proj_name has moved to the Cutting to Fitting Sub Process created by $username",
		);
		progress::create($progress_data);

		return response()->json(['success' => 'Data Added successfully.']);
	}

	public function allqrcode($proj_id)
    {
		$proj_id= intval($proj_id);

		// get process details
		$ErProcDats=process::select('proc_id','proc_name')
												->where([['proc_mne', 'LIKE', '6.%'], ['proc_lvl', 2]])
												->orWhere('proc_mne', 'LIKE', '7%')
												->get();

		$_ErProcDats=['proc_id','proc_name'];
		foreach($ErProcDats as $adats){
			$_ErProcDats['proc_id'][]=	$adats->proc_id;
			$_ErProcDats['proc_name'][]=	$adats->proc_name;
		}
		$_ErProcIds=	$_ErProcDats['proc_id'];

		$ship = ship::join('m_stat', 'tb_ship.stat_id', '=', 'm_stat.stat_id')
						->join('m_project', 'tb_ship.proj_id', '=', 'm_project.proj_id')
						->select('tb_ship.*','m_project.proj_name')
						->where('tb_ship.proj_id', $proj_id)
						->where('tb_ship.stat_id', '>', '1')
						->whereIn('tb_ship.proc_id', $_ErProcIds)
						->get();

		foreach($ship as $key => $sh){
			$name[] = $sh->proj_name;

			if($proj_id >= '1000'){
				$no_proj = '999';
			}else{
				$no_proj = sprintf('%03d', $proj_id);
			}
			
			$qrcode[$key] = "ERXX000XX0000000".$no_proj; 

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
				progress::where([ ['ship_id', $sh->ship_id], ['proc_id', 27] ])->update($prog_data);
			}
		}
			        
        view()->share('progress',$qrcode);
        $pdf = PDF::loadView('progress.allqrcode', compact('qrcode','name'));

        return $pdf->stream('qr-code-allqrcode-'.$proj_id.'.pdf');
    }

	public function qrcode($proj_id, $er_id)
    {
		$er_id= intval($er_id);

		$ship = ship::join('m_stat', 'tb_ship.stat_id', '=', 'm_stat.stat_id')
						->select('tb_ship.*')
						->where('tb_ship.ship_id', $er_id)
						->where('tb_ship.stat_id', '>', '1')
						->first();

		if($proj_id >= '1000'){
			$no_proj = '999';
		}else{
			$no_proj = sprintf('%03d', $proj_id);
		}

		$qrcode= "ERXX000XX0000000".$no_proj; 

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
			progress::where([ ['ship_id', $ship->ship_id], ['proc_id', 27] ])->update($prog_data);
		}   

        return view('progress.qrcode', compact('qrcode'));
    }

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($proj_id, $er_id)
	{
		$dats= progress::showEr($proj_id, $er_id);
		// Helpme::print_rdie($SaDats);
		return view('progress.er.show', compact('dats'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($proj_id, $er_id)
	{
		$ErProcDats=process::select('proc_id','proc_name')
												->where([['proc_mne', 'LIKE', '6.%'], ['proc_lvl', 2]])
												->orWhere('proc_mne', 'LIKE', '7%')
												->get();

		$_ErProcDats=['proc_id','proc_name'];
		foreach($ErProcDats as $adats){
			$_ErProcDats['proc_id'][]=	$adats->proc_id;
			$_ErProcDats['proc_name'][]=	$adats->proc_name;
		}

		$shipDats=	ship::where('ship_id', $er_id)->get()->last();
		$blockDats=	block::join('m_block_type', 'tb_block.bt_id', '=', 'm_block_type.bt_id')
								->select('tb_block.*', 'm_block_type.bt_name')
								->where('tb_block.proj_id', $proj_id)
								->whereIn('tb_block.ship_id', array(0,$er_id))
								->get();
		$blockSelected=array();
		$blockSelectedIds=array();
		foreach($blockDats as $aDat){
			if($aDat->ship_id != $er_id) continue;
			$blockSelected[]= $aDat;
			$blockSelectedIds[]= $aDat->block_id;
		}
		$blockSelectedIdsStr= json_encode($blockSelectedIds);

		return view('progress.er.edit', compact(['_ErProcDats','shipDats','blockSelected', 'blockSelectedIds','blockSelectedIdsStr', 'blockDats', 'proj_id']));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $proj_id, $er_id)
	{
		$counter=		intval($request->block_count);
		$proj_id=		intval($proj_id);
		$ship_id=		intval($request->hidden_id);
		$proc_id= 	intval($request->proc_id);

		$rules= 		array();
		$blockIds=	array();
		for ($i=1; $i < $counter + 1; $i++){
			$rules["block_id$i"]= 'required|integer';
			$blockIds[] = intval($request->{'block_id'.$i});
		}
		if($proc_id >= 27) $rules["delivery_at"]= 'required';

		$error = Validator::make($request->all(), $rules);
		if ($error->fails()) {
			$return = str_replace("must be an integer", "", $error->errors()->all());
			$return = str_replace(" is required.", ".", $return);
			$return = str_replace("The block id", "Please select block ", $return);
			$return = str_replace("The delivery at", "Please input date on delivery", $return);
			return response()->json(['errors' => $return]);
		}

		// declare parameter
		$proc_old=	intval($request->proc_old);
		$remark=		trim($request->remark);
		$username= 	auth()->user()->name;
		$proj_name= project::where('proj_id', $proj_id)->get('proj_name')->first()->proj_name;
		$proc_name= process::where('proc_id', $proc_id)->get('proc_name')->last()->proc_name;

		// are there panels changed?
		$old_counter= 	intval($request->old_block_count);
		$old_block_ids=	json_decode($request->old_block_ids);
		if ($old_counter != $counter || !empty(array_diff($old_block_ids,$blockIds))){
			// get all old pp_names
			$old_block_dats= block::join('m_block_type', 'tb_block.bt_id', 'm_block_type.bt_id')
												->whereIn('block_id', $old_block_ids)->get('bt_name');
			$old_block_names= array();
			foreach($old_block_dats as $adat){
				$old_block_names[]= $adat->bt_name;
			}
			$old_block_names= implode(',', $old_block_names);

			// get all new pp_names
			$new_block_dats= block::join('m_block_type', 'tb_block.bt_id', 'm_block_type.bt_id')
												->whereIn('block_id', $blockIds)->get('bt_name');
			$new_block_names= array();
			foreach($new_block_dats as $adat){
				$new_block_names[]= $adat->bt_name;
			}
			$new_block_names= implode(',', $new_block_names);

			// remove ship_id attribute on selected block_id
			block::where('proj_id', $proj_id)->whereIn('block_id', $old_block_ids)->update(array('stat_id'=> 2, 'ship_id'=>0));
			// update block
			block::where('proj_id', $proj_id)->whereIn('block_id', $blockIds)->update(array('stat_id'=> 3, 'ship_id'=>$ship_id));

			// update progress
			$prog_remark=	"Ship $proj_name has changed the composition of panel from $old_block_names to $new_block_names by $username";
			$progress_data = array(
				'proj_id'			=>  $proj_id,
				'user_id'			=>  intval(auth()->user()->id),
				'proc_id'			=>  $proc_id,
				'ship_id'			=>  $ship_id,
				'prog_remark'	=>  $prog_remark,
			);
			progress::create($progress_data);
		}

		// update ship
		$data = ship::findOrFail($ship_id);
		if ($data == null) return response()->json(['errors' => ['Data Not Found']]);
		if ($proc_id < 28){
			$form_data = array(
				'proc_id'		=>	$proc_id,
				'remark'		=>  $remark,
			);
			if($proc_id >= 27 ){
				$form_data['stat_id']= 2; // delivery sub process, no next sub process
				$form_data['delivered_at']= date_format(date_create($request->delivery_at), "Y-m-d");
			}
			else {
				$form_data['stat_id']= 1;
			}
			$data->where('ship_id', $ship_id)->update($form_data);
	
			// progress
			$prog_remark=	$proc_id != $proc_old ? "Ship $proj_name has moved to the $proc_name sub process by $username" : "Ship $proj_name has been updated by $username ";
			$progress_data = array(
				'proj_id'			=>  $proj_id,
				'user_id'			=>  intval(auth()->user()->id),
				'proc_id'			=>  $proc_id,
				'ship_id'			=>  $ship_id,
				'prog_remark'	=>  $prog_remark,
			);
			progress::create($progress_data);
		}
		else if ($proc_id == 28 ){
			// check if there are items left behind
			$errors= array();

			$block_check= block::where([['proj_id', $proj_id], ['stat_id', '<', 3]])->count();
			if($block_check > 0) $errors[] = "There are $block_check blocks on the Assembly Process that have not been used";

			$panel_check= panel::where([['proj_id', $proj_id], ['stat_id', '<', 3]])->count();
			if($panel_check > 0) $errors[] = "There are $panel_check panels on the Sub-Assembly Process that have not been used";

			$piecepart_check= piecepart::where([['proj_id', $proj_id], ['stat_id', '<', 3]])->count();
			if($piecepart_check > 0) $errors[] = "There are $piecepart_check pieceparts on the Fabrication Process that have not been used";

			$pr_check= material::where([['proj_id', $proj_id], ['pp_stat_id', '=', 3], ['pr_stat_id', '<', 3]])->count();
			if($pr_check > 0) $errors[] = "There are $pr_check materials on the Preparation Process that have not been used";

			$pp_check= material::where([['proj_id', $proj_id], ['pp_stat_id', '<', 3]])->count();
			if($pp_check > 0) $errors[] = "There are $pp_check materials on the Pra-Preparation Process that have not been used";

			// helpme::print_rdie($errors);
			if(!empty($errors)) return response()->json(['errors' => $errors]);

			// mark all is complete
			ship::where([['ship_id', $ship_id], ['proj_id', $proj_id], ['ship_id', $ship_id]])->update(array('stat_id'=> 4, 'proc_id'=>28));
			block::where([['proj_id', $proj_id], ['stat_id', 3]])->update(array('stat_id'=> 4));
			panel::where([['proj_id', $proj_id], ['stat_id', 3]])->update(array('stat_id'=> 4));
			piecepart::where([['proj_id', $proj_id], ['stat_id', 3]])->update(array('stat_id'=> 4));
			material::where([['proj_id', $proj_id], ['pr_stat_id', 3], ['pp_stat_id', 3]])->update(array('pr_stat_id'=> 4, 'pp_stat_id'=> 4));

			$progress_data = array(
				'proj_id'			=>  $proj_id,
				'user_id'			=>  intval(auth()->user()->id),
				'proc_id'			=>  28, // project finished
				'prog_remark'	=>  "Project $proj_name was completed by $username",
			);
			progress::create($progress_data);
		}

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
		return view('progress.er.delete');
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
