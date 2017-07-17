<?php 
/**
* 
*/
// header('Content-Type: multipart/form-data; charset=utf-8'); 
class FileController extends Controller
{
	public function uploadAction(){
		error_log('post:'. json_encode($_POST));
		error_log('files:'. json_encode($_FILES));
		$fileModel = Factory::M('File');
		if (! isset($_POST['token']) || ! isset($_FILES['file'])) {
			$result = array(
				'code' => 0,
				'status' => '缺少参数！'
				);
		}else if (!$fileModel->checkToken($_POST['token'])) {
			$result = array(
				'code' => 0,
				'status' => 'token无效！'
				);
		}else{
			$upload = new upload();
			$fileInfo = $_FILES['file'];
			$uploadResult = $upload->uploadFile($fileInfo);
			$readL = isset($_POST['limitRead']) ? $_POST['limitRead'] : 0;
			$writeL = isset($_POST['limitWrite']) ? $_POST['limitWrite'] : 0;
			$deleteL = isset($_POST['limitDelete']) ? $_POST['limitDelete'] : 0;
			if ($uploadResult) {
				$addResult = $fileModel->addFile($uploadResult, $fileInfo['name'], $readL, $writeL, $deleteL);
				if (!$addResult) {
					$result = array(
					'code' => 0,
					'status' => '添加文件失败！'
					);
				}else {
					$this->_updateFileRecord($addResult, 'add');
					$result['filePath'] = $uploadResult;
					$result['fileId'] = $addResult;
					$result['fileName'] = $fileInfo['name'];
					$result['code'] = 1;
					$result['status'] = 'OK';
				}
				
			}else {
				$result = array(
				'code' => 0,
				'status' => '上传失败！',
				'errorInfo' => $upload->getErrorInfo()
				);
			}
		}
		echo json_encode($result);
	}

	public function deleteAction() {
		$fileModel = Factory::M('File');
		if (! isset($_POST['token']) || ! isset($_POST['fileId'])) {
			$result = array(
				'code' => 0,
				'status' => '缺少参数！'
				);
		}else if (!$fileModel->checkToken($_POST['token'])) {
			$result = array(
				'code' => 0,
				'status' => 'token无效！'
				);
		}else{
			$userInfo = $fileModel->currentUserInfo($_POST['token']);
			$fileInfo = $fileModel->fetchOneFile($_POST['fileId']);
			$userLevel = $userInfo['limitLevel'];

			if ($userLevel > 20 || $userInfo['isAdmin'] || $userInfo['userId'] == $fileInfo['userId']) {
				$deleteResult = $fileModel->deleteFile($_POST['fileId'], $_POST['token']);
				if ($deleteResult) {
					unlink(FILE_PATH. $fileInfo['filePath']);
					$this->_updateFileRecord($_POST['fileId'], 'delete');
					$result = array(
						'code' => 1,
						'status' => 'OK！'
						);
				}else {
					$result = array(
						'code' => 0,
						'status' => '删除文件失败！'
						);
				}
			}else
			{
				$result = array(
					'code' => 0,
					'status' => '你没有权限!'
					);
			}

		}
		echo json_encode($result);
	}

	public function updateAction() {
		$fileModel = Factory::M('File');
		if (! isset($_POST['token']) || ! isset($_POST['fileId']) || ! isset($_FILES['file'])) {
			$result = array(
				'code' => 0,
				'status' => '缺少参数！'
				);
		}else if (!$fileModel->checkToken($_POST['token'])) {
			$result = array(
				'code' => 0,
				'status' => 'token无效！'
				);
		}else{
			$userInfo = $fileModel->currentUserInfo($_POST['token']);
			$file = $fileModel->fetchOneFile($_POST['fileId']);
			$userLevel = $userInfo['limitLevel'];

			if ($userLevel > 10 || $userInfo['isAdmin'] || $userInfo['userId'] == $file['userId']) {
				$upload = new upload();
				$fileInfo = $_FILES['file'];
				$uploadResult = $upload->updateFile($fileInfo, $file['filePath']);
				if ($uploadResult) {
					$this->_updateFileRecord($_POST['fileId'], 'update');
					$result = array(
						'code' => 1,
						'status' => 'OK！'
						);
				}else {
					$result = array(
						'code' => 0,
						'status' => '更新文件失败！',
						'errorInfo' => $upload->getErrorInfo()
						);
				}
			}else {
				$result = array(
					'code' => 0,
					'status' => '你没有权限'
					);
			}

			
		}
		echo json_encode($result);
	}

