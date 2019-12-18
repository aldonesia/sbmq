<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class panel extends Model
{
	protected $table = 'tb_panel';
	protected $primaryKey = 'pan_id';

	protected $fillable = [
		'proj_id','pt_id','ppos_id','block_id',
		'proc_id', 'stat_id', 'pan_no',
		'remark'
	];

	use SoftDeletes;
	protected $dates = ['deleted_at'];
}
