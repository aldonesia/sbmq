<?php

namespace App\Http\Controllers;

use App\Helpers\Helpme;
use Illuminate\Http\Request;

use Validator;

use App\process;
use App\status;
use App\piecepart;
use App\progress;
use App\panel;
use App\panel_position;
use App\panel_type;
use App\material;
use App\code;
use PDF;

use Illuminate\Support\Facades\Auth;

class SaController extends Controller
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
		$SaProcDats=process::select('proc_id','proc_name')
												->where([['proc_mne', 'LIKE', '4.%'], ['proc_lvl', 2]])
												->get();

		$_SaProcDats=['proc_id','proc_name'];
		foreach($SaProcDats as $adats){
			$_SaProcDats['proc_id'][]=	$adats->proc_id;
			$_SaProcDats['proc_name'][]=	$adats->proc_name;
		}
		if (request()->ajax()) {
			$_SaProcIds=	$_SaProcDats['proc_id'];

			return datatables()->of(
				panel::join('m_panel_position', 'tb_panel.ppos_id', '=', 'm_panel_position.ppos_id')
				->join('m_panel_type', 'tb_panel.pt_id', '=', 'm_panel_type.pt_id')
				->join('m_stat', 'tb_panel.stat_id', '=', 'm_stat.stat_id')
				->join('m_process', 'tb_panel.proc_id', '=', 'm_process.proc_id')
				->select('tb_panel.*', 'm_stat.stat_name', 'm_process.proc_name', 'm_process.proc_parid', 'm_panel_type.pt_name', 'm_panel_type.pt_shortname', 'm_panel_position.ppos_name')
				->where('tb_panel.proj_id', $proj_id)
				->whereIn('tb_panel.proc_id', $_SaProcIds)
				->orderBy('tb_panel.proc_id', 'ASC')
				->orderBy('tb_panel.stat_id', 'ASC')
				->orderBy('tb_panel.pt_id', 'ASC')
				->orderBy('tb_panel.pan_no', 'ASC')->get())
				->addColumn('proc_shortname', function ($data) {
					return trim(process::where('proc_id', $data->proc_parid)->get('proc_shortname')->first()->proc_shortname);
				})
				->addColumn('piecepart', function($data){
					$UlFormat=<<<ulf
					<ul class="list-group-flush" style="padding-inline-start:0px">%s</ul>
					ulf;
					$LiFormat=<<<lif
					<li class="list-group-item" style="background-color: transparent;border:none;"><a href="%s" data-remote="false" data-toggle="modal" data-target="#formModal">%s - %s</a></li>
					lif;
					$PpDats= piecepart::join('tb_material', 'tb_piecepart.mat_id','=','tb_material.mat_id')
										->join('m_material_type', 'tb_material.mt_id','=','m_material_type.mt_id')
										->select('pp_name', 'pp_id', 'm_material_type.mt_name')
										->where('pan_id', $data->pan_id)->get();
					$str= array();
					foreach($PpDats as $pp){
						$str[]= sprintf($LiFormat, route('fa.show', [$data->proj_id, $pp->pp_id]), $pp->mt_name, $pp->pp_name);
					}
					return sprintf($UlFormat, implode('',$str));
				})
				->addColumn('action', function ($data) {
					$format_modal = <<<frm
					<a href="%s" data-remote="false" data-toggle="modal" data-target="#formModal">
						<button type="button" title="%s" name="%s" id=%d class="%s btn %s btn-sm"><i class="fa %s"></i></button>
					</a>
					frm;
					$button = sprintf($format_modal, route('sa.show', [$data->proj_id, $data->pan_id]), "View progress Panel $data->pt_name", 'progress', $data->pt_name, 'edit', 'btn-info', 'fa-info');
					if($data->stat_id < 3 && (Auth::user()->roles->first()->name == 'Production Planning Control' || Auth::user()->roles->first()->name == 'Super Admin')) $button .= sprintf($format_modal, route('sa.edit', [$data->proj_id, $data->pan_id]), "Update Panel $data->pt_name", 'update', $data->pt_name, 'update', 'btn-primary', 'fa-edit');
					// if($data->stat_id < 2) $button .= sprintf($format_modal, route('sa.delete', [$data->proj_id, $data->pan_id]), "Delete Panel $data->pt_name", 'delete', $data->pt_name, 'delete', 'btn-danger', 'fa-trash');
					if($data->stat_id > 1) $button .= sprintf($format_modal, route('sa.qrcode', [$data->proj_id, $data->pan_id]), "View QR Code $data->pt_name", 'qrcode', $data->pt_name, 'qrcode', 'btn-secondary', 'fa-qrcode');
					return $button;
				})
				->rawColumns(['action', 'proc_shortname', 'piecepart'])
				->toJson();

		}

		$stat_dats = status::get();
		return view('progress.sa.index', compact(['proj_id', 'stat_dats', '_SaProcDats']));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create($proj_id)
	{
		$ptDats= 		panel_type::get();
		$pposDats=	panel_position::get();
		$ppDats=		piecepart::join('tb_material', 'tb_piecepart.mat_id', '=', 'tb_material.mat_id')
								->join('m_material_type', 'tb_material.mt_id', '=', 'm_material_type.mt_id')
								->select('tb_piecepart.*', 'm_material_type.mt_name')
								->where([['tb_piecepart.pan_id', 0], ['tb_piecepart.proj_id', $proj_id], ['tb_piecepart.stat_id', 2]])
								->get();
		return view('progress.sa.add', compact(['ptDats', 'pposDats', 'ppDats', 'proj_id']));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request, $proj_id)
	{
		$counter=	intval($request->pp_count);
		$proj_id=	intval($proj_id);
		$rules= array(
			'pt_id'	=> 'required|integer',
			'ppos_id'	=> 'required|integer'
		);
		$PpIds= array();
		for ($i=1; $i < $counter + 1; $i++){
			$rules["pp_id$i"]= 'required|integer';
			$PpIds[] = intval($request->{'pp_id'.$i});
		}

		$error = Validator::make($request->all(), $rules);
		if ($error->fails()) {
			$return = str_replace("must be an integer", "", $error->errors()->all());
			$return = str_replace("The pp id", "Please select piecepart ", $return);
			$return = str_replace("The pt id", "Please select panel type ", $return);
			$return = str_replace("The ppos id", "Please select panel position ", $return);
			return response()->json(['errors' => $return]);
		}

		// declare request variabel
		$pt_id= 	intval($request->pt_id);
		$ppos_id=	intval($request->ppos_id);
		$remark=	trim($request->remark);

		// insert panel
		$prev_pan=		panel::where('proj_id', $proj_id)->get('pan_no')->last();
		$prev_pan_no=	isset($prev_pan->pan_no) ? intval($prev_pan->pan_no) : 0;
		$pan_no=			$prev_pan_no + 1;
		$form_data = array(
			'proj_id'	=>  $proj_id,
			'pt_id'		=>	$pt_id,
			'ppos_id'	=>	$ppos_id,
			'pan_no'	=>	$pan_no,
			'proc_id'	=>  15, // fitting, first sub process
			'stat_id'	=>  1,	// waiting for next sub process
			'remark'	=>  $remark,
		);
		$pan_id = panel::create($form_data)->pan_id;
		if($pan_id < 1) return response()->json(['errors' => ['Data errors']]);

		// update piecepart
		piecepart::where('proj_id', $proj_id)->whereIn('pp_id', $PpIds)->update(array('stat_id'=> 3, 'pan_id'=>$pan_id));

		// insert progress
		$username= 	auth()->user()->name;
		$pt_name= 	panel_type::where('pt_id', $pt_id)->get()->last()->pt_name;
		$ppos_name=	panel_position::where('ppos_id', $ppos_id)->get()->last()->ppos_name;
		$progress_data = array(
			'proj_id'			=>  $proj_id,
			'user_id'			=>  intval(auth()->user()->id),
			'proc_id'			=>  14,	// sub assembly
			'pan_id'			=>  $pan_id,
			'prog_remark'	=>  "Panel $pt_name $ppos_name no $pan_no was added to the Sub-Assembly Process, made from $counter piecepart by $username",
		);
		progress::create($progress_data);

		$progress_data = array(
			'proj_id'			=>  $proj_id,
			'user_id'			=>  intval(auth()->user()->id),
			'proc_id'			=>  15,	// fitting
			'pan_id'			=>  $pan_id,
			'prog_remark'	=>  "Panel $pt_name $ppos_name no $pan_no has moved to the Fitting Sub Process created by $username",
		);
		progress::create($progress_data);

		return response()->json(['success' => 'Data Added successfully.']);
	}

	public function allqrcode($proj_id)
    {
		$proj_id= intval($proj_id);

		// get process details
		$SaProcDats=process::select('proc_id','proc_name')
												->where([['proc_mne', 'LIKE', '4.%'], ['proc_lvl', 2]])
												->get();

		$_SaProcDats=['proc_id','proc_name'];
		foreach($SaProcDats as $adats){
			$_SaProcDats['proc_id'][]=	$adats->proc_id;
			$_SaProcDats['proc_name'][]=	$adats->proc_name;
		}
		$_SaProcIds=	$_SaProcDats['proc_id'];

		$panel = panel::join('m_panel_position', 'tb_panel.ppos_id', '=', 'm_panel_position.ppos_id')
								->join('m_panel_type', 'tb_panel.pt_id', '=', 'm_panel_type.pt_id')
								->join('m_stat', 'tb_panel.stat_id', '=', 'm_stat.stat_id')
								->select('tb_panel.*','m_panel_type.pt_shortname','m_panel_position.ppos_name','m_panel_type.pt_name')
                                ->where('tb_panel.proj_id', $proj_id)
								->where('tb_panel.stat_id', '>', '1')
								->whereIn('tb_panel.proc_id', $_SaProcIds)
								->get();
		
		$pan_count = panel::select('tb_panel.proj_id')
							->where('tb_panel.proj_id', $proj_id)
							->where('tb_panel.stat_id', '>', '1')
							->count();
		
		foreach($panel as $key => $pan){
			$pan_id[] = $pan->pan_id;
			$name[] = $pan->ppos_name." - ".$pan->pt_name;

			$pan_no[$key] = sprintf('%02d', $pan->pan_no);
			if($pan_no[$key] >= '100'){
				$pan_no[$key] = '99';
			}
			if($proj_id >= '1000'){
				$no_proj = '999';
			}else{
				$no_proj = sprintf('%03d', $proj_id);
			}

			$qrcode[$key] = "SAXX00".$pan->ppos_id.$pan->pt_shortname.$pan_no[$key]."00000".$no_proj;

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
				progress::where([ ['pan_id', $pan->pan_id], ['proc_id', 17] ])->update($prog_data);
			}
		}
		// Helpme::print_rdie($pt_id);
			        
        view()->share('progress',$qrcode);
        $pdf = PDF::loadView('progress.allqrcode', compact('qrcode','name'));

        return $pdf->stream('qr-code-allqrcode-'.$proj_id.'.pdf');
    }

	public function qrcode($proj_id, $sa_id)
    {
		$sa_id= intval($sa_id);
		
        $panel = panel::join('m_panel_position', 'tb_panel.ppos_id', '=', 'm_panel_position.ppos_id')
						->join('m_panel_type', 'tb_panel.pt_id', '=', 'm_panel_type.pt_id')
						->join('m_stat', 'tb_panel.stat_id', '=', 'm_stat.stat_id')
						->select('tb_panel.*','m_panel_type.pt_shortname')
						->where('tb_panel.pan_id', $sa_id)
						->where('tb_panel.stat_id', '>', '1')
						->first();

		$pan_count = panel::select('tb_panel.proj_id')
							->where('tb_panel.proj_id', $proj_id)
							->where('tb_panel.stat_id', '>', '1')
							->count();

		$pan_no = sprintf('%02d', $panel->pan_no);
		if($pan_no >= '100'){
			$pan_no = '99';
		}
		if($proj_id >= '1000'){
			$no_proj = '999';
		}else{
			$no_proj = sprintf('%03d', $proj_id);
		}

		$qrcode= "SAXX00".$panel->ppos_id.$panel->pt_shortname.$pan_no."00000".$no_proj; 

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
			progress::where([ ['pan_id', $panel->pan_id], ['proc_id', 17] ])->update($prog_data);
		}   

        return view('progress.qrcode', compact('qrcode'));
    }

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($proj_id, $sa_id)
	{
		$dats= progress::showSa($proj_id, $sa_id);
		// Helpme::print_rdie($ppDats);
		return view('progress.sa.show', compact('dats'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($proj_id, $sa_id)
	{
		$SaProcDats=process::select('proc_id','proc_name')
												->where([['proc_mne', 'LIKE', '4.%'], ['proc_lvl', 2]])
												->get();

		$_SaProcDats=['proc_id','proc_name'];
		foreach($SaProcDats as $adats){
			$_SaProcDats['proc_id'][]=	$adats->proc_id;
			$_SaProcDats['proc_name'][]=	$adats->proc_name;
		}
		$ptDats= 		panel_type::get();
		$pposDats=	panel_position::get();

		$panDats=		panel::where('pan_id', $sa_id)->get()->last();
		$ppDats=		piecepart::join('tb_material', 'tb_piecepart.mat_id', '=', 'tb_material.mat_id')
								->join('m_material_type', 'tb_material.mt_id', '=', 'm_material_type.mt_id')
								->select('tb_piecepart.*', 'm_material_type.mt_name')
								->where('tb_piecepart.proj_id', $proj_id)
								->whereIn('tb_piecepart.pan_id', array(0,$sa_id))
								->get();
		$ppSelected=array();
		$ppSelectedIds=array();
		foreach($ppDats as $aDat){
			if($aDat->pan_id != $sa_id) continue;
			$ppSelected[]= $aDat;
			$ppSelectedIds[]= $aDat->pp_id;
		}
		$ppSelectedIdsStr= json_encode($ppSelectedIds);
		// helpme::print_rdie(json_decode($ppSelectedIdsStr));
		return view('progress.sa.edit', compact(['_SaProcDats','panDats','ppSelected', 'ppSelectedIds','ppSelectedIdsStr','ptDats', 'pposDats', 'ppDats', 'proj_id']));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $proj_id, $pan_id)
	{
		$counter=	intval($request->pp_count);
		$proj_id=	intval($proj_id);
		$pan_id=	intval($request->hidden_id);
		$rules= array(
			'pt_id'	=> 'required|integer',
			'ppos_id'	=> 'required|integer'
		);
		$PpIds= array();
		for ($i=1; $i < $counter + 1; $i++){
			$rules["pp_id$i"]= 'required|integer';
			$PpIds[] = intval($request->{'pp_id'.$i});
		}

		$error = Validator::make($request->all(), $rules);
		if ($error->fails()) {
			$return = str_replace("must be an integer", "", $error->errors()->all());
			$return = str_replace("The pp id", "Please select piecepart ", $return);
			$return = str_replace("The pt id", "Please select panel type ", $return);
			$return = str_replace("The ppos id", "Please select panel position ", $return);
			return response()->json(['errors' => $return]);
		}

		// declare parameter
		$proc_old=	intval($request->proc_old);
		$proc_id= 	intval($request->proc_id);
		$pt_id=			intval($request->pt_id);
		$ppos_id=		intval($request->ppos_id);
		$pan_no=		intval($request->pan_no);
		$remark=		trim($request->remark);
		$username= 	auth()->user()->name;
		$pt_name= 	panel_type::where('pt_id', $pt_id)->get()->last()->pt_name;
		$ppos_name=	panel_position::where('ppos_id', $ppos_id)->get()->last()->ppos_name;
		$proc_name= process::where('proc_id', $proc_id)->get('proc_name')->last()->proc_name;

		// are there pieceparts changed?
		$old_counter= intval($request->old_pp_count);
		$old_pp_ids=	json_decode($request->old_pp_ids);
		if ($old_counter != $counter || !empty(array_diff($old_pp_ids,$PpIds))){
			// get all old pp_names
			$old_pp_dats= piecepart::select('pp_name')->whereIn('pp_id', $old_pp_ids)->get();
			$old_pp_names= array();
			foreach($old_pp_dats as $adat){
				$old_pp_names[]= $adat->pp_name;
			}
			$old_pp_names= implode(',', $old_pp_names);

			// get all new pp_names
			$new_pp_dats= piecepart::select('pp_name')->whereIn('pp_id', $PpIds)->get();
			$new_pp_names= array();
			foreach($new_pp_dats as $adat){
				$new_pp_names[]= $adat->pp_name;
			}
			$new_pp_names= implode(',', $new_pp_names);

			// remove pan_id attribute on selected pp_id
			piecepart::where('proj_id', $proj_id)->whereIn('pp_id', $old_pp_ids)->update(array('stat_id'=> 2, 'pan_id'=>0));
			// update piecepart
			piecepart::where('proj_id', $proj_id)->whereIn('pp_id', $PpIds)->update(array('stat_id'=> 3, 'pan_id'=>$pan_id));

			// update progress
			$prog_remark=	"Panel $pt_name $ppos_name no $pan_no has changed the composition of piecepart from $old_pp_names to $new_pp_names by $username";
			$progress_data = array(
				'proj_id'			=>  $proj_id,
				'user_id'			=>  intval(auth()->user()->id),
				'proc_id'			=>  $proc_id,
				'pan_id'			=>  $pan_id,
				'prog_remark'	=>  $prog_remark,
			);
			progress::create($progress_data);
		}

		// update panel
		$data = panel::findOrFail($pan_id);
		if ($data == null) return response()->json(['errors' => ['Data Not Found']]);
		$form_data = array(
			'proc_id'		=>	$proc_id,
			'pt_id'			=>  $pt_id,
			'ppos_id'		=>  $ppos_id,
			'remark'		=>  $remark,
		);
		if($proc_id == 17 ){
			$form_data['stat_id']= 2; // fairing sub process, no next sub process
		}
		else {
			$form_data['stat_id']= 1;
		}
		$data->where('pan_id', $pan_id)->update($form_data);

		// progress
		$prog_remark=	$proc_id != $proc_old ? "Panel $pt_name $ppos_name no $pan_no has moved to the $proc_name sub process by $username" : "Panel $pt_name $ppos_name no $pan_no has been updated by $username ";
		$progress_data = array(
			'proj_id'			=>  $proj_id,
			'user_id'			=>  intval(auth()->user()->id),
			'proc_id'			=>  $proc_id,
			'pan_id'			=>  $pan_id,
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
		return view('progress.sa.delete');
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
