<?php

namespace App\Http\Controllers;

use App\Helpers\Helpme;
use Illuminate\Http\Request;
use Validator;

use App\process;
use App\status;
use App\piecepart;
use App\material_type;
use App\progress;
use App\material;
use App\code;
use PDF;

use Illuminate\Support\Facades\Auth;

class FaController extends Controller
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
		$FaProcDats=process::select('proc_id','proc_name')
												->where([['proc_mne', 'LIKE', '3.%'], ['proc_lvl', 2]])
												->get();

		$_FaProcDats=['proc_id','proc_name'];
		foreach($FaProcDats as $adats){
			$_FaProcDats['proc_id'][]=	$adats->proc_id;
			$_FaProcDats['proc_name'][]=	$adats->proc_name;
		}

		if (request()->ajax()) {
			$_FaProcIds=	$_FaProcDats['proc_id'];

			return datatables()->of(
				piecepart::join('tb_material', 'tb_piecepart.mat_id', '=', 'tb_material.mat_id')
				->join('m_material_type', 'tb_material.mt_id', '=', 'm_material_type.mt_id')
				->join('m_stat', 'tb_piecepart.stat_id', '=', 'm_stat.stat_id')
				->join('m_process', 'tb_piecepart.proc_id', '=', 'm_process.proc_id')
				->select('tb_piecepart.*', 'm_stat.stat_name', 'm_process.proc_name', 'm_process.proc_parid', 'm_material_type.mt_parid', 'm_material_type.mt_name', 'tb_material.mat_no')
				->where('tb_piecepart.proj_id', $proj_id)
				->whereIn('tb_piecepart.proc_id', $_FaProcIds)
				->orderBy('tb_piecepart.stat_id', 'ASC')
				->orderBy('tb_piecepart.mat_id', 'ASC')
				->orderBy('tb_piecepart.pp_no', 'ASC')
				->orderBy('tb_piecepart.proc_id', 'ASC')
				->get())
				->addColumn('mt_parid_name', function ($data) {
					return trim(material_type::where('mt_id', $data->mt_parid)->get('mt_name')->first()->mt_name);
				})
				->addColumn('proc_shortname', function ($data) {
					return trim(process::where('proc_id', $data->proc_parid)->get('proc_shortname')->first()->proc_shortname);
				})
				->addColumn('action', function ($data) {
					$format_modal = <<<frm
					<a href="%s" data-remote="false" data-toggle="modal" data-target="#formModal">
						<button type="button" title="%s" name="%s" id=%d class="%s btn %s btn-sm"><i class="fa %s"></i></button>
					</a>
					frm;
					$button = sprintf($format_modal, route('fa.show', [$data->proj_id, $data->pp_id]), "View progress piecepart$data->pp_name", 'progress', $data->pp_name, 'edit', 'btn-info', 'fa-info');
					if($data->stat_id < 3 && (Auth::user()->roles->first()->name == 'Production Planning Control' || Auth::user()->roles->first()->name == 'Super Admin')) $button .= sprintf($format_modal, route('fa.edit', [$data->proj_id, $data->pp_id]), "Update piecepart$data->pp_name", 'update', $data->pp_name, 'update', 'btn-primary', 'fa-edit');
					// if($data->stat_id < 2) $button .= sprintf($format_modal, route('fa.delete', [$data->proj_id, $data->pp_id]), "Delete piecepart$data->pp_name", 'delete', $data->pp_name, 'delete', 'btn-danger', 'fa-trash');
					if($data->stat_id > 1) $button .= sprintf($format_modal, route('fa.qrcode', [$data->proj_id, $data->pp_id]), "View QR Code $data->pp_name", 'qrcode', $data->pp_name, 'qrcode', 'btn-secondary', 'fa-qrcode');
					return $button;
				})
				->rawColumns(['action', 'mt_parid_name', 'proc_shortname'])
				->toJson();
		}

		$stat_dats = status::get();
		return view('progress.fa.index', compact(['proj_id', 'stat_dats', '_FaProcDats']));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create($proj_id)
	{
		$dats=	material::join('m_material_type', 'tb_material.mt_id', '=', 'm_material_type.mt_id')->where([['pp_stat_id', 3], ['pr_stat_id', 2], ['proj_id', $proj_id]])->get();
		return view('progress.fa.add', compact(['proj_id', 'dats']));
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

		$rules= array(
			'mat_id'	=> 'required|integer'
		);
		for ($i=1; $i < $counter + 1; $i++){
			$rules["pp_name$i"]= 'required';
		}

		$error = Validator::make($request->all(), $rules);

		if ($error->fails()) {
			$return = str_replace("pp name", "Piece Part Name ", $error->errors()->all());
			$return = str_replace("The mat id must be an integer", "Please Select Material", $return);
			return response()->json(['errors' => $return]);
		}

		// get process parameter
		$marking = $request->marking;
		$cutting = $request->cutting;
		$bending = $request->bending;

		// get proc id
		if ($marking == "true" && $cutting=="false") {
			$proc_id= 11;
			$stat_id=1;
		}
		else if ($marking == "true" && $cutting == "true") {
			$proc_id= 12;
			$stat_id=1;
		}
		else if ($marking == "false" && $bending == "true") {
			$proc_id=13;
			$stat_id=2;
		}
		// DB process
		$mt_dats = material::join('m_material_type', 'tb_material.mt_id', 'm_material_type.mt_id')
								->select('m_material_type.mt_name', 'tb_material.mat_no')
								->where('tb_material.mat_id', $request->mat_id)
								->get('mt_name')->last();

		$mt_name=	$mt_dats->mt_name;
		$mat_no=	$mt_dats->mat_no;
		// insert progress
		$pp_names= "";
		for ($i=1; $i < $counter + 1; $i++){
			$pp_names.= trim($request->{'pp_name' . $i}).', ';
		}
		$prog_remark= 'Material ' . $mt_name . ' no '.$mat_no.' has moved to the Fabrication Process and has been cut into '.$counter.' piecepart ('.$pp_names.') by ' . auth()->user()->name;
		$progress_data = array(
			'proj_id'               =>  $proj_id,
			'user_id'               =>  intval(auth()->user()->id),
			'proc_id'               =>  10,
			'mat_id'                =>  $request->mat_id,
			'prog_remark'           =>  $prog_remark,
		);
		progress::create($progress_data);

		$proc_name=	process::where('proc_id', $proc_id)->get('proc_name')->last()->proc_name;
		for ($i=1; $i < $counter + 1; $i++){
			// insert piecepart
			$form_data = array(
				'proj_id'	=>  $proj_id,
				'mat_id'	=>  $request->mat_id,
				'proc_id'	=>  $proc_id,
				'stat_id'	=>  $stat_id,
				'pp_name'	=>  trim($request->{'pp_name' . $i}),
				'pp_no'		=>	$i,
				'remark'	=>  trim($request->{'remark' . $i}),
			);
			$pp_id = piecepart::create($form_data)->pp_id;

			// insert progress
			$progress_data = array(
				'proj_id'			=>  $proj_id,
				'user_id'			=>  intval(auth()->user()->id),
				'proc_id'			=>  $proc_id,
				'mat_id'			=>  $request->mat_id,
				'pp_id'				=> 	$pp_id,
				'prog_remark'	=>  'Piecepart ' . $request->{'pp_name' . $i} . ' no '.$i.' has been added and moved to the '.$proc_name.' Sub Process by ' . auth()->user()->name,
				'remark'			=>  trim($request->{'remark' . $i}),
			);
			progress::create($progress_data);
		}
		// update material
		material::where('mat_id', $request->mat_id)->update(array('pr_stat_id'=> 3));
		return response()->json(['success' => 'Data Added successfully.']);
	}

	public function allqrcode($proj_id)
    {
        $proj_id= intval($proj_id);

		// get process details
		$FaProcDats=process::select('proc_id','proc_name')
												->where([['proc_mne', 'LIKE', '3.%'], ['proc_lvl', 2]])
												->get();

		$_FaProcDats=['proc_id','proc_name'];
		foreach($FaProcDats as $adats){
			$_FaProcDats['proc_id'][]=	$adats->proc_id;
			$_FaProcDats['proc_name'][]=	$adats->proc_name;
		}
		$_FaProcIds=	$_FaProcDats['proc_id'];

		$piecepart = piecepart::join('tb_material', 'tb_piecepart.mat_id', '=', 'tb_material.mat_id')
								->join('m_material_type', 'tb_material.mt_id', '=', 'm_material_type.mt_id')
								->join('m_stat', 'tb_piecepart.stat_id', '=', 'm_stat.stat_id')
								->select('tb_piecepart.*','m_material_type.mt_parid','m_material_type.mt_seq')
								->where('tb_piecepart.proj_id', $proj_id)
                                ->where('tb_piecepart.stat_id', '>', '1')
								->whereIn('tb_piecepart.proc_id', $_FaProcIds)
								->get();

		foreach($piecepart as $pp){
			$pp_id[] = $pp->pp_id;
			$name[] = $pp->pp_name;
			$mt_parid[] = $pp->mt_parid;
			$mat_id[] = $pp->mat_id;
			$mat_type[] = sprintf('%02d', $pp->mt_seq);	
		}
		foreach($mt_parid as $parent_id){ //error
			$general_mat[] = material_type::select('mt_mne')->where('mt_id', $parent_id)->get();
		}
		
		$pp_count = piecepart::select('tb_piecepart.proj_id')
								->where('tb_piecepart.proj_id', $proj_id)
								->where('tb_piecepart.stat_id', '>', '1')
								->count();
		
		foreach($piecepart as $key => $pp){
			$pp_no[$key] = sprintf('%02d', $pp->pp_no);
			if($pp_no[$key] >= '100'){
				$pp_no[$key] = '99';
			}
			if($mat_type[$key] >= '100'){
				$mat_type[$key] = '99';
			}
			if($proj_id >= '1000'){
				$no_proj = '999';
			}else{
				$no_proj = sprintf('%03d', $proj_id);
			}

			$qrcode[$key] = "FAXX000XX00".$pp_no[$key].$general_mat[$key]->first()->mt_mne.$mat_type[$key].$no_proj;
			
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
				progress::where([ ['pp_id', $pp_id[$key]], ['proc_id', 13] ])->update($prog_data);
			}
		}
			        
        view()->share('progress',$qrcode);
        $pdf = PDF::loadView('progress.allqrcode', compact('qrcode','name'));

        return $pdf->stream('qr-code-allqrcode-'.$proj_id.'.pdf');
    }

    public function qrcode($proj_id, $fa_id)
    {
		$fa_id= intval($fa_id);
		
        $piecepart = piecepart::join('tb_material', 'tb_piecepart.mat_id', '=', 'tb_material.mat_id')
								->join('m_material_type', 'tb_material.mt_id', '=', 'm_material_type.mt_id')
								->join('m_stat', 'tb_piecepart.stat_id', '=', 'm_stat.stat_id')
								->select('tb_piecepart.*')
                                ->where('tb_piecepart.pp_id', $fa_id)
                                ->where('tb_piecepart.stat_id', '>', '1')
								->first();
		
		$pp_id = $piecepart->pp_id;
		$mat_id = $piecepart->mat_id;

		$qrcode_pr = progress::join('tb_piecepart', 'tb_progress.mat_id', '=', 'tb_piecepart.mat_id')
						->join('m_code', 'tb_progress.code_id', '=', 'm_code.code_id')
						->select('m_code.code_name')
						->where('tb_progress.mat_id', $mat_id)
						->where('tb_progress.proc_id', '9')
						->first();

		if(isset($qrcode_pr)){								
			$prev_code = substr($qrcode_pr,-8,6);
		}else{
			$material = material::join('m_material_type', 'tb_material.mt_id','=','m_material_type.mt_id')
						->join('m_stat', 'tb_material.pp_stat_id', '=', 'm_stat.stat_id')
						->select('m_material_type.*')
						->where('mat_id', $mat_id)
						->where('tb_material.pp_stat_id', '>', '1')
						->first();
			
			$mt_parid = $material->mt_parid;
			$mat_type = sprintf('%02d', $material->mt_seq);

			$general_mat = material_type::where('mt_id', $mt_parid)->first()->mt_mne;

			if($mat_type >= '100'){
				$mat_type = '99';
			}
			if($proj_id >= '1000'){
				$no_proj = '999';
			}else{
				$no_proj = sprintf('%03d', $proj_id);
			}

			$prev_code= "$general_mat$mat_type$no_proj";
		}

		$pp_count = piecepart::select('tb_piecepart.proj_id')
                                ->where('tb_piecepart.proj_id', $proj_id)
                                ->where('tb_piecepart.stat_id', '>', '1')
								->count();
								
		$pp_no = sprintf('%02d', $piecepart->pp_no);
		if($pp_no >= '100'){
			$pp_no = '99';
		}

		$qrcode= "FAXX000XX00".$pp_no.$prev_code; 

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
			progress::where([ ['pp_id', $pp_id], ['proc_id', 13] ])->update($prog_data);
		}

        return view('progress.qrcode', compact('qrcode'));
    }

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($proj_id, $fa_id)
	{
		$dats= progress::showFa($proj_id, $fa_id);
		// Helpme::print_rdie($dats);
		return view('progress.fa.show', compact('dats'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, int $proj_id, int $fa_id)
	{
		// get process details
		$FaProcDats=process::select('proc_id','proc_name')
												->where([['proc_mne', 'LIKE', '3.%'], ['proc_lvl', 2]])
												->get();

		$_FaProcDats=['proc_id','proc_name'];
		foreach($FaProcDats as $adats){
			$_FaProcDats['proc_id'][]=	$adats->proc_id;
			$_FaProcDats['proc_name'][]=	$adats->proc_name;
		}

		$proj_id=	intval($proj_id);
		$fa_id=		intval($fa_id);

		// get dats
		$fa_dats= piecepart::join('tb_material', 'tb_piecepart.mat_id', 'tb_material.mat_id')
							->join('m_material_type', 'm_material_type.mt_id', 'tb_material.mt_id')
							->select('tb_piecepart.*', 'tb_material.mt_id','m_material_type.mt_name')
							->where('pp_id', $fa_id)->get()->last();

		return view('progress.fa.edit', compact(['_FaProcDats', 'fa_dats', 'proj_id', 'fa_id']));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $proj_id, $fa_id)
	{
		// validator
		$rules = array(
			'pp_name' => 'required',
		);

		$error = Validator::make($request->all(), $rules);

		if ($error->fails()) {
			$return = str_replace("pp name", "Piece Part Name ", $error->errors()->all());
			return response()->json(['errors' => $return]);
		}

		// get parameter request
		$proc_old =      intval($request->proc_old);
		$proc_id =       intval($request->proc_id);

		$pp_id =         intval($request->hidden_id);
		$proj_id =       intval($proj_id);
		$mat_id =        intval($request->mat_id);
		$mt_id =         intval($request->mt_id);
		$pp_name =			 trim($request->pp_name);
		$pp_no =				 intval($request->pp_no);
		$remark =        trim($request->remark);

		//if($proc_old == $proc_id) return response()->json(['errors' => ['The process selected is the same as before']]);

		$data = piecepart::findOrFail($pp_id);
		if ($data == null) return response()->json(['errors' => ['Data Not Found']]);

		$form_data = array(
			'proc_id'		=>	$proc_id,
			'pp_name'		=>  $pp_name,
			'remark'		=>  $remark,
		);
		if($proc_id == 13 ){
			$form_data['stat_id']= 2; // bending process, no next sub process
		}
		$data->where('pp_id', '=', $pp_id)->update($form_data);

		// create new on progress table
		$username= 	auth()->user()->name;
		$mt_name=		material_type::where('mt_id', $mt_id)->get('mt_name')->last()->mt_name;
		$proc_name= process::where('proc_id', $proc_id)->get('proc_name')->last()->proc_name;

		$prog_remark=	$proc_id != $proc_old ? "Piecepart $pp_name no $pp_no has moved to the $proc_name sub process by $username" : "Piecepart $pp_id ($pp_name) has been updated by $username ";
		$progress_data = array(
			'proj_id'			=>  $proj_id,
			'user_id'			=>  intval(auth()->user()->id),
			'proc_id'			=>  $proc_id,
			'mat_id'			=> 	$mat_id,
			'pp_id'				=>	$pp_id,
			'prog_remark'	=>  $prog_remark,
			'remark'			=>  $remark,
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
		return view('progress.fa.delete');
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
