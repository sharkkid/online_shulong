<?php
include_once("./func_plant_purchase.php");

$status_mapping = array(0=>'<font color="red">關閉</font>', 1=>'<font color="blue">啟用</font>');
$DEVICE_SYSTEM = array(
		1=>"1.7",
		2=>"2.5",
		3=>"2.8",
		4=>"3.0",
		5=>"3.5",
		6=>"3.6",
		7=>"其他"
		// 1.7, 2.5, 2.8, 3.0, 3.5, 3.6 其他
);
$permissions_mapping = array(
    1=>'<font color="#666666">1.7</font>',
    2=>'<font color="#666666">2.5</font>',
    3=>'<font color="#666666">2.8</font>',
    4=>'<font color="#666666">3.0</font>',
    5=>'<font color="#666666">3.5</font>',
    6=>'<font color="#666666">3.6</font>',
    7=>'<font color="#666666">其他</font>' 
);
$permmsion = '系統管理員';

$op=GetParam('op');
if(!empty($op)) {
	$ret_code = 1;
	$ret_msg = '';
	$ret_data = array();
	switch ($op) {
		case 'add':
		$onadd_add_date=GetParam('onadd_add_date');//建立日期
		$onadd_mod_date=GetParam('onadd_mod_date');//修改日期
		$onadd_isbought=GetParam('onadd_isbought');//苗種來源
		$onadd_part_no=GetParam('onadd_part_no');//品號
		$onadd_part_name=GetParam('onadd_part_name');//品名
		$onadd_color=GetParam('onadd_color');//花色
		$onadd_size=GetParam('onadd_size');//花徑
		$onadd_height=GetParam('onadd_height');//高度
		$onadd_pot_size=GetParam('onadd_pot_size');//適合開花盆徑
		$onadd_supplier=GetParam('onadd_supplier');//供應商
		$onadd_location=GetParam('onadd_location');//放置區
		$onadd_planting_date=GetParam('onadd_planting_date');//下種日期
		$onadd_quantity=GetParam('onadd_quantity');//下種數量
		$onadd_growing=GetParam('onadd_growing');//預計成長大小
		$onadd_quantity_shi=GetParam('onadd_quantity_shi');//換盆年
		$onadd_quantity_cha=$test;//換盆月
		$onadd_status=GetParam('onadd_status');//狀態 1 啟用 0 刪除
		$jsuser_sn = GetParam('supplier');//編輯人員

		if(empty($onadd_part_no)||empty($onadd_part_name)||empty($onadd_planting_date)||empty($onadd_quantity)||empty($onadd_growing)){
			$ret_msg = "*為必填！";
		} else { 
			$user = getUserByAccount($onadd_part_no);
			$onadd_planting_date = str2time($onadd_planting_date);
			$now = time();
			$conn = getDB();
				$sql = "INSERT INTO onliine_add_data (onadd_add_date, onadd_mod_date, onadd_part_no, onadd_part_name, onadd_color, onadd_size, onadd_height, onadd_pot_size, onadd_supplier, onadd_planting_date, onadd_quantity, onadd_growing, onadd_status, jsuser_sn, onadd_cycle, onadd_isbought, onadd_plant_st, onadd_location) " .
				"VALUES ('{$now}', '{$now}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_planting_date}', '{$onadd_quantity}', '{$onadd_growing}', '1', '{$jsuser_sn}', '{$now}', '{$onadd_isbought}', '1', '{$onadd_location}');";

				$sql2 = "INSERT INTO onliine_product_data(onproduct_add_date, onproduct_date, onproduct_status, jsuser_sn, onproduct_part_no, onproduct_part_name, onproduct_color, onproduct_size, onproduct_height, onproduct_pot_size, onproduct_supplier, onproduct_growing, onproduct_isbought, onproduct_plant_st) " .
				"VALUES ('{$now}', '{$now}', '1', '{$jsuser_sn}' , '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_growing}', '{$onadd_isbought}', '1');";

				

				if($conn->query($sql)) {
					$onadd_id = mysqli_insert_id($conn);
					if(IsProductExit($onadd_part_no,$onadd_part_name)=="0"){
						$conn->query($sql2);
					}
					// if($conn->query($sql2)){
						$sql3 = "INSERT INTO `onliine_firstplant_data`(`onfp_add_date`, `onfp_plant_date`, `jsuser_sn`, `onfp_plant_amount`,`onfp_part_no`,onadd_sn) VALUES ('{$now}', '{$onadd_planting_date}','{$jsuser_sn}','{$onadd_quantity}','{$onadd_part_no}','{$onadd_id}');";
						if($conn->query($sql3)){
							$ret_msg = "新增成功！";
						}
						else{
							$ret_msg = "新增失敗A！";
						}
					// }
					// else
					// 	$ret_msg = "新增失敗！";
				} else {
					$ret_msg = "新增失敗B！".$sql;
				}
			$conn->close();
		}
		break;

		case 'add_sub':
		$onadd_add_date=GetParam('onproduct_add_date');//建立日期
		$onadd_mod_date=GetParam('onproduct_date');//修改日期
		$onadd_part_no=GetParam('onproduct_part_no');//品號
		$onadd_part_name=GetParam('onproduct_part_name');//品名
		$onadd_color=GetParam('onproduct_color');//花色
		$onadd_size=GetParam('onproduct_size');//花徑
		$onadd_height=GetParam('onproduct_height');//高度
		$onadd_pot_size=GetParam('onproduct_pot_size');//適合開花盆徑
		$onadd_location=GetParam('onadd_location');//放置區
		$onadd_supplier=GetParam('onproduct_supplier');//供應商
		$onadd_planting_date=GetParam('onproduct_planting_date');//下種日期
		$onadd_quantity=GetParam('onproduct_quantity');//下種數量
		$onadd_growing=GetParam('onproduct_growing');//預計成長大小
		$onadd_status=GetParam('onadd_status');//狀態 1 啟用 0 刪除
		$jsuser_sn = GetParam('supplier');//編輯人員

		if(empty($onadd_part_no)||empty($onadd_part_name)||empty($onadd_planting_date)||empty($onadd_quantity)||empty($onadd_growing)){
			$ret_msg = "*為必填！";
		} else { 
			$user = getUserByAccount($onadd_part_no);
			$onadd_planting_date = str2time($onadd_planting_date);
			$now = time();
			$conn = getDB();
				$sql = "INSERT INTO onliine_add_data (onadd_add_date, onadd_mod_date, onadd_part_no, onadd_part_name, onadd_color, onadd_size, onadd_height, onadd_pot_size, onadd_supplier, onadd_planting_date, onadd_quantity, onadd_growing, onadd_status, jsuser_sn, onadd_cycle, onadd_location) " .
				"VALUES ('{$now}', '{$now}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_planting_date}', '{$onadd_quantity}', '{$onadd_growing}', '1', '{$jsuser_sn}', '{$now}', '{$onadd_location}');";
				if($conn->query($sql)) {
					$onadd_id = mysqli_insert_id($conn);
						$sql3 = "INSERT INTO `onliine_firstplant_data`(`onfp_add_date`, `onfp_plant_date`, `jsuser_sn`, `onfp_plant_amount`,`onfp_part_no`,onadd_sn) VALUES ('{$now}', '{$onadd_planting_date}','{$jsuser_sn}','{$onadd_quantity}','{$onadd_part_no}','{$onadd_id}');";
						if($conn->query($sql3))
							$ret_msg = "新增成功！";
						else
							$ret_msg = "新增失敗！";

				} else {
					$ret_msg = "新增失敗！";
				}
			$conn->close();
		}
		break;

		case 'get':
		$onadd_sn=GetParam('onadd_sn');
		$ret_data = array();
		if(!empty($onadd_sn)){
			$ret_code = 1;
			$ret_data = getUserBySn($onadd_sn);
		} else {
			$ret_code = 0;
		}

		break;

		case 'upd3':
		$onproduct_sn=GetParam('onproduct_sn');//sn
		$onproduct_part_no=GetParam('onproduct_part_no');//品號
		$onproduct_part_name=GetParam('onproduct_part_name');//品名
		$onproduct_color=GetParam('onproduct_color');//花色
		$onproduct_size=GetParam('onproduct_size');//花徑
		$onproduct_height=GetParam('onproduct_height');//高度
		$onproduct_pot_size=GetParam('onproduct_pot_size');//適合開花盆徑
		$onproduct_supplier=GetParam('onproduct_supplier');//供應商
		$onproduct_planting_date=GetParam('onproduct_planting_date');//下種日期
		$onproduct_quantity=GetParam('onproduct_quantity');//下種數量
		$onproduct_growing=GetParam('onproduct_growing');//預計成長大小
		$onproduct_quantity_shi=GetParam('onproduct_quantity_shi');//換盆年
		$onproduct_isbought=GetParam('onproduct_isbought');//苗種來源
		$onproduct_quantity_cha=$test;//換盆月
		$jsuser_sn = GetParam('supplier');//編輯人員

		if(empty($onproduct_part_no)||empty($onproduct_part_name)||empty($onproduct_growing)){
			$ret_msg = "*為必填123！";
		} else { 
			$user = getUserByAccount($onproduct_part_no);
			$onproduct_planting_date = str2time($onproduct_planting_date);
			$now = time();
			$conn = getDB();
				$sql = "UPDATE onliine_product_data SET onproduct_part_no = '$onproduct_part_no', onproduct_part_name = '$onproduct_part_name', onproduct_color ='$onproduct_color', onproduct_size = '$onproduct_size', onproduct_height = '$onproduct_height', onproduct_pot_size = '$onproduct_pot_size', onproduct_supplier = '$onproduct_supplier',onproduct_growing ='$onproduct_growing', jsuser_sn = '$jsuser_sn', onproduct_isbought = '$onproduct_isbought' WHERE onproduct_sn = $onproduct_sn;";

				if($conn->query($sql)) {
					$ret_msg = "更新成功！";

				} else {
					$ret_msg = "更新失敗！".$sql;
				}
			$conn->close();
		}
		break;


		case 'upd5':
		$onadd_sn=GetParam('onadd_sn');
		$onadd_part_no=GetParam('onadd_part_no');//品號
		$onadd_part_name=GetParam('onadd_part_name');//品名
		$onadd_color=GetParam('onadd_color');//花色
		$onadd_size=GetParam('onadd_size');//花徑
		$onadd_height=GetParam('onadd_height');//高度
		$onadd_pot_size=GetParam('onadd_pot_size');//適合開花盆徑
		$onadd_location=GetParam('onadd_location');//放置區
		$onadd_supplier=GetParam('onadd_supplier');//供應商
		$onadd_planting_date=GetParam('onadd_planting_date');//下種日期
		$onadd_quantity=GetParam('onadd_quantity');//下種數量
		$onadd_growing=GetParam('onadd_growing');//預計成長大小
		$jsuser_sn = GetParam('supplier');//編輯人員


		$user = getUserByAccount($onadd_part_no);
		$onadd_planting_date = str2time($onadd_planting_date);
		$now = time();
		$conn = getDB();
		$sql = "UPDATE onliine_add_data	SET onadd_part_no ='{$onadd_part_no}',onadd_part_name='{$onadd_part_name}',onadd_color='{$onadd_color}',onadd_size='{$onadd_size}',onadd_height='{$onadd_height}',onadd_pot_size='{$onadd_pot_size}',onadd_supplier='{$onadd_supplier}',onadd_planting_date='{$onadd_planting_date}',onadd_quantity='{$onadd_quantity}',onadd_growing='{$onadd_growing}',jsuser_sn='{$supplier}', onadd_location='{$onadd_location}' WHERE onadd_sn='{$onadd_sn}';";
		$sql2 = "UPDATE onliine_firstplant_data	SET onfp_plant_amount = '{$onadd_quantity}' WHERE onadd_sn='{$onadd_sn}' and onfp_status >= 1;";

		if($conn->query($sql)) {
			$ret_msg = "更新成功！";
			if(IsfirtPlant($onadd_sn) == "1"){
				if($conn->query($sql2)) {
					$ret_msg = "更新成功！";
				}
				else{
					$ret_msg = "更新失敗！";
				}
			}
		} else {
			$ret_msg = "更新失敗！".$sql;
		}
		$conn->close();
		
		break;

		//換盆
		case 'upd':
		$onadd_sn=GetParam('onadd_sn');
		$onadd_add_date=GetParam('onadd_add_date');//建立日期
		$onadd_mod_date=GetParam('onadd_mod_date');//修改日期
		$onadd_part_no=GetParam('onadd_part_no');//品號
		$onadd_part_name=GetParam('onadd_part_name');//品名
		$onadd_color=GetParam('onadd_color');//花色
		$onadd_size=GetParam('onadd_size');//花徑
		$onadd_height=GetParam('onadd_height');//高度
		$onadd_pot_size=GetParam('onadd_pot_size');//適合開花盆徑
		$onadd_supplier=GetParam('onadd_supplier');//供應商
		$onadd_planting_date=GetParam('onadd_planting_date');//下種日期
		$onadd_quantity=GetParam('onadd_quantity');//下種數量
		$onadd_replant_number=GetParam('onadd_replant_number');//換盆數量
		$onadd_quantity_cha123 =($onadd_quantity - $onadd_replant_number);

		$onadd_status = ($onadd_quantity_cha123 < 0) ? -1 : 1;
		$first_n_changed = getProductFirstQty($onadd_sn) - $onadd_replant_number;

		$onadd_growing=GetParam('onadd_growing');//預計成長大小
		// $onadd_status=GetParam('onadd_status');//狀態 1 啟用 0 刪除
		$jsuser_sn = GetParam('supplier');//編輯人員

		if(empty($onadd_planting_date)||empty($onadd_quantity)){
			$ret_msg = "*為必填！ onadd_quantity=".$onadd_quantity;
		} 
		else { 
			$user = getUserByAccount($onadd_part_no);
			$onadd_planting_date = str2time($onadd_planting_date);
			$now = time();
			$conn = getDB();
			if($onadd_status != -1) {
				$sql = "INSERT INTO onliine_add_data (onadd_add_date, onadd_mod_date, onadd_part_no, onadd_part_name, onadd_color, onadd_size, onadd_height, onadd_pot_size, onadd_supplier, onadd_planting_date, onadd_quantity,onadd_quantity_cha, onadd_growing, onadd_status, jsuser_sn, onadd_cycle) " .
				"VALUES ('{$now}', '{$now}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_planting_date}', '{$onadd_replant_number}','{$onadd_replant_number}', '{$onadd_growing}', '1', '{$jsuser_sn}', '{$now}');";

				if($conn->query($sql)){
					$onadd_id = mysqli_insert_id($conn);

					// //新增第一筆下種數量紀錄
					$sql2 = "INSERT INTO `onliine_firstplant_data`(`onfp_add_date`, `onfp_plant_date`, `jsuser_sn`, `onfp_plant_amount`,`onfp_part_no`,onadd_sn) VALUES ('{$now}', '{$onadd_planting_date}','{$jsuser_sn}','{$onadd_replant_number}','{$onadd_part_no}','{$onadd_id}');";
					//更新原本產品數量 (扣除換盆)
					$sql1 = "UPDATE onliine_add_data SET onadd_quantity='{$onadd_quantity_cha123}', onadd_status='{$onadd_status}' WHERE onadd_sn='{$onadd_sn}'";
					//更新原本產品的第一筆下種數量(扣除換盆)
					$sql3 = "UPDATE onliine_firstplant_data SET onfp_plant_amount='{$first_n_changed}' WHERE onadd_sn='{$onadd_sn}'";
					if($conn->query($sql2) && $conn->query($sql1) && $conn->query($sql3)){
						$ret_msg = "換盆成功！";
					}
					else{
						$ret_msg = "換盆失敗！";
					}
				}				
				if($onadd_quantity_cha123 == 0){
					$sql = "UPDATE onliine_add_data SET onadd_quantity='{$onadd_quantity_cha123}', onadd_status='-1' WHERE onadd_sn='{$onadd_sn}'";
					$conn->query($sql);
				}			
			}
			else if($onadd_status == -1){
				$ret_msg = "錯誤！換盆數量高於原下種數量！";
			}
			else {	
				$ret_msg = "換盆失敗！";
			}
			$conn->close();
		}
		break;

		//汰除---------------------------------------------
		case 'upd1':
		$onadd_sn=GetParam('onadd_sn');
		$list = getUserBySn($onadd_sn);
		$onadd_part_no = $list['onadd_part_no'];
		$onadd_part_name = $list['onadd_part_name'];
		$onadd_quantity=GetParam('onadd_quantity');//下種數量
		$jsuser_sn = GetParam('supplier');//編輯人員
		$onadd_quantity_del=GetParam('onadd_quantity_del');//汰除數量
		$onelda_reason=GetParam('onelda_reason');//汰除原因
		$jsuser_sn = GetParam('supplier');//編輯人員
		$onadd_quantity_del123 = ($onadd_quantity - $onadd_quantity_del);
		if($onadd_quantity_del123 < 0) {
			$onadd_status = -1;
		} else {
			$onadd_status = 1;
		}

		if(empty($onadd_quantity_del)){
			$ret_msg = "*為必填！";
		} 
		else if($onadd_status != -1){
			$now = time();
			$conn = getDB();
			$sql1 = "UPDATE onliine_add_data SET onadd_quantity='{$onadd_quantity_del123}', onadd_status='{$onadd_status}' WHERE onadd_sn='{$onadd_sn}'";
			$sql = "INSERT INTO online_elimination_data (onelda_add_date, onelda_mod_date, onelda_quantity, onelda_reason, onadd_sn, onadd_part_no, onadd_part_name) " .
				"VALUES ('{$now}', '{$now}', '{$onadd_quantity_del}', '{$onelda_reason}', '{$onadd_sn}', '{$onadd_part_no}', '{$onadd_part_name}');";
			if($conn->query($sql1) && $conn->query($sql)) {
				$ret_msg = "汰除完成！";
				if($onadd_quantity_del123 == 0){
					$sql = "UPDATE onliine_add_data SET onadd_quantity='{$onadd_quantity_del123}', onadd_status='-1' WHERE onadd_sn='{$onadd_sn}'";
					$conn->query($sql);
				}
			} else {
				$ret_msg = "汰除失敗！";
			}
		}
		else if($onadd_status == -1){
			$ret_msg = "錯誤！ 汰除數量不可大於下種數量！";
		}

		break;
		//汰除---------------------------------------------

		//出貨---------------------------------------------
		case 'upd2':
		$onadd_sn=GetParam('onadd_sn');
		$list = getUserBySn($onadd_sn);
		$onadd_part_no = $list['onadd_part_no'];
		$onadd_part_name = $list['onadd_part_name'];
		$onadd_quantity=GetParam('onadd_quantity');//下種數量
		$jsuser_sn = GetParam('supplier');//編輯人員
		$onadd_plant_year=GetParam('onadd_plant_year');//出貨數量
		$onshda_client=GetParam('onshda_client');//出貨客戶
		$onadd_quantity_shi123 = ($onadd_quantity - $onadd_plant_year);
		if($onadd_quantity_shi123 < 0) {
			$onadd_status = -1;
		} else {
			$onadd_status = 1;
		}

		if(empty($onadd_plant_year)){
			$ret_msg = "*為必填！";
		} 
		else if($onadd_status != -1){
			$now = time();
			$conn = getDB();
			$sql1 = "UPDATE onliine_add_data SET onadd_quantity='{$onadd_quantity_shi123}', onadd_status='{$onadd_status}' WHERE onadd_sn='{$onadd_sn}'";
			$sql = "INSERT INTO online_shipment_data (onshda_add_date, onshda_mod_date, onshda_client, onshda_quantity, onadd_sn, onadd_part_no, onadd_part_name) " .
				"VALUES ('{$now}', '{$now}', '{$onshda_client}', '{$onadd_plant_year}', '{$onadd_sn}', '{$onadd_part_no}', '{$onadd_part_name}');";
			if($conn->query($sql1) && $conn->query($sql)) {
				$ret_msg = "出貨完成！";
				if($onadd_quantity_shi123 == 0){
					$sql = "UPDATE onliine_add_data SET onadd_quantity='{$onadd_quantity_shi123}', onadd_status='-1' WHERE onadd_sn='{$onadd_sn}'";
					$conn->query($sql);
				}
			} else {
				$ret_msg = "出貨失敗！";
			}
		}
		else if($onadd_status == -1){
			$ret_msg = "錯誤！ 出貨數量不可大於下種數量！";
		}
		break;
		//出貨---------------------------------------------

		case 'del':
		$onadd_sn=GetParam('onadd_sn');

		if(empty($onadd_sn)){
			$ret_msg = "刪除失敗！";
		}else{
			$now = time();
			$conn = getDB();
			$sql = "DELETE FROM onliine_add_data WHERE onadd_sn='{$onadd_sn}'";
			if($conn->query($sql)) {
				$ret_msg = "刪除完成！";
			} else {
				$ret_msg = "刪除失敗！";
			}
			$conn->close();
		}
		break;

		//產品履歷---------------------------------------------
		case 'get_history_list':
		$onadd_sn = GetParam('onadd_sn');

		if(empty($onadd_sn)){
			$ret_msg = "查詢失敗！";
		} else {
			$ret_data = getHistory_List($onadd_sn);
		}
		break;
		//產品履歷---------------------------------------------

		//搜尋用的資料---------------------------------------------
		case 'get_all_product':
		$ret_code = 1;
		$ret_data = getAllProductsNo();

		break;

		case 'getProductByPartNo':
		$onproduct_part_no=GetParam('onproduct_part_no');
		if(!empty($onproduct_part_no)){
			$ret_code = 1;
			$ret_data = getProductByPartNo($onproduct_part_no);
		}
		else{
			$ret_code = 0;
		}

		break;

		default:
		$ret_msg = 'error!';
		break;
	}

	echo enclode_ret_data($ret_code, $ret_msg, $ret_data);
	exit;
} else {
	// search
	if(($onadd_sn = GetParam('onadd_sn'))) {
		$search_where[] = "onadd_sn like '%{$onadd_sn}%'";
		$search_query_string['onadd_sn'] = $onadd_sn;
	}
	if(($onadd_part_no = GetParam('onadd_part_no'))) {
		$search_where[] = "onadd_part_no like '%{$onadd_part_no}%'";
		$search_query_string['onadd_part_no'] = $onadd_part_no;
	}
	if(($onadd_part_name = GetParam('onadd_part_name'))) {
		$search_where[] = "onadd_part_name like '%{$onadd_part_name}%'";
		$search_query_string['onadd_part_name'] = $onadd_part_name;
	}
	if(($onadd_location = GetParam('onadd_location'))) {
		$search_where[] = "onadd_location like '%{$onadd_location}%'";
		$search_query_string['onadd_location'] = $onadd_location;
	}

	$search_where = isset($search_where) ? implode(' and ', $search_where) : '';
	$search_query_string = isset($search_query_string) ? http_build_query($search_query_string) : '';

	// page
	$pg_page = GetParam('pg_page', 1);
	$pg_rows = 20;
	$pg_total = GetParam('pg_total')=='' ? getUserQty($search_where) : GetParam('pg_total');
	$pg_offset = $pg_rows * ($pg_page - 1);
	$pg_pages = $pg_rows == 0 ? 0 : ( (int)(($pg_total + ($pg_rows - 1)) /$pg_rows) );

	$product_list = getUser($search_where, $pg_offset, $pg_rows);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html">

	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title><?php echo CN_NAME;?></title>
	<!-- Common plugins -->
	<!-- <link href="./../img/apple-touch-icon.png" rel="apple-touch-icon"> -->
	<link href="./../../images/favicon.png" rel="icon">
	<link href="./../../css1/bootstrap.min.css" rel="stylesheet">
	<link href="./../../css1/simple-line-icons.css" rel="stylesheet">
	<link href="./../../css1/font-awesome.min.css" rel="stylesheet">
	<link href="./../../css1/pace.css" rel="stylesheet">
	<link href="./../../css1/jasny-bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="./../../css1/nanoscroller.css">
	<link rel="stylesheet" href="./../../css1/metismenu.min.css">
	<link href="./../../css1/c3.min.css" rel="stylesheet">
	<link href="./../../css1/blue.css" rel="stylesheet">
	<!-- dataTables -->
	<link href="./../../css1/jquery.datatables.min.css" rel="stylesheet" type="text/css">
	<link href="./../../css1/responsive.bootstrap.min.css" rel="stylesheet" type="text/css">
	<!-- <link href="./../css1/jquery.toast.min.css" rel="stylesheet"> -->
	<!--template css-->
	<link href="./../../css1/style.css" rel="stylesheet">
	<?php include('./../htmlModule/head.php');?>
	<script src="./../../lib/jquery.twbsPagination.min.js"></script>
	<script src="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js"></script>
	<link rel="stylesheet" href="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
	<style>
	* {
	  box-sizing: border-box;
	}

	/*the container must be positioned relative:*/
	.autocomplete {
	  position: relative;
	  display: inline-block;
	}

	input {
	  border: 1px solid transparent;
	  background-color: #f1f1f1;
	  padding: 10px;
	  font-size: 16px;
	}

	input[type=submit] {
	  background-color: DodgerBlue;
	  color: #fff;
	  cursor: pointer;
	}

	.autocomplete-items {
	 /* position: absolute;*/
	  border: 1px solid #d4d4d4;
	  border-bottom: none;
	  border-top: none;
	  z-index: 99;
	  /*position the autocomplete items to be the same width as the container:*/
	  top: 100%;

	}

	.autocomplete-items div {
	  padding: 10px;
	  cursor: pointer;
	  background-color: #fff; 
	  border-bottom: 1px solid #d4d4d4; 
	}

	/*when hovering an item:*/
	.autocomplete-items div:hover {
	  background-color: #e9e9e9; 
	}

	/*when navigating through the items using the arrow keys:*/
	.autocomplete-active {
	  background-color: DodgerBlue !important; 
	  color: #ffffff; 
	}
	</style>
	<script type="text/javascript">
		$(document).ready(function() {
			var all_part_no = null;
			var all_part_name = null;


		    $("body").on("change", ".upl", function (){
		        preview(this);
		    })

			<?php
			//	init search parm
			// print "$('#search [name=onadd_status] option[value={$onadd_status}]').prop('selected','selected');";
			// print "$('#search [name=onadd_growing] option[value={$onadd_growing}]').prop('selected','selected','selected','selected','selected','selected','selected');";
			?>
			function autocomplete(inp, arr) {

		  /*the autocomplete function takes two arguments,
		  the text field element and an array of possible autocompleted values:*/
		  var currentFocus;
		  /*execute a function when someone writes in the text field:*/
		  inp.addEventListener("input", function(e) {
		      var a, b, i, val = this.value;
		      /*close any already open lists of autocompleted values*/
		      closeAllLists();
		      if (!val) { return false;}
		      currentFocus = -1;
		      /*create a DIV element that will contain the items (values):*/
		      a = document.createElement("DIV");
		      a.setAttribute("id", this.id + "autocomplete-list");
		      a.setAttribute("class", "autocomplete-items");
		      /*append the DIV element as a child of the autocomplete container:*/
		      this.parentNode.appendChild(a);
		      /*for each item in the array...*/
		      for (i = 0; i < arr.length; i++) {
		        /*check if the item starts with the same letters as the text field value:*/
		        if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
		          /*create a DIV element for each matching element:*/
		          b = document.createElement("DIV");
		          /*make the matching letters bold:*/
		          b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
		          b.innerHTML += arr[i].substr(val.length);
		          /*insert a input field that will hold the current array item's value:*/
		          b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
		          /*execute a function when someone clicks on the item value (DIV element):*/
		          b.addEventListener("click", function(e) {
		              /*insert the value for the autocomplete text field:*/
		            inp.value = this.getElementsByTagName("input")[0].value;
						$.ajax({
							url: './plant_purchase_add.php',
							type: 'post',
							dataType: 'json',
							data: {op:"getProductByPartNo",onproduct_part_no:inp.value},
							beforeSend: function(msg) {
								$("#ajax_loading").show();
							},
							complete: function(XMLHttpRequest, textStatus) {
								$("#ajax_loading").hide();
							},
							success: function(ret) {
									var data = ret.data;
									// console.log(data);
							        if(ret.code==1) {
							        	document.getElementById('dropdown_onadd_part_name').value = (data.onproduct_part_name != null) ? data.onproduct_part_name : "";
							        	document.getElementById('dropdown_onadd_color').value = (data.onproduct_color!= null) ? data.onproduct_color : "";
							        	document.getElementById('dropdown_onadd_size').value = (data.onproduct_size != null) ? data.onproduct_size : "";
							        	document.getElementById('dropdown_onadd_height').value = (data.onproduct_height != null) ? data.onproduct_height : "";
							        	document.getElementById('dropdown_onadd_location').value = (data.onproduct_location != null) ? data.onproduct_location : "";
							        	document.getElementById('dropdown_onadd_pot_size').value = (data.onproduct_pot_size != null) ? data.onproduct_pot_size : "";
							        	document.getElementById('dropdown_onadd_supplier').value = (data.onproduct_supplier != null) ? data.onproduct_supplier : "";
							        	document.getElementById('dropdown_onadd_growing').value = data.onproduct_growing;
							        }
							    },
							    error: function (xhr, ajaxOptions, thrownError) {
							    	// console.log('ajax error');
							     //    console.log(xhr);
							    }
							});
		              /*close the list of autocompleted values,
		              (or any other open lists of autocompleted values:*/
		              closeAllLists();
		          });
		          a.appendChild(b);
		        }
		      }
		  });
		  /*execute a function presses a key on the keyboard:*/
		  inp.addEventListener("keydown", function(e) {
		      var x = document.getElementById(this.id + "autocomplete-list");
		      if (x) x = x.getElementsByTagName("div");
		      if (e.keyCode == 40) {
		        /*If the arrow DOWN key is pressed,
		        increase the currentFocus variable:*/
		        currentFocus++;
		        /*and and make the current item more visible:*/
		        addActive(x);
		      } else if (e.keyCode == 38) { //up
		        /*If the arrow UP key is pressed,
		        decrease the currentFocus variable:*/
		        currentFocus--;
		        /*and and make the current item more visible:*/
		        addActive(x);
		      } else if (e.keyCode == 13) {
		        /*If the ENTER key is pressed, prevent the form from being submitted,*/
		        e.preventDefault();
		        if (currentFocus > -1) {
		          /*and simulate a click on the "active" item:*/
		          if (x) x[currentFocus].click();
		        }
		      }
		  });

		  function addActive(x) {
		    /*a function to classify an item as "active":*/
		    if (!x) return false;
		    /*start by removing the "active" class on all items:*/
		    removeActive(x);
		    if (currentFocus >= x.length) currentFocus = 0;
		    if (currentFocus < 0) currentFocus = (x.length - 1);
		    /*add class "autocomplete-active":*/
		    x[currentFocus].classList.add("autocomplete-active");
		  }
		  function removeActive(x) {
		    /*a function to remove the "active" class from all autocomplete items:*/
		    for (var i = 0; i < x.length; i++) {
		      x[i].classList.remove("autocomplete-active");
		    }
		  }
		  function closeAllLists(elmnt) {
		    /*close all autocomplete lists in the document,
		    except the one passed as an argument:*/
		    var x = document.getElementsByClassName("autocomplete-items");
		    for (var i = 0; i < x.length; i++) {
		      if (elmnt != x[i] && elmnt != inp) {
		        x[i].parentNode.removeChild(x[i]);
		      }
		    }
		  }
		  /*execute a function when someone clicks in the document:*/
		  document.addEventListener("click", function (e) {
		      closeAllLists(e.target);
		  });
  		}	
		
		$.ajax({
			url: './plant_purchase_add.php',
			type: 'post',
			dataType: 'json',
			data: {op:"get_all_product"},
			beforeSend: function(msg) {
				$("#ajax_loading").show();
			},
			complete: function(XMLHttpRequest, textStatus) {
				$("#ajax_loading").hide();
			},
			success: function(ret) {
			        if(ret.code==1) {
			        	all_part_no = ret.data;	
			        	/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
						autocomplete(document.getElementById('dropdown_onadd_part_no'), all_part_no[0]);

			        }
			    },
			    error: function (xhr, ajaxOptions, thrownError) {
		        	// console.log('ajax error');
		            // console.log(xhr);
		        }
		    });


			$('button.upd').on('click', function(){
				$('#upd-modal').modal();
				$('#upd_form')[0].reset();

				$.ajax({
					url: './plant_purchase.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get", onadd_sn:$(this).data('onadd_sn')},
					beforeSend: function(msg) {
						$("#ajax_loading").show();
					},
					complete: function(XMLHttpRequest, textStatus) {
						$("#ajax_loading").hide();
					},
					success: function(ret) {
			                // console.log(ret);
			                if(ret.code==1) {
			                	var d = ret.data;			                	
			                	$('#upd_form input[name=onadd_sn]').val(d.onadd_sn);
			                	$('#upd_form input[name=onadd_part_no]').val(d.onadd_part_no);
			                	$('#upd_form input[name=onadd_part_name]').val(d.onadd_part_name);
			                	$('#upd_form input[name=onadd_color]').val(d.onadd_color);
			                	$('#upd_form input[name=onadd_size]').val(d.onadd_size);
			                	$('#upd_form input[name=onadd_height]').val(d.onadd_height);
			                	$('#upd_form input[name=onadd_pot_size]').val(d.onadd_pot_size);
			                	$('#upd_form input[name=onadd_supplier]').val(d.onadd_supplier);			                	
			                	// $('#upd_form input[name=onadd_planting_date]').val(d.onadd_planting_date);
			                	$('#upd_form input[name=onadd_quantity]').val(d.onadd_quantity);
			                	// $('#upd_form input[name=onadd_growing]').val(d.onadd_growing);
			                	$('#upd_form [name=onadd_growing] option[value='+d.onadd_growing+']').prop('selected','selected','selected','selected','selected','selected','selected');			                	
			                	$('#upd_form [name=onadd_status] option[value='+d.onadd_status+']').prop('selected','selected');
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			});

			//汰除-----------------------------------------------------------
			$('button.upd1').on('click', function(){
				$('#upd-modal1').modal();
				$('#upd_form1')[0].reset();

				$.ajax({
					url: './plant_purchase.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get", onadd_sn:$(this).data('onadd_sn')},
					beforeSend: function(msg) {
						$("#ajax_loading").show();
					},
					complete: function(XMLHttpRequest, textStatus) {
						$("#ajax_loading").hide();
					},
					success: function(ret) {
			                // console.log(ret);
			                if(ret.code==1) {
			                	var d = ret.data;
			                	$('#upd_form1 input[name=onadd_sn]').val(d.onadd_sn);
			                	$('#upd_form1 input[name=onadd_part_no]').val(d.onadd_part_no);
			                	$('#upd_form1 input[name=onadd_quantity]').val(d.onadd_quantity);
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			});
			//汰除-----------------------------------------------------------

			//出貨-----------------------------------------------------------
			$('button.upd2').on('click', function(){
				$('#upd-modal2').modal();
				$('#upd_form2')[0].reset();

				$.ajax({
					url: './plant_purchase.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get", onadd_sn:$(this).data('onadd_sn')},
					beforeSend: function(msg) {
						$("#ajax_loading").show();
					},
					complete: function(XMLHttpRequest, textStatus) {
						$("#ajax_loading").hide();
					},
					success: function(ret) {
			                // console.log(ret);
			                if(ret.code==1) {
			                	var d = ret.data;
			                	$('#upd_form2 input[name=onadd_sn]').val(d.onadd_sn);
			                	$('#upd_form2 input[name=onadd_part_no]').val(d.onadd_part_no);
			                	$('#upd_form2 input[name=onadd_quantity]').val(d.onadd_quantity);
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			});
			//出貨-----------------------------------------------------------

			//修改-----------------------------------------------------------
			$('button.upd3').on('click', function(){
				$('#upd-modal3').modal();
				$('#upd3_form')[0].reset();

								$.ajax({
					url: './plant_purchase.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get", onadd_sn:$(this).data('onadd_sn')},
					beforeSend: function(msg) {
						$("#ajax_loading").show();
					},
					complete: function(XMLHttpRequest, textStatus) {
						$("#ajax_loading").hide();
					},
					success: function(ret) {
			                // console.log(ret);
			                if(ret.code==1) {
			                	var d = ret.data;
			                	$('#upd3_form input[name=onadd_sn]').val(d.onadd_sn);
			                	$('#upd3_form input[name=onadd_part_no]').val(d.onadd_part_no);
			                	$('#upd3_form input[name=onadd_part_name]').val(d.onadd_part_name);
			                	$('#upd3_form input[name=onadd_color]').val(d.onadd_color);
			                	$('#upd3_form input[name=onadd_size]').val(d.onadd_size);
			                	$('#upd3_form input[name=onadd_height]').val(d.onadd_height);
			                	$('#upd3_form input[name=onadd_pot_size]').val(d.onadd_pot_size);
			                	$('#upd3_form input[name=onadd_location]').val(d.onadd_location);
			                	$('#upd3_form input[name=onadd_supplier]').val(d.onadd_supplier);
			                	$('#upd3_form input[name=onadd_planting_date]').val(d.onadd_planting_date);
			                	$('#upd3_form input[name=onadd_quantity]').val(d.onadd_quantity);
			                	$('#upd3_form input[name=onadd_growing]').val(d.onadd_growing);
			                	$('#upd3_form [name=onadd_growing] option[value='+d.onadd_growing+']').prop('selected','selected','selected','selected','selected','selected','selected');			                	
			                	$('#upd3_form [name=onadd_status] option[value='+d.onadd_status+']').prop('selected','selected');
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                 //    console.log(thrownError);
		                }
		            });
			});			
			//修改-----------------------------------------------------------

			bootbox.setDefaults({
				locale: "zh_TW",
			});

			$('button.del').on('click', function(){
				onadd_sn = $(this).data('onadd_sn')
				bootbox.confirm("確認刪除？", function(result) {
					if(result) {
						$.ajax({
							url: './plant_purchase.php',
							type: 'post',
							dataType: 'json',
							data: {op:"del", onadd_sn:onadd_sn},
							beforeSend: function(msg) {
								$("#ajax_loading").show();
							},
							complete: function(XMLHttpRequest, textStatus) {
								$("#ajax_loading").hide();
							},
							success: function(ret) {
								alert_msg(ret.msg);
							},
							error: function (xhr, ajaxOptions, thrownError) {
				                	// console.log('ajax error');
				                }
				            });
					}
				});
			});
			//產生QR Code-------------------------------------------------------
			$('button.qr').on('click', function(){
				$('#qr_modal').modal();
				$.ajax({
					url: './plant_purchase.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get", onadd_sn:$(this).data('onadd_sn')},
					beforeSend: function(msg) {
						$("#ajax_loading").show();
					},
					complete: function(XMLHttpRequest, textStatus) {
						$("#ajax_loading").hide();
					},
					success: function(ret) {
			                // console.log(ret);
			                if(ret.code==1) {
			                	var d = ret.data;
			                	// console.log("log="+document.getElementById('qr_part_no').html);
			                	$('#qr_download').attr('data-onadd_sn',d.onadd_sn);
			                	$('#qr_part_no').html("品號："+d.onadd_part_no);
			                	$('#qr_part_name').html("品名："+d.onadd_part_name);
			                	$('#qr_plant_date').html("下種日期："+d.onadd_planting_date);
			                	$('#qr_part_number').html("數量："+d.onadd_quantity);
			                	var src = $('#qr_img').attr('src');
			                	$('#qr_img').attr('src',src+"onadd_part_no="+d.onadd_sn);
			                	document.getElementById('qr_cotent_recover').appendChild(document.getElementById('qr_cotent').cloneNode(true));
			                	$('#qr_cotent_recover').attr('style','display:none');
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			});
			//下載QR Code-------------------------------------------------------
			$('button.qr_download').on('click', function(){
				$('#qr_modal').modal();
				$.ajax({
					url: './plant_purchase.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get", onadd_sn:$(this).data('onadd_sn')},
					beforeSend: function(msg) {
						$("#ajax_loading").show();
					},
					complete: function(XMLHttpRequest, textStatus) {
						$("#ajax_loading").hide();
					},
					success: function(ret) {
			                // console.log(ret);
			                if(ret.code==1) {
			                	
			                	$('#qr_img').attr('style','margin-left: 0px;padding-left: 0px;width: 85px;padding-right: 0px;border-top-width: 20px;padding-top: 20px;');
			                	$('#qr_sec_cotent').attr('style','padding-left: 25px;');
			                	$('#qr_sec_cotent2').attr('style','padding-left: 38px;');
			                	$('#qr_part_no').attr('style','font-size: 14px;font-weight:bold;');
			                	$('#qr_part_name').attr('style','font-size: 14px;font-weight:bold;');
			                	$('#qr_plant_date').attr('style','font-size: 14px;font-weight:bold;');
			                	$('#qr_part_number').attr('style','font-size: 14px;font-weight:bold;');
			                	PrintElem('qr_cotent');

			                	setTimeout(
								    function() {		
								    	var qr = document.getElementById('qr_cotent_recover').children[0].children[0];					
								    	var data = document.getElementById('qr_cotent_recover').children[0].children[1];    	
								    	$('#qr_cotent').empty();
								    	$('#qr_cotent').append(qr);
								    	$('#qr_cotent').append(data);
								    	// $('#qr_cotent').html($('#qr_cotent').html()+data.children[1]);
								    	$('#qr_cotent').removeAttr('style');
								    	document.getElementById('qr_cotent_recover').removeChild(document.getElementById('qr_cotent_recover').children[0]);
								    	document.getElementById('qr_cotent_recover').appendChild(document.getElementById('qr_cotent').cloneNode(true));
								    }, 500);
			                	
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			});

			$('#add_form, #upd_form, #upd_form1, #upd_form2, #upd3_form').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					e.preventDefault();
					var param = $(this).serializeArray();
					// console.log(param);
					$(this).parents('.modal').modal('hide');
					$(this)[0].reset();

					 	// console.table(param);

					 	$.ajax({
					 		url: './plant_purchase.php',
					 		type: 'post',
					 		dataType: 'json',
					 		data: param,
					 		beforeSend: function(msg) {
					 			$("#ajax_loading").show();
					 		},
					 		complete: function(XMLHttpRequest, textStatus) {
					 			$("#ajax_loading").hide();
					 		},
					 		success: function(ret) {
					 			alert_msg(ret.msg);
					 		},
					 		error: function (xhr, ajaxOptions, thrownError) {
			                	// console.log('ajax error');
			                 //     console.log(thrownError);
			                 }
			             });
					 }
					});
			$('#datetimepicker1').datetimepicker({
		        	minView: 2,
		            language:  'zh-TW',
		            format: 'yyyy-mm-dd',
		            useCurrent: false
		        });
		        
		        $('#datetimepicker2').datetimepicker({
		        	minView: 2,
		            language:  'zh-TW',
		            format: 'yyyy-mm-dd',
		            useCurrent: false
		        });
		        $('#datetimepicker3').datetimepicker({
		        	minView: 2,
		            language:  'zh-TW',
		            format: 'yyyy-mm-dd',
		            useCurrent: false
		        });
		        $('button.cancel').on('click', function() {
					location.href = "./../";
				});
		});

			//產品履歷----------------------------------------------------------
			function history(onadd_part_no,onadd_name,onadd_sn){
				$('#history_title').html(onadd_part_no+" - "+onadd_name+" 苗種履歷");
				$('#history_modal').modal();
				$.ajax({
					url: './plant_purchase.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get_history_list", onadd_sn:onadd_sn},
					beforeSend: function(msg) {
						$("#ajax_loading").show();
					},
					complete: function(XMLHttpRequest, textStatus) {
						$("#ajax_loading").hide();
					},
					success: function(ret) {
						console.log(ret);
						$('#history_cotent').html('<div class="col-md-12"><div class="col-sm-12"><label for="addModalInput1" class="col-sm-2 control-label">操作日期</label><label for="addModalInput1" class="col-sm-2 control-label">下種日期(數量)</label><label for="addModalInput1" class="col-sm-2 control-label">換盆日期(數量)</label><label for="addModalInput1" class="col-sm-2 control-label">出貨日期(數量)</label></label><label for="addModalInput1" class="col-sm-2 control-label">汰除日期(數量)</label></div></div>');
						$.each(ret.data, function(key,value){	
							if(key < ret.data.length){
								var temp = "";
								switch(value.flag){
									case 0:
										temp ='<label for="addModalInput1" class="col-sm-2 control-label">'+value.add_date+'</label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label">'+value.mod_date+' ('+value.quantity+')</label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>';
									break;
									case 1:
										temp ='<label for="addModalInput1" class="col-sm-2 control-label">'+value.add_date+'</label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label">'+value.mod_date+' ('+value.quantity+')</label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>';
									break;
									case 2:
										temp ='<label for="addModalInput1" class="col-sm-2 control-label">'+value.add_date+'</label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label">'+value.mod_date+' ('+value.quantity+')</label>';
									break;
									case 3:
										temp ='<label for="addModalInput1" class="col-sm-2 control-label">'+value.add_date+'</label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label">'+value.mod_date+' ('+value.quantity+')</label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-sm-2 control-label"></label>';
									break;
								}
																		
								$('#history_cotent').html($('#history_cotent').html()+'<div class="col-md-12"><div class="col-sm-12">'+temp+'</div></div>');								
							}

						});
						
					},
					error: function (xhr, ajaxOptions, thrownError) {
				   	console.log('ajax error');
				        // console.log(xhr);
				    }
				});
			}
			//產品履歷----------------------------------------------------------
			function PrintElem(elem)
			{
			    var mywindow = window.open('', 'PRINT', 'height=400,width=600');

			    mywindow.document.write('<html><head><title>' + document.title  + '</title>');
			    mywindow.document.write('</head><body >');
			    mywindow.document.write('<h1>' + document.title  + '</h1>');
			    document.getElementById(elem).setAttribute("style", "width: 240px; height: 170px;");
			    mywindow.document.write(document.getElementById(elem).innerHTML);
			    mywindow.document.write('</body></html>');

			    mywindow.document.close(); // necessary for IE >= 10
			    mywindow.focus(); // necessary for IE >= 10*/

			    domtoimage.toBlob(document.getElementById(elem))
				    .then(function(blob) {
				      window.saveAs(blob, $('#qr_part_no').html());
				    });
			    mywindow.close();

			    return true;
			}

			function insert(str, index, value) {
			    return str.substr(0, index) + value + str.substr(index);
			}
			function downloadAsImg( el, filename, scale ){
			    if( scale!=undefined ) var props = {
			        width: el.clientWidth*scale*1.412,
			        height: el.clientHeight*scale,
			        style: {
			            'transform': 'scale('+scale+')',
			            'transform-origin': 'top left'
			        }
			    }
			    domtoimage.toBlob( el, props==undefined ? {} : props).then(function (blob) {
			        window.saveAs(blob, filename==undefined ? 'image.png' : filename);
			    });
			}

			/**
			 * 預覽圖
			 * @param   input 輸入 input[type=file] 的 this
			 */
			function preview(input) {
			 
			    // 若有選取檔案
			    if (input.files && input.files[0]) {
			 
			        // 建立一個物件，使用 Web APIs 的檔案讀取器(FileReader 物件) 來讀取使用者選取電腦中的檔案
			        var reader = new FileReader();
			 
			        // 事先定義好，當讀取成功後會觸發的事情
			        reader.onload = function (e) {
			            
			            console.log(e);
			 
			            // 這裡看到的 e.target.result 物件，是使用者的檔案被 FileReader 轉換成 base64 的字串格式，
			            // 在這裡我們選取圖檔，所以轉換出來的，會是如 『data:image/jpeg;base64,.....』這樣的字串樣式。
			            // 我們用它當作圖片路徑就對了。
			            $('.preview').attr('src', e.target.result);
			 
			            // 檔案大小，把 Bytes 轉換為 KB
			            var KB = format_float(e.total / 1024, 2);
			            $('.size').text("檔案大小：" + KB + " KB");
			        }
			 
			        // 因為上面定義好讀取成功的事情，所以這裡可以放心讀取檔案
			        reader.readAsDataURL(input.files[0]);
			    }
			}
 
			/**
			 * 格式化
			 * @param   num 要轉換的數字
			 * @param   pos 指定小數第幾位做四捨五入
			 */
			function format_float(num, pos)
			{
			    var size = Math.pow(10, pos);
			    return Math.round(num * size) / size;
			}
 

	</script>
</head>

<body>
	<?php include('./../htmlModule/nav.php');?>
	<!--main content start-->
	<section class="main-content">



		<!--page header start-->
		<div class="page-header">
			<div class="row">
				<div class="col-sm-6">
					<h4>苗種庫存管理</h4>
				</div>
			</div>
		</div>		

		<div id="upd-modal" class="modal upd-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">換盆</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd">
									<input type="hidden" name="onadd_sn">
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品號<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品名<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_name" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">花色</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_color" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">花徑</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_size" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">高度</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_height" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">適合開花盆徑</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_pot_size" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">供應商</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_supplier" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">下種數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">換盆日期&nbsp;<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="datetimepicker2" name="onadd_planting_date" value="<?php echo (empty($device['onadd_planting_date'])) ? '' : date('Y-m-d', $device['onadd_planting_date']);?>" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
									</div>        								
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label" >換盆數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_replant_number" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>	
									<div class="form-group">
										<label class="col-sm-2 control-label">預計成長大小<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onadd_growing">
												<option value="7">其他</option>
												<option value="6">3.6</option>
												<option value="5">3.5</option>
												<option value="4">3.0</option>
												<option value="3">2.8</option>
												<option value="2">2.5</option>
												<option selected="selected" value="1">1.7</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">更新</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!--修改----------------------------------------------------------->
		<div id="upd-modal3" class="modal upd-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd3_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">修改</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd5">
									<input type="hidden" name="onadd_sn">
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品號<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品名<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_name" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">花色</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_color" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">花徑</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_size" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">高度</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_height" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">放置區</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_location" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">適合開花盆徑</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_pot_size" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">供應商</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_supplier" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">下種數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">換盆日期&nbsp;</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="datetimepicker3" name="onadd_planting_date" value="<?php echo (empty($device['onadd_planting_date'])) ? '' : date('Y-m-d', $device['onadd_planting_date']);?>" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
									</div>        								
									<div class="form-group">
										<label class="col-sm-2 control-label">預計成長大小<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onadd_growing">
												<option value="7">其他</option>
												<option value="6">3.6</option>
												<option value="5">3.5</option>
												<option value="4">3.0</option>
												<option value="3">2.8</option>
												<option value="2">2.5</option>
												<option selected="selected" value="1">1.7</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">更新</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!--汰除----------------------------------------------------------->
		<div id="upd-modal1" class="modal upd-modal1" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd_form1" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">汰除</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd1">
									<input type="hidden" name="onadd_sn">
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品號<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">下種數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">汰除數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity_del" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">汰除原因<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onelda_reason">
												<option value="4">其他</option>
												<option value="3">黑頭</option>
												<option value="2">褐斑</option>
												<option selected="selected" value="1">軟腐</option>
											</select>
										</div>
									</div>        								
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">更新</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--汰除----------------------------------------------------------->

		<!--出貨----------------------------------------------------------->
		<div id="upd-modal2" class="modal upd-modal2" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd_form2" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">出貨</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd2">
									<input type="hidden" name="onadd_sn">
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品號<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">下種數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">出貨數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_plant_year" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">出貨對象<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onshda_client" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>         								
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">更新</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--出貨----------------------------------------------------------->

		<!--苗種履歷----------------------------------------------------------->
		<div id="history_modal" class="modal upd-modal2" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content" style="width: 1002px;">
					<form autocomplete="off" method="post" action="./" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="history_title">品號 - 品名 - 產品履歷</h4>
						</div>
						<div class="row">
							<div class="row" id="history_cotent">
								<div class="col-md-12">									
									<div class="col-sm-12">
										<label for="addModalInput1" class="col-sm-2 control-label">資料建立日期</label>
										<label for="addModalInput1" class="col-sm-2 control-label">下種日期(數量)</label>
										<label for="addModalInput1" class="col-sm-2 control-label">換盆日期(數量)</label>
										<label for="addModalInput1" class="col-sm-2 control-label">出貨日期(數量)</label>
										<label for="addModalInput1" class="col-sm-2 control-label">汰除日期(數量)</label>
									</div>	
								</div>

							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--苗種履歷----------------------------------------------------------->

		<!--QR Code產生Modal----------------------------------------------------------->
		<div id="qr_modal" class="modal upd-modal2" tabindex="-1" role="dialog">
			<div class="modal-dialog mw-100 w-75">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="history_title">二維條碼</h4>
						</div>
						<div class="row" id="qr_container">
							<div class="row" id="qr_cotent">
								<div class="col-sm-4" id="qr_sec_cotent">
									<img id="qr_img" style="margin-left: 20px;padding-left: 10px;" 
										 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/purchase/plant_purchase.php?">	
								</div>
								<div class="col-sm-8" id="qr_sec_cotent2">
									<br>
									<div id="qr_part_no" style="font-size: 20px;font-weight:bold;">品號：</div>
									<div id="qr_part_name" style="font-size: 20px;font-weight:bold;">品名：</div>
									<div id="qr_plant_date" style="font-size: 20px;font-weight:bold;">下種日期：</div>
									<div id="qr_part_number" style="font-size: 20px;font-weight:bold;">數量：</div>

								</div>
							</div>
							<div id="qr_cotent_recover" >
								
							</div>
						</div>

						<div class="modal-footer">
							<button id="qr_download" type="button" class="btn btn-primary qr_download">下載二維條碼</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--QR Code產生Modal----------------------------------------------------------->

		<!-- modal -->
		<div id="add-modal" class="modal add-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="add_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">新品項資料建立</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="add">
									<div class="form-group">
										<label class="col-sm-2 control-label">產品圖片</label>
										<div class="col-sm-10">
											<input type="hidden" name="MAX_FILE_SIZE" value="2097152">
										    <input type="hidden" id="onproduct_sn" name="onproduct_sn" value="37">
										    <input type="hidden" id="onproduct_type" name="onproduct_type" value="3">
										    <input type="hidden" id="parameters" name="parameters" value="plant_purchase_details.php">
										    <input type="file" class="upl" name="myFile" accept="image/jpeg,image/jpg,image/gif,image/png">
										    <!-- <input type='file' class="upl"> -->
										    <img class="preview" style="max-width: 500px; max-height: 500px;">
										    <div class="size"></div>
										    
									    </div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">苗種來源<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" id="addModalInput1" name="onadd_isbought" placeholder="" required minlength="1" maxlength="32">
											　<option value="0">自種苗</option>
											　<option value="1">外購苗</option>
											</select>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品號<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="dropdown_onadd_part_no" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品名<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="dropdown_onadd_part_name" name="onadd_part_name" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">花色</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="dropdown_onadd_color" name="onadd_color" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">花徑</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="dropdown_onadd_size" name="onadd_size" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">高度</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="dropdown_onadd_height" name="onadd_height" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">放置區</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="dropdown_onadd_location" name="onadd_location" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">適合開花盆徑</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="dropdown_onadd_pot_size" name="onadd_pot_size" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">供應商</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="dropdown_onadd_supplier" name="onadd_supplier" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">下種日期&nbsp;<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="datetimepicker1" name="onadd_planting_date" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">下種數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">預計成長大小<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" id="dropdown_onadd_growing" name="onadd_growing">
												<option value="7">其他</option>
												<option value="6">3.6</option>
												<option value="5">3.5</option>
												<option value="4">3.0</option>
												<option value="3">2.8</option>
												<option value="2">2.5</option>
												<option selected="selected" value="1">1.7</option>
											</select>
										</div>
									</div>

				
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="reset" class="btn btn-default">清空</button>
							<button type="submit" class="btn btn-primary">新增</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- container -->
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">

				<div class="navbar-collapse collapse pull-right" style="margin-bottom: 10px;">
						<ul class="nav nav-pills pull-right toolbar">
							<li><button data-parent="#toolbar" data-toggle="modal" data-target=".add-modal" class="accordion-toggle btn btn-primary"><i class="glyphicon glyphicon-plus"></i> 新品項建立</button></li>
							<!-- <li><button data-parent="#toolbar" class="accordion-toggle btn btn-primary" onclick="javascript:location.href='./plant_purchase_add.php'"><i class="glyphicon glyphicon-plus"></i> 新品項建立</button></li> -->
							<!-- <li><button data-parent="#toolbar" class="accordion-toggle btn btn-warning" onclick="javascript:location.href='./plant_purchase_add.php'"></i> 返回苗種資料建立</button></li> -->
						</ul>
					</div>

					<!-- search -->
					<div id="search" style="clear:both;">
						<form autocomplete="off" method="get" action="./plant_purchase.php" id="search_form" class="form-inline alert alert-info" role="form">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="searchInput1">品號</label>
										<input type="text" class="form-control" id="searchInput1" name="onadd_part_no" value="<?php echo $onadd_part_no;?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="searchInput4">品名</label>
										<input type="text" class="form-control" id="searchInput4" name="onadd_part_name" value="<?php echo $onadd_part_name;?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="searchInput2">放置區位置</label>
										<input type="text" class="form-control" id="searchInput2" name="onadd_location" value="<?php echo $onadd_location;?>" placeholder="">
									</div>

									<button type="submit" class="btn btn-info" op="search">搜尋</button>
								</div>
							</div>
						</form>
					</div>

					<!-- content -->
					<table class="table table-striped table-hover table-condensed tablesorter">
						<thead>
							<tr>
								<th>產品編號</th>
								<th>品號</th>
								<th>品名</th>
								<th>下種日期</th>
								<th>下種數量</th>
								<th>目前尺寸</th> <!-- 2019/6/19新增 -->
								<th>預計成熟日</th> <!-- 2019/6/19新增 -->
								<th>育成率</th> <!-- 2019/6/19新增 -->
								<th>放置區</th> <!-- 2019/8/7新增 -->
								<th>備註</th> <!-- 2019/6/19新增 -->
								<th>供應商</th>
								<th>訂單客戶</th> <!-- 2019/6/19新增 -->						
								<th>操作</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($product_list as $row) {
								echo '<tr>';
									if($row['onadd_part_no'] == 0){
        								echo '<td><a href="javascript:void(0);" onclick="history(\''.$row['onadd_part_no'].'\',\''.$row['onadd_part_name'].'\',\''.$row['onadd_sn'].'\')">'.date('Y',$row['onadd_planting_date']).'-'.$row['onadd_sn'].'</a></td>';//產品編號
									}else{
										echo '<td><a href="javascript:void(0);" onclick="history(\''.$row['onadd_part_no'].'\',\''.$row['onadd_part_name'].'\',\''.$row['onadd_sn'].'\')">P'.date('Y',$row['onadd_planting_date']).'-'.$row['onadd_sn'].'</a></td>';//產品編號
									}
        							echo '<td>'.$row['onadd_part_no'].'</td>';//品號
        							echo '<td>'.$row['onadd_part_name'].'</td>';//品名  							
        							echo '<td>'.date('Y-m-d',$row['onadd_planting_date']).'</td>';
        							echo '<td>'.$row['onadd_quantity'].'</td>';//品名
        							echo '<td>'.$permissions_mapping[$row['onadd_growing']].'寸'.'</td>';
        							if($row['onadd_growing']==1){
        								$list_setting = getSettingBySn(1.7);
        								$onchba_cycle = $list_setting['onchba_cycle'];
        							}else if($row['onadd_growing']==2){
        								$list_setting = getSettingBySn(2.5);
        								$onchba_cycle = $list_setting['onchba_cycle'];
        							}else if($row['onadd_growing']==5){
        								$list_setting = getSettingBySn(3.5);
        								$onchba_cycle = $list_setting['onchba_cycle'];
        							}
        							$test = date("Y/m/d", strtotime("+$onchba_cycle days", $row['onadd_planting_date']));
        							echo '<td>'.$test.'</td>';//預計成熟日

        							$onadd_cycle = ((date('m',$row['onadd_cycle']))-(date('m',$row['onadd_planting_date'])));

        							$first_plant_amount = getProductFirstQty($row['onadd_sn']);//第一次下種時間
        							//育成率 (公式 (數量-汰除)/數量)        				
        							$incubation_rate = ($first_plant_amount-getEliQtyBySn($row['onadd_sn']))/$first_plant_amount;
        							echo '<td>'.number_format(($incubation_rate*100),2).'%</td>'; 		
        							echo '<td>'.$row['onadd_location'].'</td>'; 				
        							$note = (!empty($row['onadd_quantity_cha'])) ? '<td>換盆</td>' : '<td></td>';
        							echo $note;
        							echo '<td>'.$row['onadd_supplier'].'</td>';
        							echo '<td></td>';
        							echo '<td><button type="button" class="btn btn-primary btn-xs upd5" data-onadd_sn="'.$row['onadd_sn'].'" data-toggle="collapse" data-target="#collapse'.$row['onadd_sn'].'">展開</button>';

        							// 2019/6/19新增 - 展開收回操作列表
        							echo '<td>
	        							    <div id="collapse'.$row['onadd_sn'].'" class="collapse">
	        							      <button type="button" class="btn btn-warning btn-xs upd1" data-onadd_sn="'.$row['onadd_sn'].'">汰除</button>&nbsp
	        							      <button type="button" class="btn btn-success btn-xs upd2" data-onadd_sn="'.$row['onadd_sn'].'">出貨</button>
	        							      <button type="button" class="btn btn-primary btn-xs upd" data-onadd_sn="'.$row['onadd_sn'].'">換盆</button>&nbsp;';
        							if($permmsion == '系統管理員'){
	        							echo '<button type="button" class="btn btn-success btn-xs upd3" data-onadd_sn="'.$row['onadd_sn'].'">修改</button>&nbsp;';
	        							echo '<button type="button" class="btn btn-danger btn-xs del" data-onadd_sn="'.$row['onadd_sn'].'">刪除</button>&nbsp;';
	        						}

	        						echo '<button type="button" class="btn btn-info btn-xs qr" data-onadd_sn="'.$row['onadd_sn'].'">產生二維條碼</button>&nbsp;
	        								</div>
	        							 </td>';
        							echo '</tr>';
        						}
        						?>
        					</tbody>
        				</table>

        				<?php include('./../htmlModule/page.php');?>

        			</div>
        		</div>
        	</div>

        	<!--Start footer-->
        	<footer class="footer">
        		<span>Copyright &copy; 2019. Online Plant</span>
        	</footer>
        	<!--end footer-->

        </section>
        <!--end main content-->

        <!--Common plugins-->
        <!-- <script src="./../../js1/jquery.min.js"></script> -->
        <!-- <script src="./../../js1/bootstrap.min.js"></script> -->
        <script src="./../../js1/pace.min.js"></script>
        <script src="./../../js1/jasny-bootstrap.min.js"></script>
        <script src="./../../js1/jquery.slimscroll.min.js"></script>
        <script src="./../../js1/jquery.nanoscroller.min.js"></script>
        <script src="./../../js1/metismenu.min.js"></script>
        <script src="./../../js1/float-custom.js"></script>
        <!--page script-->
        <script src="./../../js1/d3.min.js"></script>
        <script src="./../../js1/c3.min.js"></script>
        <!-- iCheck for radio and checkboxes -->
        <script src="./../../js1/icheck.min.js"></script>
        <!-- Datatables-->
        <script src="./../../js1/jquery.datatables.min.js"></script>
        <script src="./../../js1/datatables.responsive.min.js"></script>
        <script src="./../../js1/jquery.toast.min.js"></script>
        <script src="./../../js1/dashboard-alpha.js"></script>
        <script src="./../../lib/dom-to-image.js"></script>
        <script src="./../../lib/FileSaver.js"></script>
    </body>
    </html>