<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class process extends Model
{
  protected $table = 'm_process';
	protected $primaryKey = 'proc_id';

	protected $fillable = [
		'proc_mne', 'proc_parid', 'proc_lvl', 'proc_seq',
		'proc_name', 'proc_shortname', 'proc_score',
		'remark',
	];
}
