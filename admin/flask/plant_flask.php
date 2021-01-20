<?php
include_once("./func_plant_flask.php");
include_once("./../sys/func.php");

$status_mapping = array(0=>'<font color="red">關閉</font>', 1=>'<font color="blue">啟用</font>');
$DEVICE_SYSTEM = array(
		1=>"1.7",
		2=>"2.5",
		3=>"2.8",
		4=>"3.0",
		5=>"3.5",
		6=>"3.6",
		7=>"其他",
		8=>"瓶苗開瓶",
		9=>"出貨"
		// 1.7, 2.5, 2.8, 3.0, 3.5, 3.6 其他
);
$permissions_mapping = array(
	0=>'<font color="#666666">瓶苗</font>',
    1=>'<font color="#666666">1.7</font>',
    2=>'<font color="#666666">2.5</font>',
    3=>'<font color="#666666">2.8</font>',
    4=>'<font color="#666666">3.0</font>',
    5=>'<font color="#666666">3.5</font>',
    6=>'<font color="#666666">3.6</font>',
    7=>'<font color="#666666">其他</font>',
    8=>'<font color="#666666">瓶苗開瓶</font>' 
);

$permmsion = $_SESSION['user']['jsuser_admin_permit'];
$permmsion_option = $_SESSION['user']['jsuser_option'];

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
		$onadd_location=GetParam('onadd_location');//高度
		$onadd_pot_size=GetParam('onadd_pot_size');//適合開花盆徑
		$onadd_supplier=GetParam('onadd_supplier');//供應商
		$onadd_supplier_phone=GetParam('onadd_supplier_phone');//供應商電話
		$onadd_supplier_address=GetParam('onadd_supplier_address');//供應商地址
		$onadd_supplier_email=GetParam('onadd_supplier_email');//供應商郵件
		$onadd_planting_date=GetParam('onadd_planting_date');//下種日期
		$onadd_quantity_bottle=GetParam('onadd_quantity_bottle');//下種瓶數
		$onadd_quantity=GetParam('onadd_quantity');//下種數量
		$total = $onadd_quantity * $onadd_quantity_bottle;//總數
		$onadd_next_day=GetParam('onadd_next_day');//下階段(出貨/下種)天數
		// $onadd_growing=GetParam('onadd_growing');//預計成長大小
		$onadd_quantity_shi=GetParam('onadd_quantity_shi');//換盆年
		$onadd_buy_price=GetParam('onadd_buy_price');//採購價格
		if(empty($onadd_buy_price)){
			$onadd_buy_price = GetParam('onadd_foundry_price');
		}
		$onadd_sellsize=GetParam('onadd_sellsize');//預計出貨尺寸

		$onadd_buy_item_1="採購單價";//其他價格-項目
		$onadd_buy_price_1=GetParam('onadd_buy_price_1');//其他價格-金額
		$json['name']=$onadd_buy_item_1;
		$json['price']=$onadd_buy_price_1;
		$onadd_other_price = urldecode(json_encode($json,JSON_UNESCAPED_UNICODE));
		$onadd_type=GetParam('bill_mode');//;0:自種、1:代工

		$onsd_sn=GetParam('onsd_sn');
		if($onsd_sn == "0"){
			addSupplierData($onadd_supplier,$onadd_supplier_address,$onadd_supplier_phone,$onadd_supplier_email);
		}

		$onadd_quantity_cha=$test;//換盆月
		$onadd_status=GetParam('onadd_status');//狀態 1 啟用 0 刪除
		$onproduct_pic_url=GetParam('onproduct_pic_url');//產品圖片
		$IsUploadImg=IsNewProduct($onadd_part_no,$onadd_part_name);//產品是否已存在
		if(!empty($onproduct_pic_url))
			$onproduct_pic_url = ".".$onproduct_pic_url;
			$jsuser_sn = GetParam('supplier');//編輯人員

		if(empty($onadd_part_no)||empty($onadd_planting_date)||empty($onadd_quantity)||empty($onadd_sellsize)||empty($onadd_supplier)){
			$ret_msg = "*為必填！";
		} else { 
			if($IsUploadImg == "0"){
				$user = getPlantDataByAccount($onadd_part_no);
				$onadd_planting_date = str2time($onadd_planting_date);
				$now = time();
				$conn = getDB();
					$sql = "INSERT INTO onliine_add_data (onadd_add_date, onadd_mod_date, onadd_part_no, onadd_part_name, onadd_color, onadd_size, onadd_height, onadd_pot_size, onadd_supplier, onadd_planting_date, onadd_quantity, onadd_growing, onadd_status, jsuser_sn, onadd_cycle, onadd_isbought, onadd_plant_st, onadd_location, onadd_cur_size, onadd_buy_price, onadd_other_price, onadd_sellsize, onadd_quantity_bottle, onadd_supplier_phone, onadd_supplier_address, onadd_next_status, onadd_supplier_email, onadd_type) " .
					"VALUES ('{$now}', '{$now}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_planting_date}', '{$onadd_quantity}', '{$onadd_growing}', '1', '{$jsuser_sn}', '{$now}', '{$onadd_isbought}', '2', '{$onadd_location}', '8', '{$onadd_buy_price}','{$onadd_other_price}','{$onadd_sellsize}', '{$onadd_quantity_bottle}','{$onadd_supplier_phone}','{$onadd_supplier_address}','1', '{$onadd_supplier_email}', '{$onadd_type}');";

					$sql2 = "INSERT INTO onliine_product_data(onproduct_add_date, onproduct_date, onproduct_status, jsuser_sn, onproduct_part_no, onproduct_part_name, onproduct_color, onproduct_size, onproduct_height, onproduct_pot_size, onproduct_supplier, onproduct_growing, onproduct_isbought, onproduct_plant_st,onproduct_pic_url) " .
					"VALUES ('{$now}', '{$now}', '1', '{$jsuser_sn}' , '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_growing}', '{$onadd_isbought}', '2', '{$onproduct_pic_url}');";

					if($conn->query($sql)) {
						$onadd_id = mysqli_insert_id($conn);
						if(IsProductExit($onadd_part_no,$onadd_part_name)=="0"){
							$conn->query($sql2);
						}
						// if($conn->query($sql2)){
							$sql3 = "INSERT INTO `onliine_firstplant_data`(`onfp_add_date`, `onfp_plant_date`, `jsuser_sn`, `onfp_plant_amount`,`onfp_part_no`,onadd_sn) VALUES ('{$now}', '{$onadd_planting_date}','{$jsuser_sn}','{$total}','{$onadd_part_no}','{$onadd_id}');";
							if($conn->query($sql3)){
								$ret_msg = "新增成功！";
							}
							else{
								$ret_msg = "新增失敗！";
							}
						// }
						// else
						// 	$ret_msg = "新增失敗！";
					} else {
						$ret_msg = "新增失敗！";
					}
				$conn->close();
			}
			else{
				$user = getPlantDataByAccount($onadd_part_no);
				$onadd_planting_date = str2time($onadd_planting_date);
				$now = time();
				$conn = getDB();
					$sql = "INSERT INTO onliine_add_data (onadd_add_date, onadd_mod_date, onadd_part_no, onadd_part_name, onadd_color, onadd_size, onadd_height, onadd_pot_size, onadd_supplier, onadd_planting_date, onadd_quantity, onadd_growing, onadd_status, jsuser_sn, onadd_cycle, onadd_isbought, onadd_plant_st, onadd_location, onadd_cur_size,onadd_quantity_bottle, onadd_other_price, onadd_supplier_phone, onadd_supplier_address, onadd_buy_price, onadd_sellsize,onadd_next_status, onadd_supplier_email, onadd_type) " .
					"VALUES ('{$now}', '{$now}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_planting_date}', '{$onadd_quantity}', '{$onadd_growing}', '1', '{$jsuser_sn}', '{$now}', '{$onadd_isbought}', '2', '{$onadd_location}', '8','{$onadd_quantity_bottle}', '{$onadd_other_price}', '{$onadd_supplier_phone}', '{$onadd_supplier_address}', '{$onadd_buy_price}', '{$onadd_sellsize}','1', '{$onadd_supplier_email}', '{$onadd_type}');";

					$sql2 = "UPDATE onliine_product_data SET onproduct_part_no = '{$onadd_part_no}', onproduct_part_name = '{$onadd_part_name}', onproduct_color = '{$onadd_color}', onproduct_size = '{$onadd_size}', onproduct_height = '{$onadd_height}', onproduct_pot_size = '{$onadd_pot_size}', onproduct_supplier = '{$onadd_supplier}', onproduct_growing = '{$onadd_growing}', onproduct_isbought = '{$onadd_isbought}',onproduct_pic_url = '{$onproduct_pic_url}' 
					    WHERE onproduct_part_no like '{$onadd_part_no}' and onproduct_part_name like '{$onadd_part_name}';";

					if($conn->query($sql)) {
						$onadd_id = mysqli_insert_id($conn);						
							$conn->query($sql2);
							$sql3 = "INSERT INTO `onliine_firstplant_data`(`onfp_add_date`, `onfp_plant_date`, `jsuser_sn`, `onfp_plant_amount`,`onfp_part_no`,onadd_sn) VALUES ('{$now}', '{$onadd_planting_date}','{$jsuser_sn}','{$total}','{$onadd_part_no}','{$onadd_id}');";
							if($conn->query($sql3)){
								$ret_msg = "新增成功！";
							}
							else{
								$ret_msg = "新增失敗！";
							}
					} else {
						$ret_msg = "新增失敗！";
					}
				$conn->close();
			}
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
		$onadd_supplier=GetParam('onproduct_supplier');//供應商地址
		$onadd_planting_date=GetParam('onproduct_planting_date');//下種日期
		$onadd_quantity=GetParam('onproduct_quantity');//下種數量
		$onadd_growing=GetParam('onproduct_growing');//預計成長大小
		$onadd_status=GetParam('onadd_status');//狀態 1 啟用 0 刪除
		$jsuser_sn = GetParam('supplier');//編輯人員

		if(empty($onadd_part_no)||empty($onadd_part_name)||empty($onadd_planting_date)||empty($onadd_quantity)||empty($onadd_growing)){
			$ret_msg = "*為必填！";
		} else { 
			$user = getPlantDataByAccount($onadd_part_no);
			$onadd_planting_date = str2time($onadd_planting_date);
			$now = time();
			$conn = getDB();
				$sql = "INSERT INTO onliine_add_data (onadd_add_date, onadd_mod_date, onadd_part_no, onadd_part_name, onadd_color, onadd_size, onadd_height, onadd_pot_size, onadd_supplier, onadd_planting_date, onadd_quantity, onadd_growing, onadd_status, jsuser_sn, onadd_cycle, onadd_plant_st) " .
				"VALUES ('{$now}', '{$now}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_planting_date}', '{$onadd_quantity}', '{$onadd_growing}', '1', '{$jsuser_sn}', '{$now}', '2');";
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

		case 'adjust':
		$onadd_sn=GetParam('onadd_sn');
		$onadd_isbought=GetParam('onadd_isbought');//苗種來源
		$onadd_part_no=GetParam('onadd_part_no');//品號
		$onadd_part_name=GetParam('onadd_part_name');//品名
		$onadd_color=GetParam('onadd_color');//花色
		$onadd_size=GetParam('onadd_size');//花徑
		$onadd_height=GetParam('onadd_height');//高度
		$onadd_location=GetParam('onadd_location');//高度
		$onadd_pot_size=GetParam('onadd_pot_size');//適合開花盆徑
		$onadd_supplier=GetParam('onadd_supplier');//供應商
		$onadd_supplier_phone=GetParam('onadd_supplier_phone');//供應商電話
		$onadd_supplier_address=GetParam('onadd_supplier_address');//供應商地址
		$onadd_supplier_email=GetParam('onadd_supplier_email');//供應商郵件
		$onadd_planting_date=GetParam('onadd_planting_date');//下種日期
		$onadd_quantity_bottle=GetParam('onadd_quantity_bottle');//下種瓶數
		$onadd_quantity=GetParam('onadd_quantity');//下種數量
		$total = $onadd_quantity * $onadd_quantity_bottle;//總數
		$onadd_next_day=GetParam('onadd_next_day');//下階段(出貨/下種)天數
		// $onadd_growing=GetParam('onadd_growing');//預計成長大小
		$onadd_quantity_shi=GetParam('onadd_quantity_shi');//換盆年
		$onadd_buy_price=GetParam('onadd_buy_price');//採購價格
		if(empty($onadd_buy_price)){
			$onadd_buy_price = GetParam('onadd_foundry_price');
		}
		$onadd_sellsize=GetParam('onadd_sellsize');//預計出貨尺寸

		$onadd_buy_item_1="採購單價";//其他價格-項目
		$onadd_buy_price_1=GetParam('onadd_buy_price_1');//其他價格-金額
		$json['name']=$onadd_buy_item_1;
		$json['price']=$onadd_buy_price_1;
		$onadd_other_price = urldecode(json_encode($json,JSON_UNESCAPED_UNICODE));
		$onadd_type=GetParam('bill_mode');//;0:自種、1:代工

		$onsd_sn=GetParam('onsd_sn');
		if($onsd_sn == "0"){
			addSupplierData($onadd_supplier,$onadd_supplier_address,$onadd_supplier_phone,$onadd_supplier_email);
		}

		$onadd_quantity_cha=$test;//換盆月
		$onadd_status=GetParam('onadd_status');//狀態 1 啟用 0 刪除
		$onproduct_pic_url=GetParam('onproduct_pic_url');//產品圖片
		// $IsUploadImg=IsNewProduct($onadd_part_no,$onadd_part_name);//產品是否已存在
		// if(!empty($onproduct_pic_url))
		// 	$onproduct_pic_url = ".".$onproduct_pic_url;
		$jsuser_sn = GetParam('supplier');//編輯人員

		if(empty($onadd_part_no)||empty($onadd_planting_date)||empty($onadd_quantity)||empty($onadd_sellsize)||empty($onadd_supplier)){
			$ret_msg = "*為必填！";
		} else { 
			if($IsUploadImg == "0"){
				$user = getPlantDataByAccount($onadd_part_no);
				$onadd_planting_date = str2time($onadd_planting_date);
				$now = time();
				$conn = getDB();
				$sql = "UPDATE onliine_add_data SET onadd_color='{$onadd_color}', onadd_size='{$onadd_size}', onadd_height='{$onadd_height}', onadd_pot_size='{$onadd_pot_size}', onadd_supplier='{$onadd_supplier}', onadd_planting_date='{$onadd_planting_date}', onadd_quantity='{$onadd_quantity}', jsuser_sn='{$jsuser_sn}', onadd_isbought='{$onadd_isbought}', onadd_plant_st='2', onadd_location='{$onadd_location}', onadd_buy_price='{$onadd_buy_price}', onadd_other_price='{$onadd_other_price}', onadd_sellsize='{$onadd_sellsize}', onadd_quantity_bottle='{$onadd_quantity_bottle}', onadd_supplier_phone='{$onadd_supplier_phone}', onadd_supplier_address='{$onadd_supplier_address}', onadd_supplier_email='{$onadd_supplier_email}', onadd_type='{$onadd_type}' WHERE onadd_sn = '{$onadd_sn}'";

				if($conn->query($sql)) {						
					$ret_msg = "修改成功！";
				} else {
					$ret_msg = "修改失敗！";
				}
				$conn->close();
			}
			else{
				$user = getPlantDataByAccount($onadd_part_no);
				$onadd_planting_date = str2time($onadd_planting_date);
				$now = time();
				$conn = getDB();
				$sql = "UPDATE onliine_add_data SET onadd_color='{$onadd_color}', onadd_size='{$onadd_size}', onadd_height='{$onadd_height}', onadd_pot_size='{$onadd_pot_size}', onadd_supplier='{$onadd_supplier}', onadd_planting_date='{$onadd_planting_date}', onadd_quantity='{$onadd_quantity}', jsuser_sn='{$jsuser_sn}', onadd_isbought='{$onadd_isbought}', onadd_plant_st='2', onadd_location='{$onadd_location}', onadd_buy_price='{$onadd_buy_price}', onadd_other_price='{$onadd_other_price}', onadd_sellsize='{$onadd_sellsize}', onadd_quantity_bottle='{$onadd_quantity_bottle}', onadd_supplier_phone='{$onadd_supplier_phone}', onadd_supplier_address='{$onadd_supplier_address}', onadd_supplier_email='{$onadd_supplier_email}', onadd_type='{$onadd_type}' WHERE onadd_sn = '{$onadd_sn}'";

				$sql2 = "UPDATE onliine_product_data SET onproduct_part_no = '{$onadd_part_no}', onproduct_part_name = '{$onadd_part_name}', onproduct_color = '{$onadd_color}', onproduct_size = '{$onadd_size}', onproduct_height = '{$onadd_height}', onproduct_pot_size = '{$onadd_pot_size}', onproduct_supplier = '{$onadd_supplier}', onproduct_isbought = '{$onadd_isbought}',onproduct_pic_url = '{$onproduct_pic_url}'  WHERE onproduct_part_no like '{$onadd_part_no}' and onproduct_part_name like '{$onadd_part_name}';";

				if($conn->query($sql)) {
					$conn->query($sql2);
					$ret_msg = "修改成功！";
				} else {
					$ret_msg = "修改失敗！";
				}
				$conn->close();
			}
		}
		break;

		case 'get':
		$onadd_sn=GetParam('onadd_sn');
		setcookie("onadd_sn", $onadd_sn);
		setcookie("qr_sn", GetParam('qr_sn'));
		setcookie("plant_sn", GetParam('plant_sn'));
		$ret_data = array();
		if(!empty($onadd_sn)){
			$ret_code = 1;
			$ret_data = getPlantDataBysn($onadd_sn);
		} else {
			$ret_code = 0;
		}

		break;

		case 'upd':
		$onadd_sn=GetParam('onadd_sn');
		$onadd_newpot_sn=GetParam('onadd_newpot_sn');
		$sn = "0";

		if($onadd_newpot_sn == "0"){
			$sn = $onadd_sn;
		}
		else{
			$sn = $onadd_newpot_sn;
		}

		$onadd_add_date=GetParam('onadd_add_date');//建立日期
		$onadd_mod_date=GetParam('onadd_mod_date');//修改日期
		$onadd_part_no=GetParam('onadd_part_no');//品號
		$onadd_part_name=GetParam('onadd_part_name');//品名
		$onadd_color=GetParam('onadd_color');//花色
		$onadd_size=GetParam('onadd_size');//花徑
		$onadd_height=GetParam('onadd_height');//高度
		$onadd_pot_size=GetParam('onadd_pot_size');//適合開花盆徑
		$onadd_supplier=GetParam('onadd_supplier');//供應商
		$onadd_supplier_phone=GetParam('onadd_supplier_phone');//供應商電話
		$onadd_supplier_address=GetParam('onadd_supplier_address');//供應商地址
		$onadd_supplier_email=GetParam('onadd_supplier_email');//供應商郵件
		$onadd_location=GetParam('onadd_location');//放置區
		$onadd_planting_date=GetParam('onadd_planting_date');//下種日期
		$onadd_quantity=GetParam('onadd_quantity');//入庫棵數
		$onadd_quantity_bottle=GetParam('onadd_quantity_bottle');//入庫瓶數
		$onadd_left_quantity=GetParam('onadd_left_quantity');//剩餘棵數
		$onadd_cur_size=GetParam('onadd_cur_size');//目前尺寸
		$onadd_sellsize=GetParam('onadd_sellsize');//預計出貨尺寸
		$onadd_type=GetParam('bill_mode');//;0:自種、1:代工
		$onadd_isbought=GetParam('onadd_isbought');//苗株 催花

		if(GetParam('onadd_plant_day_A') != "")
			$onadd_replant_number_A=GetParam('onadd_plant_day_A');//A 下種數量
		else
			$onadd_replant_number_A=0;
		if(GetParam('onadd_plant_day_B') != "")
			$onadd_replant_number_B=GetParam('onadd_plant_day_B');//B 下種數量
		else
			$onadd_replant_number_B=0;

		if(GetParam('onadd_buy_price_A') != "")
			$onadd_buy_price_A=GetParam('onadd_buy_price_A');//A 代工價格
		else
			$onadd_buy_price_A=0;
		if(GetParam('onadd_buy_price_B') != "")
			$onadd_buy_price_B=GetParam('onadd_buy_price_B');//B 代工價格
		else
			$onadd_buy_price_B=0;

		$total = $onadd_replant_number_A + $onadd_replant_number_B;//AB苗總數
		$all_plant_number = Array();
		$all_plant_number[0][0] = $onadd_replant_number_A;
		$all_plant_number[1][0] = $onadd_replant_number_B;
		$all_plant_number[0][1] = $onadd_buy_price_A;
		$all_plant_number[1][1] = $onadd_buy_price_B;
		

		$onadd_next_status=GetParam('onadd_next_status');
		$onadd_plant_staff=GetParam('onadd_plant_staff');//種植人員
		$onadd_price_per_plant=GetParam('onadd_price_per_plant');//種植費用


		$onadd_buy_item_1="採購單價";//其他價格-項目
		$onadd_buy_price_1=GetParam('onadd_buy_price_1');//其他價格-金額
		$json['name']=$onadd_buy_item_1;
		$json['price']=$onadd_buy_price_1;
		$onadd_other_price = urldecode(json_encode($json,JSON_UNESCAPED_UNICODE));

		$onproduct_pic_url = GetParam('onproduct_pic_url');

		$onadd_quantity_cha123 =(($onadd_quantity*$onadd_quantity_bottle) - $total + $onadd_left_quantity);
		$left = (($onadd_quantity*$onadd_quantity_bottle) - $total + $onadd_left_quantity);
		if($onadd_quantity_cha123 <= 0){
			$onadd_status = -2;
			$onadd_quantity_cha123 = 0;
			$onadd_quantity_bottle = 0;
		}
		else{
			$onadd_status = 1;
			$onadd_quantity_bottle = ceil($onadd_quantity_cha123 / $onadd_quantity);
			$onadd_left_quantity = $onadd_quantity_cha123 % $onadd_quantity;
			if($onadd_left_quantity != 0)
				$onadd_quantity_bottle = $onadd_quantity_bottle - 1;
			$onadd_quantity_cha123 = $onadd_quantity;
		}
		

		$first_n_changed = getProductFirstQty($onadd_sn) - $total;

		$onadd_growing=GetParam('onadd_growing');//預計成長大小
		// $onadd_status=GetParam('onadd_status');//狀態 1 啟用 0 刪除
		$jsuser_sn = GetParam('supplier');//編輯人員
		// $isKept = GetParam('isKept');//是否汰除剩餘數量
		$onadd_id = "0";
		if(empty($onadd_planting_date)||empty($onadd_quantity)||empty($onadd_replant_number_A)){
			$ret_msg = "*為必填！";
		} 
		else { 
			$user = getPlantDataByAccount($onadd_part_no);
			$onadd_planting_date = str2time($onadd_planting_date);
			$now = time();
			$conn = getDB();
			if($onadd_status != -1) {
				foreach ($all_plant_number as $key => $plant_number) {
					$onadd_quantity = $plant_number[0];
					$onadd_buy_price = $plant_number[1];
					if($onadd_quantity != "0"){
						if($onadd_id == "0"){
							$sql = "INSERT INTO onliine_add_data (onadd_add_date, onadd_mod_date, onadd_part_no, onadd_part_name, onadd_color, onadd_size, onadd_height, onadd_pot_size, onadd_supplier, onadd_planting_date, onadd_quantity,onadd_quantity_cha, onadd_growing, onadd_status, jsuser_sn, onadd_cycle, onadd_plant_st, onadd_cur_size, onadd_location, onadd_sellsize, onadd_other_price, onadd_next_status, onadd_plant_staff, onadd_price_per_plant, onadd_supplier_address, onadd_supplier_email, onadd_supplier_phone, onadd_level, onadd_buy_price, onadd_type, onadd_isbought) " .
						
						"VALUES ('{$now}', '{$now}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_planting_date}', '{$onadd_quantity}','', '{$onadd_growing}', '1', '{$jsuser_sn}', '{$now}', 1, {$onadd_cur_size}, '{$onadd_location}', '{$onadd_sellsize}', '{$onadd_other_price}','1', '{$onadd_plant_staff}', '{$onadd_price_per_plant}', '{$onadd_supplier_address}', '{$onadd_supplier_email}', '{$onadd_supplier_phone}', '{$key}', '{$onadd_buy_price}', '{$onadd_type}', '{$onadd_isbought}');";
						}
						else{							
							$sql = "INSERT INTO onliine_add_data (onadd_add_date, onadd_mod_date, onadd_part_no, onadd_part_name, onadd_color, onadd_size, onadd_height, onadd_pot_size, onadd_supplier, onadd_planting_date, onadd_quantity,onadd_quantity_cha, onadd_growing, onadd_status, jsuser_sn, onadd_cycle, onadd_plant_st, onadd_cur_size, onadd_location, onadd_sellsize, onadd_other_price, onadd_next_status, onadd_plant_staff, onadd_price_per_plant, onadd_supplier_address, onadd_supplier_email, onadd_supplier_phone, onadd_level, onadd_AB_sn, onadd_buy_price, onadd_type, onadd_isbought) " .
						
						"VALUES ('{$now}', '{$now}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_planting_date}', '{$onadd_quantity}','', '{$onadd_growing}', '1', '{$jsuser_sn}', '{$now}', 1, {$onadd_cur_size}, '{$onadd_location}', '{$onadd_sellsize}', '{$onadd_other_price}','1', '{$onadd_plant_staff}', '{$onadd_price_per_plant}', '{$onadd_supplier_address}', '{$onadd_supplier_email}', '{$onadd_supplier_phone}', '{$key}', '{$onadd_id}','{$onadd_buy_price}', '{$onadd_type}', '{$onadd_isbought}');";
						}

						if($conn->query($sql)){
							// if(IsProductExit($onadd_part_no,$onadd_part_name,2) == "1"){
							// 	$product_data = getProductByPartNo($onadd_part_no);
								
								// $sql_product = "UPDATE `onliine_product_data` SET onproduct_pic_url = '{$onproduct_pic_url}' WHERE onproduct_part_no = '{$onadd_part_no}' AND onproduct_part_name = '{$onadd_part_name}'";
								// $conn->query($sql_product);
							// }
							$onadd_id = mysqli_insert_id($conn);
							//更新原本產品數量 (扣除換盆)
							$sql1 = "UPDATE onliine_add_data SET onadd_quantity='{$onadd_quantity_cha123}', onadd_quantity_bottle='{$onadd_quantity_bottle}', onadd_status='{$onadd_status}', onadd_left_quantity = '{$onadd_left_quantity}' WHERE onadd_sn='{$onadd_sn}'";
							//更新原本產品的第一筆下種數量(扣除換盆)
							if($conn->query($sql1)){
								$sql3 = "INSERT INTO `onliine_firstplant_data`(`onfp_add_date`, `onfp_plant_date`, `jsuser_sn`, `onfp_plant_amount`,`onfp_part_no`,onadd_sn) VALUES ('{$now}', '{$onadd_planting_date}','{$jsuser_sn}','{$onadd_quantity}','{$onadd_part_no}','{$onadd_id}');";
								if($conn->query($sql3))
									$ret_msg = "下種成功！";					
								else
									$ret_msg = "新增失敗！";
							}
							else{
								$ret_msg = "下種失敗！";
							}
						}
					}
				}
			}
			else {	
				$ret_msg = "下種失敗！";
			}
			$conn->close();
		}
		break;

		//汰除---------------------------------------------
		case 'upd1':
		$onadd_sn=GetParam('onadd_sn');
		$onadd_newpot_sn=GetParam('onadd_newpot_sn');
		if($onadd_newpot_sn == "0"){
			$list = getPlantDataBysn($onadd_sn);
		}
		else{
			$list = getPlantDataBysn($onadd_newpot_sn);
		}
		$onadd_part_no = $list['onadd_part_no'];
		$onadd_part_name = $list['onadd_part_name'];
		if(empty($onadd_part_no))
			$onadd_part_no = GetParam('onadd_part_no');
		if(empty($onadd_part_name))
			$onadd_part_name = GetParam('onadd_part_name');

		$onadd_quantity=GetParam('onadd_quantity');//下種數量
		$onadd_quantity_perbottle=GetParam('onadd_quantity_perbottle');//每坪數量
		$jsuser_sn = GetParam('supplier');//編輯人員
		$onadd_quantity_del=GetParam('onadd_quantity_del');//汰除數量
		$onelda_reason=GetParam('onelda_reason');//汰除原因

		$onadd_quantity_cha123 = $onadd_quantity - $onadd_quantity_del;

		if($onadd_quantity_cha123 < 0){
			$onadd_status = -1;
		}
		else if($onadd_quantity_cha123 == 0){
			$onadd_status = -2;
			$onadd_quantity_cha123 = 0;
			$onadd_quantity_bottle = 0;
		}
		else{
			$onadd_status = 1;
			$onadd_quantity_bottle = ceil($onadd_quantity_cha123 / $onadd_quantity_perbottle);
			$onadd_left_quantity = $onadd_quantity_cha123 % $onadd_quantity_perbottle;

			if($onadd_left_quantity > 0)
				$onadd_quantity_bottle -= 1;
		}

		if(empty($onadd_quantity_del)){
			$ret_msg = "*為必填！";
		} 
		else if($onadd_status != -1){
			$now = time();
			$conn = getDB();
			$sql1 = "UPDATE onliine_add_data SET onadd_quantity='{$onadd_quantity_perbottle}',onadd_quantity_bottle='{$onadd_quantity_bottle}',onadd_left_quantity='{$onadd_left_quantity}', onadd_status='{$onadd_status}' WHERE onadd_sn='{$onadd_sn}'";

			$sql = "INSERT INTO online_elimination_data (onelda_add_date, onelda_mod_date, onelda_quantity, onelda_reason, onadd_sn, onadd_part_no, onadd_part_name) " .
				"VALUES ('{$now}', '{$now}', '{$onadd_quantity_del}', '{$onelda_reason}', '{$onadd_newpot_sn}', '{$onadd_part_no}', '{$onadd_part_name}');";
			$ret_msg = $sql1;
			if($conn->query($sql1) && $conn->query($sql)) {
				$ret_msg = "汰除完成！";
			} else {
				$ret_msg = "汰除失敗！";
			}
		}
		else if($onadd_status == -2){
			$ret_msg = "錯誤！ 汰除數量不可大於下種數量！";
		}

		break;
		//汰除---------------------------------------------

		//出貨---------------------------------------------
		case 'upd2':
		$onadd_sn=GetParam('onadd_sn');
		$list = getPlantDataBysn($onadd_sn);
		$onadd_part_no = $list['onadd_part_no'];
		$onadd_part_name = $list['onadd_part_name'];
		$onadd_quantity=GetParam('onadd_quantity');//下種數量
		$jsuser_sn = GetParam('supplier');//編輯人員
		$onadd_plant_year=GetParam('onadd_plant_year');//汰除數量
		$onshda_client=GetParam('onshda_client');//汰除數量
		$onshda_price =GetParam('onshda_price');//售出價格(單棵)
		$onadd_quantity_shi123 = ($onadd_quantity - $onadd_plant_year);
		if($onadd_quantity_shi123<=0) {
			$onadd_status = -1;
		} else {
			$onadd_status = 1;
		}

		$onadd_newpot_sn = GetParam('onadd_newpot_sn');
		if($onadd_newpot_sn == '0'){
			if(GetParam('onadd_ml') == '0')
				$onadd_ml=GetParam('onadd_sn');//移倉原始編號
			else
				$onadd_ml=GetParam('onadd_ml');//移倉原始編號
		}else{
			if(GetParam('onadd_ml') == '0')
				$onadd_ml=$onadd_newpot_sn;//移倉原始編號
			else
				$onadd_ml=GetParam('onadd_ml');//移倉原始編號
		}

		if(empty($onadd_plant_year) || empty($onshda_price)){
			$ret_msg = "*為必填！";
		} else {
			$now = time();
			$conn = getDB();
			$sql = "UPDATE onliine_add_data SET onadd_quantity='{$onadd_quantity_shi123}', onadd_status='{$onadd_status}' WHERE onadd_sn='{$onadd_sn}'";
			if($conn->query($sql)) {
				$ret_msg = "出貨完成！";
			} else {
				$ret_msg = "出貨失敗！";
			}
			$conn->close();
		}

		if(empty($onadd_plant_year)){
			$ret_msg = "*為必填！";
		} else {
			$now = time();
			$conn = getDB();
			$sql = "INSERT INTO online_shipment_data (onshda_add_date, onshda_mod_date, onshda_client, onshda_quantity, onadd_sn, onadd_part_no, onadd_part_name, onshda_price) " .
				"VALUES ('{$now}', '{$now}', '{$onshda_client}', '{$onadd_plant_year}', '{$onadd_ml}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onshda_price}');";
			if($conn->query($sql)) {
				$ret_msg = "出貨成功！";
			} else {
				$ret_msg = "出貨失敗！";
			}			
			$conn->close();
		} 
		break;
		//出貨---------------------------------------------

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
			$user = getPlantDataByAccount($onproduct_part_no);
			$onproduct_planting_date = str2time($onproduct_planting_date);
			$now = time();
			$conn = getDB();
				$sql = "UPDATE onliine_product_data SET onproduct_part_no = '$onproduct_part_no', onproduct_part_name = '$onproduct_part_name', onproduct_color ='$onproduct_color', onproduct_size = '$onproduct_size', onproduct_height = '$onproduct_height', onproduct_pot_size = '$onproduct_pot_size', onproduct_supplier = '$onproduct_supplier',onproduct_growing ='$onproduct_growing', jsuser_sn = '$jsuser_sn', onproduct_isbought = '$onproduct_isbought' WHERE onproduct_sn = $onproduct_sn;";

				if($conn->query($sql)) {
					$ret_msg = "更新成功！";

				} else {
					$ret_msg = "更新失敗！";
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
		$onadd_location=GetParam('onadd_location');//移倉放置區
		$onadd_location_old=GetParam('onadd_location_old');//原始放置區
		$onadd_newpot_sn = GetParam('onadd_newpot_sn');//換盆原始編號
		if($onadd_newpot_sn == '0'){
			if(GetParam('onadd_ml') == '0')
				$onadd_ml=GetParam('onadd_sn');//移倉原始編號
			else
				$onadd_ml=GetParam('onadd_ml');//移倉原始編號
		}else{
			if(GetParam('onadd_ml') == '0')
				$onadd_ml=$onadd_newpot_sn;//移倉原始編號
			else
				$onadd_ml=GetParam('onadd_ml');//移倉原始編號
		}

		$onadd_ml_amount=GetParam('onadd_ml_amount');//移倉數量
		$onadd_supplier=GetParam('onadd_supplier');//供應商
		$onadd_planting_date=GetParam('onadd_planting_date');//下種日期
		$onadd_quantity=GetParam('onadd_quantity');//下種數量
		$onadd_cur_size=GetParam('onadd_cur_size');//目前尺寸
		$onadd_growing=GetParam('onadd_growing');//預計成長大小
		$onadd_sellsize=GetParam('onadd_sellsize');//預計出貨尺寸
		$jsuser_sn = GetParam('supplier');//編輯人員
		$left_amount = $onadd_quantity - $onadd_ml_amount;//移倉後剩餘數量

		$user = getPlantDataByAccount($onadd_part_no);
		$onadd_planting_date = str2time($onadd_planting_date);
		$now = time();
		$conn = getDB();
		if(empty($onadd_location) || empty($onadd_ml_amount)){
			$ret_msg = "*為必填！";
		}
		else if($left_amount < 0){
			$ret_msg = "移倉數量大於庫存數量！";
		} 
		else {

			$sql = "UPDATE onliine_add_data	SET onadd_part_no ='{$onadd_part_no}',onadd_part_name='{$onadd_part_name}',onadd_color='{$onadd_color}'	,onadd_size='{$onadd_size}',onadd_height='{$onadd_height}',onadd_pot_size='{$onadd_pot_size}',onadd_supplier='{$onadd_supplier}'	,onadd_planting_date='{$onadd_planting_date}',onadd_quantity='{$left_amount}',onadd_growing='{$onadd_growing}',jsuser_sn='{$supplier}', onadd_location='{$onadd_location_old}', onadd_cur_size='{$onadd_cur_size}', onadd_plant_st='2' WHERE onadd_sn='{$onadd_sn}';";
			
			$sql2 = "INSERT INTO onliine_add_data(onadd_part_no,onadd_part_name,onadd_color,onadd_size,onadd_height,onadd_pot_size,onadd_supplier,onadd_planting_date,onadd_quantity,onadd_growing,jsuser_sn,onadd_location,onadd_cur_size,onadd_ml,onadd_add_date,onadd_mod_date,onadd_cycle, onadd_newpot_sn, onadd_sellsize, onadd_plant_st)	
					VALUES('{$onadd_part_no}','{$onadd_part_name}','{$onadd_color}','{$onadd_size}','{$onadd_height}','{$onadd_pot_size}','{$onadd_supplier}','{$onadd_planting_date}','{$onadd_ml_amount}','{$onadd_growing}','{$supplier}','{$onadd_location}','{$onadd_cur_size}','{$onadd_ml}', '{$now}', '{$now}', '{$now}' ,'{$onadd_newpot_sn}', '{$onadd_sellsize}', '2')";

			$sql3 = "UPDATE onliine_add_data SET onadd_part_no ='{$onadd_part_no}',onadd_part_name='{$onadd_part_name}',onadd_color='{$onadd_color}'	,onadd_size='{$onadd_size}',onadd_height='{$onadd_height}',onadd_pot_size='{$onadd_pot_size}',onadd_supplier='{$onadd_supplier}'	,onadd_planting_date='{$onadd_planting_date}',onadd_quantity='{$left_amount}',onadd_growing='{$onadd_growing}',jsuser_sn='{$supplier}', onadd_location='{$onadd_location}', onadd_cur_size='{$onadd_cur_size}', onadd_status = '-1' WHERE onadd_sn='{$onadd_sn}' and onadd_status = '1';";		
			// $sql2 = "UPDATE onliine_firstplant_data	SET onfp_plant_amount = '{$onadd_quantity}' WHERE onadd_sn='{$onadd_sn}' and onfp_status >= 1;";			
	
			if($conn->query($sql)) {
				$ret_msg = "移倉成功！";
				if($left_amount == 0){
					$conn->query($sql3);
				} 
				if($conn->query($sql2)) {
					$ret_msg = "移倉成功！";
				}
				else{
					$ret_msg = "移倉失敗！";
				}
				// if(IsfirtPlant($onadd_sn) == "1"){
				// 	if($conn->query($sql2)) {
				// 		$ret_msg = "更新成功！";
				// 	}
				// 	else{
				// 		$ret_msg = "更新失敗！";
				// 	}
				// }
			} else {
				$ret_msg = "更新失敗！";
			}
		}
		$conn->close();
		
		break;

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

		case 'download':
		$onadd_sn=GetParam('onadd_sn');
		$ret_data = array();
		if(!empty($onadd_sn)){
			$ret_code = 1;
			$ret_data = qr_download($onadd_sn);
		} else {
			$ret_code = 0;
		}

		//產品履歷---------------------------------------------
		case 'get_history_list':
		$onadd_sn = GetParam('onadd_sn');

		if(empty($onadd_sn)){
			$ret_msg = "查詢失敗！";
		} else {
			$ret_data = getHistory_List($onadd_sn);
		}
		break;

		case 'get_all_supplier':
			$ret_code = 1;
			$ret_data = getAllSupplier();
			break;
		break;

		case 'getSupplierByName':
			$onadd_supplier=GetParam('onadd_supplier');
			$ret_code = 1;
			$ret_data = getSupplierByName($onadd_supplier);
			break;
		break;

		case 'get_all_plant_staff':
			$ret_code = 1;
			$ret_data = getAllPantStaff();
			break;
		break;

		//產品履歷---------------------------------------------

		default:
		$ret_msg = 'error!';
		break;
	}

	echo enclode_ret_data($ret_code, $ret_msg, $ret_data);
	exit;
} else {
	// search
	if(($onadd_sn = GetParam('onadd_sn'))) {
		// 檢查搜尋條件是否有包含大小寫的 P、p
		$ex_P = explode("P", $onadd_sn);
		$ex_p = explode("p", $onadd_sn);	
		if (count($ex_P) == 2  and $ex_P[0] == "") {
			if (isset($ex_P[1])) {
				$ex_P[1] = explode("-", $ex_P[1]);	
				$search_where[] = "onadd_isbought = 1 and FROM_UNIXTIME(onadd_planting_date,'%Y') like '%{$ex_P[1][0]}%'";
			}else{
				$search_where[] = "onadd_isbought = 1";
			}			
		}elseif(count($ex_p) == 2 and $ex_p[0] == ""){
			if (isset($ex_p[1])) {
				$ex_p[1] = explode("-", $ex_p[1]);	
				$search_where[] = "onadd_isbought = 1 and FROM_UNIXTIME(onadd_planting_date,'%Y') like '%{$ex_p[1][0]}%'";
			}else{
				$search_where[] = "onadd_isbought = 1";
			}	
		}elseif(count($ex_P) == 1 and $ex_P[0] !=""){
			if (strpos($ex_P[0],'P')) {
				$search_where[] = "onadd_isbought = 1";
			}elseif (strpos($ex_P[0],'-')) {
				$ex_ = explode("-", $ex_P[0]);
				$search_where[] = "onadd_sn IN (select onadd_sn from onliine_add_data where onadd_newpot_sn like '%{$onadd_sn}%' or onadd_sn like '%{$onadd_sn}%') or FROM_UNIXTIME(onadd_planting_date,'%Y') like '%$ex_[0]%'";
			}elseif($ex_P[0] == '-'){
				$search_where[] = "";
			}else{
				$search_where[] = "onadd_sn IN (select onadd_sn from onliine_add_data where onadd_newpot_sn like '%{$onadd_sn}%' or onadd_sn like '%{$onadd_sn}%') or FROM_UNIXTIME(onadd_planting_date,'%Y') like '%$ex_P[0]%'";
			}
		}elseif(count($ex_p) == 1 and $ex_p[0] !=""){
			if (strpos($ex_p[0],'p')) {
				$search_where[] = "onadd_isbought = 1";
			}elseif (strpos($ex_p[0],'-')) {
				$ex_ = explode("-", $ex_p[0]);
				$search_where[] = "onadd_sn IN (select onadd_sn from onliine_add_data where onadd_newpot_sn like '%{$onadd_sn}%' or onadd_sn like '%{$onadd_sn}%') or FROM_UNIXTIME(onadd_planting_date,'%Y') like '%$ex_[0]%'";
			}elseif($ex_P[0] == '-'){
				$search_where[] = "";
			}
			else{
				$search_where[] = "onadd_sn IN (select onadd_sn from onliine_add_data where onadd_newpot_sn like '%{$onadd_sn}%' or onadd_sn like '%{$onadd_sn}%') or FROM_UNIXTIME(onadd_planting_date,'%Y') like '%$ex_p[0]%'";
			}
		}
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
	$pg_total = GetParam('pg_total')=='' ? getPlantDataQty($search_where) : GetParam('pg_total');
	$pg_offset = $pg_rows * ($pg_page - 1);
	$pg_pages = $pg_rows == 0 ? 0 : ( (int)(($pg_total + ($pg_rows - 1)) /$pg_rows) );

	$product_list = getPlantData($search_where, $pg_offset, $pg_rows);
	$supplier_list = getAllSupplierData();
	// echo "<hr><hr><hr><hr><hr>";printr($ex_P);printr($ex_p);printr($ex_);printr($search_where);

	// printr(getPlantDataBysn(67));
	// exit;

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
	<script src="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>
    <script src="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.zh-TW.js" charset="UTF-8"></script>
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
	  	background: #f5f5f5;
	  	padding: 10px;
	  	font-size: 1.5rem;
	}
	.form-control {
		background: #f5f5f5;
		border-radius:0.3rem;
	}
	input[type=text] {
		background: #f5f5f5;
		border-radius:0.3rem;
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
		var all_part_no = null;
		var all_part_name = null;
		var all_supplier = null;
		var all_plant_staff = null;
		var plant_price = <?php echo json_encode($plant_price);?>;


		$(document).ready(function() {
			// 項目切換
			$('#bill_mode').val("1");
			var bill_mode = $('#bill_mode').val();
		    if (bill_mode == 1) {
		    	New_Amode();
		    }
		    if (bill_mode == 0) {
		    	New_Bmode();
		    }

			$('#bill_mode').change(function () {       
		        var bill_mode = $('#bill_mode').val();
		        if (bill_mode == 1) {
		        	New_Amode();
		        }
		        if (bill_mode == 0) {
		        	New_Bmode();
		        }
		    });

		    $('#bill_mode_adjust').change(function () {       
		        var bill_mode_adjust = $('#bill_mode_adjust').val();
		        if (bill_mode_adjust >= 1) {
		        	New_Amode_adjust();
		        }
		        if (bill_mode_adjust == 0) {
		        	New_Bmode_adjust();
		        }
		    });	

			$('#foundry_title').html("總代工價格");
			$('#foundry_title').css("width","7rem");
		    $('#onadd_isbought').change(function () {       
		        var oem_need = $('#onadd_isbought').val();
		        if (oem_need == 1) {
		        	$('#foundry_title').html("每月");
		        	$('#foundry_title').css("width","3rem");
		        }
		        if (oem_need == 0) {
		        	$('#foundry_title').html("總代工價格");
		        	$('#foundry_title').css("width","7rem");
		        }
		    });

		   	// 代工需求切換
			$('#onadd_isbought_adjust').change(function () {       
			     var oem_need = $('#onadd_isbought_adjust').val();
		        if (oem_need == 1) {
		        	$('#foundry_title_adjust').html("每月");
		        	$('#foundry_name_adjust').html("催花價格");
		        	$('#foundry_title_adjust').css("width","3rem");
		        }
		        if (oem_need == 0) {
		        	$('#foundry_title_adjust').html("總代工價格");
		        	$('#foundry_name_adjust').html("代工價格");
		        	$('#foundry_title_adjust').css("width","7rem");
		        }
			});

			//下種 - 種值費用自動帶入
			$('#seed_onadd_growing').change(function () {       
			    var n = $('#seed_onadd_growing').val();
		        
			});
		    

			function New_Amode(){
				$('#foundry_name').html("代工價格");
		        $('#order_type').show();
		        $('#OEM').show();
		        $('#UrgeFlowers').hide();
		        $('#foundry_title').show();
		        $('#bill_request_title').html("代工需求");
		        $('.foundry_option').hide();
		        $('.oem_option').show();
			}

			function New_Bmode(){
				$('#order_type').hide();
		        $('#foundry_name').html("催花價格");
		        $('#OEM').hide();
		        $('#UrgeFlowers').hide();
		        $('#foundry_title').hide();
		        $('#bill_request_title').html("訂單需求");
		        $('#bill_request_title').html("訂單需求");
		        $('.foundry_option').show();
		        $('.oem_option').hide();
			}

			function New_Amode_adjust(){
		        $('#order_type_adjust').show();
		        $('#OEM_adjust').show();
		        $('#UrgeFlowers_adjust').hide();
		        $('#foundry_title_adjust').show();
		        $('#bill_request_title_adjust').html("代工需求");
		        $('.foundry_option_adjust').hide();
		        $('.oem_option_adjust').show();
		    }

		    function New_Bmode_adjust(){
		    	$('#order_type_adjust').hide();		        
		        $('#OEM_adjust').hide();
		        $('#UrgeFlowers_adjust').hide();
		        $('#foundry_title_adjust').hide();
		        $('#bill_request_title_adjust').html("訂單需求");
		        $('.foundry_option_adjust').show();
		        $('.oem_option_adjust').hide();	
		    }

		    $("body").on("change", "#add_form input[id=myFile]", function (){
		        preview(this);
		        var files = $("#add_form input[id=myFile]").get(0).files;   		     
		        var formData = new FormData();   
    			formData.append("myFile", files[0]); 
    			formData.append("onproduct_type", "4"); 
    			$.ajax({   
			        url: './../purchase/upload_image.php',   
			        data: formData,    
			        dataType: "json",   
			        type: "POST",   
			        cache: false,   
			        contentType: false,   
			        processData: false,   
			        error: function(xhr) {   
			        },   
			        success: function(json) {   
			            
			        },   
			        complete: function(json){
			        	// $('#img_newName').html(json.responseText);   
			        	$('#img_newName').html(json.responseText);   
			        	// console.log($('#img_newName').html());
			        }   
			    });  
		    });

		    $("body").on("change", "#adjust_form input[id=myFile]", function (){
		        preview(this);
		        var files =$('#adjust_form input[id=myFile]').get(0).files;   		     
		        var formData = new FormData();   
    			formData.append("myFile", files[0]); 
    			formData.append("onproduct_type", "4"); 
    			$.ajax({   
			        url: './../purchase/upload_image.php',   
			        data: formData,    
			        dataType: "json",   
			        type: "POST",   
			        cache: false,   
			        contentType: false,   
			        processData: false,   
			        error: function(xhr) {   
			        },   
			        success: function(json) {   
			            
			        },   
			        complete: function(json){
			        	// $('#adjust_form input[name=onadd_quantity]').val(d.onadd_quantity);
			        	$('#img_newName').html(json.responseText);   
			        	// console.log($('#img_newName').html());
			        }   
			    });  
		    });

		    //修改-----------------------------------------------------------
			$('button.adjust').on('click', function(){
				$('#adjust-modal').modal();
				$('#adjust_form')[0].reset();
								$.ajax({
					url: './plant_flask.php',
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
			                if(ret.code==1) {
			                	var d = ret.data;
			                	// console.table(d);
			                	$('#adjust_form img[id=preview]').attr("src",d.img_url);
			                	$('#adjust_form input[name=onadd_sn]').val(d.onadd_sn);
			                	$('#adjust_form input[name=onadd_ml]').val(d.onadd_ml);
			                	$('#adjust_form input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
			                	$('#adjust_form input[name=onadd_part_no]').val(d.onadd_part_no);
			                	$('#adjust_form input[name=onadd_part_name]').val(d.onadd_part_name);
			                	$('#adjust_form input[name=onadd_color]').val(d.onadd_color);
			                	$('#adjust_form input[name=onadd_size]').val(d.onadd_size);
			                	$('#adjust_form [name=onadd_isbought] option[value='+d.onadd_isbought+']').prop('selected','selected');               	
			                	$('#adjust_form [name=bill_mode] option[value='+d.onadd_type+']').prop('selected','selected');

			                	$('#adjust_form [name=onadd_cur_size] option[value='+d.onadd_cur_size+']').prop('selected','selected');
			                	$('#adjust_form input[name=onadd_height]').val(d.onadd_height);
			                	$('#adjust_form input[name=onadd_pot_size]').val(d.onadd_pot_size);
			                	$('#adjust_form [name=onadd_location] option[value='+d.onadd_location+']').prop('selected','selected');
			                	$('#adjust_form [name=onadd_sellsize] option[value='+d.onadd_sellsize+']').prop('selected','selected');
			                	
			                	$('#adjust_form [name=onsd_sn] option[value='+d.onsd_sn+']').prop('selected','selected');
			                	$('#adjust_form input[name=onadd_supplier]').val(d.onadd_supplier);
								$('#adjust_form input[name=onadd_supplier_address]').val(d.onadd_supplier_address);
								$('#adjust_form input[name=onadd_supplier_email]').val(d.onadd_supplier_email);
								$('#adjust_form input[name=onadd_supplier_phone]').val(d.onadd_supplier_phone);

			                	$('#adjust_form input[name=onadd_buy_price]').val(d.onadd_buy_price);
			                	// console.log(d.img_url);
			                	if(d.img_url != null){
			                		d.img_url = "."+d.img_url.substring(22,d.img_url.length);
			                		$('#img_newName').html(d.preview_img_url);
			                	}
 
			                	$('#adjust_form input[name=onadd_planting_date]').val(d.onadd_planting_date);
			                	$('#adjust_form input[name=onadd_quantity]').val(d.onadd_quantity);
			                	$('#adjust_form input[name=onadd_quantity_bottle]').val(d.onadd_quantity_bottle);
			                	$('#adjust_form input[name=onadd_buy_price_1]').val(JSON.parse(d.onadd_other_price)['price']);

			                	if (d.onadd_type >= 1) {
							    	New_Amode_adjust();
							    }
							     else{
							    	New_Bmode_adjust();
							    }

			                	// $('#adjust_form [name=onadd_growing] option[value='+d.onadd_growing+']').prop('selected','selected');

			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
			             	// console.log('ajax error');
			              //    console.log(thrownError);
			             }
			         });
			});			
			//修改-----------------------------------------------------------

		$.ajax({
			url: './plant_flask.php',
			type: 'post',
			dataType: 'json',
			data: {op:"get_all_supplier"},
			beforeSend: function(msg) {
				$("#ajax_loading").show();
			},
			complete: function(XMLHttpRequest, textStatus) {
				$("#ajax_loading").hide();
			},
			success: function(ret) {	
		        if(ret.code==1) {
		        	all_supplier = ret.data;	
		        	console.log(all_supplier);
		        	/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
					// autocomplete_supplier(document.getElementById('autocomplete_onadd_supplier'), all_supplier[0]);

		        }
		    },
		    error: function (xhr, ajaxOptions, thrownError) {
		    	console.log('ajax error');
		        // console.log(xhr);
		    }
		});

		$.ajax({
			url: './plant_flask.php',
			type: 'post',
			dataType: 'json',
			data: {op:"get_all_plant_staff"},
			beforeSend: function(msg) {
				$("#ajax_loading").show();
			},
			complete: function(XMLHttpRequest, textStatus) {
				$("#ajax_loading").hide();
			},
			success: function(ret) {	
		        if(ret.code==1) {
		        	all_plant_staff = ret.data;	
		        	console.log(all_plant_staff);
		        	/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
					autocomplete_plant_staff(document.getElementById('autocomplete_onadd_plant_staff'), all_plant_staff[0]);

		        }
		    },
		    error: function (xhr, ajaxOptions, thrownError) {
		    	console.log('ajax error');
		        // console.log(xhr);
		    }
		});

		function autocomplete(inp, arr) {

		  /*the autocomplete function takes two arguments,
		  the text field element and an array of possible autocompleted values:*/
		  var currentFocus;
		  /*execute a function when someone writes in the text field:*/
		  inp.addEventListener("input", function(e) {
		      var a, b, i, val = this.value;
		      /*close any already open lists of autocompleted values*/
		      closeAllLists();
		      // if (!val) { return false;}
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
							url: './plant_purchase_addflask.php',
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
							        	var img = "./../purchase"+data.onproduct_pic_url.substring(1,data.onproduct_pic_url.length);
							        	$('#img_newName').html((data.onproduct_pic_url != "") ? data.onproduct_pic_url : "");
							        	document.getElementById('preview').setAttribute("src",((data.onproduct_pic_url != "") ? img : "./../purchase/images/nopic.png"));
							        	document.getElementById('dropdown_onadd_part_name').value = (data.onproduct_part_name != "") ? data.onproduct_part_name : "";
							        	document.getElementById('dropdown_onadd_color').value = (data.onadd_color!= "") ? data.onadd_color : "";
							        	document.getElementById('dropdown_onadd_size').value = (data.onadd_size != "") ? data.onadd_size : "";
							        	document.getElementById('dropdown_onadd_height').value = (data.onadd_height != "") ? data.onadd_height : "";
							        	// document.getElementById('dropdown_pot_size').value = (data.onadd_pot_size != "") ? data.onadd_pot_size : "";
							        	
							        	$('#dropdown_onadd_isbought option[value='+data.onadd_isbought+']').prop('selected','selected');
							        	if(data.onadd_isbought=='1'){

							        		document.getElementById('onadd_foundry_price').value = (data.onadd_buy_price != "") ? data.onadd_buy_price : "";
							        		FoundryChanger(data.onadd_isbought);
							        	}else{
							        		document.getElementById('onadd_buy_price').value = (data.onadd_buy_price != "") ? data.onadd_buy_price : "";
								        }
							        	

							        	document.getElementById('dropdown_quantity_bottle').value = (data.onadd_quantity_bottle != "") ? data.onadd_quantity_bottle : "";
							        	document.getElementById('dropdown_quantity').value = (data.onadd_quantity != "") ? data.onadd_quantity : "";
							        	
							        	if(data.onadd_other_price != null && data.onadd_other_price != ''){
						                	var onadd_other_price = JSON.parse(data.onadd_other_price);
					                		// document.getElementById('onadd_buy_item_1').value = (onadd_other_price.name != "") ? onadd_other_price.name : "";
					                		document.getElementById('onadd_buy_price_1').value = (onadd_other_price.price != "") ? onadd_other_price.price : "";
						                }
							        	
							        	// document.getElementById('dropdown_onadd_height').value = (data.onadd_height != "") ? data.onproduct_height : "";
							        	$('#add_form input[name=dropdown_onadd_location]').val(data.onproduct_location);
							        	// document.getElementById('dropdown_onadd_pot_size').value = (data.onproduct_pot_size != "") ? data.onproduct_pot_size : "";
							        	document.getElementById('autocomplete_onadd_supplier').value = (data.onadd_supplier != "") ? data.onadd_supplier : "";
							        	document.getElementById('dropdown_onadd_supplier_phone').value = (data.onadd_supplier_phone != "") ? data.onadd_supplier_phone : "";
							        	document.getElementById('dropdown_onadd_supplier_address').value = (data.onadd_supplier_address != "") ? data.onadd_supplier_address : "";
							        	document.getElementById('dropdown_onadd_supplier_email').value = (data.onadd_supplier_email != "") ? data.onadd_supplier_email : "";
							        	// document.getElementById('dropdown_onadd_growing').value = data.onproduct_growing;
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


		  	// 代工需求切換
			$('#dropdown_onadd_isbought').change(function () {       
		        var whohide = $('#dropdown_onadd_isbought').val();
		        FoundryChanger(whohide);
		    });

		  /*execute a function when someone clicks in the document:*/
		  document.addEventListener("click", function (e) {
		      closeAllLists(e.target);
		  });
  		}	

  		function autocomplete_supplier(inp, arr) {

		  /*the autocomplete function takes two arguments,
		  the text field element and an array of possible autocompleted values:*/
		  var currentFocus;
		  /*execute a function when someone writes in the text field:*/
		  inp.addEventListener("input", function(e) {
		      var a, b, i, val = this.value;
		      /*close any already open lists of autocompleted values*/
		      closeAllLists();
		      // if (!val) { return false;}
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
		        // if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
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
							url: './plant_flask.php',
							type: 'post',
							dataType: 'json',
							data: {op:"getSupplierByName",onadd_supplier:inp.value},
							beforeSend: function(msg) {
								$("#ajax_loading").show();
							},
							complete: function(XMLHttpRequest, textStatus) {
								$("#ajax_loading").hide();
							},
							success: function(ret) {
									var data = ret.data;
							        if(ret.code==1) {
							        	$('#dropdown_onadd_supplier_phone').val(data.onadd_supplier_phone);
										$('#dropdown_onadd_supplier_address').val(data.onadd_supplier_address);
										$('#dropdown_onadd_supplier_email').val(data.onadd_supplier_email);							        
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
		        // }
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

  		function autocomplete_plant_staff(inp, arr) {

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
		      console.log(arr);
		      for (i = 0; i < arr.length; i++) {
		        /*check if the item starts with the same letters as the text field value:*/		       
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
		         
		          /*close the list of autocompleted values,
		            (or any other open lists of autocompleted values:*/
		            closeAllLists();
		        });
		        a.appendChild(b);		        
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

  		function FoundryChanger(whohide){
  			if (whohide == 1) {
		        	$('#OEM').hide();
		        	$('#UrgeFlowers').show();
		        }
		        if (whohide == 0) {
		        	$('#OEM').show();
		        	$('#UrgeFlowers').hide();
		        }
  		}
		
		$.ajax({
			url: './plant_purchase_addflask.php',
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
					url: './plant_flask.php',
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
				                $('#upd_form input[name=bill_mode]').val(d.onadd_type);
			                	$('#upd_form input[name=onadd_part_no]').val(d.onadd_part_no);
			                	$('#upd_form input[name=onadd_part_name]').val(d.onadd_part_name);
			                	$('#upd_form input[name=onadd_color]').val(d.onadd_color);
			                	$('#upd_form input[name=onadd_quantity_bottle]').val(d.onadd_quantity_bottle);
			                	$('#upd_form input[name=onadd_buy_price]').val(d.onadd_buy_price);
			                	$('#upd_form input[name=onadd_isbought]').val(d.onadd_isbought);

			                	if(d.onadd_isbought == "1")
			                		$('#level_price').html('總代工價格');
			                	else
			                		$('#level_price').html('代工價格/月');

			                	$('#upd_form input[name=onadd_left_quantity]').val(d.onadd_left_quantity);
			                	if(d.onadd_other_price != ''){
				                	var onadd_other_price = JSON.parse(d.onadd_other_price);
				                	$('#upd_form input[name=onadd_planting_date_show]').val(d.onadd_planting_date);
				                	$('#upd_form input[name=onadd_buy_item_1]').val(onadd_other_price.name);
			                		$('#upd_form input[name=onadd_buy_price_1]').val(onadd_other_price.price);
				                }

				                $('#upd_form input[name=onadd_buy_price_A]').val(d.onadd_buy_price);				                

				                var dd = new Date();
								var month = dd.getMonth()+1;
								var day = dd.getDate();

								var output = dd.getFullYear() + '/' +
								(month<10 ? '0' : '') + month + '/' +
								(day<10 ? '0' : '') + day;

			                	$('#upd_form input[name=onadd_planting_date]').val(output);

			                	$('#preview_grow').attr('src',d.img_url);
			                	$('#upd_form input[name=onadd_size]').val(d.onadd_size);
			                	$('#upd_form input[name=onadd_height]').val(d.onadd_height);
			                	$('#upd_form input[name=onadd_supplier_phone]').val(d.onadd_supplier_phone);
			                	$('#upd_form input[name=onadd_supplier_address]').val(d.onadd_supplier_address);
			                	$('#upd_form input[name=onadd_supplier_email]').val(d.onadd_supplier_email);
			                	$('#upd_form input[name=onadd_location]').val(d.onadd_location);
			                	$('#upd_form input[name=onadd_pot_size]').val(d.onadd_pot_size);
			                	$('#upd_form input[name=onadd_supplier]').val(d.onadd_supplier);
			                	$('#upd_form input[name=onadd_quantity]').val(d.onadd_quantity);
			                	$('#upd_form [name=onadd_status] option[value='+d.onadd_status+']').prop('selected','selected');
			                	$('#upd_form [name=onadd_sellsize] option[value='+d.onadd_sellsize+']').prop('selected','selected');

			                	
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			});

			//出貨-----------------------------------------------------------
			$('button.upd2').on('click', function(){
				$('#upd-modal2').modal();
				$('#upd_form2')[0].reset();

				$.ajax({
					url: './plant_flask.php',
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
			                	$('#upd_form2 input[name=onadd_part_name]').val(d.onadd_part_name);
			                	$('#upd_form2 input[name=onadd_part_no]').val(d.onadd_part_no);
			                	$('#upd_form2 input[name=onadd_quantity]').val(d.onadd_quantity);
			                	$('#upd_form2 input[name=onadd_location]').val(d.onadd_location);
			                	$('#upd_form2 input[name=onadd_ml]').val(d.onadd_ml);
			                	$('#upd_form2 input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			});
			//出貨-----------------------------------------------------------

			//汰除-----------------------------------------------------------
			$('button.upd1').on('click', function(){
				$('#upd-modal1').modal();
				$('#upd_form1')[0].reset();

				$.ajax({
					url: './plant_flask.php',
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
			                	if(d.onadd_newpot_sn == "0"){
			                		$('#upd_form1 input[name=onadd_newpot_sn]').val(d.onadd_sn);
			                	}
			                	else{
			                		$('#upd_form1 input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
			                	}
				        		$('#upd_form1 input[name=onadd_quantity_perbottle]').val(d.onadd_quantity);
				        		$('#upd_form1 input[name=onadd_part_no]').val(d.onadd_part_no);
				        		$('#upd_form1 input[name=onadd_part_name]').val(d.onadd_part_name);
				        		$('#upd_form1 input[name=onadd_quantity]').val(
				        			parseInt(d.onadd_quantity*d.onadd_quantity_bottle)+parseInt(d.onadd_left_quantity)
				        		);
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			});
			//汰除-----------------------------------------------------------

			$('button.keep').on('click', function(){
				location.reload();
			});

			$('#change_basin_next_size').change(function () {       
			    var n = $('#change_basin_next_size').val();				        
			    $('#upd_form input[name=onadd_price_per_plant]').val(plant_price[n]);
			});

			//修改-----------------------------------------------------------
			$('button.upd3').on('click', function(){
				$('#upd-modal3').modal();
				$('#upd3_form')[0].reset();

				$.ajax({
					url: './plant_flask.php',
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
			                	$('#upd3_form input[name=onadd_ml]').val(d.onadd_ml);
			                	$('#upd3_form input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);

			                	if(d.onadd_location != "")
			                		$('#upd3_form [name=onadd_location] option[value='+d.onadd_location+']').prop('selected','selected','selected','selected','selected','selected','selected');
			                	$('#upd3_form input[name=onadd_supplier]').val(d.onadd_supplier);
			                	$('#upd3_form input[name=onadd_planting_date]').val(d.onadd_planting_date);
			                	$('#upd3_form input[name=onadd_quantity]').val(d.onadd_quantity);
			                	$('#upd3_form input[name=onadd_growing]').val(d.onadd_growing);
			                	$('#upd3_form input[name=onadd_location_old]').val(d.onadd_location);
			                	$('#upd3_form [name=onadd_growing] option[value='+d.onadd_growing+']').prop('selected','selected','selected','selected','selected','selected','selected');			                	
			                	$('#upd3_form [name=onadd_status] option[value='+d.onadd_status+']').prop('selected','selected');
			                	$('#upd3_form [name=onadd_cur_size] option[value='+d.onadd_cur_size+']').prop('selected','selected');
			                	
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
							url: './plant_flask.php',
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
				qr_sn = $(this).data('qr_sn');
				$('#qr_modal').modal();
				$.ajax({
					url: './plant_flask.php',
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
			                console.log(ret);
			                if(ret.code==1) {
			                	var d = ret.data;;
			                	$('#temp_onadd_sn').val(d.onadd_sn);
			                	if(d.img_url != "")
			                		$('#qr_product_img').attr("src",d.img_url);
			                	else
			                		$('#qr_product_img').attr("src","./../purchase/images/nopic.png");
			                	$('#qr_sn').html("產品編號："+qr_sn);
			                	$('#qr_part_no').html("品號："+d.onadd_part_no);
			                	$('#qr_part_name').html("品名："+d.onadd_part_name);
			                	$('#qr_plant_date').html("下種日期："+d.onadd_planting_date);
			                	$('#qr_part_number').html("數量："+d.onadd_quantity);
								$('#qr_location').html("位置："+d.onadd_location);
			                	var src = $('#qr_img_example').attr('src');
			                	$('#qr_img').attr('src',src+"onadd_sn="+d.onadd_sn);
			                	// document.getElementById('qr_cotent_recover').appendChild(document.getElementById('qr_cotent').cloneNode(true));
			                	// $('#qr_cotent_recover').attr('style','display:none');
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
				var onadd_sn = $('#temp_onadd_sn').val();
				$.ajax({
					url: './../../admin/purchase/plant_purchase.php',
					type: 'post',
					dataType: 'json',
					data: {op:"download", onadd_sn},
					beforeSend: function(msg) {
						$("#ajax_loading").show();
					},
					complete: function(XMLHttpRequest, textStatus) {
						$("#ajax_loading").hide();
					},
					success: function(ret) {
			                console.log(ret);
			                if(ret.code==1) {
			                	$('#qr_sticker_img').attr("src",ret.data['img_url']);
								$('#qr_sticker_sn').html($('#qr_sn').html());
								$('#qr_sticker_part_no').html($('#qr_part_no').html());
								$('#qr_sticker_part_name').html($('#qr_part_name').html());
								$('#qr_sticker_date').html($('#qr_plant_date').html());
								$('#qr_sticker_location').html($('#qr_location').html());
								$('#qr_sticker_qrcode').attr("src",($('#qr_img').attr("src")));
			                	PrintElem('qr_sticker');

			                	setTimeout(
								    function() {		
								    	document.getElementById('qr_sticker').setAttribute("style", "width: 410px; height: 720px;display:none;");
								    }, 500);			                	
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			});

			$('#add_form, #upd_form1, #upd_form2, #upd3_form, #eli_form1, #adjust_form').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					e.preventDefault();
					var param = $(this).serializeArray();
					var onproduct_pic_url = {name:"onproduct_pic_url",value:$('#img_newName').html().substring(1,$('#img_newName').html().length)};
					param.push(onproduct_pic_url);
					$(this).parents('.modal').modal('hide');
					$(this)[0].reset();
					console.log(param);
					 	$.ajax({
					 		url: './plant_flask.php',
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

				bootbox.setDefaults({
					locale: "zh_TW",
				});

				 //供應商選擇
				$('#supplier_select').change(function () {       
				    var onsd_sn = $('#supplier_select').val();
				    if(onsd_sn != "0"){
						$.ajax({
							url: './../sys/sys_supplier.php',
							type: 'post',
							dataType: 'json',
							data: {op:"get", onsd_sn:onsd_sn},
							beforeSend: function(msg) {
								$("#ajax_loading").show();
							},
							complete: function(XMLHttpRequest, textStatus) {
								$("#ajax_loading").hide();
							},
							success: function(ret) {
							    if(ret.code==1) {
							    	var d = ret.data;			        							        	
							    	$('#add_form input[name=onadd_supplier]').val(d.onsd_name);
							    	$('#add_form input[name=onadd_supplier_phone]').val(d.onsd_phone);
							    	$('#add_form input[name=onadd_supplier_address]').val(d.onsd_address);
							    	$('#add_form input[name=onadd_supplier_email]').val(d.onsd_mail);
							    }
							},
							error: function (xhr, ajaxOptions, thrownError) {

					    	}
					    });
					}
					else{
						$('#add_form input[name=onadd_supplier]').val("");
						$('#add_form input[name=onadd_supplier_phone]').val("");
						$('#add_form input[name=onadd_supplier_address]').val("");
						$('#add_form input[name=onadd_supplier_email]').val("");
					}
				});

				//供應商選擇
				$('#supplier_select_adjust').change(function () {       
				    var onsd_sn = $('#supplier_select_adjust').val();
				    if(onsd_sn != "0"){
						$.ajax({
							url: './../sys/sys_supplier.php',
							type: 'post',
							dataType: 'json',
							data: {op:"get", onsd_sn:onsd_sn},
							beforeSend: function(msg) {
								$("#ajax_loading").show();
							},
							complete: function(XMLHttpRequest, textStatus) {
								$("#ajax_loading").hide();
							},
							success: function(ret) {
							    if(ret.code==1) {
							    	var d = ret.data;			        							        	
							    	$('#adjust_form input[name=onadd_supplier]').val(d.onsd_name);
							    	$('#adjust_form input[name=onadd_supplier_phone]').val(d.onsd_phone);
							    	$('#adjust_form input[name=onadd_supplier_address]').val(d.onsd_address);
							    	$('#adjust_form input[name=onadd_supplier_email]').val(d.onsd_mail);
							    }
							},
							error: function (xhr, ajaxOptions, thrownError) {

					    	}
					    });
					}
					else{
						$('#adjust_form input[name=onadd_supplier]').val("");
						$('#adjust_form input[name=onadd_supplier_phone]').val("");
						$('#adjust_form input[name=onadd_supplier_address]').val("");
						$('#adjust_form input[name=onadd_supplier_email]').val("");
					}
				});

				$('#upd_form').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					e.preventDefault();
					var param = $(this).serializeArray();
					var onproduct_pic_url = {name:"onproduct_pic_url",value:$('#img_newName').html().substring(1,$('#img_newName').html().length)};
					param.push(onproduct_pic_url);
					console.log(param);

					$(this).parents('.modal').modal('hide');
					$(this)[0].reset();

					 	$.ajax({
					 		url: './plant_flask.php',
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
					 			if(ret.msg != '下種成功！'){
					 				alert_msg(ret.msg);
					 			}	
					 			else{	
					 				if(param[15]['value'] == "")
					 					param[15]['value'] = 0;
					 				if(param[16]['value'] == "")
					 					param[16]['value'] = 0;

					 				if(((param[9]['value']*param[10]['value']) - (parseInt(param[15]['value'])+parseInt(param[16]['value']))) > 0){	
						 				bootbox.confirm({
										    message: "下種成功！庫存剩餘數量是否汰除？",
										    buttons: {
										        confirm: {
										            label: '汰除',
										            className: 'btn-danger'
										        },
										        cancel: {
										            label: '保留',
										            className: 'btn-primary'
										        }
										    },
										    callback: function (result) {
										       	if(result) {											
													$('#eli-modal1').modal();
													$.ajax({
														url: './plant_flask.php',
														type: 'post',
														dataType: 'json',
														data: {op:"get", onadd_sn:param[1]['value']},
														beforeSend: function(msg) {
															$("#ajax_loading").show();
														},
														complete: function(XMLHttpRequest, textStatus) {
															$("#ajax_loading").hide();
														},
														success: function(ret) {
					        							    console.log(ret);
					        							    if(ret.code==1) {
					        							    	var d = ret.data;					        	
					        							    	$('#eli_form1 input[name=onadd_sn]').val(d.onadd_sn);
					        							    	$('#eli_form1 input[name=onadd_quantity_perbottle]').val(d.onadd_quantity);
					        							    	$('#eli_form1 input[name=onadd_part_no]').val(d.onadd_part_no);
					        							    	$('#eli_form1 input[name=onadd_part_name]').val(d.onadd_part_name);
					        							    	$('#eli_form1 input[name=onadd_quantity]').val(
					        							    		parseInt(d.onadd_quantity*d.onadd_quantity_bottle)+parseInt(d.onadd_left_quantity)
					        							    	);
					        							    }
					        							},
					        							error: function (xhr, ajaxOptions, thrownError) {

				            							}
				            						});
												}
												else{
													alert_msg(ret.msg);
												}
										    }
										});	 			
						 			}
						 			else{
						 				alert_msg(ret.msg);
						 			}
					 			}
					 		},
					 		error: function (xhr, ajaxOptions, thrownError) {
			                	// console.log('ajax error');
			                 //     console.log(thrownError);
			                 }
			             });
					 }
				});


		        var d = new Date();
				var month = d.getMonth()+1;
				var day = d.getDate();

				var output = d.getFullYear() + '/' +
				(month<10 ? '0' : '') + month + '/' +
				(day<10 ? '0' : '') + day;

				

				$('#datetimepicker1,#datetimepicker2,#datetimepicker3,#datetimepicker4').datetimepicker({
		        	minView: 2,
		            language:  'zh-TW',
		            format: 'yyyy-mm-dd',
		            useCurrent: false
		        });

		        $('#datetimepicker1,#datetimepicker2,#datetimepicker3,#datetimepicker4').val(output);

		        $('button.cancel').on('click', function() {
					location.href = "./../";
				});
		});
		//產品履歷----------------------------------------------------------
		function history(onadd_part_no,onadd_name,onadd_sn){
				$('#history_title').html(onadd_part_no+" - "+onadd_name+" 苗種履歷");
				$('#history_modal').modal();
				$.ajax({
					url: './plant_flask.php',
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
						$('#history_cotent').html('<div class="col-md-12"><div class="col-md-12"><label for="addModalInput1" class="col-md-2 control-label">操作日期</label><label for="addModalInput1" class="col-md-2 control-label">下種日期(數量)</label><label for="addModalInput1" class="col-md-2 control-label">換盆日期(數量)</label><label for="addModalInput1" class="col-md-2 control-label">出貨日期(數量)</label></label><label for="addModalInput1" class="col-md-2 control-label">汰除日期(數量)</label></div></div>');
						$.each(ret.data, function(key,value){	
							if(key < ret.data.length){
								var temp = "";
								switch(value.flag){
									case 0:
										temp ='<label for="addModalInput1" class="col-md-2 control-label">'+value.add_date+'</label>'+
										'<label for="addModalInput1" class="col-md-2 control-label">'+value.mod_date+' ('+value.quantity+')</label>'+
										'<label for="addModalInput1" class="col-md-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-md-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-md-2 control-label"></label>';
									break;
									case 1:
										temp ='<label for="addModalInput1" class="col-md-2 control-label">'+value.add_date+'</label>'+
										'<label for="addModalInput1" class="col-md-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-md-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-md-2 control-label">'+value.mod_date+' ('+value.quantity+')</label>'+
										'<label for="addModalInput1" class="col-md-2 control-label"></label>';
									break;
									case 2:
										temp ='<label for="addModalInput1" class="col-md-2 control-label">'+value.add_date+'</label>'+
										'<label for="addModalInput1" class="col-md-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-md-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-md-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-md-2 control-label">'+value.mod_date+' ('+value.quantity+')</label>';
									break;
									case 3:
										temp ='<label for="addModalInput1" class="col-md-2 control-label">'+value.add_date+'</label>'+
										'<label for="addModalInput1" class="col-md-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-md-2 control-label">'+value.mod_date+' ('+value.quantity+')</label>'+
										'<label for="addModalInput1" class="col-md-2 control-label"></label>'+
										'<label for="addModalInput1" class="col-md-2 control-label"></label>';
									break;
								}
																		
								$('#history_cotent').html($('#history_cotent').html()+'<div class="col-md-12"><div class="col-md-12">'+temp+'</div></div>');								
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
		function PrintElem(elem){
		    var mywindow = window.open('', 'PRINT', 'height=400,width=600');

		    mywindow.document.write('<html><head><title>' + document.title  + '</title>');
		    mywindow.document.write('</head><body >');
		    mywindow.document.write('<h1>' + document.title  + '</h1>');
		    document.getElementById(elem).setAttribute("style", "width: 410px; height: 720px;");
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
	<div style="display: none;" id="img_newName"></div>
		<!--page header start-->
		<div class="page-header">
			<div class="row">
				<div class="col-md-6">
					<h4>瓶苗庫存管理</h4>
				</div>
			</div>
		</div>

		

		<!-- 下種 -->
		<div id="upd-modal" class="modal upd-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">下種</h4>
						</div>

						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd">
									<input type="hidden" name="onadd_sn">
									<input type="hidden" name="onadd_newpot_sn">
									<!-- <input type="hidden" name="onadd_cur_size" value="1"> -->
									<input type="hidden" name="onadd_next_status">
									<input type="hidden" name="bill_mode">
									<input type="hidden" name="onadd_isbought">
									
									<!--左邊欄位-->
									<div class="col-md-5">
										<h4>產品圖片</h4>
										<div class="well" style="text-align: center;">								
											<img class="preview" id="preview_grow" style="max-width: 25rem; max-height: 25rem;">
										</div>
										<br/>
									</div>
									<div class="col-md-1">
									</div>
									<!--右邊欄位-->
									<div class="col-md-6">
										<h4>&nbsp;</h4>
										<div class="form-group">
												<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">品號<font color="red">*</font></label>
												<div class="col-md-9">
													<input type="text" class="form-control" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32">
													<div class="help-block with-errors"></div>
												</div>
											</div>

											<div class="form-group">
												<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">品名<font color="red">*</font></label>
												<div class="col-md-9">
													<input type="text" class="form-control" name="onadd_part_name" placeholder="" maxlength="32">
													<div class="help-block with-errors"></div>
												</div>
											</div>

											<div class="form-group">
												<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">入庫日期<font color="red">*</font></label>
												<div class="col-md-9">
													<div class="input-group">
													    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
													    <input type="text" class="form-control" id="datetimepicker1" name="onadd_planting_date_show" placeholder="" required minlength="1" maxlength="32">
													</div>
													<div class="help-block with-errors"></div>
												</div>
											</div>
											<div class="form-group" style="">
												<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">入庫數量<font color="red">*</font></label>
												<div class="col-md-9">
													<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
													<input type="text" class="form-control" name="onadd_quantity_bottle" placeholder="瓶" minlength="1" maxlength="32" readonly="readonly" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
													</div>
													<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">瓶</label>
													<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
													<input type="text" class="form-control" name="onadd_quantity" placeholder="棵" minlength="1" maxlength="32" readonly="readonly" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
													</div>
													<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;padding-left: 0px;padding-right: 0px;width: 4.5rem;">棵,剩餘</label>													
													<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
													<input type="text" class="form-control" name="onadd_left_quantity" placeholder="棵" minlength="1" maxlength="32" readonly="readonly" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
													</div>
													<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">棵</label>
													<div class="help-block with-errors"></div>
												</div>
											</div>
											<!-- 代工 -->
											<!-- <div class="form-group" >
												<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;"><span id="buy_price_name">代工價格</span><font color="red">*</font></label>
												<div class="col-md-9">
													<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
													<input type="text" class="form-control" name="onadd_buy_price" placeholder="元" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
													</div>
													<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">元</label>
													<div class="help-block with-errors"></div>
												</div>
											</div> -->

											<div class="form-group">
												<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">採購單價<font color="red">*</font></label>
												<div class="col-md-9" id="other_price">
													
													<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
														<input type="text" class="form-control" name="onadd_buy_price_1" placeholder="元" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
													</div>

													<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">元</label>
													
													<div class="help-block with-errors"></div>
												</div>
											</div>

											<div class="form-group">
												<label class="col-md-3 control-label">出貨規格<font color="red">*</font></label>
												<div class="col-md-9">
													<select class="form-control" name="onadd_sellsize">
														<option value="7">成花</option>
														<option value="6">3.6寸</option>
														<option value="5">3.5寸</option>
														<option value="4">3.0寸</option>
														<option value="3">2.8寸</option>
														<option value="2">2.5寸</option>
														<option selected="selected" value="1">1.7寸</option>
													</select>
												</div>
											</div>
										
									</div>
								</div>
							</div>
							<hr style="margin-top: 0rem;border-top: 2px solid #ddd;">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<!-- 下種日期 -->
										<label class="col-md-2 control-label" style="padding-left: 0px;padding-right: 0px;text-align: right;">下種日期<font color="red">*</font></label>
										<div class="col-md-4">
											<div class="input-group">
											    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
											    <input type="text" class="form-control" id="datetimepicker3" name="onadd_planting_date" value="<?php echo (empty($device['onadd_planting_date'])) ? '' : date('Y-m-d', $device['onadd_planting_date']);?>" placeholder="" required minlength="1" style="width: 90px;padding-left: 0px;padding-right: 0px;">
											</div>
											<div class="help-block with-errors"></div>
										</div>
										<!-- 下種數量 -->
										<label for="addModalInput1" class="col-md-2 control-label" style="padding-left: 0px;padding-right: 0px;text-align: right;">下種數量<font color="red">*</font></label>
										<div class="col-md-4">		
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: right;width: 4rem;padding-left: 0px;">A苗</label>									
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
												<input type="text" class="form-control" name="onadd_plant_day_A" placeholder="必填" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
											</div>
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">棵</label>											
											<div class="help-block with-errors"></div>

											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: right;width: 4rem;padding-left: 0px;">B苗</label>			
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
												<input type="text" class="form-control" name="onadd_plant_day_B" placeholder="非必填" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
											</div>
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">棵</label>											
											<div class="help-block with-errors"></div>
										</div>
										<label for="addModalInput1" class="col-md-2 control-label" style="padding-left: 0px;padding-right: 0px;text-align: right;" id="level_price">價格<font color="red">*</font></label>
										<div class="col-md-4">		
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: right;width: 4rem;padding-left: 0px;">A苗</label>									
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
												<input type="text" class="form-control" name="onadd_buy_price_A" placeholder="必填" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
											</div>
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">元</label>											
											<div class="help-block with-errors"></div>

											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: right;width: 4rem;padding-left: 0px;">B苗</label>			
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
												<input type="text" class="form-control" name="onadd_buy_price_B" placeholder="非必填" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
											</div>
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">元</label>											
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label" style="text-align: right;padding-left: 0px;padding-right: 0px;">種植人員</label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="autocomplete_onadd_plant_staff" name="onadd_plant_staff" placeholder="">
											<div class="help-block with-errors"></div>
										</div>

										<label for="addModalInput1" class="col-md-2 control-label" style="padding-left: 0px;padding-right: 0px;text-align: right;">換盆費用</label>
										<div class="col-md-4">											
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">

												<input type="text" class="form-control" id="seed_onadd_price_per_plant" name="onadd_price_per_plant" placeholder="費用"	 maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;" value="4">
											</div>
											<label for="addModalInput1" class="col-md-4 control-label" style="text-align: left;width: 5rem;padding-left: 0px;">每株</label>											
											<div class="help-block with-errors"></div>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
									<!-- 預計換盆階段 -->
									<div class="col-md-12" >
										<label class="col-md-4 control-label" style="margin-left: 0rem;">下種至<font color="red">*</font></label>
										<div class="col-md-8" >
											<select class="form-control" name="onadd_cur_size" id='change_basin_next_size'>
												<option value="7">催花</option>
												<option value="11">4.0寸(含以上)</option>
												<option value="6">3.6寸</option>
												<option value="5">3.5寸</option>
												<option value="4">3.0寸</option>
												<option value="3">2.8寸</option>
												<option value="2">2.5寸</option>
												<option value="10">2.0寸</option>
												<option selected="selected" value="1">1.7寸</option>
											</select>
											<div class="help-block" style="margin-left: rem;width: 30rem;margin-bottom: 0px;">
												<!-- <p>此欄位主要是下種之後，在下一個階段換盆尺寸，例如：瓶苗開瓶1.7 , 下一階段預計換到2.5，此欄位就設定2.5</p> -->
											</div>	
											</div>		
										</div>
										<div class="col-md-12" >
										<label class="col-md-4 control-label" style="margin-left: 0rem; ">下次換盆尺寸<font color="red">*</font></label>

										<div class="col-md-8" >
											<select class="form-control" id="seed_onadd_growing" name="onadd_growing">
												<option value="7">催花</option>
												<option value="11">4.0寸(含以上)</option>
												<option value="6">3.6寸</option>
												<option value="5">3.5寸</option>
												<option value="4">3.0寸</option>
												<option value="3">2.8寸</option>
												<option selected="selected" value="2">2.5寸</option>
												<option value="10">2.0寸</option>
											</select>
										</div>
										</div>
									</div>
								</div>
							</div>
							
						</div>
						<hr style="margin-top: 0rem;border-top: 2px solid #ddd;">
						<!--左邊欄位-->
						<div class="row">
							<div class="col-md-12">
								<div class="col-md-12">
									<h4>供應商資訊</h4>	
								</div>

								<div class="form-group">
									<label for="addModalInput1" class="col-md-2 control-label" style="text-align: right;padding-left: 0px;padding-right: 0px;">姓名<font color="red">*</font></label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="onadd_supplier" placeholder="" >
										<div class="help-block with-errors"></div>
									</div>
									<label for="addModalInput1" class="col-md-1 control-label" style="text-align: right;padding-left: 0px;padding-right: 0px;">花徑</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="onadd_size" placeholder="" >
										<div class="help-block with-errors"></div>
									</div>
									<label for="addModalInput1" class="col-md-2 control-label" style="text-align: right;padding-left: 0px;padding-right: 0px;">適合開花盆徑</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="onadd_pot_size" placeholder="" >
										<div class="help-block with-errors"></div>
									</div>
								</div>

								<div class="form-group">
									<label for="addModalInput1" class="col-md-2 control-label" style="text-align: right;padding-left: 0px;padding-right: 0px;">電話</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="onadd_supplier_phone" placeholder="">
										<div class="help-block with-errors"></div>
									</div>
									<label for="addModalInput1" class="col-md-1 control-label" style="text-align: right;padding-left: 0px;padding-right: 0px;">花色</label>
									<div class="col-md-2">
										<input type="text" class="form-control" name="onadd_color" placeholder="" >
										<div class="help-block with-errors"></div>
									</div>	
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label" style="text-align: right;padding-left: 0px;padding-right: 0px;">放置區<span style="color: red">*</span></label>
										<div class="col-md-2">
											<select class="form-control justify-content-start" name="onadd_location">
												<option value="B5">B5</option>
												<option value="B4">B4</option>
												<option value="B3">B3</option>
												<option value="B2">B2</option>
												<option value="B1">B1</option>
												<option value="A5">A5</option>
												<option value="A4">A4</option>
												<option value="A3">A3</option>
												<option value="A2">A2</option>
												<option selected="selected" value="A1">A1</option>
											</select>
										</div>
									</div>			
								</div>
								<div class="form-group">
									<label for="addModalInput1" class="col-md-2 control-label" style="text-align: right;padding-left: 0px;padding-right: 0px;">地址</label>
									<div class="col-md-4">
										<input type="text" class="form-control" name="onadd_supplier_address" placeholder="">
										<div class="help-block with-errors"></div>
									</div>
									<label for="addModalInput1" class="col-md-2 control-label" style="text-align: right;padding-left: 0px;padding-right: 0px;">Email</label>
									<div class="col-md-3">
										<input type="text" class="form-control" name="onadd_supplier_email" placeholder="">
										<div class="help-block with-errors"></div>
									</div>
								</div>
							</div>				
						</div>		

						
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">確認下種</button>
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
									<input type="hidden" name="onadd_newpot_sn">
									<input type="hidden" name="onadd_quantity_perbottle">									
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">品號<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">下種數量<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">汰除數量<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity_del" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-2 control-label">汰除原因<font color="red">*</font></label>
										<div class="col-md-10">
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
							<button type="submit" class="btn btn-danger">汰除</button>
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
									<input type="hidden" name="onadd_ml">
									<input type="hidden" name="onadd_newpot_sn">
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">品名</label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_name" placeholder=""  maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">品號<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">放置區<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_location" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">可供出貨數量<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">出貨數量<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_plant_year" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">出貨對象<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onshda_client" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>    
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">價格(單棵)<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onshda_price" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>      								
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">確認出貨</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--出貨----------------------------------------------------------->

		<!--修改----------------------------------------------------------->
		<div id="upd-modal3" class="modal upd-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd3_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">移倉</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd5">
									<input type="hidden" name="onadd_sn">
									<input type="hidden" name="onadd_ml">
									<input type="hidden" name="onadd_newpot_sn">
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">品號<font color="red">*</font></label>
										<div class="col-md-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">品名</label>
										<div class="col-md-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_part_name" placeholder=""  maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="display: none;">
										<label for="addModalInput1" class="col-md-2 control-label">花色</label>
										<div class="col-md-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_color" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="display: none;">
										<label for="addModalInput1" class="col-md-2 control-label">花徑</label>
										<div class="col-md-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_size" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="display: none;">
										<label for="addModalInput1" class="col-md-2 control-label">高度</label>
										<div class="col-md-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_height" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">放置區<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="onadd_location_old" name="onadd_location_old" placeholder="">
										</div>																				
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">預計移倉至<font color="red">*</font></label>
										<input readonly="readonly" type="text" class="form-control" id="onadd_location_old" name="onadd_location_old" placeholder="" style="display: none;">
										<div class="col-md-10">
											<select class="form-control" name="onadd_location">
												<option value="B5">B5</option>
												<option value="B4">B4</option>
												<option value="B3">B3</option>
												<option value="B2">B2</option>
												<option value="B1">B1</option>
												<option value="A5">A5</option>
												<option value="A4">A4</option>
												<option value="A3">A3</option>
												<option value="A2">A2</option>
												<option selected="selected" value="A1">A1</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">移倉數量<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_ml_amount" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="display: none;">
										<label for="addModalInput1" class="col-md-2 control-label">適合開花盆徑</label>
										<div class="col-md-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_pot_size" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="display: none;">
										<label for="addModalInput1" class="col-md-2 control-label">供應商</label>
										<div class="col-md-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_supplier" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">下種數量<font color="red">*</font></label>
										<div class="col-md-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="display: none;">
										<label class="col-md-2 control-label">換盆日期&nbsp;</label>
										<div class="col-md-10">
											<input readonly="readonly" type="text" class="form-control" id="datetimepicker3" name="onadd_planting_date" value="<?php echo (empty($device['onadd_planting_date'])) ? '' : date('Y-m-d', $device['onadd_planting_date']);?>" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
									</div>   
									<div class="form-group" style="display: none;">
										<label class="col-md-2 control-label">目前尺寸<font color="red">*</font></label>
										<div class="col-md-10">
											<select readonly="readonly" class="form-control" id="dropdown_onadd_cur_size" name="onadd_cur_size">
												<option value="8">瓶苗開瓶</option>
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
									<div class="form-group" style="display: none;">
										<label class="col-md-2 control-label">下階段換盆尺寸<font color="red">*</font></label>
										<div class="col-md-10">
											<select readonly="readonly" class="form-control" name="onadd_growing">
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
									<div class="form-group" style="display: none;">
										<label class="col-md-2 control-label">預計出貨尺寸<font color="red">*</font></label>
										<div class="col-md-10">
											<select class="form-control" name="onadd_sellsize">
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
							<button type="submit" class="btn btn-primary">確認移倉</button>
						</div>
					</form>
				</div>
			</div>
		</div>


		<!--苗種履歷----------------------------------------------------------->
		<div id="history_modal" class="modal upd-modal2" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="history_title">品號 - 品名 - 產品履歷</h4>
						</div>
						<div class="row">
							<div class="row" id="history_cotent">
								<div class="col-md-12">									
									<div class="col-md-12">
										<label for="addModalInput1" class="col-md-2 control-label">操作日期</label>
										<label for="addModalInput1" class="col-md-2 control-label">下種日期(數量)</label>
										<label for="addModalInput1" class="col-md-2 control-label">換盆日期(數量)</label>
										<label for="addModalInput1" class="col-md-2 control-label">出貨日期(數量)</label>
										<label for="addModalInput1" class="col-md-2 control-label">汰除日期(數量)</label>
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
								<div class="col-md-8" id="qr_sec_cotent">
									<!-- <input type="hidden" id="temp_onadd_sn">
									<img id="qr_img_example" style="margin-left: 20px;padding-left: 10px;display:none;" 
										 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/purchase/plant_purchase.php?">
									<img id="qr_img" style="margin-left: 20px;padding-left: 10px;" 
										 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/purchase/plant_purchase.php?">	 -->
								</div>
								<div class="col-md-8" id="qr_sec_cotent2" style="border-left-width: 20px; margin-left: 30px;">
<!-- 									<div id="qr_sn" style="font-size: 20px;font-weight:bold;">產品編號：</div>
									<div id="qr_part_no" style="font-size: 20px;font-weight:bold;">品號：</div>
									<div id="qr_part_name" style="font-size: 20px;font-weight:bold;">品名：</div>
									<div id="qr_plant_date" style="font-size: 20px;font-weight:bold;">下種日期：</div>
									<div id="qr_location" style="font-size: 20px;font-weight:bold;">位置：</div>
									<div id="qr_part_number" style="font-size: 20px;font-weight:bold;">數量：</div>		
									<img id="qr_sticker_img"style="width: 400px;height: 280px;" src=""> -->
									<img id="qr_product_img"style="width: 565px;height: 392px;margin-top: 15px;" src="">
									<div id="qr_sn" style="margin-top: 5px;font-size: 20px;"></div>
									<div id="qr_part_no" style="font-size: 20px;"></div>
									<div id="qr_part_name" style="font-size: 20px;"></div>
									<div id="qr_plant_date" style="font-size: 20px;"></div>
									<div id="qr_location" style="font-size: 20px;"></div>								
								</div>
								<div class="col-md-8" id="qr_sec_cotent">
									<input type="hidden" id="temp_onadd_sn">
									<img id="qr_img_example" style="margin-left: 20px;padding-left: 10px;display:none;" 
										 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/flask/plant_flask.php?">
									<img id="qr_img" style="margin-left: 0px;padding-left: 430px;" 
										 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/flask/plant_flask.php?">	
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
								<!--左邊欄位-->
								<div class="col-md-5">
									<input type="hidden" name="op" value="add">									
									<?php if(strpos($permmsion_option, "4") !== false || $permmsion == 0){ ?>
											<h4>產品圖片預覽</h4>
											<div class="well" style="text-align: center;width: 31.5rem;height: 31.5rem;">
												<img class="preview" id="preview" style="max-width: 25rem; max-height: 25rem;">
											    <div class="size" id="preview_size" style="font-size: 1.5rem;"></div>
											</div>
									<?php } ?>
									<input type="file" id="myFile" name="myFile" style="max-width: 31.5rem;border-radius:0.5rem;font-family: '微軟正黑體';text-align: center;vertical-align: middle;" accept="image/jpeg,image/jpg,image/png">
									<div class="form-group" style="margin-top: 2.8rem;">
										<label for="addModalInput1" style="text-align: right;" class="col-md-2 control-label">花色</label>
										<div class="col-md-9" style="text-align: left;">
											<input type="text" class="form-control" id="dropdown_onadd_color" name="onadd_color" placeholder="" >
											<div class="help-block with-errors"></div>
									</div>
									<div class="form-group" >	
										</div>
										<label for="addModalInput1" style="text-align: right;" class="col-md-2 control-label">花徑</label>
										<div class="col-md-9" style="text-align: left;">
											<input type="text" class="form-control" id="dropdown_onadd_size" name="onadd_size" placeholder="" >
											<div class="help-block with-errors"></div>
									</div>
									<div class="form-group" >	
										</div>
										<label for="addModalInput1" style="text-align: right;" class="col-md-2 control-label">高度</label>
										<div class="col-md-9" style="text-align: left;">
											<input type="text" class="form-control" id="dropdown_onadd_height" name="onadd_height" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>			
									</div>
								</div>
								<!--右邊欄位-->
								<div class="col-md-7" >
									<h4>&nbsp;</h4>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;padding-bottom: 10px;">項目<font color="red">*</font></label>
										<div class="col-md-8">
											<select class="form-control" name="bill_mode"  id='bill_mode'>
												<option value="0" selected="selected">自種 / 外購</option>
												<option value="1">代工</option>
											</select>
										</div>
									</div>									
									<div class="form-group" id='order_type' style="display: none;">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;padding-bottom: 10px;"><span id="bill_request_title">代工需求</span><font color="red">*</font></label>
										<div class="col-md-8">
											<select class="form-control" name="onadd_isbought"  id='onadd_isbought'>
												<option value="0" selected="selected" class="oem_option">苗株</option>
												<option value="1" class="oem_option">催花</option>
												<option value="2" class="foundry_option">自種苗</option>
												<option value="3" class="foundry_option">外購苗</option>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">品號<font color="red">*</font></label>
										<div class="col-md-8">
											<input type="text" class="form-control" id="dropdown_onadd_part_no" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">品名<font color="red">*</font></label>
										<div class="col-md-8">
											<input type="text" class="form-control" id="dropdown_onadd_part_name" name="onadd_part_name" placeholder="" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">下種/入庫日期<font color="red">*</font></label>
										<div class="col-md-8">
											<div class="input-group">
											    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
											    <input type="text" class="form-control" id="datetimepicker1" name="onadd_planting_date" placeholder="" required minlength="1" maxlength="32">
											</div>											
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="padding-bottom: 10px;">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">
											入庫數量<font color="red">*</font>
										</label>
										<div class="col-md-8">
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
												<input type="text" class="form-control" id="dropdown_quantity_bottle" name="onadd_quantity_bottle" placeholder="瓶" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
											</div>
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">瓶</label>
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
												<input type="text" class="form-control" id="dropdown_quantity" name="onadd_quantity" placeholder="棵" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
											</div>
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">棵</label>
											<div class="help-block with-errors"></div>
										</div>
									</div>									
									<!-- 代工 -->
									<div class="form-group"  style="padding-bottom: 10px;display: none;" id="OEM">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;"><span id="foundry_name">催花價格</span><font color="red">*</font></label>
										<div class="col-md-8">
											<label id="foundry_title" for="addModalInput1" class="col-md-1 control-label" style="text-align: right;padding-left: 0px;padding-right: 0px;width:3rem; display: none;">每月</label>
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
											<input type="text" class="form-control" id="onadd_buy_price" name="onadd_buy_price" placeholder="元" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
											</div>
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">元</label>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<!-- 催花 -->
									<div class="form-group"  id="UrgeFlowers" style="padding-bottom: 10px;display: none;">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">催花價格<font color="red">*</font></label>
										<div class="col-md-8">
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: right;padding-left: 0px;padding-right: 0px;width:3rem;">每週</label>
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
												<input type="text" class="form-control" id="onadd_foundry_price" name="onadd_foundry_price" placeholder="元" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
											</div>
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">元</label>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									
									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">採購單價<font color="red">*</font></label>
										<div class="col-md-8" id="other_price">											
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
												<input type="text" class="form-control" id="onadd_buy_price_1" name="onadd_buy_price_1" placeholder="元" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
											</div>

											<label for="addModalInput1" class="col-md-8 control-label" style="text-align: left;padding-left: 0px;">元（必填，可填 0 ）</label>
											　
											<!-- <button class="btn btn-primary" id="add_more_price">新增</button> -->
											<div class="help-block with-errors"></div>
										</div>
									</div>
									
									<div class="form-group" style="padding-bottom: 10px;padding-top: 16px; ">
										<label class="col-md-3 control-label">出貨規格<font color="red">*</font></label>
										<div class="col-md-8">
											<select class="form-control" id="dropdown_onadd_sell" name="onadd_sellsize">
												<option value="7">成花</option>
												<option value="6">3.6</option>
												<option value="5">3.5</option>
												<option value="4">3.0</option>
												<option value="3">2.8</option>
												<option value="2">2.5</option>
												<option selected="selected" value="1">1.7</option>
											</select>
										</div>
									</div>
									
									<div class="form-group" >
										<label for="addModalInput1" style="text-align: right;padding-left: 0px;" class="col-md-3 control-label" >放置區<font color="red">*</font></label>
										<div class="col-md-8">
											<select class="form-control justify-content-start" id="dropdown_location" name="onadd_location">
												<option value="B5">B5</option>
												<option value="B4">B4</option>
												<option value="B3">B3</option>
												<option value="B2">B2</option>
												<option value="B1">B1</option>
												<option value="A5">A5</option>
												<option value="A4">A4</option>
												<option value="A3">A3</option>
												<option value="A2">A2</option>
												<option selected="selected" value="A1">A1</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label" >適合開花盆徑</label>
										<div class="col-md-8">
											<input type="text" class="form-control" id="onadd_pot_size" name="onadd_pot_size" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
								</div>
								
							</div>		
							<hr style="margin-top: 0rem;border-top: 2px solid #ddd;">
							<div class="row">
								<div class="col-md-12">
									<h4>供應商資訊</h4>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-1 control-label">供應商列表<font color="red">*</font></label>
										<div class="col-md-4">
											<select class="form-control" name="onsd_sn"  id='supplier_select'>
												<option value="0" selected="selected">新增廠商</option>
												<?php
												foreach ($supplier_list as $key => $value) {
													echo '<option value="'.$value['onsd_sn'].'">'.$value['onsd_name'].'</option>';
												}
												?>
											</select>
										</div>									
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-1 control-label">姓名<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="autocomplete_onadd_supplier" name="onadd_supplier" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
										<label for="addModalInput1" class="col-md-1 control-label">電話</label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="dropdown_onadd_supplier_phone" name="onadd_supplier_phone" placeholder="">
											<div class="help-block with-errors"></div>
										</div>										
									</div>	
									<div class="form-group">
										<label for="addModalInput1" class="col-md-1 control-label">地址</label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="dropdown_onadd_supplier_address" name="onadd_supplier_address" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
										<label for="addModalInput1" class="col-md-1 control-label">Email</label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="dropdown_onadd_supplier_email" name="onadd_supplier_email" placeholder="">
											<div class="help-block with-errors"></div>
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
					</div>
						
					</form>
				</div>
			</div>
		</div>

		<!-- modal -->
		<div id="adjust-modal" class="modal adjust-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="adjust_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">瓶苗資料修改</h4>
						</div>						

						<div class="modal-body">
							<div class="row">	
								<!--左邊欄位-->
								<div class="col-md-5">
									<input type="hidden" name="op" value="adjust">		
									<input type="hidden" name="onadd_sn" value="">								
									<?php if(strpos($permmsion_option, "4") !== false || $permmsion == 0){ ?>
											<h4>產品圖片預覽</h4>
											<div class="well" style="text-align: center;width: 31.5rem;height: 31.5rem;">
												<img class="preview" id="preview" style="max-width: 25rem; max-height: 25rem;">
											    <div class="size" id="preview_size" style="font-size: 1.5rem;"></div>
											</div>
									<?php } ?>
									<input type="file" id="myFile" name="myFile" style="max-width: 31.5rem;border-radius:0.5rem;font-family: '微軟正黑體';text-align: center;vertical-align: middle;" accept="image/jpeg,image/jpg,image/png">
									<div class="form-group" style="margin-top: 2.8rem;">
										<label for="addModalInput1" style="text-align: right;" class="col-md-2 control-label">花色</label>
										<div class="col-md-9" style="text-align: left;">
											<input type="text" class="form-control" id="dropdown_onadd_color" name="onadd_color" placeholder="" >
											<div class="help-block with-errors"></div>
									</div>
									<div class="form-group" >	
										</div>
										<label for="addModalInput1" style="text-align: right;" class="col-md-2 control-label">花徑</label>
										<div class="col-md-9" style="text-align: left;">
											<input type="text" class="form-control" id="dropdown_onadd_size" name="onadd_size" placeholder="" >
											<div class="help-block with-errors"></div>
									</div>
									<div class="form-group" >	
										</div>
										<label for="addModalInput1" style="text-align: right;" class="col-md-2 control-label">高度</label>
										<div class="col-md-9" style="text-align: left;">
											<input type="text" class="form-control" id="dropdown_onadd_height" name="onadd_height" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>			
									</div>
								</div>
								<!--右邊欄位-->
								<div class="col-md-7" >
									<h4>&nbsp;</h4>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;padding-bottom: 10px;">項目<font color="red">*</font></label>
										<div class="col-md-8">
											<select class="form-control" name="bill_mode"  id='bill_mode_adjust'>
												<option value="0" selected="selected">自種 / 外購</option>
												<option value="1">代工</option>
											</select>
										</div>
									</div>									
									<div class="form-group" id='order_type_adjust' style="display: none;">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;padding-bottom: 10px;"><span id="bill_request_title">代工需求</span><font color="red">*</font></label>
										<div class="col-md-8">
											<select class="form-control" name="onadd_isbought"  id='onadd_isbought_adjust'>
												<option value="0" selected="selected" class="oem_option">苗株</option>
												<option value="1" class="oem_option">催花</option>
												<option value="2" class="foundry_option">自種苗</option>
												<option value="3" class="foundry_option">外購苗</option>
											</select>
										</div>
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">品號<font color="red">*</font></label>
										<div class="col-md-8">
											<input type="text" class="form-control" id="dropdown_onadd_part_no" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">品名<font color="red">*</font></label>
										<div class="col-md-8">
											<input type="text" class="form-control" id="dropdown_onadd_part_name" name="onadd_part_name" placeholder="" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">下種/入庫日期<font color="red">*</font></label>
										<div class="col-md-8">
											<div class="input-group">
											    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
											    <input type="text" class="form-control" id="datetimepicker1" name="onadd_planting_date" placeholder="" required minlength="1" maxlength="32">
											</div>											
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="padding-bottom: 10px;">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">
											入庫數量<font color="red">*</font>
										</label>
										<div class="col-md-8">
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
												<input type="text" class="form-control" id="dropdown_quantity_bottle" name="onadd_quantity_bottle" placeholder="瓶" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
											</div>
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">瓶</label>
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
												<input type="text" class="form-control" id="dropdown_quantity" name="onadd_quantity" placeholder="棵" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
											</div>
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">棵</label>
											<div class="help-block with-errors"></div>
										</div>
									</div>									
									<!-- 代工 -->
									<div class="form-group"  style="padding-bottom: 10px;display: none;" id="OEM_adjust">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;"><span id="foundry_name_adjust">催花價格</span><font color="red">*</font></label>
										<div class="col-md-8">
											<label id="foundry_title_adjust" for="addModalInput1" class="col-md-1 control-label" style="text-align: right;padding-left: 0px;padding-right: 0px;width:3rem; display: none;">每月</label>
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
											<input type="text" class="form-control" id="onadd_buy_price" name="onadd_buy_price" placeholder="元" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
											</div>
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">元</label>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<!-- 催花 -->
									<div class="form-group"  id="UrgeFlowers_adjust" style="padding-bottom: 10px;display: none;">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">催花價格<font color="red">*</font></label>
										<div class="col-md-8">
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: right;padding-left: 0px;padding-right: 0px;width:3rem;">每週</label>
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
												<input type="text" class="form-control" id="onadd_foundry_price" name="onadd_foundry_price" placeholder="元" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
											</div>
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">元</label>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									
									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">採購單價<font color="red">*</font></label>
										<div class="col-md-8" id="other_price">											
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
												<input type="text" class="form-control" id="onadd_buy_price_1" name="onadd_buy_price_1" placeholder="元" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
											</div>

											<label for="addModalInput1" class="col-md-8 control-label" style="text-align: left;padding-left: 0px;">元（必填，可填 0 ）</label>
											　
											<!-- <button class="btn btn-primary" id="add_more_price">新增</button> -->
											<div class="help-block with-errors"></div>
										</div>
									</div>
									
									<div class="form-group" style="padding-bottom: 10px;padding-top: 16px; ">
										<label class="col-md-3 control-label">出貨規格<font color="red">*</font></label>
										<div class="col-md-8">
											<select class="form-control" id="dropdown_onadd_sell" name="onadd_sellsize">
												<option value="7">成花</option>
												<option value="6">3.6</option>
												<option value="5">3.5</option>
												<option value="4">3.0</option>
												<option value="3">2.8</option>
												<option value="2">2.5</option>
												<option selected="selected" value="1">1.7</option>
											</select>
										</div>
									</div>
									
									<div class="form-group" >
										<label for="addModalInput1" style="text-align: right;padding-left: 0px;" class="col-md-3 control-label" >放置區<font color="red">*</font></label>
										<div class="col-md-8">
											<select class="form-control justify-content-start" id="dropdown_location" name="onadd_location">
												<option value="B5">B5</option>
												<option value="B4">B4</option>
												<option value="B3">B3</option>
												<option value="B2">B2</option>
												<option value="B1">B1</option>
												<option value="A5">A5</option>
												<option value="A4">A4</option>
												<option value="A3">A3</option>
												<option value="A2">A2</option>
												<option selected="selected" value="A1">A1</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label" >適合開花盆徑</label>
										<div class="col-md-8">
											<input type="text" class="form-control" id="onadd_pot_size" name="onadd_pot_size" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
								</div>								
							</div>		
							<hr style="margin-top: 0rem;border-top: 2px solid #ddd;">
							<div class="row">
								<div class="col-md-12">
									<h4>供應商資訊</h4>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-1 control-label">供應商列表<font color="red">*</font></label>
										<div class="col-md-4">
											<select class="form-control" name="onsd_sn"  id='supplier_select_adjust'>
												<option value="0" selected="selected">新增廠商</option>
												<?php
												foreach ($supplier_list as $key => $value) {
													echo '<option value="'.$value['onsd_sn'].'">'.$value['onsd_name'].'</option>';
												}
												?>
											</select>
										</div>									
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-1 control-label">姓名<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="autocomplete_onadd_supplier" name="onadd_supplier" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
										<label for="addModalInput1" class="col-md-1 control-label">電話</label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="dropdown_onadd_supplier_phone" name="onadd_supplier_phone" placeholder="">
											<div class="help-block with-errors"></div>
										</div>										
									</div>	
									<div class="form-group">
										<label for="addModalInput1" class="col-md-1 control-label">地址</label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="dropdown_onadd_supplier_address" name="onadd_supplier_address" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
										<label for="addModalInput1" class="col-md-1 control-label">Email</label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="dropdown_onadd_supplier_email" name="onadd_supplier_email" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
									</div>	
								</div>
							</div>					
						</div>	
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="reset" class="btn btn-default">清空</button>
							<button type="submit" class="btn btn-primary">修改</button>
						</div>
					</div>
						
					</form>
				</div>
			</div>
		</div>

		<!--汰除----------------------------------------------------------->
		<div id="eli-modal1" class="modal upd-modal1" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="eli_form1" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">汰除</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd1">
									<input type="hidden" name="onadd_sn">
									<input type="hidden" name="onadd_newpot_sn">
									<input type="hidden" name="onadd_quantity_perbottle">
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">品號<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">品名<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_name" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">剩餘數量<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">汰除數量<font color="red">*</font></label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity_del" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-2 control-label">汰除原因<font color="red">*</font></label>
										<div class="col-md-10">
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
							<button type="button" class="btn btn-primary keep" data-dismiss="modal">保留</button>
							<button type="submit" class="btn btn-danger">汰除</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--汰除----------------------------------------------------------->

		<!-- container -->
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">

					<!-- nav toolbar -->
					<div class="navbar-collapse collapse pull-right" style="margin-bottom: 10px;">
						<ul class="nav nav-pills pull-right toolbar">
							<?php if($permmsion == '0' || strpos($permmsion_option, '1') !== false){ ?>
								<li><button data-parent="#toolbar" data-toggle="modal" data-target=".add-modal" class="accordion-toggle btn btn-primary"><i class="glyphicon glyphicon-plus"></i> 新品項建立</button></li>
							<?php } ?>
							<!-- <li><button data-parent="#toolbar" class="accordion-toggle btn btn-primary" onclick="javascript:location.href='./plant_purchase_addflask.php'"><i class="glyphicon glyphicon-plus"></i>新品項建立</button></li> -->
							<!-- <li><button data-parent="#toolbar" class="accordion-toggle btn btn-warning" onclick="javascript:location.href='./plant_purchase_addflask.php'"></i> 返回瓶苗資料建立</button></li> -->
						</ul>
					</div>
					<!-- search -->
					<div id="search" style="clear:both;">
						<form autocomplete="off" method="get" action="./plant_flask.php" id="search_form" class="form-inline alert alert-info" role="form">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="searchInput0">產品編號</label>
										<input type="text" class="form-control" id="searchInput0" name="onadd_sn" value="<?php echo $onadd_sn;?>" placeholder="">
									</div>
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

					<div id="qr_sticker" style="width: 410px; height: 720px;text-align:center;display:none;">
						<img id="qr_sticker_img"style="width: 400px;height: 280px;" src="">
						<div id="qr_sticker_sn" style="text-align:left;font-size: 30px;height: 40px;margin-top: 10px;"></div>
						<div id="qr_sticker_part_no" style="text-align:left;font-size: 30px;height: 40px;"></div>
						<div id="qr_sticker_part_name" style="text-align:left;font-size: 30px;height: 40px;"></div>
						<div id="qr_sticker_date" style="text-align:left;font-size: 30px;height: 40px;"></div>
						<div id="qr_sticker_location" style="text-align:left;font-size: 30px;height: 40px;"></div>
						<div style="text-align:right;">
							<img id="qr_sticker_qrcode" style="width: 150px;" src="">
						</div>	
					</div>

					<!-- content -->
					<table class="table table-striped table-hover table-condensed tablesorter">
						<thead>
							<tr style="font-size: 1.1em">
								<th style="text-align: center;">產品編號</th>
								<th style="text-align: center;">品號</th>
								<th style="text-align: center;">品名</th>
								<th style="text-align: center;">入庫日期</th>
								<th style="text-align: center;">入庫數量</th>
								<!-- <th style="text-align: center;">目前尺寸</th> -->
								<!-- <th style="text-align: center;">下階段尺寸</th>  2019/6/19新增 --> 
								<!-- <th style="text-align: center;">預計成熟日</th> -->
								<th style="text-align: center;">供應商</th>
								<!-- <th style="text-align: center;">放置區</th>  -->
								<!-- <th style="text-align: center;">備註</th> --> <!-- 2019/6/19新增 -->
								<!-- <th style="text-align: center;">育成率</th>  --><!-- 2019/6/19新增 -->
								<th style="text-align: center;">出貨規格</th> 			
								<?php 
									$flag = false;
									for($i=1;$i<7;$i++){
										if(strpos($permmsion_option, $i."") !== false){											
											$flag = true;
										}
									}

									?>	
								<?php if($permmsion == 0 || $permmsion == 3){ ?>			
									<th colspan="1" style="text-align: center;">操作</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
        					foreach ($product_list as $row) {
								echo '<tr>';
									$sn = SnProcessor($row['onadd_sn'], $row['onadd_type'], $row['onadd_newpot_sn'], $row['onadd_ml'], $row['onadd_planting_date'], $row['onadd_AB_sn'], $row['onadd_level']);
									$qr_sn = $sn;					

									if($row['onadd_level'] == "1"){
										echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$sn.'_B</td>';//產品編號
										$qr_sn = $qr_sn."_B";
									}
									else{
										echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$sn.'</td>';//產品編號
									}
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$row['onadd_part_no'].'</td>';//品號
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$row['onadd_part_name'].'</td>';//品名  							
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;">'.date('Y-m-d',$row['onadd_planting_date']).'</td>';
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;">'.(intval(($row['onadd_quantity_bottle']*$row['onadd_quantity']))+intval($row['onadd_left_quantity'])).'</td>';//數量

        							//預計成熟日
        							$cur_size = $GLOBAL['DEVICE_SYSTEM'][$row['onadd_cur_size']];
        							$growing_size = $GLOBAL['DEVICE_SYSTEM'][$row['onadd_growing']];
        							$onchba_cycle = getSettingBySn($cur_size,$growing_size)['onchba_cycle'];

        							$onadd_cycle = ((date('m',$row['onadd_cycle']))-(date('m',$row['onadd_planting_date'])));   
        							//育成率 (公式 (數量-汰除)/數量)
        							if($row['onadd_newpot_sn'] == 0){
        								if($row['onadd_ml'] == 0){
	        								$first_plant_amount = (getProductFirstQty($row['onadd_sn']) != 0 ? getProductFirstQty($row['onadd_sn']) : 1);//第一次下種時間
	        								$incubation_rate = getProductAllNowQty($row['onadd_sn'])/$first_plant_amount;
	        							}
	        							else{
	        								$first_plant_amount = (getProductFirstQty($row['onadd_ml']) != 0 ? getProductFirstQty($row['onadd_ml']) : 1);//第一次下種時間
	        								$incubation_rate = getProductAllNowQty($row['onadd_ml'])/$first_plant_amount;
	        							}
	        						}
	        						else{
	        							$first_plant_amount = getProductFirstQty($row['onadd_newpot_sn']);//第一次下種時間
	        							$incubation_rate = getProductAllNowQty($row['onadd_newpot_sn'])/$first_plant_amount;
	        						}
        							       								     							
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$row['onadd_supplier'].'</td>';
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$SellSize[$row['onadd_sellsize']].'</td>';//出貨規格  	
        							// echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;">'.number_format(($incubation_rate*100),2).'%</td>';        					
        							if($permmsion == 0 || $permmsion == 2){
        							// 2019/6/19新增 - 展開收回操作列表
        								echo '<td style="text-align:center">
	        							    <div>';
	        							if(strpos($permmsion_option, "2") !== false || $permmsion == 0){										
											echo '<button type="button" style="background-color:#E94653;" class="btn btn-danger btn-xs upd1" data-onadd_sn="'.$row['onadd_sn'].'">汰除</button>&nbsp';
										}
										// if(strpos($permmsion_option, "5") !== false || $permmsion == 0){											
										// 	echo '<button type="button" style="background-color:#6CBF87;border:#6CBF87" class="btn btn-success btn-xs upd2" data-onadd_sn="'.$row['onadd_sn'].'">出貨</button>&nbsp;';
										// }

										if(strpos($permmsion_option, "3") !== false){											
											echo '<button type="button" style="background-color:#f67828;border:#f67828" class="btn btn-danger btn-xs adjust" data-onadd_sn="'.$row['onadd_sn'].'">修改</button>&nbsp';
										}
										
	        							echo  '<button type="button" style="background-color:#A46B62;border:#A46B62" class="btn btn-primary btn-xs upd" data-onadd_sn="'.$row['onadd_sn'].'">下種</button>&nbsp;';
	        							// echo '<button type="button" class="btn btn-success btn-xs" data-onadd_sn="'.$row['onadd_sn'].'">移倉</button>&nbsp;';
        								// if($permmsion == 0 || strpos($permmsion_option, "3") !== false){
	        							// 	echo '<button type="button" style="background-color:#FCD78B;border:#FCD78B;color:#642100" class="btn btn-success btn-xs upd3" data-onadd_sn="'.$row['onadd_sn'].'">移倉</button>&nbsp;';
	        							// }
        								// if($permmsion == 0){
	        							// 	echo '<button type="button" class="btn btn-danger btn-xs del" data-onadd_sn="'.$row['onadd_sn'].'">刪除</button>&nbsp;';
	        							// }
	        						}

	        						// if($permmsion == '0' || $permmsion == '3'){
	        						// 	echo '<td><button type="button" class="btn btn-info btn-xs"  onclick="javascript:location.href=\''.WT_SERVER.'/admin/purchase/details_table.php?onadd_part_no='.$row['onadd_part_no'].'&onadd_growing='.$row['onadd_growing'].'&onadd_quantity_del='.date("Y").'\'" >展開</button>';
	        						// }

	        						// echo '<button type="button" class="btn btn-info btn-xs qr" data-onadd_sn="'.$row['onadd_sn'].'" data-qr_sn="'.$qr_sn.'">產生二維條碼</button>&nbsp;
	        						echo '
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
    </html>?>