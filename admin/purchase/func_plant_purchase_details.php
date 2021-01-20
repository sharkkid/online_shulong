<?php
include_once(dirname(__FILE__).'/../config.php');

function dateFormat($ctime, $format='Y-m-d H:i:s') {
	$now = time();
	if($now > $ctime) {
		return '<span style="color: red">' . date($format, $ctime) . '<span>';
	} else {
		return date($format, $ctime);
	}
}

//================================
// onliine_add_data.php
//================================
function getUser($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from onliine_add_data where onadd_status>=0 onadd_plant_st=1 order by onadd_add_date desc, onadd_sn desc limit $offset, $rows";
	else
		$sql="select * from onliine_add_data where onadd_status>=0 onadd_plant_st=1 and ( $where ) order by onadd_add_date desc, onadd_sn desc limit $offset, $rows";

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

function getAllProductDataQty($where='') {
	$ret_data = 0;
	$conn = getDB();
	if(empty($where))
		$sql="select count(*) from  onliine_product_data a where onproduct_status>=0";
	else
		$sql="sselect count(*) from  onliine_product_data a where onproduct_status>=0 where onproduct_status>=0 and $where";
	// echo $sql;
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

function getUserBySn($onadd_sn) {
	$ret_data = array();
	$conn = getDB();
	$sql="select * from onliine_add_data where onadd_sn='{$onadd_sn}'";

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
	$sql="select * from onliine_add_data where onadd_part_no='{$account}'";

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
		$sql="select * from js_history a left join device_manage b on a.jshist_user=b.dema_sn order by jshist_add_date desc, jshist_sn desc limit $offset, $rows";
	else
		$sql="select * from js_history a left join device_manage b on a.jshist_user=b.dema_sn where 1=1 and ( $where ) order by jshist_add_date desc, jshist_sn desc limit $offset, $rows";

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
		$sql="select count(*) from js_history a left join device_manage b on a.jshist_user=b.dema_sn";
	else
		$sql="select count(*) from js_history a left join device_manage b on a.jshist_user=b.dema_sn where 1=1 and ( $where )";

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
		$sql="select a.dema_sn, dema_device_name, sum(jsol_count) as count from js_online a left join device_manage b on a.dema_sn=b.dema_sn group by a.dema_sn order by count desc limit $offset, $rows";
	else
		$sql="select a.dema_sn, dema_device_name, sum(jsol_count) as count from js_online a left join device_manage b on a.dema_sn=b.dema_sn where 1=1 and ( $where ) group by a.dema_sn order by count desc limit $offset, $rows";

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
		$sql="select a.dema_sn, dema_device_name, sum(jsol_count) as count from js_online a left join device_manage b on a.dema_sn=b.dema_sn group by a.dema_sn";
	else
		$sql="select a.dema_sn, dema_device_name, sum(jsol_count) as count from js_online a left join device_manage b on a.dema_sn=b.dema_sn where 1=1 and ( $where ) group by a.dema_sn";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		$ret_data = $qresult->num_rows;
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}
function getUseradd($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from onliine_add_data where onadd_status>=0 and onadd_plant_st=1 GROUP BY onadd_part_no";
	else
		$sql="select * from onliine_add_data where onadd_status>=0 and onadd_plant_st=1 and ( $where ) GROUP BY onadd_part_no";

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

function getProducts($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from  onliine_product_data a left join onliine_add_data b on a.onproduct_part_no = b.onadd_part_no where onproduct_status>=0 GROUP BY onproduct_part_no limit $offset, $rows";
	else
		$sql="select * from  onliine_product_data a left join onliine_add_data b on a.onproduct_part_no = b.onadd_part_no where onproduct_status>=0 and $where GROUP BY onproduct_part_no limit $offset, $rows";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$sql2="select SUM(onadd_quantity),onadd_cur_size from onliine_add_data where onadd_status>=0 and onadd_part_no like '".$row['onproduct_part_no']."' and onadd_part_name like '".$row['onproduct_part_name']."' and onadd_plant_st = 1";
			$qresult2 = $conn->query($sql2);
			if ($qresult2->num_rows > 0) {
				while($row2 = $qresult2->fetch_assoc()) {
					$row['onadd_cur_size'] = $row2['onadd_cur_size'];
					if($row2['SUM(onadd_quantity)'] != '')
						$row['sum'] = $row2['SUM(onadd_quantity)'];
					else
						$row['sum'] = 0;
				}
			}
			$ret_data[] = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

function getProductsQty($where='') {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from  onliine_product_data a left join onliine_add_data b on a.onproduct_part_no = b.onadd_part_no where onproduct_status>=0 GROUP BY onproduct_part_no";
	else
		$sql="select * from  onliine_product_data a left join onliine_add_data b on a.onproduct_part_no = b.onadd_part_no where onproduct_status>=0 and $where GROUP BY onproduct_part_no";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			$sql2="select SUM(onadd_quantity),onadd_cur_size from onliine_add_data where onadd_status>=0 and onadd_part_no like '".$row['onproduct_part_no']."' and onadd_part_name like '".$row['onproduct_part_name']."' and onadd_plant_st = 1";
			$qresult2 = $conn->query($sql2);
			if ($qresult2->num_rows > 0) {
				while($row2 = $qresult2->fetch_assoc()) {
					$row['onadd_cur_size'] = $row2['onadd_cur_size'];
					if($row2['SUM(onadd_quantity)'] != '')
						$row['sum'] = $row2['SUM(onadd_quantity)'];
					else
						$row['sum'] = 0;
				}
			}
			$ret_data[] = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return count($ret_data);
}

//================================
// sys_history_edit.php
//================================
function getHistoryEdit($where='', $offset=30, $rows=0) {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select dema_sn, dema_device_name, count(*) as count from js_history a left join device_manage b on a.jshist_user=b.dema_sn where jshist_op_type=2 group by dema_sn order by count desc limit $offset, $rows";
	else
		$sql="select dema_sn, dema_device_name, count(*) as count from js_history a left join device_manage b on a.jshist_user=b.dema_sn where jshist_op_type=2 and ( $where ) group by dema_sn order by count desc limit $offset, $rows";

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
		$sql="select dema_sn, dema_device_name, count(*) as count from js_history a left join device_manage b on a.jshist_user=b.dema_sn where jshist_op_type=2 group by dema_sn order by dema_sn";
	else
		$sql="select dema_sn, dema_device_name, count(*) as count from js_history a left join device_manage b on a.jshist_user=b.dema_sn where jshist_op_type=2 and ( $where ) group by dema_sn order by dema_sn";

	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		$ret_data = $qresult->num_rows;
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}
//================================
// plant_purchase_details.php
//================================
function getAllProductData($where='') {
	$ret_data = array();
	$conn = getDB();
	if(empty($where))
		$sql="select * from onliine_product_data order by onproduct_part_no";
	else
		$sql="select * from onliine_product_data where ($where) order by onproduct_part_no";

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

function getAllProductData_page($where='',$offset=30, $rows=0) {
	if($where != '')
		$where_query .= 'where '.$where;
	$ret_data = array();
	$conn = getDB();
	if($rows == 0)
		$sql="select * from onliine_product_data order by onproduct_add_date DESC";
	else
		$sql="select * from onliine_product_data $where_query order by onproduct_add_date DESC limit $offset, $rows";
	// echo $sql;
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

function update_image_url($image_url,$onproduct_sn) {
	$ret_data = array();
	$conn = getDB();
	$sql="UPDATE onliine_product_data SET onproduct_pic_url = '{$image_url}' WHERE onproduct_sn = {$onproduct_sn}";
	if($conn->query($sql))
		return true;
	else
		return false;
}

function add_image_url($image_url,$onproduct_sn) {
	$ret_data = array();
	$conn = getDB();
	$sql="INSERT INTO `onliine_pic_data`(`onpic_status`, `onpic_img_path`, `onproduct_sn`) VALUES (1,'{$image_url}','{$onproduct_sn}')";
	if($conn->query($sql))
		return true;
	else
		return false;
}

function uploadFile($fileInfo, $allowExt = array('jpeg', 'jpg', 'gif', 'png'), $maxSize = 20971520, $flag = true, $uploadPath = './images/upload') {
    // 存放錯誤訊息
    $mes = '';

    // 取得上傳檔案的擴展名
    $ext = pathinfo($fileInfo['name'], PATHINFO_EXTENSION); 

    // 確保檔案名稱唯一，防止重覆名稱產生覆蓋
    $uniName = md5(uniqid(microtime(true), true)) . '.' . $ext;
    $destination = $uploadPath . '/' . $uniName;
    
    // 判斷是否有錯誤
    if ($fileInfo['error'] > 0) {
        // 匹配的錯誤代碼
        switch ($fileInfo['error']) {
            case 1:
                $mes = '上傳的檔案超過了 php.ini 中 upload_max_filesize 允許上傳檔案容量的最大值';
                break;
            case 2:
                $mes = '上傳檔案的大小超過了 HTML 表單中 MAX_FILE_SIZE 選項指定的值';
                break;
            case 3:
                $mes = '檔案只有部分被上傳';
                break;
            case 4:
                $mes = '沒有檔案被上傳（沒有選擇上傳檔案就送出表單）';
                break;
            case 6:
                $mes = '找不到臨時目錄';
                break;
            case 7:
                $mes = '檔案寫入失敗';
                break;
            case 8:
                $mes = '上傳的文件被 PHP 擴展程式中斷';
                break;
        }
        exit($mes);
    }

    // 檢查檔案是否是通過 HTTP POST 上傳的
    if (!is_uploaded_file($fileInfo['tmp_name']))
        exit('檔案不是通過 HTTP POST 方式上傳的');
    
    // 檢查上傳檔案是否為允許的擴展名
    if (!is_array($allowExt))  // 判斷參數是否為陣列
        exit('檔案類型型態必須為 array');
    else {
        if (!in_array($ext, $allowExt))  // 檢查陣列中是否有允許的擴展名
            exit('非法檔案類型');
    }

    // 檢查上傳檔案的容量大小是否符合規範
    if ($fileInfo['size'] > $maxSize)
        exit('上傳檔案容量超過限制');

    // 檢查是否為真實的圖片類型
    if ($flag && !@getimagesize($fileInfo['tmp_name']))
        exit('不是真正的圖片類型');

    // 檢查指定目錄是否存在，不存在就建立目錄
    if (!file_exists($uploadPath))
        mkdir($uploadPath, 0777, true);  // 建立目錄
    
    // 將檔案從臨時目錄移至指定目錄
    if (!@move_uploaded_file($fileInfo['tmp_name'], $destination))  // 如果移動檔案失敗
        exit('檔案移動失敗'.$uniName);

    return $destination;
}

function getSizeQtyBySn($onadd_part_no,$onadd_part_name) {
	$ret_data = array();
	$conn = getDB();

	$sql="SELECT *,SUM(onadd_quantity) as sum FROM `onliine_add_data` WHERE onadd_part_no = '{$onadd_part_no}' and onadd_part_name = '{$onadd_part_name}' and onadd_status >= 1 and onadd_plant_st = 1 and onadd_cur_size not in(0,8) group by onadd_cur_size order by onadd_growing";
	// printr($sql);
	// exit;
	$qresult = $conn->query($sql);
	if ($qresult->num_rows > 0) {
		while($row = $qresult->fetch_assoc()) {
			if($row['onadd_type'] == 0){
				if($row['onadd_newpot_sn'] == 0){
					if($row['onadd_ml'] == 0){
						$row['onadd_sn'] = str_pad($row['onadd_sn'],5,"0",STR_PAD_LEFT);
						$sn = date('Y',$row['onadd_planting_date']).'-'.$row['onadd_sn'];
					}
					else{
						$row['onadd_ml'] = str_pad($row['onadd_ml'],5,"0",STR_PAD_LEFT);
						$sn = date('Y',$row['onadd_planting_date']).'-'.$row['onadd_ml'];       			
	        		}
	        	}
	        	else{
	        		$row['onadd_newpot_sn'] = str_pad($row['onadd_newpot_sn'],5,"0",STR_PAD_LEFT);
	        		$sn = date('Y',$row['onadd_planting_date']).'-'.$row['onadd_newpot_sn'];
	        	}
			}
			else if($row['onadd_type'] == 1){
				if($row['onadd_newpot_sn'] == 0){
					if($row['onadd_ml'] == 0){
						$row['onadd_sn'] = str_pad($row['onadd_sn'],5,"0",STR_PAD_LEFT);
						$sn = 'F'.date('Y',$row['onadd_planting_date']).'-'.$row['onadd_sn'];
					}
					else{
						$row['onadd_ml'] = str_pad($row['onadd_ml'],5,"0",STR_PAD_LEFT);
						$sn = 'F'.date('Y',$row['onadd_planting_date']).'-'.$row['onadd_ml'];
	        		}
				}
				else{
					$row['onadd_newpot_sn'] = str_pad($row['onadd_newpot_sn'],5,"0",STR_PAD_LEFT);
					$sn = 'F'.date('Y',$row['onadd_planting_date']).'-'.$row['onadd_newpot_sn'];
				}
			}
			else{
				if($row['onadd_newpot_sn'] == 0){
					if($row['onadd_ml'] == 0){
						$row['onadd_sn'] = str_pad($row['onadd_sn'],5,"0",STR_PAD_LEFT);
						$sn = 'O'.date('Y',$row['onadd_planting_date']).'-'.$row['onadd_sn'];
					}
					else{
						$row['onadd_ml'] = str_pad($row['onadd_ml'],5,"0",STR_PAD_LEFT);
						$sn = 'O'.date('Y',$row['onadd_planting_date']).'-'.$row['onadd_ml'];
	        		}
				}
				else{
					$row['onadd_newpot_sn'] = str_pad($row['onadd_newpot_sn'],5,"0",STR_PAD_LEFT);
					$sn = 'O'.date('Y',$row['onadd_planting_date']).'-'.$row['onadd_newpot_sn'];
				}
			}

			if($row['onadd_level'] == "1"){
				$sn = $sn."_B";
			}


			$row['sn'] = $sn;
			$ret_data[] = $row;
		}
		$qresult->free();
	}
	$conn->close();
	return $ret_data;
}

?>