<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class material_type extends Model
{
    protected $table = 'm_material_type';
    protected $primaryKey = 'mt_id';

    protected $fillable = [
        'mt_id', 'mt_mne','mt_parid',
        'mt_lvl', 'mt_seq', 'mt_name', 'remark'
    ];
}
