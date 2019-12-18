<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class block_type extends Model
{
	protected $table = 'm_block_type';
	protected $primaryKey = 'bt_id';

	protected $fillable = [
		'bt_name', 'bt_shortname',
		'remark'
	];
}
