<?php
include_once(dirname(__FILE__).'/../_setting.php');

function getAppBySys($sys) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from js_app where jsapp_sys='{$sys}'";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		if ($row = $qresult->fetch_assoc()) {
			$ret_data = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getApp() {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from js_app";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while ($row = $qresult->fetch_assoc()) {
			$ret_data[] = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}
?>