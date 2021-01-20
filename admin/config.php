<?php
session_start();
require_once dirname(__FILE__) . './../_setting.php';

if (!isset($_SESSION['user']) || $_SESSION['key'] != md5($_SESSION['user']['jsuser_account'] . get_ip() . 'online_web')){
	if (!preg_match("/sys_login.php/", $_SERVER['SCRIPT_NAME'])) {
		$_SESSION['LOGIN_REDIRECT'] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		header("Location: http://{$_SERVER['HTTP_HOST']}/online_shulong/admin/sys/sys_login.php");
		exit;
	}
}

function record_online_time() {
	$now = time();
	$end = $now+180;
	$jsuser_sn = $_SESSION['user']['jsuser_sn'];
	if(!empty($jsuser_sn)) {
		$conn = getDB();
		try {
			$conn->autocommit(false);
		
			$sql = "UPDATE js_online SET jsol_end='{$end}', jsol_count={$end}-jsol_start WHERE jsuser_sn='{$jsuser_sn}' and jsol_start<='{$now}' and jsol_end>='{$now}'";
			if(!$conn->query($sql))
				throw new Exception('error');
			$rows = mysqli_affected_rows($conn);
			if($rows==0) {
				$count = $end - $now;
				$sql = "INSERT INTO js_online (jsuser_sn, jsol_start, jsol_end, jsol_count) VALUES ('{$jsuser_sn}', '{$now}', '{$end}', '{$count}')";
				if(!$conn->query($sql))
				throw new Exception('error');
			}
			$conn->commit();
		} catch (Exception $e) {
			$conn->rollback();
		}
		$conn->close();
	}
}
record_online_time();

function returnChkText($before, $after, $colValue) {
	$chk = ($before != $after)?("$colValue"."由 $before 修改為 $after, "):("");
	return $chk;
}

?>
