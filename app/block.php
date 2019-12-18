<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class block extends Model
{
	protected $table = 'tb_block';
	protected $primaryKey = 'block_id';

	protected $fillable = [
		'proj_id','bt_id','ship_id',
		'proc_id', 'stat_id', 'block_no',
		'remark'
	];

	use SoftDeletes;
	protected $dates = ['deleted_at'];
}
