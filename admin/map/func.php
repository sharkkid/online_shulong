<?php
include_once(dirname(__FILE__).'/../config.php');

//================================
// map.php
//================================
function getmanageLogBySn($onadd_sn) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from onliine_add_data where dema_sn='{$onadd_sn}'";

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

function uploadFile($target_dir, $file_name=null, $input_name="file") {
    $ret['result'] = false;
    $ret['file_name'] = '';
    $ret['msg'] = '';
    
    // 取得副檔名
    $file_name_array = explode('.', $_FILES[$input_name]["name"]);
    $file_ext = array_pop($file_name_array);
    
    $ret['file_name'] = empty($file_name) ? $_FILES[$input_name]["name"] : $file_name . '.' . $file_ext;
    
    if(empty($_FILES[$input_name]["name"])){
        $ret['msg'] = '無上傳檔案';
        return $ret;
    }
    
    if ($_FILES[$input_name]["size"] > 52428800) { // 50 * 1024 * 1024 = 50MB
        $ret['msg'] = "檔案大小限制50MB";
        return $ret;
    }
    
    $file_name_array = explode('.', $ret['file_name']);
    $ret['ext'] = array_pop($file_name_array); // 副檔名
    if($ret['ext']!='jpg') {
        $ret['msg'] = "只接受JPG檔上傳 (.jpg)";
        return $ret;
    }
    
    $target_file = $target_dir . $ret['file_name'];
    if (move_uploaded_file($_FILES[$input_name]["tmp_name"], $target_file)) {
        $ret['msg'] = "上傳成功";
        $ret['result'] = true;
        return $ret;
    } else {
        $ret['msg'] = "Sorry, there was an error uploading your file.";
        return $ret;
    }
}

function getManager() {
    $ret_data = array();
    $conn = getDB();
    $sql="SELECT * FROM `js_config` WHERE jsc_key like '%Map%'";
    $qresult = $conn->query($sql);
    if ($qresult->num_rows > 0) {
        while ($row = $qresult->fetch_assoc()) {
            $ret_data[$row['jsc_key']] = $row['jsc_value'];
        }
        $qresult->free();
    }
    $conn->close();
    return $ret_data;
}

function getManagerByKey($jsc_key) {
    $ret_data = array();
    $conn = getDB();
    $sql="SELECT * FROM `js_config` WHERE jsc_key like '{$jsc_key}'";
    $qresult = $conn->query($sql);
    if ($qresult->num_rows > 0) {
        while ($row = $qresult->fetch_assoc()) {
            $ret_data['map'] = $row['jsc_value'];
        }
        $qresult->free();
    }
    $conn->close();
    return $ret_data;
}

?>