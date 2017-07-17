<?php 
/**
* 
*/
class FileModel extends Model
{
	public function addFile($path='', $name='', $readL=0, $writeL=0, $deleteL=0) {
		$fileId = $this->_generateFileId();
		$time = date('Y-m-d H:i:s'); // 当前时间
		$userId = $this->currentUserId($_POST['token']);
		$name = $this->_dao->escapeString($name);
		$readL = $this->_dao->escapeString($readL);
		$writeL = $this->_dao->escapeString($writeL);
		$deleteL = $this->_dao->escapeString($deleteL);
		$sql = "INSERT into file (fileId, userId, addTime, filePath, fileName, limitRead, limitWrite, limitDelete) values ($fileId, '$userId', '$time', '$path', $name, $readL, $writeL, $deleteL)";
		$result = $this->_dao->query($sql);
		if ($result == false) {
			return false;
		}else {
			return $fileId;
		}
	}
	/*
	public function updateFile($path='') {
		$fileId = $this->_generateFileId();
		$time = date('Y-m-d H:i:s'); // 当前时间
		$userId = $_SESSION['user']['userId'];
		$name = $this->_dao->escapeString($name);
		$sql = "INSERT into file (fileId, userId, addTime, filePath, fileName) values ($fileId, '$userId', '$time', '$path', $name)";
		$result = $this->_dao->query($sql);
		if ($result == false) {
			return false;
		}else {
			return $fileId;
		}
	}
	*/

	public function updateLevel($fileId=0, $key='', $value='') {
		$fileId = $this->_dao->escapeStringNoQMarks($fileId);
		$key = $this->_dao->escapeStringNoQMarks($key);
		$value = $this->_dao->escapeStringNoQMarks($value);
		$sql = "UPDATE file set $key=$value where fileId=$fileId";
		$result = $this->_dao->update($sql);
		return $result;
	}

	public function deleteFile($fileId=0, $token) {
		$fileId = $this->_dao->escapeString($fileId);
		if ($this->isAdmin($token)) {
			$sql = "DELETE from file where fileId=$fileId";
		}else {
			$userId = $this->currentUserId($token);
			$sql = "DELETE from file where userId=$userId and fileId=$fileId";
		}
		
		$result = $this->_dao->query($sql);
		return $result;
	}

	public function fetchAllFile() {
		$sql = "SELECT * from file";
		$result = $this->_dao->fetchAll($sql);
		foreach ($result as $row) {
			$new = array_filter($row, function($key){ // 过滤掉 filePath 字段
				return $key != "filePath";
			}, ARRAY_FILTER_USE_KEY);
			$rows[] = $new;
		}
		return $result;
	}

	public function fetchOneFile($fileId=0) {
		$fileId = $this->_dao->escapeString($fileId);
		$sql = "SELECT * from file where fileId=$fileId";
		$result = $this->_dao->fetchRow($sql);
		return $result;
	}

	private function _generateFileId() {
		$sql = "SELECT fileId from file";
		$fileIds = $this->_dao->fetchAll($sql);
		$fileId = rand(10000, 99999);
		while (in_array(array('fileId' => $fileId), $fileIds)) {
			$fileId = rand(10000, 99999);
		}
		return $fileId;
	}
}
 ?>