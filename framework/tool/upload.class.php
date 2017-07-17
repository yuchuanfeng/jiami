<?php 
/**
* 
*/
class upload
{
	private $_extList = array('.txt', '.doc', '.docx');
	private $_maxSize = 2097152;
	private $_uploadPath = FILE_PATH;
	private $_prefix = '';

	private $_errorInfo;

	public function getErrorInfo() {
		return $this->_errorInfo;
	}

	public function setExtList($extList = array()) {
		$this->_extList = $extList;
	}

	public function setMaxSize($maxSize=0) {
		$this->_maxSize = $maxSize;
	}

	public function setUploadPath($uploadPath='./') {
		if (is_dir($uploadPath)) {
			$this->_uploadPath = $uploadPath;
		}
	}

	public function setPrefix($prefix='') {
		$this->_prefix = $prefix;
	}

	public function uploadFile($fileInfo=array()) {
		
		if (! $this->_checkFileInfo($fileInfo)) {
			return false;
		}

		
		// 上传目录
		$uploadPath = $this->_uploadPath;
		$subDir = date('Ymd'). '/';
		if (! is_dir($uploadPath. $subDir)) {
			mkdir($uploadPath. $subDir);
		}
		// 目标文件 名
		$ext = strrchr($fileInfo['name'], '.');
		$uploadFileName = uniqid($this->_prefix, true). $ext;

		$result = move_uploaded_file($fileInfo['tmp_name'], $uploadPath. $subDir. $uploadFileName);
		if ($result) {
			return $subDir. $uploadFileName;
		}else {
			$this->_errorInfo = '移动失败';
			return false;
		}
	}

	public function updateFile($fileInfo=array(), $subPath) {
		if (! $this->_checkFileInfo($fileInfo)) {
			return false;
		}
		// 判断类型是否一致
		$newExt = strrchr($fileInfo['name'], '.');
		$originalExt = strrchr($subPath, '.');
		if ($newExt != $originalExt) {
			$this->_errorInfo = '文件与原后缀不一致!';
			return false;
		}
		// 上传目录
		$uploadPath = $this->_uploadPath;

		$result = move_uploaded_file($fileInfo['tmp_name'], $uploadPath. $subPath);
		if ($result) {
			return true;
		}else {
			$this->_errorInfo = '更新失败';
			return false;
		}
	}

	private function _ext2Mime($extList=array()) {
		$ext2mimeList = require_once './framework/tool/ext2mime.php';
		foreach ($ext2mimeList as $ext) { // $ext 是对应的值
			$mimeList[] = $ext;
		}
		return $mimeList;
	}

	private function _checkFileInfo($fileInfo) {
	  	if ($fileInfo['error'] != 0) {
			$this->_errorInfo = '上传出现错误。。';
			return false;
		}

		$ext = strrchr($fileInfo['name'], '.');
		if (!in_array($ext, $this->_extList)) {
			$this->_errorInfo = '文件【后缀名】类型错误';
			return false;
		}

		$mimeList = $this->_ext2Mime($this->_extList);
		if (! in_array($fileInfo['type'], $mimeList)) {
			$this->_errorInfo = '文件[MIME]类型错误';
			return false;
		}

		$finfo = new Finfo(FILEINFO_MIME_TYPE);
		$realMime = $finfo->file($fileInfo['tmp_name']);
		if (! in_array($realMime, $mimeList)) {
			$this->_errorInfo = '文件[真实MIME]类型错误';
			return false;
		}

		// 大小是否在限制之内
		if ($fileInfo['size'] > $this->_maxSize) {
			$this->_errorInfo = '文件过大';
			return false;
		}

		// 判断是否为上传 的文件
		if (! is_uploaded_file($fileInfo['tmp_name'])) {
			$this->_errorInfo = '上传文件被破坏';
			return false;
		} 

		return true;
	}
}
 ?>