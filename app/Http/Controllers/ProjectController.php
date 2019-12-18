<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\project;
use App\progress;
// use App\material;
use Validator;
use PDF;

class ProjectController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		if (request()->ajax()) {
			return datatables()->of(project::latest()
				->join('users', 'm_project.user_id', '=', 'users.id')
				->select('m_project.*', 'users.name')
				->orderBy('m_project.proj_id', 'ASC')->get())
				->addColumn('action', function ($data) {
					$details_link = "/admin/projects/$data->proj_id";
					$button = "<button type=\"button\" title=\"Details Project $data->proj_name\" name=\"details\" id=\"$data->proj_id\" class=\"detail btn btn-info btn-sm\" onclick=\"window.location.href='$details_link'\"><i class=\"fa fa-info\"></i></button>";
					$button .= '&nbsp;&nbsp;';
					$button .= '<button type="button" title="Edit Project ' . $data->proj_name . '" name="edit" id="' . $data->proj_id . '" class="edit btn btn-primary btn-sm"><i class="fa fa-edit"></i></button>';
					$button .= '&nbsp;&nbsp;';
					$button .= '<button type="button" title="Delete Project ' . $data->proj_name . '" name="delete" id="' . $data->proj_id . '" class="delete btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>';
					return $button;
				})
				->rawColumns(['action'])
				->make(true);
		}
		return view('project.index');
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
		$rules = array(
			'proj_name' => 'required',
			'proj_building_no' => 'required',
		);

		$error = Validator::make($request->all(), $rules);

		if ($error->fails()) {
			return response()->json(['errors' => str_replace("proj", "", $error->errors()->all())]);
		}

		// create new on project table
		$form_data = array(
			'user_id'               =>  intval(auth()->user()->id),
			'proj_name'             =>  trim($request->proj_name),
			'proj_building_no'      =>  trim($request->proj_building_no),
			'proj_owner'            =>  trim($request->proj_owner),
			'proj_workgroup'        =>  trim($request->proj_workgroup),
			'proj_weight_factor'    =>  trim($request->proj_weight_factor),
			'remark'                =>  trim($request->remark),
		);
		$proj_id = project::create($form_data)->proj_id;

		// create new on progress table
		$progress_data = array(
			'proj_id'			=>  intval($proj_id),
			'user_id'			=>  intval(auth()->user()->id),
			'proc_id'			=>  1,
			'prog_remark'	=>  'Project ' . $request->proj_name . ' was created by ' . auth()->user()->name,
			'remark'			=>	trim($request->remark)
		);
		$prog_id = progress::create($progress_data)->prog_id;

		if ($proj_id > 0 && $prog_id > 0) {
			return response()->json(['success' => 'Data Added successfully.']);
		} else {
			return response()->json(['errors' => ['Data Error']]);
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show(int $id)
	{
		$projects = Project::join('users', 'm_project.user_id', '=', 'users.id')
			->select('m_project.*', 'users.name')
			->where('proj_id', $id)->first();

		$proj_id = $projects->proj_id;

		return view('project.show', compact('projects', 'proj_id'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id)
	{
		if (request()->ajax()) {
			$data = project::findOrFail($id);
			return response()->json(['data' => $data]);
		}
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request)
	{
		$rules = array(
			'proj_name' => 'required',
		);

		$error = Validator::make($request->all(), $rules);

		if ($error->fails()) {
			return response()->json(['errors' => str_replace("proj", "", $error->errors()->all())]);
		}
		$data = project::findOrFail($request->hidden_id);
		if ($data == null) return response()->json(['errors' => ['Data Not Found']]);

		$form_data = array(
			'proj_name'						=>  trim($request->proj_name),
			'proj_building_no'		=>  trim($request->proj_building_no),
			'proj_owner'					=>  trim($request->proj_owner),
			'proj_workgroup'			=>  trim($request->proj_workgroup),
			'proj_weight_factor'	=>  trim($request->proj_weight_factor),
			'remark'							=>  trim($request->remark),
		);
		$data->where('proj_id', '=', $request->hidden_id)->update($form_data);

		// create new on progress table
		$progress_data = array(
			'proj_id'			=>  intval($request->hidden_id),
			'user_id'			=>  intval(auth()->user()->id),
			'proc_id'			=>  1,
			'prog_remark'	=>  'project ' . $request->proj_name . ' has been updated by ' . auth()->user()->name,
			'remark'			=>  trim($request->remark),
		);
		progress::create($progress_data);

		return response()->json(['success' => 'Data is successfully updated']);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$data = project::findOrFail($id);
		if ($data) {
			$data->delete();
			$progress_data = array(
				'proj_id'			=>  intval($id),
				'user_id'			=>  intval(auth()->user()->id),
				'proc_id'			=>  1,
				'prog_remark'	=>  'project ' . $data->proj_name . ' was deleted by ' . auth()->user()->name,
			);
			$prog_id = progress::create($progress_data)->id;
		}
	}
}
