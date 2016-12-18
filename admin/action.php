<?php
session_start();
if(!isset($_SESSION["username"])) {
	echo json_encode(array('msg' => '别这样', 'ok' => false));
	return;
}

$actionName = $_GET["action"];

if($actionName == 'publish') {
	publish();
} elseif ($actionName == 'delete') {
	delete();
} elseif ($actionName == 'newfile') {
	newFile();
} elseif ($actionName == 'rename') {
	fileRename();
}

function publish() {
	$blog = param('blog');
	$filename = param('filename');
	if(empty($blog) || empty($filename)) {
		echo json_encode(array('msg' => '客户端数据错误，发布失败', 'ok' => false));
		return;
	}

	checkFilename($filename);
	$path = getFilePathByName($filename);
	try {
	    if(file_put_contents($path, $blog) > 0) {
			echo json_encode(array('msg' => '发布成功', 'ok' => true, 'name' => $filename));
			callbackGithook();
		} else {
			echo json_encode(array('msg' => '保存文件时错误，发布失败', 'ok' => false));
		}
	} catch (Exception $e) {
		error_log(e);
	    echo json_encode(array("msg" => "保存出现错误，发布失败(".$e->getMessage().")", "ok" => false));
	}
}

function delete() {
	$filename = param('filename');
	if(empty($filename)) {
		echo json_encode(array('msg' => '客户端数据错误，删除失败', 'ok' => false));
		return;
	}

	checkFilename($filename);
	$path = getFilePathByName($filename);
	if(unlink($path)) {
		echo json_encode(array('msg' => '删除成功', 'ok' => true));
		callbackGithook();
	} else {
		echo json_encode(array('msg' => '删除失败', 'ok' => false));
	}
}

function newFile( ) {
	$filename = param('filename');
	if(empty($filename)) {
		echo json_encode(array('msg' => '客户端数据错误，删除失败', 'ok' => false));
		return;
	}

	checkFilename($filename);
	$path = getFilePathByName($filename);
	if(file_exists($path)) {
		echo json_encode(array('msg' => '文件名已经存在', 'ok' => false));
	} else {
		$newfile = fopen($path, "w");
		if($newfile) {
			fclose($newfile);
			echo json_encode(array('msg' => '新建成功', 'ok' => true, 'name' => $filename));
			callbackGithook();
		} else {
			echo json_encode(array('msg' => '文件创建失败', 'ok' => false));
		}
	}
}

function fileRename() {
	$oldName = param("oldname");
	$newName = param("newname");
	if(empty($oldName)|| empty($newName)) {
		echo json_encode(array('msg' => '客户端数据错误，操作失败', 'ok' => false));
		return;
	}

	checkFilename($oldName);
	checkFilename($newName);

	$oldFilePath = getFilePathByName(oldName);
	$newFilePath = getFilePathByName(newName);
	if(!file_exists($oldFilePath)) {
		echo json_encode(array('msg' => '源文件不存在，操作失败', 'ok' => false));
	} elseif (file_exists($newFilePath)) {
		echo json_encode(array('msg' => '新文件名重复存在，操作失败', 'ok' => false));
	} else {
		rename($oldFilePath, $newFilePath);
		echo json_encode(array('msg' => '重命名成功', 'ok' => true, 'name' => $newName));
		callbackGithook();
	}
}

function param($pkey) {
    $pvalue = $_GET[$pkey];
	if(!isset($pvalue)) {
        $pvalue = $_POST[$pkey];
    }
    return $pvalue;
}

function checkFilename($filename) {
	if(strpos($filename, "_posts") != 0 && strpos($filename, "_drafts") != 0) {
		throw new Exception("文件名错误");
	}
}

function getFilePathByName($name, $type = null) {
	// if(!isset($type)) {
	// 	$type = param('type');
	// }

	// $filePath = null;
	// if($type == 'post') {
	// 	$filePath = "../../_posts/".$name;
	// } else if($type == 'draft') {
	// 	$filePath = "../../_drafts/".$name;
	// }
	// return filePath;
	return "../../".$name;
}

function callbackGithook() {
	exec("bloghook");
}

?>