	public function readAction() {
		$fileModel = Factory::M('File');
		if (! isset($_POST['token']) || ! isset($_POST['fileId']) ) {
			$result = array(
				'code' => 0,
				'status' => '缺少参数！'
				);
		}else if (!$fileModel->checkToken($_POST['token'])) {
			$result = array(
				'code' => 0,
				'status' => 'token无效！'
				);
		}else{
			$userInfo = $fileModel->currentUserInfo($_POST['token']);
			$fileInfo = $fileModel->fetchOneFile($_POST['fileId']);
			$userLevel = $userInfo['limitLevel'];
			error_log(json_encode($userInfo));
			if ($fileInfo) {
				$result = array(
					'code' => 0,
					'status' => '文件不存在！'
					);
			}else
			if ($userLevel > 0 || $userInfo['isAdmin'] || $userInfo['userId'] == $fileInfo['userId']) {
				$result = array(
					'code' => 1,
					'status' => 'OK！',
					'filePath' => $fileInfo['filePath']
					);
			}else{
				$result = array(
					'code' => 0,
					'status' => '你没有权限'
					);
			}
		}
		echo json_encode($result);
	}

	public function fetchAllAction() {
		$fileModel = Factory::M('File');
		error_log('post:'. json_encode($_POST));
		if (! isset($_POST['token']) ) {
			$result = array(
				'code' => 0,
				'status' => '缺少参数！'
				);
		}else if (!$fileModel->checkToken($_POST['token'])) {
			$result = array(
				'code' => 0,
				'status' => 'token无效！'
				);
		}else{
			$fetchResult = $fileModel->fetchAllFile();
			if ($fetchResult) {
				$result = array(
					'code' => 1,
					'status' => 'OK！',
					'list' => $fetchResult
					);
			}else {
				$result = array(
					'code' => 0,
					'status' => '获取文件列表失败！'
					);
			}
		}
		echo json_encode($result);
	}

	public function updateLimitLevelAction() {
		$fileModel = Factory::M('File');
		if (! isset($_POST['token']) || ! isset($_POST['fileId']) || count($_POST) < 3) {
			$result = array(
				'code' => 0,
				'status' => '缺少参数！'
				);
		}else if (!$fileModel->checkToken($_POST['token'])) {
			$result = array(
				'code' => 0,
				'status' => 'token无效！'
				);
		}else{

			$userId = $fileModel->currentUserId($_POST['token']);
			$fileInfo = $fileModel->fetchOneFile($_POST['fileId']);
			if ($userInfo['isAdmin'] || $userId == $fileInfo['userId']) {
				$limitArray = array('limitRead', 'limitWrite', 'limitDelete');
				foreach ($_POST as $key => $value) {
					// error_log('key:'. $key);
					// error_log('value:'. $value);
					// error_log('in_array:'. in_array($key, $limitArray));
					if (in_array($key, $limitArray)) {
						$result = $fileModel->updateLevel($_POST['fileId'], $key, $value);
						if (!$result) {
							$result = array(
							'code' => 0,
							'status' => '更新文件权限失败！'
							);
							echo json_encode($result);
							return;
						}
					}
				}
				$result = array(
						'code' => 1,
						'status' => 'OK！'
						);
			}else {
				$result = array(
					'code' => 0,
					'status' => '你没有权限'
					);
			}
			
		}
		echo json_encode($result);
	}

	private function _updateFileRecord($fileId, $mode) {
		$fileModel = Factory::M('ModifyFile');
		$fileModel->updateFileRecord($fileId, $mode, $_POST['token']);
	}
}
 ?>