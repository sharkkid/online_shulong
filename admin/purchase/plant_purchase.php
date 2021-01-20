<?php
include_once("./func_plant_purchase.php");
include_once("./../sys/func.php");

$qr_sn = "";
$status_mapping = array(0=>'<font color="red">關閉</font>', 1=>'<font color="blue">啟用</font>');

$permissions_mapping = getMapping_size();
// printr(getProductAllNowQty(84));

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
		$onadd_pot_size=GetParam('onadd_pot_size');//適合開花盆徑
		$onadd_supplier=GetParam('onadd_supplier');//供應商
		$onadd_supplier_phone=GetParam('onadd_supplier_phone');//供應商電話
		$onadd_supplier_address=GetParam('onadd_supplier_address');//供應商地址
		$onadd_supplier_email=GetParam('onadd_supplier_email');//供應商email
		$onadd_plant_staff=GetParam('onadd_plant_staff');//種植人員
		$onadd_location=GetParam('onadd_location');//放置區
		$onadd_planting_date=GetParam('onadd_planting_date');//下種日期
		// $onadd_quantity=GetParam('onadd_quantity');//下種數量

		$onadd_cost_plant=GetParam('onadd_cost_plant');//苗株費用
		$onadd_cost_cup=GetParam('onadd_cost_cup');//軟杯費用
		$onadd_cost_grass=GetParam('onadd_cost_grass');//水草費用
		$onadd_cost_labour=GetParam('onadd_cost_labour');//人工費用
		$onadd_cost_month=GetParam('onadd_cost_month');//種植費用
		$onadd_buy_price_A=GetParam('onadd_buy_price_A');//採購費用
		$onadd_buy_price_B=GetParam('onadd_buy_price_B');//採購費用
		if(empty($onadd_buy_price_A)){
			$onadd_buy_price_A=GetParam('onadd_foundry_price_A');
			$onadd_buy_price_B=GetParam('onadd_foundry_price_B');
		}

		$onadd_buy_item_1="採購單價";//採購單價
		$onadd_buy_price_1=GetParam('onadd_buy_price_1');//其他價格-金額
		$json['name']=$onadd_buy_item_1;
		$json['price']=$onadd_buy_price_1;
		$onadd_other_price = urldecode(json_encode($json,JSON_UNESCAPED_UNICODE));
		$onadd_next_status=GetParam('onadd_next_status');//下一階段狀態
		$onadd_cur_size=GetParam('onadd_cur_size');//目前尺寸
		$onadd_growing=GetParam('onadd_growing');//預計成長大小		
		if($onadd_next_status == '2'){
			$onadd_growing="催花";
			$onadd_specification=GetParam('onadd_specification_other');//催花規格
		}
		else if($onadd_next_status == '3'){
			$onadd_growing="出貨";
		}

		$onadd_sellsize=GetParam('onadd_sellsize');//預計出貨尺寸
		$onadd_quantity_shi=GetParam('onadd_quantity_shi');//換盆年
		$onadd_quantity_cha=$test;//換盆月

		if(GetParam('onadd_plant_day_A') != "")
			$onadd_replant_number_A=GetParam('onadd_plant_day_A');//A 下種數量
		else
			$onadd_replant_number_A=0;
		if(GetParam('onadd_plant_day_B') != "")
			$onadd_replant_number_B=GetParam('onadd_plant_day_B');//B 下種數量
		else
			$onadd_replant_number_B=0;

		$total = $onadd_replant_number_A + $onadd_replant_number_B;//AB苗總數
		$all_plant_number = Array();
		$all_plant_number[0][0] = $onadd_replant_number_A;
		$all_plant_number[1][0] = $onadd_replant_number_B;
		$all_plant_number[0][1] = $onadd_buy_price_A;
		$all_plant_number[1][1] = $onadd_buy_price_B;
		
		$onadd_id = 0;//最後新增onadd的id

		$onsd_sn=GetParam('onsd_sn');
		if($onsd_sn == "0"){
			addSupplierData($onadd_supplier,$onadd_supplier_address,$onadd_supplier_phone,$onadd_supplier_email);
		}

		foreach ($all_plant_number as $key => $plant_number) {
			$onadd_quantity = $plant_number[0];
			$onadd_buy_price = $plant_number[1];
			$onadd_status=GetParam('onadd_status');//狀態 1 啟用 0 刪除
			$onproduct_pic_url=GetParam('onproduct_pic_url');//產品圖片
			$IsUploadImg=IsNewProduct($onadd_part_no,$onadd_part_name);//產品是否已存在
			if(!empty($onproduct_pic_url))
				$onproduct_pic_url = ".".$onproduct_pic_url;
			$jsuser_sn = GetParam('supplier');//編輯人員
			$onadd_type=GetParam('bill_mode');//;0:自種、1:代工

			if(empty($onadd_part_no)||empty($onadd_planting_date)||empty($onadd_replant_number_A)){
				if($onadd_next_status == '1'){
					$IsSetCycle=getTargetSize($DEVICE_SYSTEM[$onadd_cur_size],$DEVICE_SYSTEM[$onadd_growing]);//是否有此種植週期
					if(empty($IsSetCycle))
						$ret_msg = "尚未設定該原始尺寸至預計成長尺寸！";
					else
						$ret_msg = "*為必填！";
				}
				else{
					$ret_msg = "*為必填！";
				}
			} else { 			
				if($onadd_quantity != "0"){
					if($IsUploadImg == "0"){
						$user = getPlantDataByAccount($onadd_part_no);
						$onadd_planting_date = strtotime(GetParam('onadd_planting_date'));
						$now = time();
						$conn = getDB();
							$sql = "INSERT INTO onliine_add_data (onadd_add_date, onadd_mod_date, onadd_part_no, onadd_part_name, onadd_color, onadd_size, onadd_height, onadd_pot_size, onadd_supplier, onadd_planting_date, onadd_quantity, onadd_growing, onadd_status, jsuser_sn, onadd_cycle, onadd_isbought, onadd_plant_st, onadd_location, onadd_cur_size, onadd_sellsize, onadd_buy_price, onadd_next_status,onadd_type, onadd_level, onadd_supplier_email, onadd_supplier_phone, onadd_supplier_address, onadd_AB_sn, onadd_other_price, onadd_specification,onadd_plant_staff) " .
							"VALUES ('{$now}', '{$now}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_planting_date}', '{$onadd_quantity}', '{$onadd_growing}', '1', '{$jsuser_sn}', '{$now}', '{$onadd_isbought}', '1', '{$onadd_location}', '{$onadd_cur_size}', '$onadd_sellsize', '{$onadd_buy_price}', '{$onadd_next_status}','{$onadd_type}', '{$key}', '{$onadd_supplier_email}', '{$onadd_supplier_phone}', '{$onadd_supplier_address}', '{$onadd_id}', '{$onadd_other_price}', '{$onadd_specification}','{$onadd_plant_staff}');";

							$sql2 = "INSERT INTO onliine_product_data(onproduct_add_date, onproduct_date, onproduct_status, jsuser_sn, onproduct_part_no, onproduct_part_name, onproduct_color, onproduct_size, onproduct_height, onproduct_pot_size, onproduct_supplier, onproduct_growing, onproduct_isbought, onproduct_plant_st,onproduct_pic_url) " .
							"VALUES ('{$now}', '{$now}', '1', '{$jsuser_sn}' , '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_growing}', '{$onadd_isbought}', '1', '{$onproduct_pic_url}');";

							if($conn->query($sql)) {
								$onadd_id = mysqli_insert_id($conn);
								if(IsProductExit($onadd_part_no,$onadd_part_name)=="0"){
									$conn->query($sql2);
								}
								$sql3 = "INSERT INTO `onliine_firstplant_data`(`onfp_add_date`, `onfp_plant_date`, `jsuser_sn`, `onfp_plant_amount`,`onfp_part_no`,onadd_sn) VALUES ('{$now}', '{$onadd_planting_date}','{$jsuser_sn}','{$onadd_quantity}','{$onadd_part_no}','{$onadd_id}');";
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
					else{
						// $user = getPlantDataByAccount($onadd_part_no);
						$onadd_planting_date = strtotime(GetParam('onadd_planting_date'));
						$now = time();
						$conn = getDB();
						$ret_msg = $IsUploadImg;
							$sql = "INSERT INTO onliine_add_data (onadd_add_date, onadd_mod_date, onadd_part_no, onadd_part_name, onadd_color, onadd_size, onadd_height, onadd_pot_size, onadd_supplier, onadd_planting_date, onadd_quantity, onadd_growing, onadd_status, jsuser_sn, onadd_cycle, onadd_isbought, onadd_plant_st, onadd_location, onadd_cur_size, onadd_sellsize, onadd_buy_price, onadd_next_status,onadd_type, onadd_level, onadd_supplier_email, onadd_supplier_phone, onadd_supplier_address, onadd_AB_sn, onadd_other_price, onadd_specification) " .
							"VALUES ('{$now}', '{$now}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_planting_date}', '{$onadd_quantity}', '{$onadd_growing}', '1', '{$jsuser_sn}', '{$now}', '{$onadd_isbought}', '1', '{$onadd_location}', '{$onadd_cur_size}', '$onadd_sellsize', '{$onadd_buy_price}', '{$onadd_next_status}','{$onadd_type}', '{$key}', '{$onadd_supplier_email}', '{$onadd_supplier_phone}', '{$onadd_supplier_address}', '{$onadd_id}', '{$onadd_other_price}', '{$onadd_specification}');";

							$sql2 = "UPDATE onliine_product_data SET onproduct_part_no = '{$onadd_part_no}', onproduct_part_name = '{$onadd_part_name}', onproduct_color = '{$onadd_color}', onproduct_size = '{$onadd_size}', onproduct_height = '{$onadd_height}', onproduct_pot_size = '{$onadd_pot_size}', onproduct_supplier = '{$onadd_supplier}', onproduct_growing = '{$onadd_growing}', onproduct_isbought = '{$onadd_isbought}',onproduct_pic_url = '{$onproduct_pic_url}' 
							    WHERE onproduct_part_no like '{$onadd_part_no}' and onproduct_part_name like '{$onadd_part_name}';";

							if($conn->query($sql)) {
								$onadd_id = mysqli_insert_id($conn);						
								$conn->query($sql2);
								$sql3 = "INSERT INTO `onliine_firstplant_data`(`onfp_add_date`, `onfp_plant_date`, `jsuser_sn`, `onfp_plant_amount`,`onfp_part_no`,onadd_sn) VALUES ('{$now}', '{$onadd_planting_date}','{$jsuser_sn}','{$onadd_quantity}','{$onadd_part_no}','{$onadd_id}');";
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
			}
		}
		break;

		case 'adjust':
		$onadd_sn=GetParam('onadd_sn');
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
		$onadd_supplier_phone=GetParam('onadd_supplier_phone');//供應商電話
		$onadd_supplier_address=GetParam('onadd_supplier_address');//供應商地址
		$onadd_supplier_email=GetParam('onadd_supplier_email');//供應商email
		$onadd_plant_staff=GetParam('onadd_plant_staff');//種植人員
		$onadd_location=GetParam('onadd_location');//放置區
		$onadd_planting_date=GetParam('onadd_planting_date');//下種日期
		// $onadd_quantity=GetParam('onadd_quantity');//下種數量

		$onadd_cost_plant=GetParam('onadd_cost_plant');//苗株費用
		$onadd_cost_cup=GetParam('onadd_cost_cup');//軟杯費用
		$onadd_cost_grass=GetParam('onadd_cost_grass');//水草費用
		$onadd_cost_labour=GetParam('onadd_cost_labour');//人工費用
		$onadd_cost_month=GetParam('onadd_cost_month');//種植費用

		if(empty($onadd_buy_price_A)){
			$onadd_buy_price_A=GetParam('onadd_foundry_price_A');
			$onadd_buy_price_B=GetParam('onadd_foundry_price_B');
		}

		$onadd_buy_item_1="採購單價";//採購單價
		$onadd_buy_price_1=GetParam('onadd_buy_price_1');//其他價格-金額
		$json['name']=$onadd_buy_item_1;
		$json['price']=$onadd_buy_price_1;
		$onadd_other_price = urldecode(json_encode($json,JSON_UNESCAPED_UNICODE));
		$onadd_next_status=GetParam('onadd_next_status');//下一階段狀態
		$onadd_cur_size=GetParam('onadd_cur_size');//目前尺寸
		$onadd_growing=GetParam('onadd_growing');//預計成長大小
		if($onadd_next_status == '2'){
			$onadd_specification=GetParam('onadd_specification');
			$onadd_growing="催花";
			if($onadd_specification == "其他"){
				$onadd_specification=GetParam('onadd_specification_other');
			}
		}
		else if($onadd_next_status == '3'){
			$onadd_growing="出貨";
		}

		$onadd_sellsize=GetParam('onadd_sellsize');//預計出貨尺寸
		$onadd_quantity_shi=GetParam('onadd_quantity_shi');//換盆年
		$onadd_quantity_cha=$test;//換盆月

		if(GetParam('onadd_plant_day_A') != "")
			$onadd_quantity=GetParam('onadd_plant_day_A');//A 下種數量

		if(GetParam('onadd_plant_day_B') != "")
			$onadd_quantity=GetParam('onadd_plant_day_B');//B 下種數量

		if(GetParam('onadd_buy_price_A') != "")
			$onadd_buy_price=GetParam('onadd_buy_price_A');
		if(GetParam('onadd_buy_price_B') != "")
			$onadd_buy_price=GetParam('onadd_buy_price_B');
		if(GetParam('onadd_foundry_price_A') != "")
			$onadd_buy_price=GetParam('onadd_foundry_price_A');
		if(GetParam('onadd_foundry_price_B') != "")
			$onadd_buy_price=GetParam('onadd_foundry_price_B');

		$onadd_isbought=GetParam('onadd_isbought');
		$onadd_type=GetParam('bill_mode');


		$onproduct_pic_url=GetParam('onproduct_pic_url');//產品圖片
		$IsUploadImg=IsNewProduct($onadd_part_no,$onadd_part_name);//產品是否已存在
		if(!empty($onproduct_pic_url))
			$onproduct_pic_url = ".".$onproduct_pic_url;
		// $jsuser_sn = GetParam('supplier');//編輯人員
		if($onadd_growing == "出貨")
			$IsSetCycle=getTargetSize($DEVICE_SYSTEM[$onadd_cur_size],"出貨");//是否有此種植週期
		elseif ($onadd_growing == "催花") {
			$IsSetCycle=getTargetSize($DEVICE_SYSTEM[$onadd_cur_size],"催花");//是否有此種植週期
		}
		else{
			$IsSetCycle=getTargetSize($DEVICE_SYSTEM[$onadd_cur_size],$DEVICE_SYSTEM[$onadd_growing]);//是否有此種植週期
		}
		if(empty($IsSetCycle)){
			$ret_msg = "尚未設定 ".$DEVICE_SYSTEM[$onadd_cur_size]." 至 ".$DEVICE_SYSTEM[$onadd_growing]." 的成長周期！";
		}
		else{
			if(empty($onadd_part_no)||empty($onadd_planting_date)||empty($onadd_quantity)||empty($onadd_growing)||empty($IsSetCycle)){
					$ret_msg = "*為必填！";
			} else { 
				$user = getPlantDataByAccount($onadd_part_no);
				$onadd_planting_date = str2time($onadd_planting_date);
				$now = time();
				$conn = getDB();

				// 修改換盆後更新Log
			   $sql_newpot_sn = "SELECT onadd_newpot_sn FROM onliine_add_data WHERE onadd_sn='{$onadd_sn}';";
			   $onadd_newpot_sn = $conn->query($sql_newpot_sn)->fetch_assoc()['onadd_newpot_sn'];
			   if ($onadd_newpot_sn != 0 ) {
			    sql_onliine_basin_log($onadd_newpot_sn,$onadd_cur_size,$onadd_sellsize,$onadd_sn,"",'update');
			   }

				if($IsUploadImg == "0"){
					$sql = "UPDATE onliine_add_data	SET onadd_part_no ='{$onadd_part_no}',onadd_part_name='{$onadd_part_name}',onadd_color='{$onadd_color}'	,onadd_size='{$onadd_size}',onadd_height='{$onadd_height}',onadd_pot_size='{$onadd_pot_size}',onadd_supplier='{$onadd_supplier}',onadd_planting_date='{$onadd_planting_date}',onadd_quantity='{$onadd_quantity}',onadd_growing='{$onadd_growing}', onadd_location='{$onadd_location}', onadd_cur_size='{$onadd_cur_size}', onadd_buy_price='{$onadd_buy_price}',onadd_sellsize = '{$onadd_sellsize}', onadd_isbought='{$onadd_isbought}', onadd_type='{$onadd_type}', onadd_other_price='{$onadd_other_price}', onadd_next_status='{$onadd_next_status}', onadd_specification='{$onadd_specification}',onadd_plant_staff = '{$onadd_plant_staff}' WHERE onadd_sn='{$onadd_sn}';";
				
					if($conn->query($sql)) {
						$ret_msg = "修改成功！";
					} else {
						$ret_msg = "修改失敗！";
					}
				}
				else{
					$sql = "UPDATE onliine_add_data	SET onadd_part_no ='{$onadd_part_no}',onadd_part_name='{$onadd_part_name}',onadd_color='{$onadd_color}'	,onadd_size='{$onadd_size}',onadd_height='{$onadd_height}',onadd_pot_size='{$onadd_pot_size}',onadd_supplier='{$onadd_supplier}',onadd_planting_date='{$onadd_planting_date}',onadd_quantity='{$onadd_quantity}',onadd_growing='{$onadd_growing}', onadd_location='{$onadd_location}', onadd_cur_size='{$onadd_cur_size}', onadd_buy_price='{$onadd_buy_price}',onadd_sellsize = '{$onadd_sellsize}' ,onadd_sellsize = '{$onadd_sellsize}', onadd_isbought='{$onadd_isbought}', onadd_type='{$onadd_type}', onadd_other_price='{$onadd_other_price}', onadd_next_status='{$onadd_next_status}', onadd_specification='{$onadd_specification}' ,onadd_plant_staff = '{$onadd_plant_staff}'  WHERE onadd_sn='{$onadd_sn}';";

					$sql2 = "UPDATE onliine_product_data SET onproduct_pic_url = '{$onproduct_pic_url}' WHERE onproduct_part_no='{$onadd_part_no}' AND onproduct_part_name = '{$onadd_part_name}';";
				
					if($conn->query($sql)) {
						$ret_msg = "修改成功！";
						if($conn->query($sql2)){
							$ret_msg = "修改成功！";
						}
					} else {
						$ret_msg = "修改失敗！";
					}
				}
				// $ret_msg .= ",IsUploadImg=".$IsUploadImg.",onproduct_pic_url=".$onproduct_pic_url;
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
			$ret_data = getPlantDataSn($onadd_sn);
		} else {
			$ret_code = 0;
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
			$ret_msg = "*為必填！";
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
		$onadd_AB_sn = GetParam('onadd_AB_sn');//AB苗原始編號
		$onadd_ml = GetParam('onadd_ml');//轉移原始日
		$onadd_other_price = GetParam('onadd_other_price');//採購單價

		if($onadd_AB_sn == '0'){
			if($onadd_newpot_sn == '0'){
				if($onadd_ml == '0'){				
					$sn=$onadd_sn;
				}				
				else{
					$sn=GetParam('onadd_ml');
				}
			}
			else{
				$sn=GetParam('onadd_newpot_sn');
			}
		}
		else{
			$sn=GetParam('onadd_AB_sn');
		}

		$onadd_add_date = GetParam('onadd_add_date');//新增日期
		$onadd_stock_status=GetParam('onadd_stock_status');//狀態
		$onadd_now_level=GetParam('onadd_now_level');//AB苗種
		$onadd_level=GetParam('onadd_level');//AB苗種
		$onadd_type=GetParam('onadd_type');//類別
		$onadd_next_status=GetParam('onadd_next_status');//下階段狀態
		$onadd_ml_amount=GetParam('onadd_ml_amount');//移倉數量
		$onadd_supplier=GetParam('onadd_supplier');//供應商
		$onadd_planting_date=GetParam('onadd_planting_date');//下種日期
		$onadd_quantity=GetParam('onadd_quantity');//下種數量
		$onadd_cur_size=GetParam('onadd_cur_size');//目前尺寸
		$onadd_growing=GetParam('onadd_growing');//預計成長大小
		$onadd_sellsize=GetParam('onadd_sellsize');//預計出貨尺寸
		$onadd_buy_price=GetParam('onadd_buy_price');//採購單價
		$jsuser_sn = GetParam('supplier');//編輯人員
		$left_amount = $onadd_quantity - $onadd_ml_amount;//移倉後剩餘數量
		$AllNowQty = getProductAllNowQty($onadd_sn,$onadd_level);
		$Allleft_amount = $AllNowQty - $onadd_ml_amount;//更新第一次下種數量

		$user = getPlantDataByAccount($onadd_part_no);
		$onadd_planting_date = str2time($onadd_planting_date);
		$now = time();
		$conn = getDB();
		if(empty($onadd_location) || empty($onadd_ml_amount)){
			$ret_msg = "*為必填！";
		}
		else if($left_amount < 0){
			$ret_msg = "轉移/更換數量大於庫存數量！";
		} 
		else {
			if($onadd_stock_status == "0"){
				$sql = "UPDATE onliine_add_data	SET onadd_part_no ='{$onadd_part_no}',onadd_part_name='{$onadd_part_name}',onadd_color='{$onadd_color}'	,onadd_size='{$onadd_size}',onadd_height='{$onadd_height}',onadd_pot_size='{$onadd_pot_size}',onadd_supplier='{$onadd_supplier}'	,onadd_planting_date='{$onadd_planting_date}',onadd_quantity='{$left_amount}',onadd_growing='{$onadd_growing}',jsuser_sn='{$supplier}', onadd_location='{$onadd_location_old}', onadd_cur_size='{$onadd_cur_size}' WHERE onadd_sn='{$onadd_sn}';";
				
				$sql2 = "INSERT INTO onliine_add_data(onadd_part_no,onadd_part_name,onadd_color,onadd_size,onadd_height,onadd_pot_size,onadd_supplier,onadd_planting_date,onadd_quantity,onadd_growing,jsuser_sn,onadd_location,onadd_cur_size,onadd_ml,onadd_add_date,onadd_mod_date,onadd_cycle, onadd_newpot_sn, onadd_sellsize, onadd_buy_price, onadd_type, onadd_next_status, onadd_level, onadd_other_price)	
						VALUES('{$onadd_part_no}','{$onadd_part_name}','{$onadd_color}','{$onadd_size}','{$onadd_height}','{$onadd_pot_size}','{$onadd_supplier}','{$onadd_planting_date}','{$onadd_ml_amount}','{$onadd_growing}','{$supplier}','{$onadd_location}','{$onadd_cur_size}','{$sn}', '{$now}', '{$now}', '{$now}' ,'{$onadd_newpot_sn}', '{$onadd_sellsize}', '{$onadd_buy_price}', '{$onadd_type}', '{$onadd_next_status}', '{$onadd_now_level}', '{$onadd_other_price}')";

				$sql3 = "UPDATE onliine_add_data SET onadd_part_no ='{$onadd_part_no}',onadd_part_name='{$onadd_part_name}',onadd_color='{$onadd_color}'	,onadd_size='{$onadd_size}',onadd_height='{$onadd_height}',onadd_pot_size='{$onadd_pot_size}',onadd_supplier='{$onadd_supplier}'	,onadd_planting_date='{$onadd_planting_date}',onadd_quantity='{$left_amount}',onadd_growing='{$onadd_growing}',jsuser_sn='{$supplier}', onadd_location='{$onadd_location}', onadd_cur_size='{$onadd_cur_size}', onadd_status = '-1' WHERE onadd_sn='{$onadd_sn}' and onadd_status = '1';";		
				// $sql2 = "UPDATE onliine_firstplant_data	SET onfp_plant_amount = '{$onadd_quantity}' WHERE onadd_sn='{$onadd_sn}' and onfp_status >= 1;";			
		
				if($conn->query($sql)) {
					$ret_msg = "更新成功！";
					if($left_amount == 0){
						$conn->query($sql3);
					} 
					if($conn->query($sql2)) {
						$ret_msg = "更新成功！";
					}
					else{
						$ret_msg = "更新失敗！";
					}
				} 
				else {
					$ret_msg = "更新失敗！";
				}
			}
			else{
				$sql = "UPDATE onliine_add_data	SET onadd_quantity='{$left_amount}' WHERE onadd_sn='{$onadd_sn}';";

				// $sql_adjust_firstplant = "UPDATE onliine_firstplant_data SET onfp_plant_amount = '{$Allleft_amount}' WHERE onadd_sn='{$onadd_sn}';"		
				// if($conn->query($sql)) {			
					// $conn->query($sql_adjust_firstplant);		
					// $data = getABdataByDateAndLevel($onadd_sn);
					// $onadd_ml_amount += $data['onadd_quantity'];
					// $Allml_amount += $onadd_ml_amount;
					// $onadd_sn = $data['onadd_sn'];

					// $sql2 = "UPDATE onliine_add_data SET onadd_part_no ='{$onadd_part_no}',onadd_part_name='{$onadd_part_name}',onadd_color='{$onadd_color}'	,onadd_size='{$onadd_size}',onadd_height='{$onadd_height}',onadd_pot_size='{$onadd_pot_size}',onadd_supplier='{$onadd_supplier}' ,onadd_planting_date='{$onadd_planting_date}',onadd_quantity='{$onadd_ml_amount}',onadd_growing='{$onadd_growing}',jsuser_sn='{$supplier}', onadd_location='{$onadd_location_old}', onadd_cur_size='{$onadd_cur_size}', onadd_status = 1 WHERE onadd_sn='{$onadd_sn}';";

					// $sql_adjust_firstplant = "UPDATE onliine_firstplant_data SET onfp_plant_amount = '{$Allml_amount}' WHERE onadd_sn='{$onadd_sn}';";

					if($onadd_AB_sn != "0"){
						if(IsABLevelExist($onadd_AB_sn)){
							$data = getABdataByDateAndLevel($onadd_AB_sn);
							$onadd_ml_amount += $data['onadd_quantity'];
							$Allml_amount += $onadd_ml_amount;
							$onadd_sn = $onadd_AB_sn;

							$sql2 = "UPDATE onliine_add_data SET onadd_quantity='{$Allml_amount}' WHERE onadd_sn='{$onadd_sn}';";
						}
					}
					else{
						if($data['onadd_level'] == "0")
							$onadd_level = "1";
						else
							$onadd_level = "0";

						$sql2 = "INSERT INTO onliine_add_data (onadd_add_date, onadd_mod_date, onadd_part_no, onadd_part_name, onadd_color, onadd_size, onadd_height, onadd_pot_size, onadd_supplier, onadd_planting_date, onadd_quantity, onadd_growing, onadd_status, jsuser_sn, onadd_cycle, onadd_isbought, onadd_plant_st, onadd_location, onadd_cur_size, onadd_sellsize, onadd_buy_price, onadd_next_status,onadd_type, onadd_level, onadd_supplier_email, onadd_supplier_phone, onadd_supplier_address, onadd_AB_sn, onadd_other_price, onadd_specification) " .
						"VALUES ('{$now}', '{$now}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_planting_date}', '{$onadd_quantity}', '{$onadd_growing}', '1', '{$jsuser_sn}', '{$now}', '{$onadd_isbought}', '1', '{$onadd_location}', '{$onadd_cur_size}', '$onadd_sellsize', '{$onadd_buy_price}', '{$onadd_next_status}','{$onadd_type}', '{$onadd_level}', '{$onadd_supplier_email}', '{$onadd_supplier_phone}', '{$onadd_supplier_address}', '{$onadd_sn}', '{$onadd_other_price}', '{$onadd_specification}');";
					}

					// if($conn->query($sql2)) {
					// 	// $conn->query($sql_adjust_firstplant);
					// 	$ret_msg = "更新成功！";
					// }
					// else{
					// 	$ret_msg = "更新失敗！";
					// }

					// if($left_amount == 0){
					// 	$conn->query($sql3);						
					// } 

				// }
				// else {
				// 	$ret_msg = "更新失敗！";
				// }
			}
		}
		$conn->close();
		
		break;

		//換盆
		case 'upd':
		$onadd_sn=GetParam('onadd_sn');
		$onadd_newpot_sn=GetParam('onadd_newpot_sn');
		$onadd_AB_sn=GetParam('onadd_AB_sn');
		$onadd_level=GetParam('onadd_level');
		$onadd_ml=GetParam('onadd_ml');

		if($onadd_AB_sn == '0'){
			if($onadd_newpot_sn == '0'){
				if($onadd_ml == '0'){				
					$sn=$onadd_sn;
				}				
				else{
					$sn=GetParam('onadd_ml');
				}
			}
			else{
				$sn=GetParam('onadd_newpot_sn');
			}
		}
		else{
			$sn=GetParam('onadd_AB_sn');
		}

		$onadd_add_date=GetParam('onadd_add_date');//建立日期
		$onadd_mod_date=GetParam('onadd_mod_date');//修改日期
		$onadd_part_no=GetParam('onadd_part_no');//品號
		$onadd_part_name=GetParam('onadd_part_name');//品名
		$onadd_color=GetParam('onadd_color');//花色
		$onadd_size=GetParam('onadd_size');//花徑
		$onadd_height=GetParam('onadd_height');//高度
		$onadd_location=GetParam('onadd_location');//放置區
		$onadd_pot_size=GetParam('onadd_pot_size');//適合開花盆徑
		$onadd_supplier=GetParam('onadd_supplier');//供應商
		$onadd_planting_date=GetParam('onadd_planting_date');//下種日期
		$onadd_quantity=GetParam('onadd_quantity');//下種數量
		// $onadd_next_status=GetParam('onadd_next_status');//下一階段狀態
		$onadd_replant_number=GetParam('onadd_replant_number');//換盆數量
		$onadd_type=GetParam('onadd_type');//0:自種、1:代工
		$onadd_plant_staff=GetParam('onadd_plant_staff');//種植人員
		$onadd_price_per_plant=GetParam('onadd_price_per_plant');//種植費用
		$onadd_other_price=GetParam('onadd_other_price');//其他價格

		$onadd_quantity_cha123 =($onadd_quantity - $onadd_replant_number);

		$onadd_status = ($onadd_quantity_cha123 < 0) ? -1 : 1;
		$first_n_changed = getProductFirstQty($onadd_sn) - $onadd_replant_number;

		$onadd_growing=GetParam('onadd_growing');//預計成長大小
		$flag = false;
		if($onadd_growing == "UrgeFLowers"){
			if(!empty(GetParam('onadd_foundry_type')))
				$onadd_growing=GetParam('onadd_foundry_type');//催花規格
			else
				$onadd_growing="催花";
			$onadd_next_status = 2;
		}
		else if($onadd_growing == "Shipment"){
			$onadd_growing='出貨';//出貨
			$onadd_next_status = 3;
		}
		else{
			$flag = true;
			$onadd_next_status = 1;
		}

		$onadd_cur_size=GetParam('onadd_cur_size');//換盆尺寸
		$onadd_sellsize=GetParam('onadd_sellsize');//預估出貨尺寸
		// $onadd_status=GetParam('onadd_status');//狀態 1 啟用 0 刪除
		$jsuser_sn = GetParam('supplier');//編輯人員
		if($flag)
			$IsSetCycle=getTargetSize($DEVICE_SYSTEM[$onadd_cur_size],$DEVICE_SYSTEM[$onadd_growing]);//是否有此種植週期		
		else
			$IsSetCycle=getTargetSize($DEVICE_SYSTEM[$onadd_cur_size],$onadd_growing);//是否有此種植週期	

		if(empty($IsSetCycle)){
			$ret_msg = "尚未設定 ".$DEVICE_SYSTEM[$onadd_cur_size]." 至 ".$DEVICE_SYSTEM[$onadd_growing]." 的成長周期！";
			break;
		}

		if(empty($onadd_planting_date)||empty($onadd_quantity)||empty($onadd_growing)){
			$ret_msg = "*為必填！";			
		} 
		else { 			
			$user = getPlantDataByAccount($onadd_part_no);
			$onadd_planting_date = str2time($onadd_planting_date);
			$now = time();
			$conn = getDB();
			$onadd_cost = array();			
			if($onadd_status != -1) {

			   //  if ($onadd_type == 1) {
			   //  	// 代工價格    
				  //   $sql_previous_cost = "SELECT onadd_buy_price FROM onliine_add_data where onadd_sn='{$onadd_sn}'";
				  //   $previous_cost = $conn->query($sql_previous_cost)->fetch_assoc()['onadd_buy_price'];
				  //   $this_time_cost = computing_costs($sn,$onadd_sn,$onadd_planting_date);
			   //  	$total_cost = $this_time_cost;
			   //  	$total_cost = $total_cost+$onadd_price_per_plant;
			   //  	$sql = "INSERT INTO onliine_add_data (onadd_add_date, onadd_mod_date, onadd_part_no, onadd_part_name, onadd_color, onadd_size, onadd_height, onadd_pot_size, onadd_supplier, onadd_planting_date, onadd_quantity,onadd_buy_price,onadd_quantity_cha, onadd_growing, onadd_status, jsuser_sn, onadd_cycle, onadd_newpot_sn, onadd_cur_size, onadd_location, onadd_sellsize, onadd_next_status, onadd_plant_staff, onadd_price_per_plant, onadd_other_price, onadd_type, onadd_level,onadd_cost_plant) " .
						// "VALUES ('{$now}', '{$now}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_planting_date}', '{$onadd_replant_number}','{$previous_cost}','{$onadd_replant_number}', '{$onadd_growing}', '1', '{$jsuser_sn}', '{$now}', '{$sn}', '{$onadd_cur_size}', '{$onadd_location}', '{$onadd_sellsize}', '{$onadd_next_status}', '{$onadd_plant_staff}', '{$onadd_price_per_plant}', '{$onadd_other_price}', '{$onadd_type}', '{$onadd_level}','{$total_cost}');";	
			   //  }else{
					// 上次換盆成本價格    
				    $sql_total_last_cost = "SELECT onadd_basin_cost FROM onliine_add_data where onadd_sn='{$onadd_sn}'";
				    $last_total_cost = $conn->query($sql_total_last_cost)->fetch_assoc()['onadd_buy_price'];
				    // 換盆總花費
				    $this_time_cost = computing_costs($sn,$onadd_sn,$onadd_planting_date);
				    
				  //   // 累計加總
				    $total_cost = $this_time_cost+$last_total_cost;
				    $sql = "INSERT INTO onliine_add_data (onadd_add_date, onadd_mod_date, onadd_part_no, onadd_part_name, onadd_color, onadd_size, onadd_height, onadd_pot_size, onadd_supplier, onadd_planting_date, onadd_quantity,onadd_basin_cost,onadd_quantity_cha, onadd_growing, onadd_status, jsuser_sn, onadd_cycle, onadd_newpot_sn, onadd_cur_size, onadd_location, onadd_sellsize, onadd_next_status, onadd_plant_staff, onadd_price_per_plant, onadd_other_price, onadd_type, onadd_level) " .
						"VALUES ('{$now}', '{$now}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_planting_date}', '{$onadd_replant_number}','{$total_cost}','{$onadd_replant_number}', '{$onadd_growing}', '1', '{$jsuser_sn}', '{$now}', '{$sn}', '{$onadd_cur_size}', '{$onadd_location}', '{$onadd_sellsize}', '{$onadd_next_status}', '{$onadd_plant_staff}', '{$onadd_price_per_plant}', '{$onadd_other_price}', '{$onadd_type}', '{$onadd_level}');";
			    // }
				if($conn->query($sql)){
					$onadd_id = mysqli_insert_id($conn);

					// 換盆log----------------strat  
				    sql_onliine_basin_log($sn,$onadd_cur_size,$onadd_sellsize,$onadd_sn,$onadd_id,'add');
				    // 換盆前log----------------end

					//新增第一筆下種數量紀錄
					// $sql2 = "INSERT INTO `onliine_firstplant_data`(`onfp_add_date`, `onfp_plant_date`, `jsuser_sn`, `onfp_plant_amount`,`onfp_part_no`,onadd_sn) VALUES ('{$now}', '{$onadd_planting_date}','{$jsuser_sn}','{$onadd_replant_number}','{$onadd_part_no}','{$onadd_id}');";
					//更新原本產品數量 (扣除換盆)
					$sql1 = "UPDATE onliine_add_data SET onadd_quantity='{$onadd_quantity_cha123}', onadd_status='{$onadd_status}' WHERE onadd_sn='{$onadd_sn}'";
					//更新原本產品的第一筆下種數量(扣除換盆)
					// $sql3 = "UPDATE onliine_firstplant_data SET onfp_plant_amount='{$first_n_changed}' WHERE onadd_sn='{$onadd_sn}'";
					if($conn->query($sql1)){
						$ret_msg = "換盆成功！";
					}
					else{
						$ret_msg = "換盆失敗！";
					}
				}				
				if($onadd_quantity_cha123 <= 0){
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
		$onadd_newpot_sn=GetParam('onadd_newpot_sn');
		if($onadd_newpot_sn == "0"){
			$list = getPlantDataSn($onadd_sn);
		}
		else{
			$list = getPlantDataSn($onadd_newpot_sn);
		}
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
				"VALUES ('{$now}', '{$now}', '{$onadd_quantity_del}', '{$onelda_reason}', '{$onadd_newpot_sn}', '{$onadd_part_no}', '{$onadd_part_name}');";
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
		$list = getPlantDataSn($onadd_sn);
		$onadd_part_no = $list['onadd_part_no'];
		$onadd_part_name = $list['onadd_part_name'];
		$onadd_quantity=GetParam('onadd_quantity');//下種數量
		$jsuser_sn = GetParam('supplier');//編輯人員
		$onadd_plant_year=GetParam('onadd_plant_year');//出貨數量
		$onshda_price=GetParam('onshda_price');//單棵價格
		$onshda_client=GetParam('onshda_client');//出貨客戶
		$onadd_data_sn = GetParam('onadd_sn');//新流水號
		$onadd_AB_sn = GetParam('onadd_AB_sn');//AB編號
		$onadd_ml= GetParam('onadd_ml');//換倉編號
		$onshda_add_date=strtotime(GetParam('onshda_add_date'));//出貨日期
		// $onriadd_other_item=GetParam('onriadd_other_item');//其他價格-項目
		// $onadd_other_price = GetParam('onadd_other_price');//其他價格
		// $total_cost_week = GetParam('total_cost_week');//下種至出貨週數	
		// $total_cost_shipment = GetParam('total_cost_shipment');//總代工費用	
		$onadd_quantity_shi123 = ($onadd_quantity - $onadd_plant_year);
		if($onadd_quantity_shi123 < 0) {
			$onadd_status = -1;
		} else {
			$onadd_status = 1;
		}

		$onadd_newpot_sn = GetParam('onadd_newpot_sn');

		if($onadd_AB_sn == '0'){
			if($onadd_newpot_sn == '0'){
				if($onadd_ml == '0'){				
					$sn=GetParam('onadd_sn');
				}				
				else{
					$sn=GetParam('onadd_ml');
				}
			}
			else{
				$sn=GetParam('onadd_newpot_sn');
			}
		}
		else{
			$sn=GetParam('onadd_AB_sn');
		}

		if(empty($onadd_plant_year)){
			$ret_msg = "*為必填！";
		} 
		else if($onadd_status != -1){
			$now = time();
			$conn = getDB();
			$sql1 = "UPDATE onliine_add_data SET onadd_quantity='{$onadd_quantity_shi123}', onadd_status='{$onadd_status}' WHERE onadd_sn='{$onadd_sn}'";
			$sql2 = "INSERT INTO `online_shipment_data` (onshda_add_date, onshda_mod_date, onshda_client, onshda_quantity, onadd_sn, onadd_part_no, onadd_part_name, onadd_data_sn, onshda_real_price) VALUES ('{$onshda_add_date}','{$now}','{$onshda_client}','{$onadd_plant_year}','{$sn}','{$onadd_part_no}','{$onadd_part_name}','{$onadd_data_sn}', '{$onshda_price}');";				
			if($conn->query($sql1) && $conn->query($sql2)) {
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

		case 'DelayAmonth':
		$onadd_sn=GetParam('onadd_sn');

		if(empty($onadd_sn)){
			$ret_msg = "保留失敗！";
		}else{
			$now = time();
			$conn = getDB();
			// $onadd_planting_date = strtotime(date("Y-m-d", getPlantDataSn($onadd_sn)['onadd_planting_date_unix']) . " + 1 month");
			$onadd_planting_date = strtotime(date("Y-m-d", getPlantDataSn($onadd_sn)['onadd_planting_date_unix']));
			$sql = "UPDATE onliine_add_data SET onadd_planting_date = '{$onadd_planting_date}' WHERE onadd_sn = $onadd_sn";
			if($conn->query($sql)) {
				$ret_msg = "保留完成！";
			} else {
				$ret_msg = "保留失敗！";
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

		//搜尋用的資料---------------------------------------------
		case 'get_all_product':
		$ret_code = 1;
		$ret_data = getAllProductsNo();

		break;

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
				printr();
				$search_where[] = "onadd_sn IN (select onadd_sn from onliine_add_data where onadd_newpot_sn in ({$onadd_sn}) or onadd_sn in ({$onadd_sn})) or FROM_UNIXTIME(onadd_planting_date,'%Y') like '%$ex_[0]%'";
			}elseif($ex_P[0] == '-'){
				$search_where[] = "";
			}else{
				$search_where[] = "onadd_sn IN (select onadd_sn from onliine_add_data where onadd_newpot_sn in ({$onadd_sn}) or onadd_sn in ({$onadd_sn})) or FROM_UNIXTIME(onadd_planting_date,'%Y') like '%$ex_P[0]%'";
			}
		}elseif(count($ex_p) == 1 and $ex_p[0] !=""){
			if (strpos($ex_p[0],'p')) {
				$search_where[] = "onadd_isbought = 1";
			}elseif (strpos($ex_p[0],'-')) {
				$ex_ = explode("-", $ex_p[0]);
				$search_where[] = "onadd_sn IN (select onadd_sn from onliine_add_data where onadd_newpot_sn in ({$onadd_sn}) or onadd_sn in ({$onadd_sn})) or FROM_UNIXTIME(onadd_planting_date,'%Y') like '%$ex_[0]%'";
			}elseif($ex_P[0] == '-'){
				$search_where[] = "";
			}
			else{
				$search_where[] = "onadd_sn IN (select onadd_sn from onliine_add_data where onadd_newpot_sn in ({$onadd_sn}) or onadd_sn in ({$onadd_sn})) or FROM_UNIXTIME(onadd_planting_date,'%Y') like '%$ex_p[0]%'";
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
	if(($onadd_cur_size = GetParam('onadd_growing'))) {
		if($onadd_cur_size == 7){
			$search_where[] = "onadd_cur_size in (3,4,5,7)";
		}else{
			$search_where[] = "onadd_cur_size = {$onadd_cur_size}";			
		}
		$search_query_string['onadd_growing'] = $onadd_cur_size;
	}

	$search_where = isset($search_where) ? implode(' and ', $search_where) : '';
	$search_query_string = isset($search_query_string) ? http_build_query($search_query_string) : '';
	// page
	$pg_page = GetParam('pg_page', 1);
	$pg_rows = 20;
	$pg_total = GetParam('pg_total')=='' ? getPlantDataQty($search_where) : GetParam('pg_total');
	$pg_offset = $pg_rows * ($pg_page - 1);
	$pg_pages = $pg_rows == 0 ? 0 : ( (int)(($pg_total + ($pg_rows - 1)) /$pg_rows) );

	$product_list = getPlantData($search_where, $pg_offset, $pg_rows,$onadd_sn);
	$supplier_list = getAllSupplierData();
	$Staffs = get_Staffs();
	// echo "<hr><hr><hr><hr><hr>";printr($ex_P);printr($ex_p);printr($ex_);printr($search_where);
	// exit();
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
		$(document).ready(function() {
			var all_part_no = null;
			var all_part_name = null;
			var all_produce_img = null;
			var all_supplier = null;
			var plant_price = <?php echo json_encode($plant_price);?>;

		    $("body").on("change", "#add_form input[id=myFile]", function (){
		        preview(this);
		        var files = $("#add_form input[id=myFile]").get(0).files;   		     
		        var formData = new FormData();   
    			formData.append("myFile", files[0]); 
    			formData.append("onproduct_type", "4"); 
    			$.ajax({   
			        url: './upload_image.php',   
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
			        url: './upload_image.php',   
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

		    $('#bill_mode').val(1);
		    New_Amode();

		    $('#bill_mode').change(function () {       
		        var bill_mode = $('#bill_mode').val();
		        if (bill_mode >= 1) {
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
		        $('.foundry_option').show();
		        $('.oem_option').hide();	
		    }

		    function New_Amode_adjust(){
		    	$('#foundry_name_adjust').html("代工價格");
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
		        $('#foundry_name_adjust').html("催花價格");
		        $('#OEM_adjust').hide();
		        $('#UrgeFlowers_adjust').hide();
		        $('#foundry_title_adjust').hide();
		        $('#bill_request_title_adjust').html("訂單需求");
		        $('.foundry_option_adjust').show();
		        $('.oem_option_adjust').hide();	
		    }
		    
		    $.ajax({
				url: '../flask/plant_flask.php',
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
				url: '../flask/plant_flask.php',
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
			        	// console.log(all_plant_staff);
			        	/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
						autocomplete_plant_staff(document.getElementById('autocomplete_onadd_plant_staff'), all_plant_staff[0]);

			        }
			    },
			    error: function (xhr, ajaxOptions, thrownError) {
			    	console.log('ajax error');
			        // console.log(xhr);
			    }
			});

			// 代工需求切換
			$('#dropdown_onadd_isbought').change(function () {       
			    var whohide = $('#dropdown_onadd_isbought').val();
			    FoundryChanger(whohide);
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
								        	$('#img_newName').html((data.onproduct_pic_url != "") ? data.onproduct_pic_url : "");
								        	document.getElementById('preview').setAttribute("src",((data.onproduct_pic_url != '') ? data.onproduct_pic_url : "./images/nopic.png"));
								        	$('#add_form input[name=onadd_part_no]').val((data.onproduct_part_no != "") ? data.onproduct_part_no : "");
								        	$('#add_form input[name=onadd_part_name]').val((data.onproduct_part_name != "") ? data.onproduct_part_name : "");
								        	$('#add_form input[name=onadd_color]').val((data.onproduct_color != "") ? data.onproduct_color : "");
								        	$('#add_form input[name=onadd_size]').val((data.onproduct_size != "") ? data.onproduct_size : "");
								        	$('#add_form input[name=onadd_height]').val((data.onproduct_height != "") ? data.onproduct_height : "");

								        	// document.getElementById('dropdown_onadd_location').value = (data.onproduct_location != "") ? data.onproduct_location : "";
								        	$('#add_form input[name=onadd_pot_size]').val((data.onproduct_pot_size != "") ? data.onproduct_pot_size : "");
								        	$('#add_form input[name=onadd_supplier]').val((data.onproduct_supplier != "") ? data.onproduct_supplier : "");
								        	$('#add_form [name=onadd_growing] option[value='+d.onproduct_growing+']').prop('selected','selected');	 

								        	// $('#add_form input[name=onadd_buy_price]').val(data.onadd_buy_price);
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
								url: '../flask/plant_flask.php',
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
			      // if (!val) { return false;}
			      currentFocus = -1;
			      
			      /*create a DIV element that will contain the items (values):*/
			      a = document.createElement("DIV");
			      a.setAttribute("id", this.id + "autocomplete-list");
			      a.setAttribute("class", "autocomplete-items");
			      /*append the DIV element as a child of the autocomplete container:*/
			      this.parentNode.appendChild(a);
			      /*for each item in the array...*/
			      // console.log("focus");
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
			        console.log("focus");
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
			  	console.log("removeActive");
			    /*a function to remove the "active" class from all autocomplete items:*/
			    for (var i = 0; i < x.length; i++) {
			      x[i].classList.remove("autocomplete-active");
			    }
			  }
			  function closeAllLists(elmnt) {
			  	console.log("closeAllLists");
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
				url: './plant_purchase.php',
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
				        	console.log(all_part_no);
				        	/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
							autocomplete(document.getElementById('autocomplete_dropdown_onadd_part_no'), all_part_no[0]);

				        }
				    },
				    error: function (xhr, ajaxOptions, thrownError) {
			        	console.log('ajax error');
			            // console.log(xhr);
			        }
			    });

				$('button.keep').on('click', function(){
					location.reload();
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
				                	$('#upd_form input[name=onadd_ml]').val(d.onadd_ml);
				                	$('#upd_form input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
				                	$('#upd_form input[name=onadd_AB_sn]').val(d.onadd_AB_sn);
				                	$('#upd_form input[name=onadd_level]').val(d.onadd_level);	
				                	$('#upd_form input[name=onadd_type]').val(d.onadd_type);

				                	$('#upd_form input[name=onadd_part_no]').val(d.onadd_part_no);
				                	$('#upd_form input[name=onadd_part_name]').val(d.onadd_part_name);
				                	$('#upd_form input[name=onadd_color]').val(d.onadd_color);
				                	$('#upd_form input[name=onadd_size]').val(d.onadd_size);
				                	$('#upd_form input[name=onadd_height]').val(d.onadd_height);
				                	$('#upd_form input[name=onadd_pot_size]').val(d.onadd_pot_size);
				                	$('#upd_form input[name=onadd_supplier]').val(d.onadd_supplier);			  
				                	$('#upd_form [name=onadd_location] option[value='+d.onadd_location+']').prop('selected','selected');	 
				                	$('#upd_form input[name=onadd_sellsize]').val(d.onadd_sellsize);	
				                	$('#upd_form input[name=onadd_other_price]').val(d.onadd_other_price);					                	
				                	// $('#upd_form input[name=onadd_planting_date]').val(d.onadd_planting_date);
				                	$('#upd_form input[name=onadd_quantity]').val(d.onadd_quantity);
				                	$('#upd_form [name=onadd_cur_size] option[value='+d.onadd_growing+']').prop('selected','selected','selected','selected','selected','selected','selected');
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

				//清空
				$('#add_form button[type=reset]').on('click', function(){
					$('#add_form img[id=preview]').attr("src","");
					$('#add_form div[id=preview_size]').html("");
				});
				$('#adjust_form button[type=reset]').on('click', function(){
					$('#adjust_form img[id=preview]').attr("src","");
					$('#adjust_form div[id=preview_size]').html("");
				});
				$('#btn_add').on('click',function(){
					$('#img_newName').html("");
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
				                	if(d.onadd_newpot_sn == "0"){
					                	$('#upd_form1 input[name=onadd_newpot_sn]').val(d.onadd_sn);
					                }
					                else{
					                	$('#upd_form1 input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
					                }
				                	
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
				                console.log(ret);
				                if(ret.code==1) {
				                	var d = ret.data;
				                	// if(d.onadd_type == 1){
				                	// 	onadd_buy_price
				                	// }
				                	
				                	$('#upd_form2 input[name=onadd_sn]').val(d.onadd_sn);
				                	$('#upd_form2 input[name=onadd_part_name]').val(d.onadd_part_name);
				                	$('#upd_form2 input[name=onadd_part_no]').val(d.onadd_part_no);
				                	$('#upd_form2 input[name=onadd_quantity]').val(d.onadd_quantity);
									$('#upd_form2 input[name=onadd_location]').val(d.onadd_location);
				                	$('#upd_form2 input[name=onadd_ml]').val(d.onadd_ml);
				                	$('#upd_form2 input[name=onadd_AB_sn]').val(d.onadd_AB_sn);
				                	$('#upd_form2 input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
				                	$('#upd_form2 input[name=total_cost_shipment]').val(d.onadd_total);
				                	if (d.onadd_weekday == 0) {
				                		d.onadd_weekday = 1;
				                	}
				                	$('#upd_form2 input[name=total_cost_week]').val(d.onadd_weekday);
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
				$('button.adjust').on('click', function(){
					$('#adjust-modal').modal();
					$('#adjust_form')[0].reset();
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
				                console.log(ret);
				                if(ret.code==1) {
				                	var d = ret.data;
				                	$('#adjust_form img[id=preview]').attr("src",d.img_url);
				                	$('#adjust_form input[name=onadd_sn]').val(d.onadd_sn);
				                	$('#adjust_form input[name=onadd_ml]').val(d.onadd_ml);
				                	$('#adjust_form input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
				                	$('#adjust_form input[name=onadd_part_no]').val(d.onadd_part_no);
				                	$('#adjust_form input[name=onadd_part_name]').val(d.onadd_part_name);
				                	$('#adjust_form input[name=onadd_color]').val(d.onadd_color);
				                	$('#adjust_form input[name=onadd_size]').val(d.onadd_size);
				                	$('#adjust_form [name=onadd_cur_size] option[value='+d.onadd_cur_size+']').prop('selected','selected');
				                	$('#adjust_form input[name=onadd_height]').val(d.onadd_height);
				                	$('#adjust_form input[name=onadd_pot_size]').val(d.onadd_pot_size);
				                	$('#adjust_form [name=onadd_location] option[value='+d.onadd_location+']').prop('selected','selected');
				                	$('#adjust_form [name=onsd_sn] option[value='+d.onsd_sn+']').prop('selected','selected');
				                	$('#adjust_form input[name=onadd_supplier]').val(d.onadd_supplier);
									$('#adjust_form input[name=onadd_supplier_address]').val(d.onadd_supplier_address);
									$('#adjust_form input[name=onadd_supplier_email]').val(d.onadd_supplier_email);
									$('#adjust_form input[name=onadd_supplier_phone]').val(d.onadd_supplier_phone);
									$('#adjust_form [name=onadd_isbought] option[value='+d.onadd_isbought+']').prop('selected','selected');
									$('#adjust_form [name=onadd_plant_staff] option[value='+d.onadd_plant_staff+']').prop('selected','selected','selected','selected','selected','selected','selected');
				                	$('#adjust_form input[name=onadd_buy_price]').val(d.onadd_buy_price);
				                	$('#img_newName').html(d.img_url);
				                	// $('#dropdown_onadd_cur_size').val(d.onadd_cur_size);
				                	$('#adjust_form input[name=onadd_planting_date]').val(d.onadd_planting_date);
				                	$('#adjust_form input[name=onadd_quantity]').val(d.onadd_quantity);

				                	$('#adjust_form [name=onadd_growing] option[value='+d.onadd_growing+']').prop('selected','selected');
				                	if(d.onadd_sellsize != ""){		
				                		$('#adjust_form [name=onadd_sellsize] option[value='+d.onadd_sellsize+']').prop('selected','selected');
				                	}		
				                	$('#adjust_form [name=onadd_status] option[value='+d.onadd_status+']').prop('selected','selected');


				                	$('#adjust_form [name=bill_mode] option[value='+d.onadd_type+']').prop('selected','selected');

				                	$('#adjust_form [name=onadd_next_status] option[value='+d.onadd_next_status+']').prop('selected','selected');

				                	if (d.onadd_type >= 1) {
							        	New_Amode_adjust();
							        }
							        else{
							        	New_Bmode_adjust();
							        }
							        $('#adjust_form [name=onadd_isbought] option[value='+d.onadd_isbought+']').prop('selected','selected');

							        if(d.onadd_level == 0){
								        $('#adjust_form input[name=onadd_plant_day_A]').val(d.onadd_quantity);
								        $('#adjust_form input[name=onadd_plant_day_A]').removeAttr("readonly");
								        $('#adjust_form input[name=onadd_plant_day_B]').attr("readonly","readonly");
								        $('#adjust_form input[name=onadd_plant_day_B]').val('');

								        $('#adjust_form input[name=onadd_buy_price_A]').val(d.onadd_quantity);
								        $('#adjust_form input[name=onadd_buy_price_A]').removeAttr("readonly");
								        $('#adjust_form input[name=onadd_buy_price_B]').attr("readonly","readonly");
								        $('#adjust_form input[name=onadd_buy_price_B]').val('');

								        if (d.onadd_isbought == 1) {
								        	$('#OEM_adjust').hide();
								        	$('#UrgeFlowers_adjust').show();
								        	$('#adjust_form input[name=onadd_foundry_price_B]').val('');
								        	$('#adjust_form input[name=onadd_foundry_price_A]').val(d.onadd_buy_price);
								        	$('#adjust_form input[name=onadd_foundry_price_A]').removeAttr("readonly");
									        $('#adjust_form input[name=onadd_foundry_price_B]').attr("readonly","readonly");
								        }
								        if (d.onadd_isbought == 0) {
								        	$('#OEM_adjust').show();
								        	$('#UrgeFlowers_adjust').hide();
								        	$('#adjust_form input[name=onadd_buy_price_B]').val('');
								        	$('#adjust_form input[name=onadd_buy_price_A]').val(d.onadd_buy_price);	
								        	$('#adjust_form input[name=onadd_buy_price_A]').removeAttr("readonly");
									        $('#adjust_form input[name=onadd_buy_price_B]').attr("readonly","readonly");
								        }
								    }
								    else{
								    	$('#adjust_form input[name=onadd_plant_day_B]').val(d.onadd_quantity);
								    	$('#adjust_form input[name=onadd_plant_day_B]').removeAttr("readonly");
								    	$('#adjust_form input[name=onadd_plant_day_A]').attr("readonly","readonly");

								    	$('#adjust_form input[name=onadd_buy_price_B]').val(d.onadd_quantity);
								        $('#adjust_form input[name=onadd_buy_price_B]').removeAttr("readonly");
								        $('#adjust_form input[name=onadd_buy_price_A]').attr("readonly","readonly");

								        if (d.onadd_isbought == 1) {
								        	$('#OEM_adjust').hide();
								        	$('#UrgeFlowers_adjust').show();
								        	$('#adjust_form input[name=onadd_foundry_price_A]').val('');
								        	$('#adjust_form input[name=onadd_foundry_price_B]').val(d.onadd_buy_price);	
									        $('#adjust_form input[name=onadd_foundry_price_B]').removeAttr("readonly");
									        $('#adjust_form input[name=onadd_foundry_price_A]').attr("readonly","readonly");					        	
								        }
								        if (d.onadd_isbought == 0) {
								        	$('#OEM_adjust').show();
								        	$('#UrgeFlowers_adjust').hide();
								        	$('#adjust_form input[name=onadd_buy_price_A]').val('');
								        	$('#adjust_form input[name=onadd_buy_price_B]').val(d.onadd_buy_price);	
								        	$('#adjust_form input[name=onadd_buy_price_B]').removeAttr("readonly");
									        $('#adjust_form input[name=onadd_buy_price_A]').attr("readonly","readonly");		        	
								        }
								    }						        

							        $('#adjust_form input[name=onadd_buy_price_1]').val(JSON.parse(d.onadd_other_price)['price']);
							        // console.log(d.onadd_growing);
							        if(d.onadd_growing == "催花"){
							        	$('#adjust_form [name=onadd_next_status] option[value=2]').prop('selected','selected');
							        	$('#form_2_adjust').show();
							        	if(d.onadd_specification == "花梗出貨" || d.onadd_specification == "成花出貨"){
							        		$('#adjust_form [name=onadd_specification] option[value='+d.onadd_specification+']').prop('selected','selected');
							        		$('#adjust_form [name=onadd_specification_other]').hide();
							        	}
							        	else{
							        		$('#adjust_form [name=onadd_specification] option[value=其他]').prop('selected','selected');
							        		$('#adjust_form [name=onadd_specification_other]').val(d.onadd_specification);
							        		$('#adjust_form [name=onadd_specification_other]').show();
							        	}
							        }
							        else if(d.onadd_growing == "出貨"){
							        	$('#adjust_form [name=onadd_specification] option[value=3]').prop('selected','selected');
							        }
							        else{
							        	$('#adjust_form [name=onadd_specification] option[value=1]').prop('selected','selected');
							        }
				                }
				            },
				            error: function (xhr, ajaxOptions, thrownError) {
			                	// console.log('ajax error');
			                 //    console.log(thrownError);
			                }
			            });
				});			
				//修改-----------------------------------------------------------

				//移倉-----------------------------------------------------------
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
				                	$('#upd3_form input[name=onadd_ml]').val(d.onadd_ml);
				                	$('#upd3_form input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
				                	$('#upd3_form input[name=onadd_AB_sn]').val(d.onadd_AB_sn);
				                	$('#upd3_form input[name=onadd_other_price]').val(d.onadd_other_price);				                	
				                	
				                	$('#upd3_form input[name=onadd_part_no]').val(d.onadd_part_no);
				                	$('#upd3_form input[name=onadd_part_name]').val(d.onadd_part_name);
				                	$('#upd3_form input[name=onadd_color]').val(d.onadd_color);
				                	$('#upd3_form input[name=onadd_size]').val(d.onadd_size);
				                	$('#upd3_form input[name=onadd_height]').val(d.onadd_height);
				                	$('#upd3_form input[name=onadd_pot_size]').val(d.onadd_pot_size);
				                	$('#upd3_form input[name=onadd_location]').val(d.onadd_location);
				                	$('#upd3_form input[name=onadd_location_old]').val(d.onadd_location);
				                	$('#upd3_form input[name=onadd_supplier]').val(d.onadd_supplier);
				                	$('#upd3_form input[name=onadd_buy_price]').val(d.onadd_buy_price);
				                	$('#dropdown_onadd_cur_size').val(d.onadd_cur_size);
				                	$('#upd3_form input[name=onadd_planting_date]').val(d.onadd_planting_date);
				                	$('#upd3_form input[name=onadd_quantity]').val(d.onadd_quantity);
									$('#upd3_form input[name=onadd_type]').val(d.onadd_type);
									$('#upd3_form input[name=onadd_next_status]').val(d.onadd_next_status);
									$('#upd3_form input[name=onadd_add_date]').val(d.onadd_add_date);
									$('#upd3_form [name=onadd_isbought] option[value='+d.onadd_isbought+']').prop('selected','selected');
									
									


				                	if(d.onadd_level == 0){
				                		$('#upd3_form [name=onadd_now_level] option[value=0]').prop('selected','selected');
				                		$('#upd3_form [name=onadd_level] option[value=1]').prop('selected','selected');
				                	}
				                	else{
										$('#upd3_form [name=onadd_now_level] option[value=1]').prop('selected','selected');
				                		$('#upd3_form [name=onadd_level] option[value=0]').prop('selected','selected');
				                	}
				                	

				                	$('#upd3_form [name=onadd_growing] option[value='+d.onadd_growing+']').prop('selected','selected');
				                	if(d.onadd_sellsize != ""){		
				                		$('#upd3_form [name=onadd_sellsize] option[value='+d.onadd_sellsize+']').prop('selected','selected');
				                	}		
				                	$('#upd3_form [name=onadd_status] option[value='+d.onadd_status+']').prop('selected','selected');

				                	if(d.onadd_isbought == 1){
				                		$('#relocation_buy_price').html("每月代工價格");
				                	}
				                	else{
				                		$('#relocation_buy_price').html("總代工價格");
				                	}				                	
				                }
				            },
				            error: function (xhr, ajaxOptions, thrownError) {
			                	// console.log('ajax error');
			                 //    console.log(thrownError);
			                }
			            });
				});			
				//移倉-----------------------------------------------------------

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
					var qr_sn = $(this).data('qr_sn');
					$.ajax({
						url: './plant_purchase.php',
						type: 'post',
						dataType: 'json',
						data: {op:"get", onadd_sn:$(this).data('onadd_sn'), qr_sn: qr_sn, plant_sn:"1"},
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
				                	console.log(d);
				                	$('#temp_onadd_sn').val(d.onadd_sn);

				                	if(d.img_url != "")
				                		$('#qr_product_img').attr("src",d.img_url);
				                	else
				                		$('#qr_product_img').attr("src","./images/nopic.png");

				     //            	if($('#qr_product_img').width() > 565)
				     //            		$('#qr_product_img').width(550);
									// if($('#qr_product_img').height() > 392)
				     //            		$('#qr_product_img').height(392);
	 
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
					var qr_sn = $('#qr_sn').html();
					var qr_part_no = $('#qr_part_no').html();
					var qr_part_name = $('#qr_part_name').html();
					var qr_plant_date = $('#qr_plant_date').html();
					var qr_location = $('#qr_location').html();
					var img_src = $('#qr_product_img').attr("src");
					var qr_src = $('#qr_img').attr("src");
					var data = "?qr_sn="+qr_sn+"&qr_part_no="+qr_part_no+"&qr_part_name="+qr_part_name+"&qr_plant_date="+qr_plant_date+"&qr_location="+qr_location+"&img_src="+img_src+"&qr_src="+qr_src;
					window.open(
					 '<?php echo WT_SERVER;?>/admin/purchase/test.php'+data,
					  '_blank' // <- This is what makes it open in a new window.
					);
				});

				
				//新增其他價格-------------------------------------------------------
				$('#add_more_price').on('click', function(){
					// var html = $('#other_price').html();
					// html += '<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;"><input type="text" class="form-control" id="onadd_buy_item_1" name="onadd_buy_item_1" placeholder="項目" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">												</div><label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">,</label><div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;"><input type="text" class="form-control" id="onadd_buy_price_1" name="onadd_buy_price_1" placeholder="元" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;"></div><label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">元</label><button class="btn btn-primary" id="add_more_price">新增</button><div class="help-block with-errors"></div>';
					// $('#other_price').html(html);
				});


				$('#add_form, #upd_form1, #upd3_form, #eli_form1, #adjust_form').validator().on('submit', function(e) {
					if (!e.isDefaultPrevented()) {
						// var files = $("#myFile").get(0).files;   
						e.preventDefault();
						var param = $(this).serializeArray();
						var onproduct_pic_url = {name:"onproduct_pic_url",value:$('#img_newName').html().substring(1,$('#img_newName').html().length)};
						param.push(onproduct_pic_url);
						console.log(param);
						$(this).parents('.modal').modal('hide');
						$(this)[0].reset();
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

					//出貨、換盆後動作----------------------------------------------------------
					$('#upd_form, #upd_form2').validator().on('submit', function(e) {
					if (!e.isDefaultPrevented()) {
						// var files = $("#myFile").get(0).files;   
						e.preventDefault();
						var param = $(this).serializeArray();
						var onproduct_pic_url = {name:"onproduct_pic_url",value:$('#img_newName').html().substring(1,$('#img_newName').html().length)};
						param.push(onproduct_pic_url);
						$(this).parents('.modal').modal('hide');
						var quantity = 0;
						console.log(param);
						$(this)[0].reset();
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
						 			if(ret.msg != '出貨完成！' && ret.msg != '換盆成功！'){
						 				alert_msg(ret.msg);
						 			}	
						 			else{	
						 				if(ret.msg == '出貨完成！'){
						 					quantity = param[8]['value'] - param[12]['value'];
						 				}
						 				else{
						 					quantity = param[13]['value'] - param[20]['value'];
						 				}

						 				if(quantity > 0){		 			
							 				bootbox.confirm({
							 					message:ret.msg+"，庫存剩餘數量是否汰除？",
							 					buttons: {
											        confirm: {
											            label: '保留',
											            className: 'btn-primary'
											        },
											        cancel: {
											            label: '汰除',
											            className: 'btn-danger'
											        }
											    },
											    callback: function(result) {
													if(result) {																					
														$.ajax({
															url: './plant_purchase.php',
															type: 'post',
															dataType: 'json',
															data: {op:"DelayAmonth", onadd_sn:param[1]['value']},
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

					            							    }
					            						});
													}
													else{
														$('#eli-modal1').modal();
														$.ajax({
															url: './plant_purchase.php',
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
						        							        	$('#eli_form1 input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
						        							        	$('#eli_form1 input[name=onadd_part_no]').val(d.onadd_part_no);
						        							        	$('#eli_form1 input[name=onadd_part_name]').val(d.onadd_part_name);
						        							        	$('#eli_form1 input[name=onadd_quantity]').val(d.onadd_quantity);
						        							        	$('#eli_form1 input[name=onriadd_other_item]').val(d.onriadd_other_item);
						        							        	$('#eli_form1 input[name=onadd_other_price]').val(d.onadd_other_price);	
						        							        }
						        							    },
						        							    error: function (xhr, ajaxOptions, thrownError) {

					            							    }
					            						});
													}
												}
											});

							 			}
							 			else if(quantity < 0){
							 				alert_msg("錯誤！輸入數量高於原始數量！");
							 			}
							 			else{
							 				alert_msg(ret.msg);
							 			}
						 			}					            
						 		},
						 		error: function (xhr, ajaxOptions, thrownError) {
				                	console.log('ajax error');
				                 //     console.log(thrownError);
				                 }
				             });
						 }
					});

					//出貨、換盆後動作----------------------------------------------------------

					$('#datetimepicker1,#datetimepicker_adjsut').datetimepicker({
			        	minView: 2,
			            language:  'zh-TW',
			            format: 'yyyy-mm-dd',
			            useCurrent: false
			        });

			        var d = new Date();
					var month = d.getMonth()+1;
					var day = d.getDate();

					var output = d.getFullYear() + '/' +
					(month<10 ? '0' : '') + month + '/' +
					(day<10 ? '0' : '') + day;

					$("#datetimepicker1").val(output);

			        $('#datetimepicker2, #datetimepicker3, #datetimepicker4, #onshda_add_date').datetimepicker({
			        	minView: 2,
			            language:  'zh-TW',
			            format: 'yyyy-mm-dd',
			            useCurrent: false
			        });

			        $('button.cancel').on('click', function() {
						location.href = "./../";
					});

					// 代工需求切換
					$('#onadd_isbought').change(function () {       
				        var whohide = $('#onadd_isbought').val();
				        if (whohide == 1) {
				        	$('#OEM').hide();
				        	$('#UrgeFlowers').show();
				        }
				        if (whohide == 0) {
				        	$('#OEM').show();
				        	$('#UrgeFlowers').hide();
				        }
				    });

				    // 代工需求切換
					$('#onadd_isbought_adjust').change(function () {       
				        var whohide = $('#onadd_isbought_adjust').val();
				        if (whohide == 1) {
				        	$('#OEM_adjust').hide();
				        	$('#UrgeFlowers_adjust').show();
				        	$('#adjust_form input[name=onadd_buy_price_A]').val('');
							$('#adjust_form input[name=onadd_buy_price_B]').val('');
				        }
				        if (whohide == 0) {
				        	$('#OEM_adjust').show();
				        	$('#UrgeFlowers_adjust').hide();
				        	$('#adjust_form input[name=onadd_foundry_price_A]').val('');
							$('#adjust_form input[name=onadd_foundry_price_B]').val('');
				        }
				    });

					// 下階段換盆尺寸 切換
					$('#next_status').change(function () {       
				        var whohide = $('#next_status').val();
				        if (whohide == 1) {
				        	$('#form_1').show();
				        	$('#form_2').hide();
				        }
				        else if(whohide == 2) {
				        	$('#form_1').hide();
				        	$('#form_2').show();
				        }
				        else if(whohide == 3) {
				        	$('#form_1').hide();
				        	$('#form_2').hide();
				        }
				    });

				    // 下階段換盆尺寸 切換
					$('#change_basin').change(function () {       
				        var whohide = $('#change_basin').val();		       
				        if (whohide == "UrgeFLowers") {
				        	$('#form_3').hide();
				        	$('#form_4').show();
				        }
				        else{
				        	$('#form_3').show();
				        	$('#form_4').hide();
				        }
				    });

				    // 下階段換盆尺寸 切換
					$('#next_status_adjust').change(function () {       
				        var whohide = $('#next_status_adjust').val();
				        if (whohide == 1) {
				        	$('#form_1_adjust').show();
				        	$('#form_2_adjust').hide();
				        }
				        else if(whohide == 2) {
				        	$('#form_1_adjust').hide();
				        	$('#form_2_adjust').show();
				        }
				        else if(whohide == 3) {
				        	$('#form_1_adjust').hide();
				        	$('#form_2_adjust').hide();
				        }
				    });

				    // 下階段換盆尺寸 切換
					$('#change_basin_adjust').change(function () {       
				        var whohide = $('#change_basin_adjust').val();		       
				        if (whohide == "UrgeFLowers") {
				        	$('#form_3_adjust').hide();
				        	$('#form_4_adjust').show();
				        }
				        else{
				        	$('#form_3_adjust').show();
				        	$('#form_4_adjust').hide();
				        }
				    });
				    
				     // 移倉與AB苗 切換
					$('#onadd_stock_status').change(function () {       
				        var whohide = $('#onadd_stock_status').val();		       
				        if (whohide == 0) {
				        	$('#now_level').hide();
				        	$('#relevel').hide();
				        	$('#now_location').show();
				        	$('#relocation').show();
				        	$('#stock_number_title').html("移倉數量");
				        }
				        if (whohide == 1) {
				        	$('#now_level').show();
				        	$('#relevel').show();
				        	$('#now_location').hide();
				        	$('#relocation').hide();
				        	$('#stock_number_title').html("更換數量");
				        }
				    });

				    //催花其他輸入				    
				    $('#dropdown_onadd_specification_other').change(function () {       
				        var whohide = $('#dropdown_onadd_specification_other').val();	
				        if (whohide == "其他") {
				        	$('#onadd_specification_other').show();
				        }
				        else{
				        	$('#onadd_specification_other').hide();
				        }
				    });

				    //催花其他輸入				    
				    $('#adjust_dropdown_onadd_specification_other').change(function () {       
				        var whohide = $('#adjust_dropdown_onadd_specification_other').val();	
				        if (whohide == "其他") {
				        	$('#adjust_onadd_specification_other').show();
				        }
				        else{
				        	$('#adjust_onadd_specification_other').hide();
				        }
				    });

				    $('#change_basin_next_size').change(function () {       
				        var n = $('#change_basin_next_size').val();			        
				        // $('#upd_form input[name=onadd_price_per_plant]').val(plant_price[n]);
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
							$('#history_cotent').html('<div class="col-md-12"><div class="col-md-12"><table class="table table-hover"><thead><tr><th style="text-align:center;">操作日期</th><th style="text-align:center;">下種日期(數量)</th><th style="text-align:center;">換盆日期(數量)</th><th style="text-align:center;">出貨日期(數量)</th><th style="text-align:center;">汰除日期(數量)</th></tr></thead>');
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
			function PrintElem(elem)
			{
				var mywindow = window.open('', 'PRINT', 'height=1160,width=820');

				mywindow.document.write('<html><head><title>' + document.title  + '</title>');
				mywindow.document.write('</head><body >');
				mywindow.document.write('<h1>' + document.title  + '</h1>');
				document.getElementById(elem).setAttribute("style", "width: 820px; height: 1160px;");
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
					<h4>苗株庫存管理</h4>
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
									<input type="hidden" name="onadd_newpot_sn">
									<input type="hidden" name="onadd_ml">
									<input type="hidden" name="onadd_AB_sn">
									<input type="hidden" name="onadd_sellsize">
									<input type="hidden" name="onadd_other_price">
									<input type="hidden" name="bill_mode">
									<input type="hidden" name="onadd_level">
									<input type="hidden" name="onadd_type">									

									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">品號<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>									
										<label for="addModalInput1" class="col-md-2 control-label">品名</label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_name" placeholder=""  maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">放置區<font color="red">*</font></label>
										<div class="col-md-4">
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
										<label for="addModalInput1" class="col-md-2 control-label">下種數量<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="display: none;">
										<label for="addModalInput1" class="col-md-2 control-label">花色</label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_color" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="display: none;">
										<label for="addModalInput1" class="col-md-2 control-label">花徑</label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_size" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="display: none;">
										<label for="addModalInput1" class="col-md-2 control-label">高度</label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_height" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="display: none;">
										<label for="addModalInput1" class="col-md-2 control-label">適合開花盆徑</label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_pot_size" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group" style="display: none;">
										<label for="addModalInput1" class="col-md-2 control-label">供應商</label>
										<div class="col-md-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_supplier" placeholder="" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-md-2 control-label">換盆日期&nbsp;<font color="red">*</font></label>
										<div class="col-md-4">
											<div class="input-group">
											    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
											    <input type="text" class="form-control" id="datetimepicker2" name="onadd_planting_date" value="<?php echo (empty($device['onadd_planting_date'])) ? '' : date('Y-m-d', $device['onadd_planting_date']);?>" placeholder="">
											</div>											
											<div class="help-block with-errors"></div>
										</div>
										<label for="addModalInput1" class="col-md-2 control-label" >換盆數量<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_replant_number" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>	
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label" >即將換盆至<font color="red">*</font></label>
										<div class="col-md-4">
											<select class="form-control" name="onadd_cur_size" id='change_basin_next_size'>
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
										<!-- <label class="col-md-2 control-label">下一階段狀態<font color="red">*</font></label>
										<div class="col-md-4">
											<select class="form-control" name="onadd_next_status" id='change_basin'>
												<option selected="selected" value="1">換盆</option>
												<option value="2">催花</option>
												<option value="3">出貨</option>
											</select>
										</div> -->
										<label class="col-md-2 control-label">下一階段狀態<font color="red">*</font></label>
										<div class="col-md-4">
											<select class="form-control" name="onadd_growing">
												<option value="UrgeFLowers">催花</option>
												<option value="Shipment">出貨</option>
												<option value="6">3.6</option>
												<option value="5">3.5</option>
												<option value="4">3.0</option>
												<option value="3">2.8</option>
												<option value="2">2.5</option>
												<option selected="selected" value="1">1.7</option>
											</select>
										</div>
									</div>	
									<!-- <div class="form-group" id="form_3" >
										<div class="col-md-6">
										</div>
										<label class="col-md-2 control-label">下一階段狀態<font color="red">*</font></label>
										<div class="col-md-4">
											<select class="form-control" name="onadd_growing">
												<option value="UrgeFLowers">催花</option>
												<option value="online_shipment_data">出貨</option>
												<option value="6">3.6</option>
												<option value="5">3.5</option>
												<option value="4">3.0</option>
												<option value="3">2.8</option>
												<option value="2">2.5</option>
												<option selected="selected" value="1">1.7</option>
											</select>
										</div>
									</div> -->
									<div class="form-group" id="form_4" style="display: none;">
										<div class="col-md-6">
										</div>
										<label class="col-md-2 control-label">催花規格<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_foundry_type" placeholder="規格" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label" style="">種植人員<font color="red">*</font></label>
										<div class="col-md-2">
											<select class="form-control"  id="autocomplete_onadd_plant_staff" name="onadd_plant_staff">	 
	        									<?php 
	        									foreach ($Staffs as $key => $value) {
	        										echo "<option value=".$value['jsuser_sn'].">".$value['jsuser_name']."</option>";
	        									}
	        									?>
	    									</select>
										</div>										
										<label for="addModalInput1" class="col-md-1 control-label" style="padding-left: 0px;padding-right: 0px;text-align: right;">換盆費用</label>
										<div class="col-md-3">											
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">

											<input type="text" class="form-control" name="onadd_price_per_plant" placeholder="費用" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-left: 0px;border-left: 0px;height: 28px;padding-left: 0px;text-align: center;" value="">
											</div>
											<label for="addModalInput1" class="col-md-4 control-label" style="text-align: left;width: 5rem;padding-left: 0px;">每株</label>											
											<div class="help-block with-errors"></div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">確認</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!--移倉----------------------------------------------------------->
		<div id="upd-modal3" class="modal upd-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd3_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">轉移</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd5">
									<input type="hidden" name="onadd_sn">
									<input type="hidden" name="onadd_ml">
									<input type="hidden" name="onadd_newpot_sn">
									<input type="hidden" name="onadd_type">
									<input type="hidden" name="onadd_next_status">
									<input type="hidden" name="onadd_add_date">
									<input type="hidden" name="onadd_AB_sn">
									<input type="hidden" name="onadd_other_price">

									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">狀態</label>
										<div class="col-md-10">
											<select class="form-control" name="onadd_stock_status" id="onadd_stock_status">
												<option value="1">規格更換(AB苗)</option>
												<option selected="selected" value="0">移倉</option>
											</select>
										</div>
									</div>
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
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_part_name" placeholder="" maxlength="32">
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

									<div class="form-group" style="display: none;" id="now_level">
										<label for="addModalInput1" class="col-md-2 control-label">目前規格</label>
										<div class="col-md-10">											
											<select class="form-control" name="onadd_now_level">
												<option value="1">B苗</option>
												<option value="0">A苗</option>
											</select>
										</div>
									</div>

									<div class="form-group" style="display: none;" id="relevel">
										<label for="addModalInput1" class="col-md-2 control-label">預計更換為</label>
										<div class="col-md-10">
											<select class="form-control" name="onadd_level">
												<option value="1">B苗</option>
												<option value="0">A苗</option>
											</select>
										</div>
									</div>

									<div class="form-group" id="now_location">
										<label for="addModalInput1" class="col-md-2 control-label">目前放置位置<font color="red">*</font></label>
										<div class="col-md-10">
											<input readonly="readonly" type="text" class="form-control" id="onadd_location_old" name="onadd_location_old" placeholder="" readonly="true">
										</div>																				
									</div>

									<div class="form-group" id="relocation">
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
										<label for="addModalInput1" class="col-md-2 control-label" id="stock_number_title">移倉數量<font color="red">*</font></label>
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
												<option value="9">出貨</option>
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

									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label" id="relocation_buy_price">代工單價<font color="red">*</font></label>
										<div class="col-md-10">
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="onadd_buy_price" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
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
									<input type="hidden" name="onadd_AB_sn">

									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">品名</label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_name" placeholder=""  maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>									
										<label for="addModalInput1" class="col-md-2 control-label">品號<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">放置區<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_location" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>									
										<label for="addModalInput1" class="col-md-2 control-label">可供出貨數量<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_quantity" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div> 

									<div class="form-group">
										<label class="col-md-2 control-label">出貨日期&nbsp;<font color="red">*</font></label>
										<div class="col-md-4">
											<div class="input-group">
											    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
											    <input type="text" required class="form-control" id="onshda_add_date" name="onshda_add_date" value="" placeholder="">
											</div>											
											<div class="help-block with-errors"></div>
										</div>
										<label for="addModalInput1" class="col-md-2 control-label">出貨數量<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_plant_year" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>

									<div class="form-group">												
										<label for="addModalInput1" class="col-md-2 control-label">實際售價<font color="red">*</font></label>
										<div class="col-md-4">
											<input type="text" class="form-control" id="addModalInput1" name="onshda_price" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
										<label for="addModalInput1" class="col-md-2 control-label">出貨對象<font color="red">*</font></label>
										<div class="col-md-4">
											<select class="form-control" name="onshda_client"  id='onshda_client'>
												<?php
												foreach ($supplier_list as $key => $value) {
													echo '<option value="'.$value['onsd_name'].'">'.$value['onsd_name'].'</option>';
												}
												?>
											</select>
										</div>	
									</div>   

									<div class="form-group">
										<!-- 成本 -->
										<label for="addModalInput1" class="col-md-2 control-label">累計成本</label>
										<label class="col-md-1 control-label" style="padding-left: 0px;padding-right: 0px;text-align: right;width: 5.5rem;">共種植</label>
										<div class="col-md-1" style="padding-left: 0px;padding-right: 0px;text-align: left;width: 5.5rem;">	
											<input type="text" readonly="readonly" class="form-control" id="total_cost_week" name="total_cost_week" placeholder=""  minlength="1" maxlength="32"  style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;padding-left: 0px;padding-right: 0px;width: 5.5rem;">
										</div>
										<label class="col-md-1 control-label" style="padding-left: 0px;padding-right: 0px;text-align: left;width: 5.5rem;">週，共計</label>
										<div class="col-md-1" style="padding-left: 0px;padding-right: 0px;text-align: left;width: 5.5rem;">	
											<input type="text" readonly="readonly" class="form-control" id="total_cost_shipment" name="total_cost_shipment" placeholder=""  minlength="1" maxlength="32"  style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;padding-left: 0px;padding-right: 0px;">
										</div>									
										<label class="col-md-1 control-label" style="padding-left: 0px;padding-right: 0px;text-align: left;width: 5.5rem;">元</label>
									</div>


									<!-- <div class="form-group">	
										<div class="col-md-6" ></div>
										<label for="addModalInput1" class="col-md-2 control-label">其他價格</label>
										<div class="col-md-1" style="">	
											<input type="text" class="form-control" id="onriadd_other_item" name="onriadd_other_item" placeholder="" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;padding-left: 0px;padding-right: 0px;">
										</div>
										<label class="col-md-1 control-label" style="padding-left: 0px;padding-right: 0px;text-align: left;width: 1rem;">，</label>
										<div class="col-md-1" style="padding-left: 0px;padding-right: 0px;text-align: left;width: 5.5rem;">	
											<input type="text" class="form-control" id="onadd_other_price" name="onadd_other_price" placeholder="" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;padding-left: 0px;padding-right: 0px;">										
										</div>
										<label class="col-md-1 control-label" style="padding-left: 0px;padding-right: 0px;text-align: left;width: 2rem;">元</label>			
									</div>    		 -->						
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
									<div class="col-md-12">
										<label for="addModalInput1" class="col-md-2 control-label">資料建立日期</label>
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
						<div class="row" id="qr_container" >
							<div class="row" id="qr_cotent">
								<!-- <div class="col-md-8" id="qr_sec_cotent"> -->
									<!-- <input type="hidden" id="temp_onadd_sn">
									<img id="qr_img_example" style="margin-left: 20px;padding-left: 10px;display:none;" 
										 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/purchase/plant_purchase.php?">
									<img id="qr_img" style="margin-left: 20px;padding-left: 10px;" 
										 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/purchase/plant_purchase.php?">	 -->
								<!-- </div> -->
								<div class="col-md-12" id="qr_sec_cotent2" style="text-align: center;">
<!-- 								<div id="qr_sn" style="font-size: 20px;font-weight:bold;">產品編號：</div>
									<div id="qr_part_no" style="font-size: 20px;font-weight:bold;">品號：</div>
									<div id="qr_part_name" style="font-size: 20px;font-weight:bold;">品名：</div>
									<div id="qr_plant_date" style="font-size: 20px;font-weight:bold;">下種日期：</div>
									<div id="qr_location" style="font-size: 20px;font-weight:bold;">位置：</div>
									<div id="qr_part_number" style="font-size: 20px;font-weight:bold;">數量：</div>		
									<img id="qr_sticker_img"style="width: 400px;height: 280px;" src=""> -->		
									<br>							
									<img id="qr_product_img" class="img-thumbnail" style="max-height: 32vh;max-width: 32vw;text-align: center;" src="">
								</div>
								<p style="margin: 10px">
								<div class="col-md-12" id="qr_sec_cotent2">
									<br>
									<div class="col-md-5" style="text-align: left;margin-left: 6rem;padding-left: 0px;">
										<span id="qr_sn" style="font-size: 20px;"></span><br>
										<span id="qr_part_no" style="font-size: 20px;"></span><br>
										<span id="qr_part_name" style="font-size: 20px;"></span><br>
										<span id="qr_plant_date" style="font-size: 20px;"></span><br>
										<span id="qr_location" style="font-size: 20px;"></span><br>
									</div>
									<div class="col-md-5" id="qr_sec_cotent"  style="text-align: center;">
										<input type="hidden" id="temp_onadd_sn">
										<img id="qr_img"  style="margin-left: 0px;" 
											 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/purchase/plant_purchase_m.php?">
										<img id="qr_img_example" style="display:none;" 
											 src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/purchase/plant_purchase_m.php?">											
									</div>
								</div>
							</div>
							<!-- <div id="qr_cotent_recover" >
								
							</div> -->
						</div>

						<div class="modal-footer">
							<button id="qr_download" type="button" class="btn btn-primary qr_download">列印二維條碼</button>
							<button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--QR Code產生Modal----------------------------------------------------------->		

		<!--新增Modal----------------------------------------------------------->
		<div id="add-modal" class="modal add-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="add_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">苗株資料建立</h4>
						</div>

						<div class="container-fluid">
							<div class="row">
								<input type="hidden" name="op" value="add">
								<input type="hidden" id="IsUploadImg" value="">
								<!--左邊欄位-->
								<div class="col-md-6" style="margin-top: 2rem;">
									<?php if(strpos($permmsion_option, "4") !== false || $permmsion == 0){ ?>
										<h4>產品圖片預覽</h4>
										<div class="well" style="text-align: center;width: 31.5rem;height: 31.5rem;">			
											<img class="preview" id="preview" style="max-width: 25rem; max-height: 25rem;">
										    <div class="size" id="preview_size" style="font-size: 1.5rem;"></div>
										</div>
									<?php } ?>
									<input type="file" class="upl" id="myFile" name="myFile" style="max-width: 31.5rem;border-radius:0.5rem;font-family: '微軟正黑體';" accept="image/jpeg,image/jpg,image/gif,image/png">
									<br/>
									<div class="form-group">
										<label class="col-md-3 control-label">目前尺寸<font color="red">*</font></label>
										<div class="col-md-8">
											<select class="form-control" id="dropdown_onadd_cur_size" name="onadd_cur_size">
												<option value="7">其他</option>
												<option value="11">4.0(含以上)</option>
												<option value="6">3.6</option>
												<option value="5">3.5</option>
												<option value="4">3.0</option>
												<option value="3">2.8</option>
												<option value="2">2.5</option>
												<option value="10">2.0</option>
												<option selected="selected" value="1">1.7</option>
											</select>
										</div>
									</div>
									<div class="form-group" style="padding-top: 0.9rem;">
										<label class="col-md-3 control-label" style="vertical-align: middle;top: 0rem;">下階段狀態<font color="red">*</font></label>
										<div class="col-md-8">
											<select class="form-control" name="onadd_next_status" id="next_status">
												<option value="3">出貨</option>
												<option value="2">催花</option>
												<option selected="selected" value="1">換盆</option>
											</select>
										</div>
									</div>
									<!-- 狀態切換 start -->
									<div class="form-group" id='form_1'>
										<label class="col-md-3 control-label" style="vertical-align: middle;top: 0rem;">預計換盆尺寸<font color="red">*</font></label>
										<div class="col-md-8">
											<select readonly="readonly" class="form-control" id="dropdown_onadd_growing" name="onadd_growing">
												<option value="9">出貨</option>
												<option value="6">3.6</option>
												<option value="5">3.5</option>
												<option value="4">3.0</option>
												<option value="3">2.8</option>
												<option value="2">2.5</option>
												<option value="10">2.0</option>
												<option selected="selected" value="1">1.7</option>
											</select>
										</div>
									</div>

									<div class="form-group" style="display: none;" id='form_2'>
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">規格<font color="red">*</font></label>
										<div class="col-md-8">
											<select readonly="readonly" class="form-control" id="dropdown_onadd_specification_other" name="onadd_specification">
												<option value="花梗出貨">花梗出貨</option>
												<option value="成花出貨" selected="selected">成花出貨</option>
												<option value="其他">其他</option>
											</select>
											<input style="display: none;" id="onadd_specification_other" type="text" class="form-control" name="onadd_specification_other" placeholder="請輸入規格" maxlength="32">
										</div>
									</div>
									<!-- 狀態切換 end -->
									<div class="form-group" style="">
										<label class="col-md-3 control-label">出貨規格<font color="red">*</font></label>
										<div class="col-md-8">
											<select class="form-control" id="dropdown_onadd_sell" name="onadd_sellsize">
												<option value="7">成花</option>
												<option value="11">4.0(含以上)</option>
												<option value="6">3.6</option>
												<option value="5">3.5</option>
												<option value="4">3.0</option>
												<option value="3">2.8</option>
												<option value="2">2.5</option>
												<option value="10">2.0</option>
												<option selected="selected" value="1">1.7</option>
											</select>
										</div>
									</div>
									<!-- <div class="form-group">										
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">放置區<font color="red">*</font></label>
										<div class="col-md-8">
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
									</div> -->

									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label" >放置區</label>
										<div class="col-md-8">
											<input type="text" class="form-control" id="dropdown_onadd_location" name="onadd_location" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>

									<div class="form-group">
    									<label for="searchInput1" class="col-md-3 control-label " style="padding-left: 0px;">種植人員<font color="red">*</font></label>
    									<div class="col-md-8">
    										<select class="form-control" name="onadd_plant_staff">
	        									<?php 
	        									foreach ($Staffs as $key => $value) {
	        										echo "<option value=".$value['jsuser_sn'].">".$value['jsuser_name']."</option>";
	        									}
	        									?>
	    									</select>
	    								</div>
    								</div>
								</div>
								<!--右邊欄位-->
								<div class="col-md-6" style="margin-top: 2rem;">
									<h4>&nbsp;</h4>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;padding-bottom: 10px;">項目<font color="red">*</font></label>
										<div class="col-md-8">
											<select class="form-control" name="bill_mode"  id='bill_mode'>
												<option value="0" selected="selected">自種 / 外購</option>
												<option value="1">代工</option>
												<option value="2">委外代工</option>
											</select>
										</div>
									</div>

									<div class="form-group" id='order_type' style="display: none;">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;padding-bottom: 10px;"><span id="bill_request_title">訂單需求</span><font color="red">*</font></label>
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
											<input type="text" class="form-control" id="autocomplete_dropdown_onadd_part_no" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32">
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
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">下種日期<font color="red">*</font></label>
										<div class="col-md-8">
											<div class="input-group">
											    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
											    <input type="text" class="form-control" id="datetimepicker1" name="onadd_planting_date" placeholder="" required minlength="1" maxlength="32">
											</div>											
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">下種/入庫數量<font color="red">*</font></label>
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
									</div>
									<!-- 代工 -->
									<div class="form-group"  style="padding-bottom: 10px;display: none;" id="OEM">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;"><span id="foundry_name">催花價格</span><font color="red">*</font></label>
										<label for="addModalInput1" class="control-label" style="padding-left: 0px;"><span id="note">(總代工價格)</span></label>
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
									<!-- 催花 -->
									<div class="form-group"  id="UrgeFlowers" style="padding-bottom: 10px;display: none;">
										<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">催花價格<font color="red">*</font></label>
										<label for="addModalInput1" class="control-label" style="padding-left: 0px;"><span id="note">(代工價格/月)</span></label>
										<div class="col-md-4">
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: right;width: 4rem;padding-left: 0px;">A苗</label>									
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
												<input type="text" class="form-control" name="onadd_foundry_price_A" placeholder="必填" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
											</div>
											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">元</label>											
											<div class="help-block with-errors"></div>

											<label for="addModalInput1" class="col-md-1 control-label" style="text-align: right;width: 4rem;padding-left: 0px;">B苗</label>			
											<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
												<input type="text" class="form-control" name="onadd_foundry_price_B" placeholder="非必填" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
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
									<div class="form-group" style="padding-top: 2.09rem;">
										<label for="addModalInput1" class="col-md-3 control-label">花色</label>
										<div class="col-md-8">
											<input type="text" class="form-control" id="dropdown_onadd_color" name="onadd_color" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div  class="form-group" >
										<label for="addModalInput1" class="col-md-3 control-label">高度</label>
										<div class="col-md-8">
											<input type="text" class="form-control" id="dropdown_onadd_height" name="onadd_height" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label">花徑</label>
										<div class="col-md-8">
											<input type="text" class="form-control" id="dropdown_onadd_size" name="onadd_size" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-md-3 control-label" >適合開花盆徑</label>
										<div class="col-md-8">
											<input type="text" class="form-control" id="dropdown_onadd_pot_size" name="onadd_pot_size" placeholder="" >
											<div class="help-block with-errors"></div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<hr style="margin-top: 0rem;border-top: 2px solid #ddd;">

						<div class="container-fluid">
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
					</form>
				</div>
			</div>
		</div>

		<!--修改----------------------------------------------------------->
		<div id="adjust-modal" class="modal adjust-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="adjust_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">苗株資料修改</h4>
						</div>
						<div class="modal-body">
							<div class="container-fluid">
								<div class="row">
									<input type="hidden" name="op" value="adjust">
										<input type="hidden" name="onadd_sn">
										<input type="hidden" id="IsUploadImg" value="">
									<!--左邊欄位-->
									<div class="col-md-5" style="margin-top: 2rem;">
										<?php if(strpos($permmsion_option, "4") !== false || $permmsion == 0){ ?>
											<h4>產品圖片預覽</h4>
											<div class="well" style="text-align: center;width: 31.5rem;height: 31.5rem;">			
												<img class="preview" id="preview" style="max-width: 25rem; max-height: 25rem;">
											    <div class="size" id="preview_size" style="font-size: 1.5rem;"></div>
											</div>
										<?php } ?>
										<input type="file" class="upl" id="myFile" name="myFile" style="max-width: 31.5rem;border-radius:0.5rem;font-family: '微軟正黑體';" accept="image/jpeg,image/jpg,image/gif,image/png">
										<br/>
										<div class="form-group">
											<label class="col-md-3 control-label">目前尺寸<font color="red">*</font></label>
											<div class="col-md-8">
												<select class="form-control" id="dropdown_onadd_cur_size" name="onadd_cur_size">
													<option value="7">其他</option>
													<option value="11">4.0(含以上)</option>
													<option value="6">3.6</option>
													<option value="5">3.5</option>
													<option value="4">3.0</option>
													<option value="3">2.8</option>
													<option value="2">2.5</option>
													<option value="10">2.0</option>
													<option selected="selected" value="1">1.7</option>
												</select>
											</div>
										</div>
										<div class="form-group" style="padding-top: 0.9rem;">
											<label class="col-md-3 control-label" style="vertical-align: middle;top: -1rem;">下階段狀態<font color="red">*</font></label>
											<div class="col-md-8">
												<select class="form-control" name="onadd_next_status" id="next_status_adjust">
													<option value="3">出貨</option>
													<option value="2">催花</option>
													<option selected="selected" value="1">換盆</option>
												</select>
											</div>
										</div>
										<!-- 狀態切換 start -->
										<div class="form-group" id='form_1_adjust'>
											<label class="col-md-3 control-label" style="vertical-align: middle;top: -1rem;">預計換盆尺寸<font color="red">*</font></label>
											<div class="col-md-8">
												<select readonly="readonly" class="form-control" id="dropdown_onadd_growing" name="onadd_growing">
													<option value="9">出貨</option>
													<option value="6">3.6</option>
													<option value="5">3.5</option>
													<option value="4">3.0</option>
													<option value="3">2.8</option>
													<option value="2">2.5</option>
													<option value="10">2.0</option>
													<option selected="selected" value="1">1.7</option>
												</select>
											</div>
										</div>

										<div class="form-group" style="display: none;" id='form_2_adjust'>
											<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">規格<font color="red">*</font></label>
											<div class="col-md-8">
												<select readonly="readonly" class="form-control" id="adjust_dropdown_onadd_specification_other" name="onadd_specification">
													<option value="花梗出貨">花梗出貨</option>
													<option value="成花出貨" selected="selected">成花出貨</option>
													<option value="其他">其他</option>
												</select>
												<input style="display: none;" id="adjust_onadd_specification_other" type="text" class="form-control" name="onadd_specification_other" placeholder="請輸入規格" maxlength="32">
											</div>
										</div>
										<!-- 狀態切換 end -->
										<div class="form-group" style="margin-bottom: 2.5rem;">
											<label class="col-md-3 control-label">出貨規格<font color="red">*</font></label>
											<div class="col-md-8">
												<select class="form-control" id="dropdown_onadd_sell" name="onadd_sellsize">
													<option value="7">成花</option>
													<option value="11">4.0(含以上)</option>
													<option value="6">3.6</option>
													<option value="5">3.5</option>
													<option value="4">3.0</option>
													<option value="3">2.8</option>
													<option value="2">2.5</option>
													<option value="10">2.0</option>
													<option selected="selected" value="1">1.7</option>
												</select>
											</div>
										</div>
										<div class="form-group">										
											<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">放置區<font color="red">*</font></label>
											<div class="col-md-8">
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
										<div class="form-group">
											<label for="addModalInput1" class="col-md-3 control-label" style="">種植人員<font color="red">*</font></label>
											<div class="col-md-8">
												<select class="form-control"  id="autocomplete_onadd_plant_staff" name="onadd_plant_staff">
		        									<?php 
		        									foreach ($Staffs as $key => $value) {
		        										echo "<option value=".$value['jsuser_sn'].">".$value['jsuser_name']."</option>";
		        									}
		        									?>
		    									</select>
											</div>	
										</div>	
									</div>
									<!--右邊欄位-->
									<div class="col-md-7" style="margin-top: 2rem;">
										<h4>&nbsp;</h4>
										<div class="form-group">
											<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;padding-bottom: 10px;">項目<font color="red">*</font></label>
											<div class="col-md-8">
												<select class="form-control" name="bill_mode"  id='bill_mode_adjust'>
													<option value="0" selected="selected">自種 / 外購</option>
													<option value="1">代工</option>
													<option value="2">委外代工</option>
												</select>
											</div>
										</div>

										<div class="form-group" id='order_type_adjust' style="display: none;">
											<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;padding-bottom: 10px;"><span id="bill_request_title_adjust">訂單需求</span><font color="red">*</font></label>
											<div class="col-md-8">
												<select class="form-control" name="onadd_isbought"  id='onadd_isbought_adjust'>
													<option value="0" selected="selected" class="oem_option">苗株</option>
													<option value="1" class="oem_option_adjust">催花</option>
													<option value="2" class="foundry_option_adjust">自種苗</option>
													<option value="3" class="foundry_option_adjust">外購苗</option>
												</select>
											</div>
										</div>

										<div class="form-group">
											<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">品號<font color="red">*</font></label>
											<div class="col-md-8">
												<input type="text" class="form-control" id="autocomplete_dropdown_onadd_part_no" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32">
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
											<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">下種日期<font color="red">*</font></label>
											<div class="col-md-8">
												<div class="input-group">
												    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
												    <input type="text" class="form-control" id="datetimepicker_adjsut" name="onadd_planting_date" placeholder="" required minlength="1" maxlength="32">
												</div>											
												<div class="help-block with-errors"></div>
											</div>
										</div>
										<div class="form-group">
											<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">下種/入庫數量<font color="red">*</font></label>
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
										</div>
										<!-- 代工 -->
										<div class="form-group"  style="padding-bottom: 10px;display: none;" id="OEM_adjust">
											<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;"><span id="foundry_name_adjust">催花價格</span><font color="red">*</font></label>
											<label for="addModalInput1" class="control-label" style="padding-left: 0px;"><span id="note">(總代工價格)</span></label>
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
										<!-- 催花 -->
										<div class="form-group"  id="UrgeFlowers_adjust" style="padding-bottom: 10px;display: none;">
											<label for="addModalInput1" class="col-md-3 control-label" style="padding-left: 0px;">催花價格<font color="red">*</font></label>
											<label for="addModalInput1" class="control-label" style="padding-left: 0px;"><span id="note">(代工價格/月)</span></label>
											<div class="col-md-4">
												<label for="addModalInput1" class="col-md-1 control-label" style="text-align: right;width: 4rem;padding-left: 0px;">A苗</label>									
												<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
													<input type="text" class="form-control" name="onadd_foundry_price_A" placeholder="必填" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
												</div>
												<label for="addModalInput1" class="col-md-1 control-label" style="text-align: left;width: 1rem;padding-left: 0px;">元</label>											
												<div class="help-block with-errors"></div>

												<label for="addModalInput1" class="col-md-1 control-label" style="text-align: right;width: 4rem;padding-left: 0px;">B苗</label>			
												<div class="col-md-1" style="padding-right: 0px;padding-left: 0px;width: 6rem;">
													<input type="text" class="form-control" name="onadd_foundry_price_B" placeholder="非必填" minlength="1" maxlength="32" style="border-bottom: 1px solid rgba(0, 0, 0, 0.6);border-top: 0px;border-right: 0px;border-left: 0px;height: 28px;padding: 0px;text-align: center;">
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
										<div class="form-group" style="padding-top: 2.09rem;">
											<label for="addModalInput1" class="col-md-3 control-label">花色</label>
											<div class="col-md-8">
												<input type="text" class="form-control" id="dropdown_onadd_color" name="onadd_color" placeholder="" >
												<div class="help-block with-errors"></div>
											</div>
										</div>
										<div  class="form-group" >
											<label for="addModalInput1" class="col-md-3 control-label">高度</label>
											<div class="col-md-8">
												<input type="text" class="form-control" id="onadd_height" name="onadd_height" placeholder="" >
												<div class="help-block with-errors"></div>
											</div>
										</div>
										<div class="form-group">
											<label for="addModalInput1" class="col-md-3 control-label">花徑</label>
											<div class="col-md-8">
												<input type="text" class="form-control" id="dropdown_onadd_size" name="onadd_size" placeholder="" >
												<div class="help-block with-errors"></div>
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
							</div>

							<hr style="margin-top: 0rem;border-top: 2px solid #ddd;">

							<div class="container-fluid">
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
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="reset" class="btn btn-default">清空</button>
							<button type="submit" class="btn btn-primary">更新</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!--圖片上傳Modal----------------------------------------------------------->
		<div id="upload_img_modal" class="modal upd-modal2" tabindex="-1" role="dialog">
			<div class="modal-dialog mw-100 w-75">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<h4 class="modal-title" id="history_title">提示視窗！</h4>							
						</div>		
						<div style="text-align: center;font-size: 15px">確認上傳此圖片嗎？</div>				
						<div class="modal-footer">
							<button id="upload_img_yes" type="button" class="btn btn-primary">確認</button>
							<button id="upload_img_no" type="button" class="btn btn-default" data-dismiss="modal">關閉</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--圖片上傳Modal----------------------------------------------------------->

		<!-- container -->
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">

				<div class="navbar-collapse collapse pull-right" style="margin-bottom: 10px;">
					<ul class="nav nav-pills pull-right toolbar">
						<?php if($permmsion == 0 || strpos($permmsion_option, '1') !== false){ ?>
							<li><button data-parent="#toolbar" id="btn_add" data-toggle="modal" data-target=".add-modal" class="accordion-toggle btn btn-primary"><i class="glyphicon glyphicon-plus"></i> 苗株資料建立</button></li>
						<?php } ?>
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
						<div id="qr_sticker_sn" style="text-align:left;font-size: 30px;height: 40px;margin-top: 10px;">產品編號:2019-17</div>
						<div id="qr_sticker_part_no" style="text-align:left;font-size: 30px;height: 40px;">品號:P1015</div>
						<div id="qr_sticker_part_name" style="text-align:left;font-size: 30px;height: 40px;">品名:維維安Vivian (2號)</div>
						<div id="qr_sticker_date" style="text-align:left;font-size: 30px;height: 40px;">下種日期:2019-08-01</div>
						<div id="qr_sticker_location" style="text-align:left;font-size: 30px;height: 40px;">位置:A5</div>
						<div style="text-align:left;">
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
								<th style="text-align: center;">下種日期</th>
								<th style="text-align: center;">下種數量</th>
								<th style="text-align: center;">目前尺寸</th> <!-- 2019/8/29新增 -->
								<th style="text-align: center;">下階段換盆尺寸</th> <!-- 2019/6/19新增 -->
								<th style="text-align: center;">預計出貨規格</th> <!-- 2020/01/2新增 -->
								<th style="text-align: center;">預計成熟日/出貨日</th> <!-- 2019/6/19新增 -->								
								<th style="text-align: center;">育成率</th> <!-- 2019/6/19新增 -->
								<th style="text-align: center;">放置區</th> <!-- 2019/8/7新增 -->
								<th style="text-align: center;">備註</th> <!-- 2019/6/19新增 -->
								<th style="text-align: center;">供應商</th>
								
								<!--<th style="text-align: center;">訂單客戶</th>  2019/6/19新增 -->			
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
						<tbody >
							<?php
							foreach ($product_list as $row) {								
								echo '<tr>';//產品編號
									$sn = SnProcessor($row['onadd_sn'], $row['onadd_type'], $row['onadd_newpot_sn'], $row['onadd_ml'], $row['onadd_planting_date'], $row['onadd_AB_sn'], $row['onadd_level']);
									$qr_sn = $sn;

									echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$sn.'</td>';

        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$row['onadd_part_no'].'</td>';//品號
        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$row['onadd_part_name'].'</td>';//品名  							
        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.date('Y-m-d',$row['onadd_planting_date']).'</td>';
        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$row['onadd_quantity'].'</td>';//品名

        							//目前尺寸
        							if(!empty($DEVICE_SYSTEM[$row['onadd_cur_size']])){        								
        								if($row['onadd_cur_size'] == 8){//瓶苗開瓶
											echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$DEVICE_SYSTEM[$row['onadd_cur_size']].'</td>';
        								}
										else{
	        								echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$DEVICE_SYSTEM[$row['onadd_cur_size']].'</td>';
										}
	        						}
	        						else{
	        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;"></td>';
	        						}

	        						//下階段換盆
	        						if($row['onadd_growing'] == "出貨"){//出貨
										echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;"><font color="#666666">'.(($DEVICE_SYSTEM[$row['onadd_growing']] != '') ? $DEVICE_SYSTEM[$row['onadd_growing']] : $row['onadd_growing']).'</font></td>';
        							}
									else{
	        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;"><font color="#666666">'.(($DEVICE_SYSTEM[$row['onadd_growing']] != '') ? $DEVICE_SYSTEM[$row['onadd_growing']] : $row['onadd_growing']).(($row['onadd_specification'] != '') ? " (".$row['onadd_specification'].")" : "").'</font></td>';
									}
									
	        						//預計出貨尺寸
	        						if(!empty($row['onadd_sellsize'])){
        								echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$SellSize[$row['onadd_sellsize']].'</td>';
        							}
        							else{
        								echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;"></td>';
        							}

        							//預計成熟日
        							$cur_size = $DEVICE_SYSTEM[$row['onadd_cur_size']];
        							if($row['onadd_next_status'] == 1){
	        							$growing_size = $DEVICE_SYSTEM[$row['onadd_growing']];
	        						}
	        						else if($row['onadd_next_status'] == 2){
	        							$growing_size = "催花";
	        						}
	        						else if($row['onadd_next_status'] == 3 || strpos($row['onadd_growing'],'出貨') !== false){
	        							$growing_size = "出貨";
	        						}

        							$onchba_cycle = getSettingBySn($cur_size,$growing_size)['onchba_cycle'];
        							$pre_days = date("Y/m/d", strtotime("+$onchba_cycle days", $row['onadd_planting_date']));
        							
        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$pre_days.'</td>';
        							$onadd_cycle = ((date('m',$row['onadd_cycle']))-(date('m',$row['onadd_planting_date'])));
        							 	
	        						$livability = getLivability($row['onadd_AB_sn'], $row['onadd_newpot_sn'], $row['onadd_ml'], $row['onadd_sn'], $row['onadd_level']);

        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.number_format(($livability*100),2).'%</td>'; 	

        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$row['onadd_location'].'</td>'; 		

        							if(!empty($row['onadd_quantity_cha'])){
        								$note = '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">換盆</td>';
        							}	
        							else if($row['onadd_ml'] != '0'){
        								$note = '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">轉移</td>';
        							}
        							else{
        								$note = '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;"></td>';
        							}    						
        							echo $note;
        							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$row['onadd_supplier'].'</td>';
   
        							if($permmsion == 0 || $permmsion == 2){
        								echo '<td style="vertical-align: middle;text-align:center" style="border-right:0.1rem #BEBEBE dashed;text-align: center;">';  
										if(strpos($permmsion_option, "2") !== false || $permmsion == 0){										
											echo '<span >
	        							  	<button type="button" style="background-color:#E94653;" class="btn btn-danger btn-xs upd1" data-onadd_sn="'.$row['onadd_sn'].'">汰除</button>
	        							  </span>';
										}

										if(strpos($permmsion_option, "3") !== false){											
											echo '<span >
	        							  	<button type="button" style="background-color:#f67828;border:#f67828" class="btn btn-danger btn-xs adjust" data-onadd_sn="'.$row['onadd_sn'].'">修改</button>
	        							  </span>';
										}

										if(strpos($permmsion_option, "5") !== false || $permmsion == 0){											
											echo '<span >
	        							      	<button type="button" style="background-color:#6CBF87;border:#6CBF87" class="btn btn-success btn-xs upd2" data-onadd_sn="'.$row['onadd_sn'].'">出貨</button>
	        							      </span>';
										}
										if(strpos($permmsion_option, "6") !== false || $permmsion == 0){											
											echo '<span ><button type="button" style="background-color:#FCD78B;border:#FCD78B;color:#642100" class="btn btn-warning btn-xs upd3" data-onadd_sn="'.$row['onadd_sn'].'">轉移</button></span>';
										}
										
										// if($row['onadd_cur_size'] != $row['onadd_sellsize']){
	        								echo '<span >
	        							      	<button type="button" style="background-color:#A46B62;border:#A46B62" class="btn btn-primary btn-xs upd" data-onadd_sn="'.$row['onadd_sn'].'">換盆</button>
	        							      </span>';        							
										// }
										// else{
										// 	echo '<span >
	        	// 						      	<button type="button" style="background-color:#A46B62;border:#A46B62" class="btn btn-primary btn-xs upd" data-onadd_sn="'.$row['onadd_sn'].'" disabled="disabled">換盆</button>
	        	// 						      </span>';    
										// }
	        							// if($permmsion == 0 || $permmsion == 0){
	        							// 	echo '<button type="button" class="btn btn-danger btn-xs del" data-onadd_sn="'.$row['onadd_sn'].'">刪除</button>&nbsp;';
	        							// }
	        							
	        							echo '<span > </span><span ><button type="button" class="btn btn-default btn-xs qr" data-onadd_sn="'.$row['onadd_sn'].'" data-qr_sn="'.$qr_sn.'"><span style="font-size:2em;color:#000000" class="glyphicon glyphicon-qrcode"></span></button>';
	        							
	        						}       	

	        						// if($permmsion == 0 || $permmsion == 3){
	        						// 	echo '<td style="vertical-align: middle;text-align:center">
	        						// 		<button type="button" class="btn btn-info btn-xs" onclick="javascript:location.href=\''.WT_SERVER.'/admin/purchase/details_table.php?onadd_part_no='.$row['onadd_part_no'].'&onadd_growing='.$row['onadd_growing'].'&onadd_part_name='.$row['onadd_part_name'].'&onadd_quantity_del='.date("Y").'\'" ><span class="glyphicon glyphicon-list-alt" style="font-size:1.3em"></span> 展開</button>';
	        						// }							
	        						echo '</td>';
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
        <!-- <script src="./../../js1/bootstrap-datepicker.js"></script> -->
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