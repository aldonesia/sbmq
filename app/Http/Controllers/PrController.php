<?php

namespace App\Http\Controllers;

use App\Helpers\Helpme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\process;
use App\material;
use App\material_type;
use App\status;
use App\progress;
use App\code;
use PDF;

use Illuminate\Support\Facades\Auth;
class prController extends Controller
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
		$PrProcDats=process::select('proc_id','proc_name')
								->where([['proc_mne', 'LIKE', '2.%'], ['proc_lvl', 2]])
								->orWhere([['proc_name', 'LIKE', 'Identification'], ['proc_lvl', 2]])
								->get();

		$_PrProcDats=['proc_id','proc_name'];
		foreach($PrProcDats as $adats){
			$_PrProcDats['proc_id'][]=	$adats->proc_id;
			$_PrProcDats['proc_name'][]=	$adats->proc_name;
		}
		// Helpme::print_rdie($_PrProcDats);

		if (request()->ajax()) {
			$_PrProcIds=	$_PrProcDats['proc_id'];

			return datatables()->of(
				material::join('m_material_type', 'tb_material.mt_id', '=', 'm_material_type.mt_id')
				->join('m_stat', 'tb_material.pr_stat_id', '=', 'm_stat.stat_id')
				->join('m_process', 'tb_material.pr_proc_id', '=', 'm_process.proc_id')
				->select('tb_material.*', 'm_material_type.mt_name', 'm_material_type.mt_parid', 'm_stat.stat_name', 'm_process.proc_name', 'm_process.proc_parid')
				->where('tb_material.proj_id', $proj_id)
				->whereIn('tb_material.pr_proc_id', $_PrProcIds)
				->orderBy('tb_material.pr_proc_id', 'ASC')
				->orderBy('tb_material.pr_stat_id', 'ASC')
				->orderBy('tb_material.mt_id', 'ASC')
				->orderBy('tb_material.mat_no', 'ASC')
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
					$button = sprintf($format_modal, route('pr.show', [$data->proj_id, $data->mat_id]), "View progress item $data->mt_name", 'progress', $data->mat_id, 'edit', 'btn-info', 'fa-info');
					if($data->pr_stat_id < 3 && (Auth::user()->roles->first()->name == 'Production Planning Control' || Auth::user()->roles->first()->name == 'Super Admin')) $button .= sprintf($format_modal, route('pr.edit', [$data->proj_id, $data->mat_id]), "Update item $data->mt_name", 'update', $data->mat_id, 'update', 'btn-primary', 'fa-edit');
					// if($data->pr_stat_id < 2) $button .= sprintf($format_modal, route('pr.delete', [$data->proj_id, $data->mat_id]), "Delete item $data->mt_name", 'delete', $data->mat_id, 'delete', 'btn-danger', 'fa-trash');
					if($data->pr_stat_id > 1) $button .= sprintf($format_modal, route('pr.qrcode', [$data->proj_id, $data->mat_id]), "View QR Code $data->mt_name", 'qrcode', $data->mat_id, 'qrcode', 'btn-secondary', 'fa-qrcode');
					return $button;
				})
				->rawColumns(['action', 'mt_parid_name', 'proc_shortname'])
				->toJson();
		}

		$stat_dats = status::select('stat_name')->get();
		return view('progress.pr.index', compact(['proj_id', 'stat_dats', '_PrProcDats']));
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	public function allqrcode($proj_id)
    {
        $proj_id= intval($proj_id);

		$PpProcDats=process::select('proc_id','proc_name')
								->where([['proc_mne', 'LIKE', '2.%'], ['proc_lvl', 2]])
								->orWhere([['proc_name', 'LIKE', 'Identification'], ['proc_lvl', 2]])
								->get();

		$_PrProcDats=['proc_id','proc_name'];
		foreach($PpProcDats as $adats){
			$_PrProcDats['proc_id'][]=	$adats->proc_id;
			$_PrProcDats['proc_name'][]=	$adats->proc_name;
		}
		$_PrProcIds=	$_PrProcDats['proc_id'];
        
        $material = material::join('m_material_type', 'tb_material.mt_id','=','m_material_type.mt_id')
								->join('m_stat', 'tb_material.pr_stat_id', '=', 'm_stat.stat_id')
								->select('m_material_type.*','tb_material.mat_id')
                                ->where('tb_material.proj_id', $proj_id)
                                ->where('tb_material.pr_stat_id', '>', '1')
								->whereIn('tb_material.pr_proc_id', $_PrProcIds)
								->get();
                
        foreach($material as $mat){
			$mt_parid[] = $mat->mt_parid;
			$name[] = $mat->mt_name;		
			$mat_id[] = $mat->mat_id;		
			$mat_type[] = sprintf('%02d', $mat->mt_seq);	
		}
		foreach($mt_parid as $parent_id){
			$general_mat[] = material_type::select('mt_mne')->where('mt_id', $parent_id)->get();
		}
		
		foreach($material as $key => $mat){
			if($mat_type[$key] >= '100'){
				$mat_type[$key] = '99';
			}
			if($proj_id >= '1000'){
				$no_proj = '999';
			}else{
				$no_proj = sprintf('%03d', $proj_id);
			}			
			
			$qrcode[$key] = "PRXX000XX0000".$general_mat[$key]->first()->mt_mne.$mat_type[$key].$no_proj; 

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
				progress::where([ ['mat_id', $mat_id[$key]], ['proc_id', 9] ])->update($prog_data);
			}
		}
			        
        view()->share('progress',$qrcode);
        $pdf = PDF::loadView('progress.allqrcode', compact('qrcode','name'));

        return $pdf->stream('qr-code-allqrcode-'.$proj_id.'.pdf');
    }

    public function qrcode($proj_id, $mat_id)
    {
        $mat_id= intval($mat_id);        
        
        $qrcode_pp = progress::join('tb_material', 'tb_progress.mat_id', '=', 'tb_material.mat_id')
								->join('m_code', 'tb_progress.code_id', '=', 'm_code.code_id')
								->select('m_code.code_name')
								->where('tb_progress.mat_id', $mat_id)
                                ->where('tb_progress.proc_id', '5')
								->first();
								
		if(isset($qrcode_pp)){								
			$prev_code = substr($qrcode_pp,-8,6);
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

		$qrcode= "PRXX000XX0000$prev_code"; 

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
			progress::where([ ['mat_id', $mat_id], ['proc_id', 9] ])->update($prog_data);
		}   

        return view('progress.qrcode', compact('qrcode'));
    }

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($proj_id, $pr)
	{
		$dats = progress::showPr($proj_id, $pr);

		return view('progress.pr.show', compact('dats'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, int $proj_id, int $pr_id)
	{
		$proj_id=	intval($proj_id);
		$pr_id=		intval($pr_id);

		// get dats
		$mat_dats=	material::select('tb_material.*', 'm_material_type.mt_name','m_material_type.mt_parid')
								->join('m_material_type', 'tb_material.mt_id', '=', 'm_material_type.mt_id')
								->where([
									['tb_material.mat_id', $pr_id],
									['tb_material.proj_id', $proj_id]
								])
								->get()->last();
		$mat_dats->purchased_at= is_null($mat_dats->purchased_at) ? null : date_format( date_create( $mat_dats->purchased_at ), "Y-m-d" );
		$mat_dats->arrived_at= is_null($mat_dats->arrived_at) ? null : date_format( date_create( $mat_dats->arrived_at ), "Y-m-d" );

		// get parent material type
		$mt_dats=	material_type::select('mt_id', 'mt_name')
							->where('mt_id', $mat_dats->mt_parid)
							->get()->last();

		// get process details
		$PrProcDats=process::select('proc_id','proc_name')
								->where([['proc_mne', 'LIKE', '2.%'], ['proc_lvl', 2]])
								->orWhere([['proc_name', 'LIKE', 'Identification'], ['proc_lvl', 2]])
								->get();

		$_PrProcDats=['proc_id','proc_name'];
		foreach($PrProcDats as $adats){
			$_PrProcDats['proc_id'][]=	$adats->proc_id;
			$_PrProcDats['proc_name'][]=	$adats->proc_name;
		}

		return view('progress.pr.edit', compact(['proj_id', 'pr_id', 'mat_dats', 'mt_dats', '_PrProcDats']));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $proj_id, $pr_id)
	{
		// get parameter request
		$proc_old =	intval($request->proc_old);
		$proc_id =	intval($request->proc_id);

		$mat_id =		intval($request->hidden_id);
		$proj_id =	intval($proj_id);
		$mt_id =		intval($request->mt_id);
		$mat_no =		intval($request->mat_no);
		$remark =		trim($request->remark);

		//if($proc_old == $proc_id) return response()->json(['errors' => ['The process selected is the same as before']]);

		$data = material::findOrFail($mat_id);
		if ($data == null) return response()->json(['errors' => ['Data Not Found']]);

		$form_data = array(
			'pr_proc_id'		=>	$proc_id,
			'pr_stat_id'		=>  1,
			'remark'				=>  $remark,
		);
		if($proc_old == 5) $form_data['pp_stat_id']= 3; // Mark on PP, as used on PR
		if($proc_id == 9 ) $form_data['pr_stat_id']= 2; // Primering process, no next sub process
		$data->where('mat_id', '=', $mat_id)->update($form_data);

		// create new on progress table
		$username= 	auth()->user()->name;
		$mt_name=		material_type::where('mt_id', $mt_id)->get('mt_name')->last()->mt_name;
		if ($proc_id != $proc_old) $proc_name = process::where('proc_id', $proc_id)->get('proc_name')->last()->proc_name;
		$prog_remark=	$proc_id != $proc_old ? "Material $mt_name no '.$mat_no.' has moved to the $proc_name sub process by $username" : "Material $mat_id ($mt_name) has been updated by $username ";

		if($proc_old == 5){
			$progress_data = array(
				'proj_id'               =>  $proj_id,
				'user_id'               =>  intval(auth()->user()->id),
				'proc_id'               =>  process::where('proc_name', 'LIKE', 'Preparation%')->get('proc_id')->last()->proc_id,
				'mat_id'                =>  $mat_id,
				'prog_remark'           =>  'Material ' . $mt_name . ' no '.$mat_no.' has moved to the Preparation Process by ' . $username,
			);
			progress::create($progress_data);
		}
		$progress_data = array(
			'proj_id'			=>  $proj_id,
			'user_id'			=>  intval(auth()->user()->id),
			'proc_id'			=>  $proc_id,
			'mat_id'			=> 	$mat_id,
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
		return view('progress.pr.delete');
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
