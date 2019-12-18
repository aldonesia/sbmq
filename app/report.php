<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class report extends Model
{
  protected $table = 'tb_report';
	protected $primaryKey = 'report_id';

	protected $fillable = [
		'proj_id', 'report_plan', 'report_month', 'report_year',
		'remark'
	];
}
