<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class status extends Model
{
	protected $table = 'm_stat';
	protected $primaryKey = 'stat_id';

	protected $fillable = [
		'stat_name', 'remark',
	];
}
