<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class material extends Model
{
	protected $table = 'tb_material';
	protected $primaryKey = 'mat_id';

	protected $fillable = [
		'mt_id', 'proj_id', 'pp_proc_id', 'pp_stat_id', 'pr_proc_id', 'pr_stat_id',
		'mat_spec', 'mat_thick', 'mat_no',
		'purchased_at', 'arrived_at', 'remark'
	];

	use SoftDeletes;
	protected $dates = ['deleted_at'];
}
