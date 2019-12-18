<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class code extends Model
{
	protected $table = 'm_code';
    protected $primaryKey = 'code_id';
    
	protected $fillable = [
		'code_name', 'code_stat', 'remark',
	];
}
