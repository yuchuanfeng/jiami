<?php 
/**
* 
*/
class ModifyFileModel extends Model
{
	public function updateFileRecord($fileId=0, $modifyMode='', $token) {
		$fileId = $this->_dao->escapeString($fileId);
		$modifyMode = $this->_dao->escapeString($modifyMode);
		$time = date('Y-m-d H:i:s');
		$userId = $this->currentUserId($token);
		$sql = "INSERT into modifyRecord (userId, fileId, modifyDate, modifyMode) values($userId, $fileId, '$time', $modifyMode)";
		$this->_dao->query($sql);
	}
}
 ?>