<?php
include_once(dirname(__FILE__).'/../config.php');
//================================
// sys_user.php
//================================
function getUser($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from js_user where jsuser_status>=0 order by jsuser_add_date desc, jsuser_sn desc limit $offset, $rows";
	else
		$sql="select * from js_user where jsuser_status>=0 and ( $where ) order by jsuser_add_date desc, jsuser_sn desc limit $offset, $rows";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[] = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getUserQty($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select count(*) from js_user where jsuser_status>=0";
	else
		$sql="select count(*) from js_user where jsuser_status>=0 and ( $where )";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row['count(*)'];
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getUserBySn($jsuser_sn) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from js_user where jsuser_sn='{$jsuser_sn}'";

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

function getUserByAccount($account) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from js_user where jsuser_account='{$account}'";

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

//================================
// sys_history.php
//================================
function getHistory($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from js_history a left join js_user b on a.jshist_user=b.jsuser_sn order by jshist_add_date desc, jshist_sn desc limit $offset, $rows";
	else
		$sql="select * from js_history a left join js_user b on a.jshist_user=b.jsuser_sn where 1=1 and ( $where ) order by jshist_add_date desc, jshist_sn desc limit $offset, $rows";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[] = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getHistoryQty($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select count(*) from js_history a left join js_user b on a.jshist_user=b.jsuser_sn";
	else
		$sql="select count(*) from js_history a left join js_user b on a.jshist_user=b.jsuser_sn where 1=1 and ( $where )";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row['count(*)'];
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

//================================
// sys_history_online.php
//================================
function getHistoryOnline($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select a.jsuser_sn, jsuser_name, sum(jsol_count) as count from js_online a left join js_user b on a.jsuser_sn=b.jsuser_sn group by a.jsuser_sn order by count desc limit $offset, $rows";
	else
		$sql="select a.jsuser_sn, jsuser_name, sum(jsol_count) as count from js_online a left join js_user b on a.jsuser_sn=b.jsuser_sn where 1=1 and ( $where ) group by a.jsuser_sn order by count desc limit $offset, $rows";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[] = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getHistoryOnlineQty($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select a.jsuser_sn, jsuser_name, sum(jsol_count) as count from js_online a left join js_user b on a.jsuser_sn=b.jsuser_sn group by a.jsuser_sn";
	else
		$sql="select a.jsuser_sn, jsuser_name, sum(jsol_count) as count from js_online a left join js_user b on a.jsuser_sn=b.jsuser_sn where 1=1 and ( $where ) group by a.jsuser_sn";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		$ret_data = $qresult->num_rows;
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

//================================
// sys_history_edit.php
//================================
function getHistoryEdit($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select jsuser_sn, jsuser_name, count(*) as count from js_history a left join js_user b on a.jshist_user=b.jsuser_sn where jshist_op_type=2 group by jsuser_sn order by count desc limit $offset, $rows";
	else
		$sql="select jsuser_sn, jsuser_name, count(*) as count from js_history a left join js_user b on a.jshist_user=b.jsuser_sn where jshist_op_type=2 and ( $where ) group by jsuser_sn order by count desc limit $offset, $rows";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[] = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getHistoryEditQty($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select jsuser_sn, jsuser_name, count(*) as count from js_history a left join js_user b on a.jshist_user=b.jsuser_sn where jshist_op_type=2 group by jsuser_sn order by jsuser_sn";
	else
		$sql="select jsuser_sn, jsuser_name, count(*) as count from js_history a left join js_user b on a.jshist_user=b.jsuser_sn where jshist_op_type=2 and ( $where ) group by jsuser_sn order by jsuser_sn";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		$ret_data = $qresult->num_rows;
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

//================================
// sys_supplier.php
//================================
function getAllSupplierData() {
	$ret_data = array();
	$conn = getDB();

	$sql="SELECT * FROM `onliine_supplier_data` WHERE onsd_status >= 1";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[] = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getSupplierData($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="SELECT * FROM `onliine_supplier_data` WHERE onsd_status >= 1 limit $offset, $rows";
	else
		$sql="SELECT * FROM `onliine_supplier_data` WHERE onsd_status >= 1 and ( $where ) limit $offset, $rows";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data[] = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getSupplierDataQty($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="SELECT count(*) FROM `onliine_supplier_data` WHERE onsd_status >= 1";
	else
		$sql="SELECT count(*) FROM `onliine_supplier_data` WHERE onsd_status >= 1 and ( $where )";
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row['count(*)'];
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getSupplierDataBySn($onsd_sn) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="SELECT * FROM `onliine_supplier_data` WHERE onsd_status >= 1 AND onsd_sn = $onsd_sn";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$ret_data = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}


function addSupplierData($onsd_name,$onsd_address,$onsd_phone,$onsd_mail) {
	$now = time();
	$conn = getDB();
	$sql = "INSERT INTO `onliine_supplier_data`(`onsd_name`, `onsd_phone`, `onsd_mail`, `onsd_address`, `onsd_status`) VALUES ('{$onsd_name}','{$onsd_phone}','{$onsd_mail}','{$onsd_address}',1)";			
	if($conn->query($sql)) {
		$ret_msg = "新增成功！";
	} else {
		$ret_msg = "新增失敗！";
	}
	$conn->close();
	return $ret_msg;
}


?>