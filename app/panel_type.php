<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class panel_type extends Model
{
	protected $table = 'm_panel_type';
	protected $primaryKey = 'pt_id';

	protected $fillable = [
		'pt_name', 'pt_shortname','remark'
	];
}
