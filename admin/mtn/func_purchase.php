<?php
include_once(dirname(__FILE__).'/../config.php');

//================================
// mtn_purchase.php
//================================
function getConstLog($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from mtn_purchase a left join em_manage b on a.emm_sn=b.emm_sn left join js_user c on a.jsuser_sn=c.jsuser_sn where a.mtn_status>=0 order by a.mtn_add_date desc, a.mtn_sn desc limit $offset, $rows";
	else
		$sql="select * from mtn_purchase a left join em_manage b on a.emm_sn=b.emm_sn left join js_user c on a.jsuser_sn=c.jsuser_sn where a.mtn_status>=0 and ( $where ) order by a.mtn_add_date desc, a.mtn_sn desc limit $offset, $rows";

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

function getConstLogQty($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select count(*) from mtn_purchase a left join em_manage b on a.emm_sn=b.emm_sn left join js_user c on a.jsuser_sn=c.jsuser_sn where a.mtn_status>=0";
	else
		$sql="select count(*) from mtn_purchase a left join em_manage b on a.emm_sn=b.emm_sn left join js_user c on a.jsuser_sn=c.jsuser_sn where a.mtn_status>=0 and ( $where )";

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

function getConstLogBySn($mtn_sn) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from mtn_purchase where mtn_sn='{$mtn_sn}'";

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

function getPhoto($tag, $id) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from js_photo where jsphoto_tag='{$tag}' and jsphoto_id='{$id}'and jsphoto_status=1";

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

function getAllEmList() {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from em_manage where emm_status='1'";

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

//================================
//
//================================

?>
