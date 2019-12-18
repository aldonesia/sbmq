<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Validator;
use App\status;
use App\progress;
use App\process;
use App\material;
use App\material_type;
use App\code;
use App\Helpers\Helpme;
use App\project;
use PDF;

use Illuminate\Support\Facades\Auth;

class ppController extends Controller
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
		$PpProcDats=process::select('proc_id','proc_name')
												->where([['proc_mne', 'LIKE', '1.%'], ['proc_lvl', 2]])
												->get();

		$_PpProcDats=['proc_id','proc_name'];
		foreach($PpProcDats as $adats){
			$_PpProcDats['proc_id'][]=	$adats->proc_id;
			$_PpProcDats['proc_name'][]=	$adats->proc_name;
		}

		if (request()->ajax()) {
			$_PpProcIds=	$_PpProcDats['proc_id'];

			return datatables()->of(
				material::join('m_material_type', 'tb_material.mt_id', '=', 'm_material_type.mt_id')
				->join('m_stat', 'tb_material.pp_stat_id', '=', 'm_stat.stat_id')
				->join('m_process', 'tb_material.pp_proc_id', '=', 'm_process.proc_id')
				->select('tb_material.*', 'm_material_type.mt_name', 'm_material_type.mt_parid', 'm_stat.stat_name', 'm_process.proc_name', 'm_process.proc_parid')
				->where('tb_material.proj_id', $proj_id)
				->whereIn('tb_material.pp_proc_id', $_PpProcIds)
				->orderBy('tb_material.pp_proc_id', 'ASC')
				->orderBy('tb_material.pp_stat_id', 'ASC')
				->orderBy('tb_material.mt_id', 'ASC')
				->orderBy('tb_material.mat_no', 'ASC')->get())
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
					$button = sprintf($format_modal, route('pp.show', [$data->proj_id, $data->mat_id]), "View progress item $data->mt_name", 'progress', $data->mat_id, 'edit', 'btn-info', 'fa-info');
					if($data->pp_stat_id < 3 && (Auth::user()->roles->first()->name == 'Production Planning Control' || Auth::user()->roles->first()->name == 'Super Admin')) $button .= sprintf($format_modal, route('pp.edit', [$data->proj_id, $data->mat_id]), "Update item $data->mt_name", 'update', $data->mat_id, 'update', 'btn-primary', 'fa-edit');
					if($data->pp_stat_id < 2) $button .= sprintf($format_modal, route('pp.delete', [$data->proj_id, $data->mat_id]), "Delete item $data->mt_name", 'delete', $data->mat_id, 'delete', 'btn-danger', 'fa-trash');
					if($data->pp_stat_id > 1) $button .= sprintf($format_modal, route('pp.qrcode', [$data->proj_id, $data->mat_id]), "View QR Code $data->mt_name", 'qrcode', $data->mat_id, 'qrcode', 'btn-secondary', 'fa-qrcode');
					return $button;
				})
				->rawColumns(['action', 'mt_parid_name', 'proc_shortname'])
				->toJson();
		}
		$stat_dats = status::get();
		return view('progress.pp.index', compact(['proj_id', 'stat_dats', '_PpProcDats']));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create($proj_id)
	{
		$mt_lvl1 = material_type::select('mt_id', 'mt_name')->where('mt_lvl', 1)->get();
		return view('progress.pp.add', compact('mt_lvl1', 'proj_id'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request, $proj_id)
	{
		// validator
		$rules = array(
			'mt_id' => 'required',
			'mat_spec' => 'required',
			'purchased_at' => 'required',
			'mat_thick' => 'nullable|regex:/^\d+(\.\d{1,8})?$/',
		);

		$error = Validator::make($request->all(), $rules);

		if ($error->fails()) {
			$return = str_replace("mt id", "Material type and sub type", $error->errors()->all());
			$return = str_replace("mat spec", "Specification", $return);
			$return = str_replace("mat thick", "Thickness", $return);
			return response()->json(['errors' => $return]);
        }

        // get parameter request
		$proj_id =       intval($proj_id);
		$mt_id =         intval($request->mt_id);
		$mat_spec =      trim($request->mat_spec);
		$mat_thick =     floatval($request->mat_thick);
		$mat_no =    			intval($request->mat_no);
		$purchased_at =  date_format(date_create($request->purchased_at), "Y/m/d");
		$remark =        trim($request->remark);

		$mat_ids= array();
		$prev_mat=		material::where([['mt_id', $mt_id], ['proj_id', $proj_id]])->get('mat_no')->last();
		//$prev_mat=		material::where('proj_id', $proj_id)->get('mat_no')->last();
		$prev_mat_no=	isset($prev_mat->mat_no) ? intval($prev_mat->mat_no) : 0;
		for($i=1; $i <= $mat_no; $i++){
			// create new on project table
			$form_data = array(
				'proj_id'       =>  $proj_id,
				'mt_id'         =>  $mt_id,
				'pp_proc_id'		=>  3,
				'pr_proc_id'		=>  3,
				'pp_stat_id'    =>  1,
				'mat_spec'      =>  $mat_spec,
				'mat_thick'     =>  $mat_thick,
				'mat_no'   			=>  $i + $prev_mat_no,
				'purchased_at'  =>  $purchased_at,
				'remark'        =>  $remark,
			);
			$mat_id = material::create($form_data)->mat_id;
			$mat_ids[]= $mat_id;
			// create new on progress table
			$mat_id =			intval($mat_id);
			$mt_name =		trim(material_type::where('mt_id', $mt_id)->get('mt_name')->first()->mt_name);
			$pp_proc_id=	process::where('proc_name', 'LIKE', 'Pra%')->get('proc_id')->last()->proc_id;
			$is_inserted=	progress::where([['proc_id', $pp_proc_id], ['proj_id', $proj_id]])->count();
			if($is_inserted < 1){
				$proj_name=	project::where('proj_id', $proj_id)->get('proj_name')->first()->proj_name;
				$progress_data = array(
					'proj_id'               =>  $proj_id,
					'user_id'               =>  intval(auth()->user()->id),
					'proc_id'               =>  $pp_proc_id,
					'mat_id'                =>  0,
					'prog_remark'           =>  'Project ' . $proj_name . ' has moved to the Pra Preparation Process by ' . auth()->user()->name,
				);
				progress::create($progress_data);
			}
			$progress_data = array(
				'proj_id'               =>  $proj_id,
				'user_id'               =>  intval(auth()->user()->id),
				'proc_id'               =>  process::where('proc_name', 'LIKE', 'Purchase%')->get('proc_id')->last()->proc_id,
				'mat_id'                =>  $mat_id,
				'prog_remark'           =>  'Material ' . $mt_name . ' no '.$i.' has been purchased at ' . $purchased_at . ' by ' . auth()->user()->name,
				'remark'								=>	$remark,
			);
			progress::create($progress_data);
		}

		if (!empty($mat_ids)) return response()->json(['success' => 'Data Added successfully.']);
		else return response()->json(['errors' => ['Data Error']]);
	}
    
    public function allqrcode($proj_id)
    {
		$proj_id= intval($proj_id);

		$PpProcDats=process::select('proc_id','proc_name')
												->where([['proc_mne', 'LIKE', '1.%'], ['proc_lvl', 2]])
												->get();

		$_PpProcDats=['proc_id','proc_name'];
		foreach($PpProcDats as $adats){
			$_PpProcDats['proc_id'][]=	$adats->proc_id;
			$_PpProcDats['proc_name'][]=	$adats->proc_name;
		}
		$_PpProcIds=	$_PpProcDats['proc_id'];
        
        $material = material::join('m_material_type', 'tb_material.mt_id','=','m_material_type.mt_id')
								->join('m_stat', 'tb_material.pp_stat_id', '=', 'm_stat.stat_id')
								->select('m_material_type.*','tb_material.mat_id','m_material_type.mt_seq')
                                ->where('tb_material.proj_id', $proj_id)
                                ->where('tb_material.pp_stat_id', '>', '1')
								->whereIn('tb_material.pp_proc_id', $_PpProcIds)
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
			
			$qrcode[$key] = "PPXX000XX0000".$general_mat[$key]->first()->mt_mne.$mat_type[$key].$no_proj;

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
				progress::where([ ['mat_id', $mat_id[$key]], ['proc_id', 5] ])->update($prog_data);
			}
		}
			        
        view()->share('progress',$qrcode);
        $pdf = PDF::loadView('progress.allqrcode', compact('qrcode','name'));

        return $pdf->stream('qr-code-allqrcode-'.$proj_id.'.pdf');
    }

    public function qrcode($proj_id, $mat_id)
    {
        $mat_id= intval($mat_id);        
        
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

		$qrcode= "PPXX000XX0000$general_mat$mat_type$no_proj";
		
		$qrcode_data = array(
			'code_name' =>  $qrcode,
		);
		// Helpme::print_rdie($qrcode_data);
		if (code::where('code_name', '=', $qrcode_data)->exists()) {
			//return
		}else{
			$code_id= code::create($qrcode_data)->code_id;
			$prog_data= array(
				'code_id' => $code_id,
			);
			progress::where([ ['mat_id', $mat_id], ['proc_id', 5] ])->update($prog_data);
		}      

        return view('progress.qrcode', compact('qrcode'));
    }

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($proj_id, $pp)
	{
		$dats = progress::showPp($proj_id, $pp);

		return view('progress.pp.show', compact('dats'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, int $proj_id, int $pp_id)
	{
		$proj_id=	intval($proj_id);
		$pp_id=		intval($pp_id);

		// get dats
		$mat_dats=	material::select('tb_material.*', 'm_material_type.mt_name','m_material_type.mt_parid')
								->join('m_material_type', 'tb_material.mt_id', '=', 'm_material_type.mt_id')
								->where([
									['tb_material.mat_id', $pp_id],
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
		$PpProcDats=process::select('proc_id','proc_name')->where([['proc_mne', 'LIKE', '1.%'], ['proc_lvl', 2]])
								->get();

		$_PpProcDats=['proc_id','proc_name'];
		foreach($PpProcDats as $adats){
			$_PpProcDats['proc_id'][]=	$adats->proc_id;
			$_PpProcDats['proc_name'][]=	$adats->proc_name;
		}

		return view('progress.pp.edit', compact(['proj_id', 'pp_id', 'mat_dats', 'mt_dats', '_PpProcDats']));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $proj_id, $pp_id)
	{
		$proc_id=	intval($request->proc_id);

		// validator
		$rules = array(
			'mt_id' => 'required',
			'mat_spec' => 'required',
			'purchased_at' => 'required',
			'mat_thick' => 'nullable|regex:/^\d+(\.\d{1,8})?$/',
		);
		if ($proc_id > 3) $rules['arrived_at']= 'required';

		$error = Validator::make($request->all(), $rules);

		if ($error->fails()) {
			$return = str_replace("mt id", "Material type and sub type", $error->errors()->all());
			$return = str_replace("mat spec", "Specification", $return);
			$return = str_replace("mat thick", "Thickness", $return);
			return response()->json(['errors' => $return]);
		}

		// get parameter request
		$proc_old =      intval($request->proc_old);
		$proc_id =       intval($request->proc_id);

		$mat_id =        intval($request->hidden_id);
		$proj_id =       intval($proj_id);
		$mt_parid =      intval($request->mt_parid);
		$mt_id =         intval($request->mt_id);
		$mat_spec =      trim($request->mat_spec);
		$mat_thick =     floatval($request->mat_thick);
		$mat_no =				 intval($request->mat_no);
		$purchased_at =  date_format(date_create($request->purchased_at), "Y/m/d");
		$arrived_at =  	 empty($request->arrived_at) || is_null($request->arrived_at) ? '' : date_format(date_create($request->arrived_at), "Y/m/d");
		$remark =        trim($request->remark);

		//if($proc_old == $proc_id) return response()->json(['errors' => ['The process selected is the same as before']]);

		$data = material::findOrFail($mat_id);
		if ($data == null) return response()->json(['errors' => ['Data Not Found']]);

		$form_data = array(
			'pp_proc_id'		=>	$proc_id,
			'pr_proc_id'		=>	$proc_id,
			'mat_spec'		 	=>  $mat_spec,
			'mat_thick'			=>  $mat_thick,
			'mat_no'				=>  $mat_no,
			'purchased_at'	=>  $purchased_at,
			'arrived_at'    =>  $arrived_at,
			'remark'				=>  $remark,
		);
		if($proc_id == 5 ){
			$form_data['pp_stat_id']= 2; // identification process, no next sub process
			$form_data['pr_stat_id']= 1; // identification process, marking for next process
		}
		$data->where('mat_id', '=', $mat_id)->update($form_data);

		// create new on progress table
		$username= 	auth()->user()->name;
		$mt_name=		material_type::where('mt_id', $mt_id)->get('mt_name')->last()->mt_name;
		$proc_name = $proc_id != $proc_old ? process::where('proc_id', $proc_id)->get('proc_name')->last()->proc_name : '';

		if($proc_old == 3 && $proc_id > 3){
			$progress_data = array(
				'proj_id'               =>  $proj_id,
				'user_id'               =>  intval(auth()->user()->id),
				'proc_id'               =>  4,
				'mat_id'                =>  $mat_id,
				'prog_remark'           =>  'Material ' . $mt_name . 'no '.$mat_no.' arrived at ' . $arrived_at . ' by ' . auth()->user()->name,
			);
			progress::create($progress_data);
		}

		$prog_remark=	$proc_id != $proc_old ? "Material $mt_name no '.$mat_no.' has moved to the $proc_name sub process by $username" : "Material $mat_id ($mt_name) updated by $username ";
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
	public function delete($proj_id, $pp_id)
	{
		$proj_id = intval($proj_id);
		$pp_id = intval($pp_id);

		return view('progress.pp.delete', compact(['pp_id', 'proj_id']));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, int $proj_id)
	{
		$mat_id =    intval($request->id);
		$proj_id =   intval(intval($proj_id));

		$data = material::findOrFail($mat_id);
		if ($data) {
			$data->delete();
			$mt_name =   trim(material_type::where('mt_id', $data->mt_id)->get('mt_name')->first()->mt_name);
			$progress_data = array(
				'proj_id'               =>  $proj_id,
				'user_id'               =>  intval(auth()->user()->id),
				'proc_id'               =>  intval($data->pp_proc_id),
				'mat_id'                =>  $mat_id,
				'prog_remark'           =>  'Material ' . $mt_name . ' no '.$data->mat_no.' was deleted by ' . auth()->user()->name,
			);
			$prog_id = progress::create($progress_data)->prog_id;

			if ($prog_id > 0) return response()->json(['success' => 'Data Delted successfully.']);
			else return response()->json(['errors' => 'Data Cant Delete']);
		}
		return response()->json(['errors' => 'Data Not Found']);
	}

	// ajax function
	public function ajax_get_mt_lvl2(Request $request)
	{
		if (!request()->ajax()) return;

		$parid = intval($request->parid);
		$format = <<<aaa
        <div class="form-group row mt-lvl-2">
        <label class="col-lg-4 col-form-label" for="mt_id">Material Sub Type</label>
        <div class="col-lg-8">
        <select class="form-control" id="mt_id" name="mt_id">
        %s
        </select>
        </div>
        </div>
        aaa;
		$mt = DB::select("select mt_id, mt_name from m_material_type where mt_parid = $parid ORDER BY mt_name ASC", [1]);
		$str = '<option>Select Item</option>';
		foreach ($mt as $item) {
			$str .= "<option value=\"$item->mt_id\"> $item->mt_name</option>";
		}
		$data = sprintf($format, $str);
		return response()->json(['data' => $data]);
	}
}
