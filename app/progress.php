<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\piecepart;
use App\panel;
use App\block;
class progress extends Model
{
	protected $table = 'tb_progress';
	protected $primaryKey = 'prog_id';

	protected $fillable = [
		'proj_id', 'user_id', 'proc_id',
		'mat_id', 'pp_id', 'pan_id', 'block_id', 'ship_id',
		'prog_remark', 'remark'
	];

	use SoftDeletes;
	protected $dates = ['deleted_at'];

	public static function showPp($proj_id, $id){
		$dats=  progress::select('tb_progress.*', 'users.name', 'm_process.proc_name')
		->join('users', 'tb_progress.user_id', '=', 'users.id')
		->join('m_process', 'tb_progress.proc_id', '=', 'm_process.proc_id')
		->where('tb_progress.proc_id', '<=', '5')
		->whereIn('tb_progress.mat_id', [0,$id])
		->whereIn('tb_progress.proj_id', [0,$proj_id])
		->orderBy('created_at', 'ASC')
		->get();
		return $dats;
	}

	public static function showPr($proj_id, $id){
		$dats=  progress::select('tb_progress.*', 'users.name', 'm_process.proc_name')
		->join('users', 'tb_progress.user_id', '=', 'users.id')
		->join('m_process', 'tb_progress.proc_id', '=', 'm_process.proc_id')
		->where('tb_progress.proc_id', '<=', '9')
		->whereIn('tb_progress.mat_id', [0,$id])
		->whereIn('tb_progress.proj_id', [0,$proj_id])
		->orderBy('created_at', 'ASC')
		->get();
		return $dats;
	}

	public static function showFa($proj_id, $id){
		$mat_id=	piecepart::where('pp_id', $id)->get('mat_id')->last()->mat_id;
		$dats= 		progress::select('tb_progress.*', 'users.name', 'm_process.proc_name')
			->join('users', 'tb_progress.user_id', '=', 'users.id')
			->join('m_process', 'tb_progress.proc_id', '=', 'm_process.proc_id')
			->where('tb_progress.proc_id', '<=', '13')
			->whereIn('tb_progress.pp_id', [0,$id])
			->whereIn('tb_progress.mat_id', [0,$mat_id])
			->whereIn('tb_progress.proj_id', [0,$proj_id])
			->orderBy('created_at', 'ASC')
			->get();
		return $dats;
	}

	public static function showSa($proj_id, $id){
		$pan_id=	intval($id);
		$ppDats=	piecepart::where('pan_id', $pan_id)->get();
		$pp_ids= array(0);
		$mat_ids= array(0);
		foreach ($ppDats as $aDat){
			$pp_ids[]= $aDat->pp_id;
			$mat_ids[]= $aDat->mat_id;
		}
		$dats= progress::select('tb_progress.*', 'users.name', 'm_process.proc_name')
			->join('users', 'tb_progress.user_id', '=', 'users.id')
			->join('m_process', 'tb_progress.proc_id', '=', 'm_process.proc_id')
			->where('tb_progress.proc_id', '<=', '17')
			->whereIn('tb_progress.pan_id', [0,$pan_id])
			->whereIn('tb_progress.pp_id', $pp_ids)
			->whereIn('tb_progress.mat_id', $mat_ids)
			->whereIn('tb_progress.proj_id', [0,$proj_id])
			->orderBy('proc_id', 'ASC')
			->orderBy('created_at', 'ASC')
			->get();

		return $dats;
	}

	public static function showAs($proj_id, $id){
		$block_id=	intval($id);
		$SaDats=	panel::where('block_id', $block_id)->get();
		$pan_ids= array(0);
		foreach ($SaDats as $aDat){
			$pan_ids[]= $aDat->pan_id;
		}

		$ppDats=	piecepart::whereIn('pan_id', $pan_ids)->get();
		$pp_ids= array(0);
		$mat_ids= array(0);
		foreach ($ppDats as $aDat){
			$pp_ids[]= $aDat->pp_id;
			$mat_ids[]= $aDat->mat_id;
		}

		$dats= progress::select('tb_progress.*', 'users.name', 'm_process.proc_name')
			->join('users', 'tb_progress.user_id', '=', 'users.id')
			->join('m_process', 'tb_progress.proc_id', '=', 'm_process.proc_id')
			->where('tb_progress.proc_id', '<=', '21')
			->whereIn('tb_progress.block_id', [0,$block_id])
			->whereIn('tb_progress.pan_id', $pan_ids)
			->whereIn('tb_progress.pp_id', $pp_ids)
			->whereIn('tb_progress.mat_id', $mat_ids)
			->whereIn('tb_progress.proj_id', [0,$proj_id])
			->orderBy('proc_id', 'ASC')
			->orderBy('created_at', 'ASC')
			->get();

		return $dats;
	}

	public static function showEr($proj_id, $id){
		$ship_id=	intval($id);
		$ErDats=	block::where('ship_id', $ship_id)->get();
		$block_ids= array(0);
		foreach ($ErDats as $aDat){
			$block_ids[]= $aDat->block_id;
		}

		$SaDats=	panel::whereIn('block_id', $block_ids)->get();
		$pan_ids= array(0);
		foreach ($SaDats as $aDat){
			$pan_ids[]= $aDat->pan_id;
		}

		$ppDats=	piecepart::whereIn('pan_id', $pan_ids)->get();
		$pp_ids= array(0);
		$mat_ids= array(0);
		foreach ($ppDats as $aDat){
			$pp_ids[]= $aDat->pp_id;
			$mat_ids[]= $aDat->mat_id;
		}

		$dats= progress::select('tb_progress.*', 'users.name', 'm_process.proc_name')
			->join('users', 'tb_progress.user_id', '=', 'users.id')
			->join('m_process', 'tb_progress.proc_id', '=', 'm_process.proc_id')
			->where('tb_progress.proc_id', '<=', '28')
			->whereIn('tb_progress.ship_id', [0,$ship_id])
			->whereIn('tb_progress.block_id', $block_ids)
			->whereIn('tb_progress.pan_id', $pan_ids)
			->whereIn('tb_progress.pp_id', $pp_ids)
			->whereIn('tb_progress.mat_id', $mat_ids)
			->whereIn('tb_progress.proj_id', [0,$proj_id])
			->orderBy('proc_id', 'ASC')
			->orderBy('created_at', 'ASC')
			->get();

		return $dats;
	}
}
