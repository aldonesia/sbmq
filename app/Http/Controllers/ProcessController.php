<?php

namespace App\Http\Controllers;

use App\Helpers\Helpme;
use App\process;
use Illuminate\Http\Request;
use Validator;

class ProcessController extends Controller
{
	public function edit($proj_id){
		$dats=	process::get();
		//Helpme::print_rdie($dats);
		return view('process.edit', compact('dats', 'proj_id'));
	}

	public function update(Request $request){
		$counter=			intval($request->proc_count);
		$total_score=	intval($request->total_score);

		$rules= array(
			'total_score'	=> 'required|integer|min:100|max:100'
		);

		$error = Validator::make($request->all(), $rules);

		if ($error->fails()) {
			return response()->json(['errors' => $error->errors()->all()]);
		}

		// update
		for ($i=0; $i< $counter; $i++){
			if(!isset($request->{'proc_id' . $i})) continue;

			$proc_id=		intval($request->{'proc_id' . $i});
			$form_data= array(
				'proc_score'	=>  floatval($request->{'proc_score' . $i}),
				'remark'			=>  trim($request->{'remark' . $i}),
			);
			process::where('proc_id', $proc_id)->update($form_data);
		}
		return response()->json(['success' => 'Data is successfully updated']);
	}
}
