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
	7=>"其他",
	8=>"瓶苗下重"
		// 1.7, 2.5, 2.8, 3.0, 3.5, 3.6 其他
);
$permissions_mapping = array(
	0=>'<font color="#666666">瓶苗</font>',
	1=>'<font color="#666666">1.7寸</font>',
	2=>'<font color="#666666">2.5寸</font>',
	3=>'<font color="#666666">2.8寸</font>',
	4=>'<font color="#666666">3.0寸</font>',
	5=>'<font color="#666666">3.5寸</font>',
	6=>'<font color="#666666">3.6寸</font>',
	7=>'<font color="#666666">成花</font>',
	8=>'<font color="#666666">瓶苗開瓶</font>' 
);

$permmsion = $_SESSION['user']['jsuser_admin_permit'];
// line_notify("浪漫duke, 找尋屬於妳的浪漫");
// exit;


$op=GetParam('op');
if(!empty($op)) {
	$ret_code = 1;
	$ret_msg = '';
	$ret_data = array();
	switch ($op) {

		case 'get':
			$onproduct_sn=GetParam('onproduct_sn');
			$ret_data = array();
			if(!empty($onproduct_sn)){
				$ret_code = 1;
				$ret_data = getProductData($onproduct_sn);
				// $ret_msg = "123";
			} else {
				$ret_code = 0;
			}
			// printr($ret_data);
			// exit;

		break;

		case 'IsOver5Pics':
			$onproduct_sn=GetParam('onproduct_sn');
			$ret_data = array();
			if(!empty($onproduct_sn)){
				$ret_code = 1;
				$ret_data = getPicQty($onproduct_sn);
				// $ret_msg = "123";
			} else {
				$ret_code = 0;
			}
			// printr($ret_data);
			// exit;

		break;

		//新增預計出貨---------------------------------------------
		case 'upd1':
			$onadd_sn = GetParam('onadd_sn');
			$onbuda_quantity = GetParam('onbuda_quantity');
			$quantity = GetParam('quantity');//此批號數量
			$onbuda_size = GetParam('onbuda_size');
			$onbuda_date = strtotime (GetParam('onbuda_date'));
			$onbuda_client = GetParam('onbuda_client');
			$onbuda_seller = GetParam('onbuda_seller');
			$onbuda_sell_price = GetParam('onbuda_sell_price');
			$onbuda_year = substr(GetParam('onbuda_date'),0,4);
			$onbuda_month = substr(GetParam('onbuda_date'),5,-3);
			$now = time();
			$conn = getDB();
			$left_number = $quantity - $onbuda_quantity;
			if($left_number < 0){
				$ret_msg = "預計出貨數量大於此批號可供出貨量！";
			}
			else{
				$data = getDetailsBySn($onadd_sn);
				// $onbuda_quantity += $data['onadd_prebill_quantity'];
				$sql = "INSERT INTO `onliine_business_data`(`onbuda_add_date`, `onbuda_mod_date`, `onbuda_status`, `onbuda_part_no`, `onbuda_part_name`, `onbuda_date`, `onbuda_quantity`, `onbuda_size`, `onbuda_client`, `onbuda_year`, `onbuda_day`, onbuda_seller, onbuda_sell_price, onadd_sn) ".
					"VALUES('{$now}', '{$now}', '1','".$data['onadd_part_no']."','".$data['onadd_part_name']."','{$onbuda_date}','{$onbuda_quantity}','{$onbuda_size}','{$onbuda_client}','{$onbuda_year}','{$onbuda_month}','{$onbuda_seller}', '{$onbuda_sell_price}', '{$onadd_sn}');";
					$ret_msg = $sql;
				if($conn->query($sql)) {
					$sql2 = "UPDATE onliine_add_data SET onadd_prebill_quantity = '{$onbuda_quantity}' WHERE onadd_sn = '{$onadd_sn}'";
					if($conn->query($sql2)) {
						line_notify("\n預定出貨通知
".Date('Y-m-d',$now)." , 有一筆預定訂單資料新增：\n
客戶：".$onbuda_client."\n
預定交貨尺寸：".$DEVICE_SYSTEM[$onbuda_size]."\n
預定交貨數量：".$onbuda_quantity."\n
預定交貨日期：".GetParam('onbuda_date'));	
						$ret_msg = "預訂出貨成功！";
					}
					else{
						$ret_msg = "更新資料失敗！";
					}	
				} else {
					$ret_msg = "失敗！";
				}
			}

		break;

		//修改預計出貨---------------------------------------------
		case 'upd1_adjust':
			$onadd_sn = GetParam('onadd_sn');
			$onbuda_sn = GetParam('onbuda_sn');
			$onbuda_quantity = GetParam('onbuda_quantity');
			$quantity = GetParam('quantity');//此批號數量
			$onbuda_size = GetParam('onbuda_size');
			$onbuda_date = strtotime (GetParam('onbuda_date'));
			$onbuda_client = GetParam('onbuda_client');
			$onbuda_seller = GetParam('onbuda_seller');
			$onbuda_sell_price = GetParam('onbuda_sell_price');
			$onbuda_year = substr(GetParam('onbuda_date'),0,4);
			$onbuda_month = substr(GetParam('onbuda_date'),5,-3);

			$now = time();
			$conn = getDB();

			if(empty($onbuda_quantity) || empty($onbuda_date)){
				$ret_msg = "*為必填！";
			}
			else{
				$sql = "UPDATE `onliine_business_data` SET `onadd_sn` = '{$onadd_sn}', `onbuda_quantity` = '{$onbuda_quantity}', `onbuda_size` = '{$onbuda_size}', `onbuda_date` = '{$onbuda_date}', `onbuda_client` = '{$onbuda_client}', `onbuda_seller` = '{$onbuda_seller}', `onbuda_sell_price` = '{$onbuda_sell_price}', `onbuda_year` = '{$onbuda_year}', `onbuda_month` = '{$onbuda_month}' WHERE onbuda_sn = '{$onbuda_sn}'";

				if($conn->query($sql)) {
					$ret_msg = "修改成功！";						
				} else {
					$ret_msg = "修改失敗！";
				}
			}
		break;
		//新增預計出貨---------------------------------------------

		//預計出貨修改---------------------------------------------
		case 'customer_get':
			$onbuda_sn=GetParam('onbuda_sn');
			if(empty($onbuda_sn)){
				$ret_code = 0;
			}else{
				$ret_code = 1;
				$ret_data = getExpectedListBySn($onbuda_sn);
			}
		break;

		case 'updprebill':
			$onbd_part_no = GetParam('onbd_part_no');
			$onbd_part_name = GetParam('onbd_part_name');
			$onbd_quantity = GetParam('onbd_quantity');
			$onbd_sell_date = strtotime(GetParam('onbd_sell_date'));
			$onbd_size = GetParam('onbd_size');
			$onbd_client = GetParam('onbd_client');
			$onbd_seller = GetParam('onbd_seller');
			$onbd_sell_price = GetParam('onbd_sell_price');

			$now = time();
			$conn = getDB();

			if(empty($onbd_quantity) || empty($onbd_sell_date)){
				$ret_msg = "*為必填！";
			}
			else{
				$sql = "INSERT INTO `onliine_bill_data`(`onbd_add_date`, `onbd_mod_date`, `onbd_status`, `onbd_part_no`, `onbd_part_name`, `onbd_sell_date`, `onbd_quantity`, `onbd_sell_price`, `onbd_size`, `onbd_seller`, `onbd_client`) 
				VALUES ('{$now}', '{$now}', '1', '{$onbd_part_no}', '{$onbd_part_name}', '{$onbd_sell_date}', '{$onbd_quantity}', '{$onbd_sell_price}', '{$onbd_size}', '{$onbd_seller}', '{$onbd_client}')";
				if($conn->query($sql)) {					
					line_notify("\n預定訂單通知
".Date('Y-m-d',$now)." , 有一筆預定訂單資料新增：\n
品名：".$onbd_part_name."\n
品號：".$onbd_part_no."\n
預定交貨尺寸：".$DEVICE_SYSTEM[$onbd_size]."\n
預定交貨數量：".$onbd_quantity."\n
預定交貨日期：".GetParam('onbd_sell_date'));	
					$ret_msg = "預定成功！";					
				} else {
					$ret_msg = "預定失敗！";
				}
			}
		break;

		//修改預定訂單---------------------------------------------
		case 'updprebill_adjust':
			$onbd_sn = GetParam('onbd_sn');
			$onbd_part_no = GetParam('onbd_part_no');
			$onbd_part_name = GetParam('onbd_part_name');
			$onbd_quantity = GetParam('onbd_quantity');
			$onbd_sell_date = strtotime(GetParam('onbd_sell_date'));
			$onbd_size = GetParam('onbd_size');
			$onbd_client = GetParam('onbd_client');
			$onbd_seller = GetParam('onbd_seller');
			$onbd_sell_price = GetParam('onbd_sell_price');

			$now = time();
			$conn = getDB();

			if(empty($onbd_quantity) || empty($onbd_sell_date)){
				$ret_msg = "*為必填！";
			}
			else{
				$sql = "UPDATE `onliine_bill_data` SET `onbd_mod_date` = '{$now}', `onbd_part_no` = '{$onbd_part_no}', `onbd_part_name` = '{$onbd_part_name}', `onbd_sell_date` = '{$onbd_sell_date}', `onbd_quantity` = '{$onbd_quantity}', `onbd_sell_price` = '{$onbd_sell_price}', `onbd_size` = '{$onbd_size}', `onbd_seller` = '{$onbd_seller}', `onbd_client` = '{$onbd_client}' WHERE onbd_sn = '{$onbd_sn}'";

				if($conn->query($sql)) {
					$ret_msg = "修改成功！";						
				} else {
					$ret_msg = "修改失敗！";
				}
			}
		break;

		//刪除---------------------------------------------
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

		//刪除預定訂單---------------------------------------------
		case 'prebill_del':
			$onbd_sn=GetParam('onbd_sn');
			if(empty($onbd_sn)){
				$ret_msg = "刪除失敗！";
			}else{
				$now = time();
				$conn = getDB();
				$sql = "DELETE FROM onliine_bill_data WHERE onbd_sn = '{$onbd_sn}'";
				if($conn->query($sql)) {
					$ret_msg = "刪除完成！";
				} else {
					$ret_msg = "刪除失敗！";
				}
				$conn->close();
			}
		break;		

		//預定訂單修改---------------------------------------------
		case 'prebill_get':
			$onbd_sn=GetParam('onbd_sn');
			if(empty($onbd_sn)){
				$ret_msg = "搜尋失敗！";
			}else{
				$ret_code = 1;
				$ret_data = getPrebillListBySn($onbd_sn);
			}
		break;

		//刪除預計出貨---------------------------------------------
		case 'customer_del':
			$onbuda_sn=GetParam('onbuda_sn');
			if(empty($onbuda_sn)){
				$ret_msg = "刪除失敗！";
			}else{
				$now = time();
				$conn = getDB();
				$sql = "DELETE FROM onliine_business_data WHERE onbuda_sn = '{$onbuda_sn}'";
				if($conn->query($sql)) {
					$ret_msg = "刪除完成！";
				} else {
					$ret_msg = "刪除失敗！";
				}
				$conn->close();
			}
		break;	
		
		//取得預計出貨明細-----------------------------------
		case 'get_customer_list':
		$onbuda_part_no=GetParam('onadd_part_no');
		$year=GetParam('year');
		$month=GetParam('month');
		$size=GetParam('size');
		if(empty($onbuda_part_no)){
			$ret_code = 0;
		}else{
			$ret_msg = '';
			$ret_code = 1;
			$ret_data = getExpectedList($onbuda_part_no,$year,$month,$size);
		}
		break;

		//取得預定訂單明細-----------------------------------
		case 'get_prebill_list':
		$onbd_part_no=GetParam('onbd_part_no');
		$onbd_part_name=GetParam('onbd_part_name');
		$year=GetParam('year');
		$month=GetParam('month');
		$size=GetParam('size');
		if(empty($onbd_part_no) || empty($onbd_part_name)){
			$ret_code = 0;
		}else{
			$ret_code = 1;
			$ret_data = getPrebillDetailList($onbd_part_no,$onbd_part_name,$year,$month,$size);
		}
		break;

		//汰除---------------------------------------------
		case 'eli':
		$onadd_sn=GetParam('onadd_sn');
		$onadd_newpot_sn=GetParam('onadd_newpot_sn');
		if($onadd_newpot_sn == "0"){
			$list = getUserBySn($onadd_sn);
		}
		else{
			$list = getUserBySn($onadd_newpot_sn);
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

		default:
			$ret_msg = 'error!';
		break;
	}

	echo enclode_ret_data($ret_code, $ret_msg, $ret_data);
	exit;
} else {
	function getSQL($qry) {
		$conn = getDB();
		$result = $conn->query($qry);
		$conn->close();
		return $result;
	}

	$onadd_part_no = GetParam('onadd_part_no');
	$onadd_part_name = GetParam('onadd_part_name');
	$onadd_growing = GetParam('onadd_growing');
	$onadd_quantity_del = GetParam('onadd_quantity_del');
	$expected_count_list = getExpectedShipByMonth($onadd_quantity_del,$onadd_part_no,$onadd_growing,"0,2");
	$oem_expected_count_list = getExpectedShipByMonth($onadd_quantity_del,$onadd_part_no,$onadd_growing,1);
	$business_data = getBusinessData($onadd_part_no,$onadd_quantity_del);
	// printr($oem_expected_count_list);
	// exit;
	$data_list = getDataDetails($onadd_part_no,$onadd_part_name);
	$eli_list = getQuantityForseller($data_list[0]['onproduct_part_no'],$data_list[0]['onproduct_part_name']);
	$prebill_list = getPrebillList($onadd_part_no,$onadd_part_name,$onadd_quantity_del);
	$onproduct_sn = $data_list[0]['onproduct_sn'];
	// printr($eli_list);
	// exit;
}
$Staffs = get_Staffs();

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
	<script type="text/javascript">
		//汰除-----------------------------------------------------------
			function do_emli(onadd_sn){
				$('#eli-modal1').modal();
				$('#eli_form1')[0].reset();

				$.ajax({
					url: './plant_purchase.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get", onadd_sn:onadd_sn},
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
			                	$('#eli_form1 input[name=onadd_sn]').val(d.onadd_sn);
			                	$('#eli_form1 input[name=onadd_part_no]').val(d.onadd_part_no);
			                	$('#eli_form1 input[name=onadd_quantity]').val(d.onadd_quantity);
			                	if(d.onadd_newpot_sn == "0"){
				                	$('#eli_form1 input[name=onadd_newpot_sn]').val(d.onadd_sn);
				                }
				                else{
				                	$('#eli_form1 input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
				                }
			                	
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			};
			//汰除-----------------------------------------------------------
		$(document).ready(function() {
			$('#carousel-example-generic').carousel({
			    interval: false
			});

			<?php
					//	init search parm
			// print "$('#search [name=onadd_status] option[value={$onadd_status}]').prop('selected','selected');";
			// print "$('#search [name=onadd_growing] option[value={$onadd_growing}]').prop('selected','selected','selected','selected','selected','selected','selected');";
			?>

			

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

			$('button.upd1').on('click', function(){
				sn = $(this).data('sn');
				showsn = $(this).data('showsn');
				quantity = $(this).data('quantity');
				$('#upd_form1 input[name=onadd_sn]').val(sn);
			    $('#upd_form1 input[name=sn]').val(showsn);
			    $('#upd_form1 input[name=quantity]').val(quantity);

			    $('#upd-modal1').modal();
			});

			//預定訂單按鈕
			$('button.upd_prebill').on('click', function(){				
				$('#updprebill_form input[name=onbd_part_no]').val('<?php echo $onadd_part_no?>');
			    $('#updprebill_form input[name=onbd_part_name]').val('<?php echo $onadd_part_name?>');

			    $('#updprebill-modal').modal();
			});

			

			$('#upd_form1, #updprebill_form, #updprebill_adjust_form').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					e.preventDefault();
					var param = $(this).serializeArray();
					$(this).parents('.modal').modal('hide');
					$(this)[0].reset();
					 	console.table(param);
					$.ajax({
						url: './details_table.php',
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
			          		console.log('ajax error');
			               // console.log(xhr);
			           }
			       });
				}
			});

			$('#datetimepicker1, #prebill_datetimepicker').datetimepicker({
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

			$('button.cancel').on('click', function() {
				location.href = "./../";
			});
		});

		function customer_list(onadd_part_no,year,month,size){
			$('#month_customers_title').html(year+" 年 "+month+" 月 - "+onadd_part_no+" 客戶明細(預計出貨)");
			$('#modal_month_customers').modal();
			$.ajax({
				url: './details_table.php',
				type: 'post',
				dataType: 'json',
				data: {op:"get_customer_list", onadd_part_no:onadd_part_no, year:year, month:month, size:size},
				beforeSend: function(msg) {
					$("#ajax_loading").show();
				},
				complete: function(XMLHttpRequest, textStatus) {
					$("#ajax_loading").hide();
				},
				success: function(ret) {
					$('#month_customers_cotent').html('<div class="col-md-12"><div class="col-sm-12"><label for="addModalInput1" class="col-sm-1 control-label" style="width: 90px;">客戶名稱</label><label for="addModalInput1" class="col-sm-2 control-label" style="width: 110px;">預計出貨數量</label><label for="addModalInput1" class="col-sm-2 control-label">預計出貨日期</label><label for="addModalInput1" class="col-sm-2 control-label">預計出貨尺寸</label><label for="addModalInput1" class="col-sm-2 control-label">該筆新增日期</label><label for="addModalInput1" class="col-sm-2 control-label" style="width: 90px;">售出價格</label><label for="addModalInput1" class="col-sm-1 control-label" style="width: 90px;">操作</label></div></div>');
					$.each(ret.data, function(key,value){	
						if(key < ret.data.length){										
							// $('#month_customers_cotent').html($('#month_customers_cotent').html()+'<div class="col-md-12"><div class="col-sm-12"><label for="addModalInput1" class="col-sm-1 control-label" style="width: 90px;">'+value.onbuda_client+'</label><label for="addModalInput1" class="col-sm-2 control-label" style="width: 110px;">'+value.onbuda_quantity+'</label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbuda_date+'</label></label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbuda_size+'吋</label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbuda_add_date+'</label><label for="addModalInput1" class="col-sm-2 control-label" style="width: 90px;">$'+value.onbuda_sell_price+'</label><label for="addModalInput1" class="col-sm-1 control-label" style="width: 120px;"><button type="button" style="background-color:#f67828;border:#f67828" class="btn btn-danger btn-xs customer_adjust" data-onbuda_sn="'+value.onbuda_sn+'">修改</button> <button type="button" style="background-color:#E94653;border:#f67828" class="btn btn-danger btn-xs customer_del" data-onbuda_sn="'+value.onbuda_sn+'">刪除</button></label></div></div>');
							$('#month_customers_cotent').html($('#month_customers_cotent').html()+'<div class="col-md-12"><div class="col-sm-12"><label for="addModalInput1" class="col-sm-1 control-label" style="width: 90px;">'+value.onbuda_client+'</label><label for="addModalInput1" class="col-sm-2 control-label" style="width: 110px;">'+value.onbuda_quantity+'</label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbuda_date+'</label></label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbuda_size+'吋</label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbuda_add_date+'</label><label for="addModalInput1" class="col-sm-2 control-label" style="width: 90px;">$'+value.onbuda_sell_price+'</label></div></div>');									
						}

					});

					$('button.customer_del').on('click', function(){
						onbuda_sn = $(this).data('onbuda_sn');
						bootbox.confirm("確認刪除？", function(result) {
							if(result) {
								$.ajax({
									url: './details_table.php',
									type: 'post',
									dataType: 'json',
									data: {op:"customer_del", onbuda_sn:onbuda_sn},
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

					$('button.customer_adjust').on('click', function(){
						var onbuda_sn = $(this).data('onbuda_sn');
						var onadd_sn = $(this).data('onadd_sn');
						var quantity = $(this).data('quantity');

						$.ajax({
							url: './details_table.php',
							type: 'post',
							dataType: 'json',
							data: {op:"customer_get", onbuda_sn:onbuda_sn},
							beforeSend: function(msg) {
								$("#ajax_loading").show();
							},
							complete: function(XMLHttpRequest, textStatus) {
								$("#ajax_loading").hide();
							},
							success: function(ret) {
								var data = ret.data;						
								// $('#upd_adjust-modal1 input[name=onadd_sn]').val(data.onadd_sn);
								$('#upd_adjust-modal1 input[name=onbuda_quantity]').val(data.onbuda_quantity);
								$('#upd_adjust-modal1 input[name=onbuda_date]').val(data.onbuda_date);
								$('#upd_adjust-modal1 [name=onbuda_size] option[value='+data.onbuda_size+']').prop('selected','selected');
								$('#upd_adjust-modal1 input[name=onbuda_client]').val(data.onbuda_client);
								$('#upd_adjust-modal1 input[name=onbuda_seller]').val(data.onbuda_seller);
								$('#upd_adjust-modal1 input[name=onbuda_sell_price]').val(data.onbuda_sell_price);

								$('#upd_adjust-modal1').modal('show');
							},
							error: function (xhr, ajaxOptions, thrownError) {
						        	
						    }
						});
					});	
					
				},
				error: function (xhr, ajaxOptions, thrownError) {
			   	console.log('ajax error');
			        // console.log(xhr);
			    }
			});
			// $('#month_customers_cotent').html($('#month_customers_cotent').html()+'<div class="col-md-12"><div class="col-sm-10"><label for="addModalInput1" class="col-sm-2 control-label">客戶名稱</label><label for="addModalInput1" class="col-sm-2 control-label">預計出貨數量</label><label for="addModalInput1" class="col-sm-2 control-label">預計出貨日期</label><label for="addModalInput1" class="col-sm-2 control-label">該筆新增日期</label></div></div>');
		}

		function prebill_list(onadd_part_no,onadd_part_name,year,month,size){
			$('#month_prebill_title').html(year+" 年 "+month+" 月 - "+onadd_part_no+" 客戶明細(預定訂單)");
			$('#modal_month_prebill').modal();
			$.ajax({
				url: './details_table.php',
				type: 'post',
				dataType: 'json',
				data: {op:"get_prebill_list", onbd_part_no:onadd_part_no, onbd_part_name:onadd_part_name, year:year, month:month, size:size},
				beforeSend: function(msg) {
					$("#ajax_loading").show();
				},
				complete: function(XMLHttpRequest, textStatus) {
					$("#ajax_loading").hide();
				},
				success: function(ret) {
					// console.log(ret);
					$('#month_prebill_cotent').html('<div class="col-md-12"><div class="col-sm-12"><label for="addModalInput1" class="col-sm-1 control-label" style="width: 90px;">客戶名稱</label><label for="addModalInput1" class="col-sm-2 control-label" style="width: 110px;">預計出貨數量</label><label for="addModalInput1" class="col-sm-2 control-label">預計出貨日期</label><label for="addModalInput1" class="col-sm-2 control-label">預計出貨尺寸</label><label for="addModalInput1" class="col-sm-2 control-label">該筆新增日期</label><label for="addModalInput1" class="col-sm-2 control-label" style="width: 90px;">售出價格</label><label for="addModalInput1" class="col-sm-1 control-label" style="width: 90px;">操作</label></div></div>');

					$.each(ret.data, function(key,value){	
						if(key < ret.data.length){										
							// $('#month_prebill_cotent').html($('#month_prebill_cotent').html()+'<div class="col-md-12"><div class="col-sm-12"><label for="addModalInput1" class="col-sm-1 control-label" style="width: 90px;">'+value.onbd_client+'</label><label for="addModalInput1" class="col-sm-2 control-label" style="width: 110px;">'+value.onbd_quantity+'</label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbd_sell_date+'</label></label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbd_size+'吋</label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbd_add_date+'</label><label for="addModalInput1" class="col-sm-2 control-label" style="width: 90px;">$'+value.onbd_sell_price+'</label><label for="addModalInput1" class="col-sm-1 control-label" style="width: 120px;"><button type="button" style="background-color:#f67828;border:#f67828" class="btn btn-danger btn-xs updprebill_adjust" data-onbd_sn="'+value.onbd_sn+'">修改</button> <button type="button" style="background-color:#E94653;border:#f67828" class="btn btn-danger btn-xs prebill_del" data-onbd_sn="'+value.onbd_sn+'">刪除</button></label></div></div>');	
							$('#month_prebill_cotent').html($('#month_prebill_cotent').html()+'<div class="col-md-12"><div class="col-sm-12"><label for="addModalInput1" class="col-sm-1 control-label" style="width: 90px;">'+value.onbd_client+'</label><label for="addModalInput1" class="col-sm-2 control-label" style="width: 110px;">'+value.onbd_quantity+'</label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbd_sell_date+'</label></label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbd_size+'吋</label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbd_add_date+'</label><label for="addModalInput1" class="col-sm-2 control-label" style="width: 90px;">$'+value.onbd_sell_price+'</label></div></div>');								
						}
					});

					$('button.prebill_del').on('click', function(){
						onbd_sn = $(this).data('onbd_sn');
						bootbox.confirm("確認刪除？", function(result) {
							if(result) {
								$.ajax({
									url: './details_table.php',
									type: 'post',
									dataType: 'json',
									data: {op:"prebill_del", onbd_sn:onbd_sn},
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

					$('button.updprebill_adjust').on('click', function(){
						var onbd_sn = $(this).data('onbd_sn');
						$.ajax({
							url: './details_table.php',
							type: 'post',
							dataType: 'json',
							data: {op:"prebill_get", onbd_sn:onbd_sn},
							beforeSend: function(msg) {
								$("#ajax_loading").show();
							},
							complete: function(XMLHttpRequest, textStatus) {
								$("#ajax_loading").hide();
							},
							success: function(ret) {
								var data = ret.data;								
								$('#updprebill_adjust-modal input[name=onbd_sn]').val(data.onbd_sn);
								$('#updprebill_adjust-modal input[name=onbd_part_no]').val(data.onbd_part_no);
								$('#updprebill_adjust-modal input[name=onbd_part_name]').val(data.onbd_part_name);
								$('#updprebill_adjust-modal input[name=onbd_quantity]').val(data.onbd_quantity);
								$('#updprebill_adjust-modal input[name=onbd_sell_date]').val(data.onbd_sell_date);
								$('#updprebill_adjust-modal [name=onbd_size] option[value='+data.onbd_size+']').prop('selected','selected');
								$('#updprebill_adjust-modal input[name=onbd_client]').val(data.onbd_client);
								$('#updprebill_adjust-modal input[name=onbd_seller]').val(data.onbd_seller);
								$('#updprebill_adjust-modal input[name=onbd_sell_price]').val(data.onbd_sell_price);

								$('#updprebill_adjust-modal').modal('show');
							},
							error: function (xhr, ajaxOptions, thrownError) {
						        	
						    }
						});
					});	
				},
				error: function (xhr, ajaxOptions, thrownError) {
			   		// console.log('ajax error');
			        // console.log(xhr);
			    }
			});
		}

		function upd_btn_click(onproduct_sn) {
			$.ajax({
					url: './details_table.php',
					type: 'post',
					dataType: 'json',
					data: {op:"IsOver5Pics", onproduct_sn:onproduct_sn},
					beforeSend: function(msg) {
						$("#ajax_loading").show();
					},
					complete: function(XMLHttpRequest, textStatus) {
						$("#ajax_loading").hide();
					},
					success: function(ret) {
						if(ret.data >= 5){
							alert("圖片至多只能上傳5張！");
						}
						else{
							$('#Upload_Image_Modal').modal('show');
		  					$('#onproduct_sn').val(onproduct_sn);
						}
					},
					error: function (xhr, ajaxOptions, thrownError) {
				   	console.log('ajax error');
				        // console.log(xhr);
				    }
				});
		}
	</script>
</head>

<body>
	<?php include('./../htmlModule/nav.php');?>
	<!--main content start-->
	<section class="main-content">
		<!--page header start-->
		<div style="visibility: hidden;" id="hidden_onproduct_sn"><?php echo $onproduct_sn; ?></div>
		<div class="page-header">
			<div class="row">
				<div class="col-sm-6">
					<h4>可供量表</h4>
				</div>
			</div>
		</div>

		<!--modal-->
		<div class='modal fade' id='Upload_Image_Modal' role='dialog'>
			<div class='modal-dialog modal-lg'>
				<div class='modal-content'>
					<div class='modal-body'>
						<h4 class="modal-title">照片上傳</h4>
						<form action="./upload_image.php" method="post" enctype="multipart/form-data">
						    <!-- 限制上傳檔案的最大值 -->
						    <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
						    <input type="hidden" id="onproduct_sn" name="onproduct_sn" value="">
						    <input type="hidden" id="onproduct_type" name="onproduct_type" value="2">
						    <input type="hidden" id="parameters" name="parameters" value="<?php echo "details_table.php?onadd_part_no=".GetParam('onadd_part_no')."&onadd_growing=".GetParam('onadd_growing').'&onadd_quantity_del='.GetParam('onadd_quantity_del').'&onadd_part_name='.GetParam('onadd_part_name'); ?>">
						    <!-- accept 限制上傳檔案類型 -->
						    <input type="file" name="myFile" accept="image/jpeg,image/jpg,image/gif,image/png">

						    <input type="submit" value="上傳檔案">
						</form>
					</div>	
				</div>		
			</div>			
		</div>

		<!--顯示月份出貨明細----------------------------------------------------------->
		<div id="modal_month_customers" class="modal upd-modal2" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd_form2" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="month_customers_title">出貨</h4>
						</div>
						<div class="modal-body">
							<div class="row" id="month_customers_cotent">
								<div class="col-md-12">									
									<div class="col-sm-10">
										<label for="addModalInput1" class="col-sm-2 control-label">客戶名稱</label>
										<label for="addModalInput1" class="col-sm-2 control-label">預計出貨數量</label>
										<label for="addModalInput1" class="col-sm-2 control-label">預計出貨日期</label>
										<label for="addModalInput1" class="col-sm-2 control-label">該筆新增日期</label>
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
		<!--顯示月份出貨明細----------------------------------------------------------->

		<!--顯示預定訂單----------------------------------------------------------->
		<div id="modal_month_prebill" class="modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="prebill_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="month_prebill_title">出貨</h4>
						</div>
						<div class="modal-body">
							<div class="row" id="month_prebill_cotent">
								<div class="col-md-12">									
									<div class="col-sm-10">
										<label for="addModalInput1" class="col-sm-2 control-label">客戶名稱</label>
										<label for="addModalInput1" class="col-sm-2 control-label">預計出貨數量</label>
										<label for="addModalInput1" class="col-sm-2 control-label">預計出貨日期</label>
										<label for="addModalInput1" class="col-sm-2 control-label">該筆新增日期</label>
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
		<!--顯示預定訂單----------------------------------------------------------->

		<!--汰除----------------------------------------------------------->
		<div id="eli-modal1" class="modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./details_table.php" id="eli_form1" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">汰除</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="eli">
									<input type="hidden" name="onadd_sn">
									<input type="hidden" name="onadd_newpot_sn">
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

		<!--預計出貨----------------------------------------------------------->
		<div id="upd-modal1" class="modal upd-modal1" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd_form1" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">新增預定出貨</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd1">
									<input type="hidden" name="onadd_sn">
									<input type="hidden" name="quantity">

									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">批號<font color="red">*</font></label>
										<div class="col-sm-10">											
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="sn" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">預計出貨數量<font color="red">*</font></label>
										<div class="col-sm-10">
											
											<input type="text" class="form-control" id="addModalInput1" name="onbuda_quantity" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label class="col-sm-2 control-label">預計出貨日期&nbsp;</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="prebill_datetimepicker" name="onbuda_date" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">預計出貨尺寸<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onbuda_size">
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
										<label for="addModalInput1" class="col-sm-2 control-label">出貨對象<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onbuda_client" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>   
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">銷售人員<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onbuda_seller">
	    										<option value="-1">全部</option>
	        									<?php 
	        									foreach ($Staffs as $key => $value) {
	        										if ($onbd_seller == $value['jsuser_sn']) {
	        											echo "<option value=".$value['jsuser_sn']." selected>".$value['jsuser_name']."</option>";
	        										}else{
	        											echo "<option value=".$value['jsuser_sn'].">".$value['jsuser_name']."</option>";
	        										}
	        									}
	        									?>
    										</select>
											<div class="help-block with-errors"></div>
										</div>
									</div>  
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">銷售價格(單棵)<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onbuda_sell_price" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>  							
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">新增</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--預計出貨----------------------------------------------------------->

		<!--預計出貨修改----------------------------------------------------------->
		<div id="upd_adjust-modal1" class="modal upd-modal1" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd_adjust_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">預定出貨修改</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd1_adjust">
									<input type="hidden" name="onadd_sn">
									<input type="hidden" name="onbuda_sn">
									<input type="hidden" name="quantity">

									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">批號<font color="red">*</font></label>
										<div class="col-sm-10">											
											<input readonly="readonly" type="text" class="form-control" id="addModalInput1" name="sn" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">預計出貨數量<font color="red">*</font></label>
										<div class="col-sm-10">
											
											<input type="text" class="form-control" id="addModalInput1" name="onbuda_quantity" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label class="col-sm-2 control-label">預計出貨日期&nbsp;</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="prebill_datetimepicker" name="onbuda_date" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">預計出貨尺寸<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onbuda_size">
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
										<label for="addModalInput1" class="col-sm-2 control-label">出貨對象<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onbuda_client" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>   
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">銷售人員<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onbuda_seller">
	    										<option value="-1">全部</option>
	        									<?php 
	        									foreach ($Staffs as $key => $value) {
	        										if ($onbd_seller == $value['jsuser_sn']) {
	        											echo "<option value=".$value['jsuser_sn']." selected>".$value['jsuser_name']."</option>";
	        										}else{
	        											echo "<option value=".$value['jsuser_sn'].">".$value['jsuser_name']."</option>";
	        										}
	        									}
	        									?>
    										</select>
											<div class="help-block with-errors"></div>
										</div>
									</div>  
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">銷售價格(單棵)<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onbuda_sell_price" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>  							
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">修改</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--預計出貨修改----------------------------------------------------------->

		<!--預定訂單----------------------------------------------------------->
		<div id="updprebill-modal" class="modal upd-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="updprebill_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">新增預定訂單</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="updprebill">

									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品號<font color="red">*</font></label>
										<div class="col-sm-10">											
											<input readonly="readonly" type="text" class="form-control" name="onbd_part_no" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品名<font color="red">*</font></label>
										<div class="col-sm-10">											
											<input readonly="readonly" type="text" class="form-control" name="onbd_part_name" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">預定訂單數量<font color="red">*</font></label>
										<div class="col-sm-10">
											
											<input type="text" class="form-control" name="onbd_quantity" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label class="col-sm-2 control-label">預定交貨日期&nbsp;</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="datetimepicker1" name="onbd_sell_date" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">預定交貨尺寸<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onbd_size">
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
										<label for="addModalInput1" class="col-sm-2 control-label">出貨對象<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" name="onbd_client" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>   
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">銷售人員<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onbd_seller">
	    										<option value="-1">全部</option>
	        									<?php 
	        									foreach ($Staffs as $key => $value) {
	        										if ($onbd_seller == $value['jsuser_sn']) {
	        											echo "<option value=".$value['jsuser_sn']." selected>".$value['jsuser_name']."</option>";
	        										}else{
	        											echo "<option value=".$value['jsuser_sn'].">".$value['jsuser_name']."</option>";
	        										}
	        									}
	        									?>
    										</select>
											<div class="help-block with-errors"></div>
										</div>
									</div>  
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">銷售價格(單棵)<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" name="onbd_sell_price" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>  							
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">新增</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--預定訂單----------------------------------------------------------->

		<!--預定訂單修改----------------------------------------------------------->
		<div id="updprebill_adjust-modal" class="modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="updprebill_adjust_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">預定訂單修改</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="updprebill_adjust">
									<input type="hidden" name="onbd_sn" value="">

									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品號<font color="red">*</font></label>
										<div class="col-sm-10">											
											<input readonly="readonly" type="text" class="form-control" name="onbd_part_no" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品名<font color="red">*</font></label>
										<div class="col-sm-10">											
											<input readonly="readonly" type="text" class="form-control" name="onbd_part_name" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>

									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">預定訂單數量<font color="red">*</font></label>
										<div class="col-sm-10">
											
											<input type="text" class="form-control" name="onbd_quantity" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label class="col-sm-2 control-label">預定交貨日期&nbsp;</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="datetimepicker1" name="onbd_sell_date" placeholder="">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2 control-label">預定交貨尺寸<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onbd_size">
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
										<label for="addModalInput1" class="col-sm-2 control-label">出貨對象<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" name="onbd_client" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>   
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">銷售人員<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onbd_seller">
	    										<option value="-1">全部</option>
	        									<?php 
	        									foreach ($Staffs as $key => $value) {
	        										if ($onbd_seller == $value['jsuser_sn']) {
	        											echo "<option value=".$value['jsuser_sn']." selected>".$value['jsuser_name']."</option>";
	        										}else{
	        											echo "<option value=".$value['jsuser_sn'].">".$value['jsuser_name']."</option>";
	        										}
	        									}
	        									?>
    										</select>
											<div class="help-block with-errors"></div>
										</div>
									</div>  
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">銷售價格(單棵)<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" name="onbd_sell_price" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>  							
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">修改</button>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!--預定訂單修改----------------------------------------------------------->

		<!-- Page Content -->
		<div  class="container-fluid">
			<div class="row">
				<!-- <div class="col-md-5"></div> -->
				<div class="col-md-5">
					<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
						<!-- Indicators -->
						<ol class="carousel-indicators">
							<?php							
							$pics = getPic($onproduct_sn);
							// printr(count($pics));
							for($i=0;$i<count($pics);$i++){
								if($i==0)
									echo '<li data-target="#carousel-example-generic" data-slide-to="'.$i.'" class="active"></li>';
								else
									echo '<li data-target="#carousel-example-generic" data-slide-to="'.$i.'"></li>';
							}
							?>
						</ol>
						<div class="carousel-inner" style='text-align:center;'>
							<?php
								if(!empty($data_list[0]['onproduct_pic_url'])){									
									for($i=0;$i<count($pics);$i++){
										if($i==0){
											echo '<div class="item active">';
												echo "<img class='img-rounded' src='".$pics[$i]['onpic_img_path']."'>";
											echo '</div>';
										}
										else{
											echo '<div class="item">';
												echo "<img class='img-rounded' src='".$pics[$i]['onpic_img_path']."'>";
											echo '</div>';
										}
									}
								}
								else{
									if($pics > 0){
										for($i=1;$i<count($pics);$i++){
											if($i==1){												
												echo '<div class="item active">';
													echo "<img class='img-rounded' src='".$pics[$i]['onpic_img_path']."'>";
												echo '</div>';
											}
											else{
												echo '<div class="item">';
													echo "<img class='img-rounded' src='".$pics[$i]['onpic_img_path']."'>";
												echo '</div>';
											}
										}
									}
									else{
										echo "<img class='img-rounded' style='text-align:center;' src='images/nopic.png' >";
									}
								}
							?>
							
						</div>
					</div>
					<a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
						<span class="glyphicon glyphicon-chevron-left"></span>
					</a>
					<a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
						<span class="glyphicon glyphicon-chevron-right"></span>
					</a>
				</div> 
				<div class="col-md-4">
					<?php
					$data_list = $data_list[0];


						echo '<div style="display:none" id="onproduct_sn">'.$data_list['onproduct_sn'].'</div>';
						echo '<h3>'.$onproduct_part_no.'</h3>';
					?> 
						<table style="font-size: 1.5rem" class="table table-hover">
							<thead>
								<tr>
									<th style="text-align: center;font-size: 1.2em">詳細資料</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>品號(Part no.)：
										<?php echo $data_list['onproduct_part_no']; ?> 
									</td>
								</tr>
								<tr>
									<td>品名(Part name.)：
										<?php echo $data_list['onproduct_part_name']; ?>
									</td>
								</tr>
								<tr>
									<td>花色 (Flower Color)：
										<?php echo $data_list['onproduct_color']; ?>
									</td>
								</tr>
								<tr>
									<td>花徑 (Flower Size)：
										<?php echo $data_list['onproduct_size']; ?>
									</td>
								</tr>
								<tr>
									<td>高度 (Plant Height)：
										<?php echo $data_list['onproduct_height']; ?>
									</td>
								</tr>
								<tr>
									<td>適合開花盆徑 (Suitable flowering pot size)：
										<?php echo $data_list['onproduct_pot_size']; ?> 
									</td>
								</tr>
							</tbody>								
						</table>				
					<?php 
				?> 
				</div>         
			</div>
		</div>
		<hr>
		
			</div>
		</div></br>

		<!-- <?php printr($data_list);?> -->
		<div class="col-md-6" style="margin-bottom: 10px;clear:both;">
			<ul class="nav nav-pills pull-right toolbar">
				<?php if($permmsion == 0 || $permmsion == 3){ 
					?>
					<li><button type="button" class="btn btn-primary btn-xs" onClick="upd_btn_click(<?php echo $onproduct_sn; ?>)"><i class="glyphicon glyphicon-plus"></i>新增更多圖片</button></li>
					<li><button type="button" class="btn btn-primary btn-xs upd_prebill"><i class="glyphicon glyphicon-plus"></i>預定訂單</button></li>
				<?php } ?>
			</ul>
			<hr>
			<table id="table_summary" class="table table-hover">
				<thead style="font-size: 1.3em">
					<tr>
						<th style="text-align: center;">產品批號</th>
						<th style="text-align: center;">下種日期</th>
						<th style="text-align: center;">目前尺寸</th>
						<th style="text-align: center;">數量</th>
						<th style="text-align: center;">已預訂數量</th>
						<th style="text-align: center;">操作</th>
						<!-- <?php if($permmsion == 0){ ?>
							<th style="text-align: center;">操作</th> 
						<?php } ?>   -->    						
					</tr>
				</thead>
				<tbody></tbody>
					<?php
						// printr($eli_list);
						foreach ($eli_list as $row) {											
							echo '<tr>';
							$AlreadyPrebillQuantity = getAlreadyPrebillQuantityBySn($row['onadd_sn']);
							$herf_sn = 0;
							$presell_flag = true;
							$IsShowedExpectedSell = true;
							$left_presell_amount = ($row['SUM(onadd_quantity)'] - $AlreadyPrebillQuantity);
							if($left_presell_amount <= 0)
								$presell_flag = false;

							$sn = SnProcessor($row['onadd_sn'], $row['onadd_type'], $row['onadd_newpot_sn'], $row['onadd_ml'], $row['onadd_planting_date'], $row['onadd_AB_sn'], $row['onadd_level']);

							echo '<td style="vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;">'.$sn.'</td>';//產品編號

							echo '<td style="vertical-align: middle;text-align: center;">'.date('Y-m-d',$row['onadd_planting_date']).'</td>';
							// if($eli_list[$i]['onadd_cur_size'] == 0 || $eli_list[$i]['onadd_cur_size'] == 8)
							// 	echo '<td style="vertical-align: middle;text-align: center;">'.$permissions_mapping[$eli_list[$i]['onadd_cur_size']].'</td>';
							// else
							echo '<td style="vertical-align: middle;text-align: center;">'.$permissions_mapping[$row['onadd_cur_size']].'</td>';
							echo '<td style="vertical-align: middle;text-align: center;">'.$row['SUM(onadd_quantity)'].'</td>';
							echo '<td style="vertical-align: middle;text-align: center;">'.$AlreadyPrebillQuantity.'</td>';
							// echo '<td style="vertical-align: middle;text-align: center;"></td>';
							echo '<td style="vertical-align: middle;text-align: center;"><button type="button" class="btn btn-info btn-xs" onclick="location.href=\''.WT_URL_ROOT.'/admin/purchase/plant_purchase.php?onadd_sn='.$herf_sn.'\'"><i class="glyphicon glyphicon-search"></i> 查看</button> ';
							if($IsShowedExpectedSell && $presell_flag)
								echo '<button type="button" class="btn btn-primary btn-xs upd1" data-sn="'.intval($row['onadd_sn']).'" data-showsn="'.$sn.'" data-quantity="'.$left_presell_amount.'"><i class="glyphicon glyphicon-plus"></i> 預定出貨</button>';
							echo '</td>';							
							// if($permmsion == 0){
							// 	echo '<td style="vertical-align: middle;text-align: center;">'.'<a href="javascript:do_emli(\''.$eli_list[$i]['onadd_sn'].'\');"><button type="button" class="btn btn-xs btn-warning">汰除</button></a></td>'; 
							// }     
							echo '</tr>';  							
						}
					?>    
				</tbody>
			</table>
		</div>

		<!-- container -->
		<div  class="container-fluid">
			<div class="row">
				<div class="col-md-8">
					<?php
					$href = './details_table.php?onadd_part_no='.$onadd_part_no.'&onadd_growing='.$onadd_growing.'&onadd_quantity_del='.'2020'.'&end='.$end;
					?>

					<!-- details_table.php?onadd_part_no=PP-0052&onadd_growing=1&onadd_quantity_del=2019 -->
					<ul class="nav nav-tabs" style="font-size: 1.2em">
						<?php
						$font_size = '';
						// echo GetParam('onadd_quantity_del');
						for($i=0;$i<5;$i++){
							$n = ((date('Y')-1)+$i);
							if($n == GetParam('onadd_quantity_del')){
								echo '<li class="active"><a style="color:#000000;">'.$n.'</a></li>';
							}
							else{
								echo '<li class="active"><a style="color:#23b7e5;" href="'.WT_URL_ROOT.'/admin/purchase/details_table.php?onadd_part_no='.GetParam('onadd_part_no').'&onadd_growing='.GetParam('onadd_growing').'&onadd_quantity_del='.$n.'&onadd_part_name='.GetParam('onadd_part_name').'">'.$n.'</a></li>';
							}
						}
						?>
					</ul>
                </div>
            </div>
        </div>
        <div class="container-fluid">
        	<div class="row">
        		<div class="col-md-8">

        			<table id="table_summary" class="table table-hover table-condensed table-bordered">
        				<thead>
        					<tr>
        						<th style="text-align: left; font-size: 1.2em" colspan="13" class="tableheader">自種/外購苗，委外代工可供出貨量表</th>
        					</tr>
        					<tr>
        						<th style="text-align: center; vertical-align: middle;font-size: 1.1em" rowspan="2" >出售</br>尺寸</th>
        						<th style="text-align: center; font-size: 1.2em" colspan="12" class="tableheader" align="center">可供出售月份(系統計算)</th>
        					</tr>
        					<tr>
        						<th style="text-align: center;">一月</th>
        						<th style="text-align: center;">二月</th>
        						<th style="text-align: center;">三月</th>
        						<th style="text-align: center;">四月</th>
        						<th style="text-align: center;">五月</th>
        						<th style="text-align: center;">六月</th>
        						<th style="text-align: center;">七月</th>
        						<th style="text-align: center;">八月</th>
        						<th style="text-align: center;">九月</th>
        						<th style="text-align: center;">十月</th>
        						<th style="text-align: center;">十一月</th>
        						<th style="text-align: center;">十二月</th>
        					</tr>
        				</thead>
        				<tbody>
        					<?php
        					for($i=0;$i<8;$i++){
        						$n = 0;
        						for($j=1;$j<=12;$j++){
        							$n += $expected_count_list[$i][$j];
        						}
        						if($n != 0){
        							echo '<tbody>';
        							echo '<td style="text-align: center;">'.$permissions_mapping[$i].'</td>';
		        					for($j = 1 ;$j <= 12;$j++){
		        						
		        							echo '<td style="text-align: center;">';

											if(!empty($expected_count_list['sn'][$j]))
												echo '<a href="'.WT_SERVER.'/admin/purchase/plant_purchase.php?onadd_sn='.substr($expected_count_list['sn'][$j], 0, -1).'">'.$expected_count_list[$i][$j].'</a>';
											else
												echo $expected_count_list[$i][$j];	

		        							echo '</td>';//預計成熟月份數量		        					
		                            }
		                            echo '</tbody>';
        						}

        					}
                             ?>
                         </tbody>
                     </table>

                     <table id="table_summary" class="table table-hover table-condensed table-bordered">
        				<thead>
        					<tr>
        						<th style="text-align: left; font-size: 1.2em" colspan="13" class="tableheader">代工苗預計出貨量表</th>
        					</tr>
        					<tr>
        						<th style="text-align: center; vertical-align: middle;font-size: 1.1em" rowspan="2">出售</br>尺寸</th>
        						<th style="text-align: center; font-size: 1.2em" colspan="12" class="tableheader" align="center">代工預計出售月份</th>
        					</tr>
        					<tr>
        						<th style="text-align: center;">一月</th>
        						<th style="text-align: center;">二月</th>
        						<th style="text-align: center;">三月</th>
        						<th style="text-align: center;">四月</th>
        						<th style="text-align: center;">五月</th>
        						<th style="text-align: center;">六月</th>
        						<th style="text-align: center;">七月</th>
        						<th style="text-align: center;">八月</th>
        						<th style="text-align: center;">九月</th>
        						<th style="text-align: center;">十月</th>
        						<th style="text-align: center;">十一月</th>
        						<th style="text-align: center;">十二月</th>
        					</tr>
        				</thead>
        				<tbody>
        					<?php
        					for($i=0;$i<8;$i++){
        						$n = 0;
        						for($j=1;$j<=12;$j++){
        							$n += $oem_expected_count_list[$i][$j];
        						}
        						if($n != 0){
        							echo '<tbody>';
        							echo '<td style="text-align: center;">'.$permissions_mapping[$i].'</td>';
		        					for($j = 1 ;$j <= 12;$j++){
		        						echo '<td style="text-align: center;">';

										if(!empty($oem_expected_count_list['sn'][$j]))
											echo '<a href="'.WT_SERVER.'/admin/purchase/plant_purchase.php?onadd_sn='.substr($oem_expected_count_list['sn'][$j], 0, -1).'">'.$oem_expected_count_list[$i][$j].'</a>';
										else
											echo $oem_expected_count_list[$i][$j];	

		        						echo '</td>';//預計成熟月份數量		
		                            }
		                            echo '</tbody>';
        						}

        					}
                             ?>
                         </tbody>
                     </table>

                     <table id="table_summary" class="table table-striped table-hover table-condensed table-bordered">
        				<thead>
        					<tr>
        						<th style="text-align: left; font-size: 1.2em" colspan="13" class="tableheader">業務預定供出貨訂單</th>
        					</tr>
        					<tr>
        						<th style="text-align: center; vertical-align: middle;font-size: 1.1em" rowspan="2">預計</br>尺寸</th>
        						<th style="text-align: center;font-size: 1.2em" colspan="12" class="tableheader" align="center">現有庫存預定訂單</th>
        					</tr>
        					<tr>
        						<th style="text-align: center;">一月</th>
        						<th style="text-align: center;">二月</th>
        						<th style="text-align: center;">三月</th>
        						<th style="text-align: center;">四月</th>
        						<th style="text-align: center;">五月</th>
        						<th style="text-align: center;">六月</th>
        						<th style="text-align: center;">七月</th>
        						<th style="text-align: center;">八月</th>
        						<th style="text-align: center;">九月</th>
        						<th style="text-align: center;">十月</th>
        						<th style="text-align: center;">十一月</th>
        						<th style="text-align: center;">十二月</th>
        					</tr>
        				</thead>

        					<?php        					
        					for($size_n=1;$size_n <= 6;$size_n++){
        						echo '<tbody>'; 
        						if(!empty($business_data[$size_n]['size'])){
	            	             	echo '<td style="vertical-align: middle; text-align: center;">'.$permissions_mapping[$business_data[$size_n]['size']].'</td>';
	        						for($i = 1 ;$i <= 12;$i++){
	        							if(!empty($business_data[$size_n][$i]))
	            	                        echo '<td style="vertical-align: middle;text-align: center;"><a href="javascript: void(0)" onclick="customer_list(\''.$onadd_part_no.'\','.$onadd_quantity_del.','.($i).','.$business_data[$size_n]['size'].')">'.$business_data[$size_n][$i].'</a></td>';//品號
	            	                   	else
	            	                   		echo '<td style="vertical-align: middle; text-align: center;">0</td>';
	            	             	}
	            	             }
            	             	echo '</tbody>';        					
                        	}
                            ?>
                         
                     </table>


                     <table class="table table-striped table-hover table-condensed table-bordered">
        				<thead>
        					<tr>
        						<th style="text-align: left; font-size: 1.2em" colspan="13" class="tableheader">業務預定訂單</th>
        					</tr>
        					<tr>
        						<th style="text-align: center; vertical-align: middle;font-size: 1.1em" rowspan="2">預定</br>尺寸</th>
        						<th style="text-align: center;font-size: 1.2em" colspan="12" class="tableheader" align="center">訂單月份</th>
        					</tr>
        					<tr>
        						<th style="text-align: center;">一月</th>
        						<th style="text-align: center;">二月</th>
        						<th style="text-align: center;">三月</th>
        						<th style="text-align: center;">四月</th>
        						<th style="text-align: center;">五月</th>
        						<th style="text-align: center;">六月</th>
        						<th style="text-align: center;">七月</th>
        						<th style="text-align: center;">八月</th>
        						<th style="text-align: center;">九月</th>
        						<th style="text-align: center;">十月</th>
        						<th style="text-align: center;">十一月</th>
        						<th style="text-align: center;">十二月</th>
        					</tr>
        				</thead>
        					<?php
        					// printr($prebill_list);
        					// exit;
        					for($i=0;$i<8;$i++){
        						$n = 0;
        						for($j=1;$j<=12;$j++){
        							$n += $prebill_list[$i][$j];
        						}
        						if($n != 0){
        							echo '<tbody>';
        							echo '<td style="text-align: center;">'.$permissions_mapping[$i].'</td>';
		        					for($j = 1 ;$j <= 12;$j++){
										if($prebill_list[$i][$j] != "0"){
											echo '<td style="vertical-align: middle;text-align: center;"><a href="javascript: void(0)" onclick="prebill_list(\''.$onadd_part_no.'\',\''.$onadd_part_name.'\','.$onadd_quantity_del.','.($j).','.($i).')">'.$prebill_list[$i][$j].'</a></td>';
										}
										else{
											echo '<td style="text-align: center;">'.$prebill_list[$i][$j].'</td>';	
										}	
		                            }
		                            echo '</tbody>';
        						}

        					}
                            ?>                         
                     </table>
                 </div>
             </div>
         </div>
         <?php
         	// $data = "2019-05-01";
         	// echo substr($data,0,4);
         	// echo substr($data,6,-3);
         ?>
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
 </body>
 </html>?>