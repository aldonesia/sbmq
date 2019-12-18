<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class piecepart extends Model
{
	protected $table = 'tb_piecepart';
	protected $primaryKey = 'pp_id';

	protected $fillable = [
		'mat_id', 'proj_id', 'pan_id', 'proc_id', 'stat_id',
		'pp_name','pp_no', 'remark'
	];

	use SoftDeletes;
	protected $dates = ['deleted_at'];
}
