<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class panel_position extends Model
{
	protected $table = 'm_panel_position';
	protected $primaryKey = 'ppos_id';

	protected $fillable = [
		'ppos_name','remark'
	];

}
