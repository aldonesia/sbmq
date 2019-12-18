<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class project extends Model
{
	protected $table = 'm_project';
	protected $primaryKey = 'proj_id';

	protected $fillable = [
		'user_id', 'proj_name', 'proj_building_no', 'proj_owner',
		'proj_workgroup', 'proj_weight_factor', 'remark'
	];

	use SoftDeletes;
	protected $dates = ['deleted_at'];
}
