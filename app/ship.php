<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ship extends Model
{
  protected $table = 'tb_ship';
	protected $primaryKey = 'ship_id';

	protected $fillable = [
		'proj_id', 'proc_id', 'stat_id', 'delivered_at',
		'remark'
	];

	use SoftDeletes;
	protected $dates = ['deleted_at'];
}
