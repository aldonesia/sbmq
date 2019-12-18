<?php
namespace App\Helpers;

class Helpme {
	public static function print_rdie($str){
		echo "<pre>";
		print_r($str);
		echo "</pre>";
		die();
	}

	public static function mergePerKey($array1,$array2)
	{
		$mergedArray = [];

		foreach ($array1 as $key => $value) 
		{
			if(isset($array2[$key]))
			{
				$mergedArray[$value] = null;

				continue;
			}
			$mergedArray[$value] = $array2[$key];
		}

		return $mergedArray;
	}
}
