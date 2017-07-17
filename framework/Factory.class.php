<?php 
/**
* 
*/
class Factory
{
	
	public static function M($modelName='') {
		static $modelList = array();
		if (!isset($modelList[$modelName])) {
			$modelClassName = $modelName. 'Model';
			$modelList[$modelName] = new $modelClassName();
		}

		return $modelList[$modelName];
	}
}
 ?>