<?php 
session_start();  
$username = param("username");
$password = param("password");

if(empty($username) || empty($password)) {
	echo json_encode(array('msg' => '客户端数据错误，登录失败', 'ok' => false));
	return;
}

$usersConfig = json_decode(file_get_contents("users.conf"), true);

// error_log(print_r($usersConfig, 1));

foreach ($usersConfig as $user) {
	if($username == $user["username"] && $password == $user[password]) {
		$_SESSION["username"] = $username;
		echo json_encode(array('msg' => '登录成功', 'ok' => true));
		return;
	}
}

function param($pkey) {
    $pvalue = $_GET[$pkey];
	if(!isset($pvalue)) {
        $pvalue = $_POST[$pkey];
    }
    return $pvalue;
}
?>