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
		8=>"瓶苗開瓶",
		9=>"出貨"
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

$permmsion = $_SESSION['user']['jsuser_admin_permit'];

$op=GetParam('op');
if(!empty($op)) {
	$ret_code = 1;
	$ret_msg = '';
	$ret_data = array();
	switch ($op) {
		case 'add':
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
		$onadd_growing=GetParam('onadd_growing');//預計成長大小
		// if($onadd_growing==1){
		// 	$list_setting = getSettingBySn(1.7);
		// 	$onchba_cycle = $list_setting['onchba_cycle'];
		// }else if($onadd_growing==2){
		// 	$list_setting = getSettingBySn(2.5);
		// 	$onchba_cycle = $list_setting['onchba_cycle'];
		// }else if($onadd_growing==5){
		// 	$list_setting = getSettingBySn(3.5);
		// 	$onchba_cycle = $list_setting['onchba_cycle'];
		// }
		// $test = date("m", strtotime("+$onchba_cycle months", $onadd_planting_date));
		$onadd_quantity_shi=GetParam('onadd_quantity_shi');//換盆年
		$onadd_quantity_cha=$test;//換盆月
		$onadd_status=GetParam('onadd_status');//狀態 1 啟用 0 刪除
		$jsuser_sn = GetParam('supplier');//編輯人員
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

		case 'delay':
			$onadd_sn=GetParam('onadd_sn');
			$onadd_delay_reason=GetParam('onadd_delay_reason');
			$ret_data = array();
			if(!empty($onadd_sn)){
				$conn = getDB();
				$sql = "UPDATE onliine_add_data SET onadd_schedule = '3', onadd_delay_reason = '{$onadd_delay_reason}' WHERE onadd_sn='{$onadd_sn}'";
				if($conn->query($sql)){
					$ret_code = 1;
				$ret_msg = "延後成功!";
				}
				else{
					$ret_msg = "延後失敗!";
				$ret_code = 0;
				}				
			} else {
				$ret_msg = "延後失敗!";
				$ret_code = 0;
			}
		break;
	
		//換盆
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
			$onadd_location=GetParam('onadd_location');//放置區
			$onadd_pot_size=GetParam('onadd_pot_size');//適合開花盆徑
			$onadd_supplier=GetParam('onadd_supplier');//供應商
			$onadd_planting_date=GetParam('onadd_planting_date');//下種日期
			$onadd_quantity=GetParam('onadd_quantity');//下種數量
			$onadd_replant_number=GetParam('onadd_replant_number');//換盆數量
			$onadd_quantity_cha123 =($onadd_quantity - $onadd_replant_number);

			$first_n_changed = getProductFirstQty($onadd_sn) - $onadd_replant_number;

			$onadd_growing=GetParam('onadd_growing');//預計成長大小
			$onadd_cur_size=GetParam('onadd_cur_size');//換盆尺寸
			$onadd_status=GetParam('onadd_status');//狀態 1 啟用 0 刪除
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
					$sql = "INSERT INTO onliine_add_data (onadd_add_date, onadd_mod_date, onadd_part_no, onadd_part_name, onadd_color, onadd_size, 	onadd_height, onadd_pot_size, onadd_supplier, onadd_planting_date, onadd_quantity,onadd_quantity_cha, onadd_growing, 	onadd_status, jsuser_sn, onadd_cycle, onadd_newpot_sn, onadd_cur_size, onadd_location) " .
					"VALUES ('{$now}', '{$now}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_planting_date}', '{$onadd_replant_number}','{$onadd_replant_number}', '{$onadd_growing}', '1', '{$jsuser_sn}', '{$now}', '{$sn}', '{$onadd_cur_size}', '{$onadd_location}');";
	
					if($conn->query($sql)){
						$onadd_id = mysqli_insert_id($conn);
						//更新原本產品數量 (扣除換盆)
						$sql1 = "UPDATE onliine_add_data SET onadd_quantity='{$onadd_quantity_cha123}', onadd_status='{$onadd_status}' WHERE onadd_sn='{$onadd_sn}'";
						if($conn->query($sql1)){
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

			$now = time();
			$conn = getDB();
			$sql1 = "UPDATE onliine_add_data SET onadd_schedule='2' WHERE onadd_sn='{$onadd_sn}'";
			if($conn->query($sql1)) {
				$ret_msg = "完成！";
			} else {
				$ret_msg = "失敗！";
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
		$onshda_price=GetParam('onshda_price');//單棵價格
		$onshda_client=GetParam('onshda_client');//出貨客戶
		$onadd_quantity_shi123 = ($onadd_quantity - $onadd_plant_year);
		if($onadd_quantity_shi123 < 0) {
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

		if(empty($onadd_plant_year)){
			$ret_msg = "*為必填！";
		} 
		else if($onadd_status != -1){
			$now = time();
			$conn = getDB();
			$sql1 = "UPDATE onliine_add_data SET onadd_quantity='{$onadd_quantity_shi123}', onadd_status='{$onadd_status}' WHERE onadd_sn='{$onadd_sn}'";
			$sql = "INSERT INTO online_shipment_data (onshda_add_date, onshda_mod_date, onshda_client, onshda_quantity, onadd_sn, onadd_part_no, onadd_part_name, onshda_price) " .
				"VALUES ('{$now}', '{$now}', '{$onshda_client}', '{$onadd_plant_year}', '{$onadd_ml}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onshda_price}');";
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

		default:
		$ret_msg = 'error!';
		break;
	}

	echo enclode_ret_data($ret_code, $ret_msg, $ret_data);
	exit;
} else {
	// search
	if(($onadd_part_no = GetParam('onadd_part_no'))) {
		$search_where[] = "onadd_part_no like '%{$onadd_part_no}%'";
		$search_query_string['onadd_part_no'] = $onadd_part_no;
	}
	if(($onadd_part_name = GetParam('onadd_part_name'))) {
		$search_where[] = "onadd_part_name like '%{$onadd_part_name}%'";
		$search_query_string['onadd_part_name'] = $onadd_part_name;
	}
	if(($onadd_supplier = GetParam('onadd_supplier'))) {
		$search_where[] = "onadd_supplier like '%{$onadd_supplier}%'";
		$search_query_string['onadd_supplier'] = $onadd_supplier;
	}
	if(($onadd_status = GetParam('onadd_status', -1))>=0) {
		$search_where[] = "onadd_status='{$onadd_status}'";
		$search_query_string['onadd_status'] = $onadd_status;
	}
	if(($onadd_growing = GetParam('onadd_growing', -1))>=0) {
        $search_where[] = "onadd_growing='{$onadd_growing}'";
        $search_query_string['onadd_growing'] = $onadd_growing;
    }
	$search_where = isset($search_where) ? implode(' and ', $search_where) : '';
	$search_query_string = isset($search_query_string) ? http_build_query($search_query_string) : '';

	// page
	$pg_page = GetParam('pg_page', 1);
	$pg_rows = 20;
	$pg_total = GetParam('pg_total')=='' ? getUserQty_re($search_where) : GetParam('pg_total');
	$pg_offset = $pg_rows * ($pg_page - 1);
	$pg_pages = $pg_rows == 0 ? 0 : ( (int)(($pg_total + ($pg_rows - 1)) /$pg_rows) );

	$user_list = getUser_re($search_where, $pg_offset, $pg_rows);
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
	<script type="text/javascript">
		$(document).ready(function() {
			<?php
					//	init search parm
			print "$('#search [name=onadd_status] option[value={$onadd_status}]').prop('selected','selected');";
			print "$('#search [name=onadd_growing] option[value={$onadd_growing}]').prop('selected','selected','selected','selected','selected','selected','selected');";
			?>

			//換盆-----------------------------------------------------------
			$('button.upd').on('click', function(){
				$('#upd-modal').modal();
				$('#upd_form')[0].reset();

				$.ajax({
					url: './plant_re_schedule.php',
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
			                	$('#upd_form input[name=onadd_sn]').val(d.onadd_sn);
			                	if(d.onadd_newpot_sn == 0){	                	
				                	$('#upd_form input[name=onadd_newpot_sn]').val(d.onadd_sn);
				                }
				                else{
				                	$('#upd_form input[name=onadd_newpot_sn]').val(d.onadd_newpot_sn);
				                }
			                	$('#upd_form input[name=onadd_part_no]').val(d.onadd_part_no);
			                	$('#upd_form input[name=onadd_part_name]').val(d.onadd_part_name);
			                	$('#upd_form input[name=onadd_color]').val(d.onadd_color);
			                	$('#upd_form input[name=onadd_size]').val(d.onadd_size);
			                	$('#upd_form input[name=onadd_height]').val(d.onadd_height);
			                	$('#upd_form input[name=onadd_pot_size]').val(d.onadd_pot_size);
			                	$('#upd_form input[name=onadd_supplier]').val(d.onadd_supplier);			  
			                	$('#upd_form input[name=onadd_location]').val(d.onadd_location);	              	
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
			//換盆-----------------------------------------------------------

			//出貨-----------------------------------------------------------
			$('button.upd2').on('click', function(){
				$('#upd-modal2').modal();
				$('#upd_form2')[0].reset();

				$.ajax({
					url: '../purchase/plant_purchase.php',
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
					url: './plant_re_schedule.php',
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

			bootbox.setDefaults({
				locale: "zh_TW",
			});


			//延後-----------------------------------------------------------
			$('button.delay').on('click', function(){
				$('#delay-modal').modal();
				$('#delay_form')[0].reset();				
				var onadd_sn = $(this).data('onadd_sn');
				$('#delay_form input[name=onadd_sn]').val(onadd_sn);			
			});

			$('#add_form, #upd_form, #upd_form1, #upd_form2, #delay_form').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					e.preventDefault();
					var param = $(this).serializeArray();

					$(this).parents('.modal').modal('hide');
					$(this)[0].reset();

					 	// console.table(param);

					 	$.ajax({
					 		url: './plant_re_schedule.php',
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
			                     // console.log(xhr);
			                 }
			             });
					 }
				});


		        $('button.cancel').on('click', function() {
					location.href = "./../";
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
		});
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
					<h4>每週排程</h4>
				</div>
			</div>
		</div>

		<!-- modal -->
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
										<label for="addModalInput1" class="col-sm-2 control-label">放置區</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_location" placeholder="" readonly="readonly">
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
										<label for="addModalInput1" class="col-sm-2 control-label">換盆數量<font color="red">*</font></label>
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
										<label for="addModalInput1" class="col-sm-2 control-label" >換盆尺寸<font color="red">*</font></label>
										<div class="col-sm-10">
											<select class="form-control" name="onadd_cur_size">
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
										<label for="addModalInput1" class="col-sm-2 control-label">品名<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_name" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">品號<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_part_no" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">放置區<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_location" placeholder="" required minlength="1" maxlength="32" readonly="readonly">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">可供出貨數量<font color="red">*</font></label>
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
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">價格(單棵)<font color="red">*</font></label>
										<div class="col-sm-10">
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

		<!-- modal -->
		<div id="delay-modal" class="modal delay-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="delay_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="schedule_title">工作排程延後設定</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="delay">
									<input type="hidden" name="onadd_sn">
									<input type="hidden" name="onadd_newpot_sn">
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">延後原因<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onadd_delay_reason" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button type="submit" class="btn btn-primary">確認延後</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- container -->
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">

					<!-- search -->
					<div id="search" style="clear:both;">
						<form autocomplete="off" method="get" action="./plant_re_schedule.php" id="search_form" class="form-inline alert alert-info" role="form">
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

									<button type="submit" class="btn btn-info" op="search">搜尋</button>
								</div>
							</div>
						</form>
					</div>

					<!-- content -->
					<table class="table table-striped table-hover table-condensed tablesorter">
						<thead>
							<tr  style="font-size: 1.1em">
								<th style="text-align: center;">產品編號</th>
								<th style="text-align: center;">品號</th>
								<th style="text-align: center;">品名</th>
								<th style="text-align: center;">下種日期</th>
								<th style="text-align: center;">下種數量</th>
								<!-- <th style="text-align: center;">預計成長大小</th> -->
								<th style="text-align: center;">下一階段換盆/出貨日期</th>
								<!-- <th style="text-align: center;">總下種週期</th>       							 -->
								<th style="text-align: center;">供應商</th>
								<?php if($permmsion == 0){ ?>			
									<th style="text-align: center;">操作</th>
								<?php } ?>	
							</tr>
						</thead>
						<tbody>
							<?php
        						// $setting_list = getsetting();
        						// foreach ($setting_list as $i=>$v) {
        						// 	$onchba_size = $v['onchba_size'];
        						// 	$onchba_cycle = $v['onchba_cycle'];
        						// }
								foreach ($user_list as $row) {
								echo '<tr>';
        							if($row['onadd_plant_st'] == 1){//產品編號
										echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;vertical-align: middle;">'.date('Y',$row['onadd_planting_date']).'-'.$row['onadd_sn'].'</td>';
									}
									else{
										echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;vertical-align: middle;">'.'P'.date('Y',$row['onadd_planting_date']).'-'.$row['onadd_sn'].'</td>';
									} 	
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;vertical-align: middle;">'.$row['onadd_part_no'].'</td>';//品號
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;vertical-align: middle;">'.$row['onadd_part_name'].'</td>';//品名  	
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;vertical-align: middle;">'.date('Y/m/d',$row['onadd_planting_date']).'</td>';        							
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;vertical-align: middle;">'.$row['onadd_quantity'].'</td>';//數量

        							$cur_size = $DEVICE_SYSTEM[$row['onadd_cur_size']];
        							$growing_size = $DEVICE_SYSTEM[$row['onadd_growing']];
        							$onchba_cycle = getSettingBySn($cur_size,$growing_size)['onchba_cycle'];
        							$test = date("Y/m/d", strtotime("+$onchba_cycle days", $row['onadd_planting_date']));
        							
        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;vertical-align: middle;">'.$test.'</td>';

        							echo '<td style="border-right:0.1rem #BEBEBE dashed;text-align: center;vertical-align: middle;">'.$row['onadd_supplier'].'</td>';//品名
        								if($permmsion == 0){ 
        									// echo '<td style="text-align: center;">
        									// <span >
	        							 //      	<button type="button" style="background-color:#6CBF87;border:#6CBF87" class="btn btn-success btn-xs upd2" data-onadd_sn="'.$row['onadd_sn'].'">出貨</button>
	        							 //    </span>
        									// <span><button type="button" style="background-color:#A46B62;border:#A46B62" class="btn btn-primary btn-xs upd" data-onadd_sn="'.$row['onadd_sn'].'">換盆</button>
        									// </span>
        									// <span><button type="button" class="btn btn-danger btn-xs delay" data-onadd_sn="'.$row['onadd_sn'].'">延後</button>
        									// </span>';
        									echo '<td style="text-align: center;">
        									<span >
	        							      	<button type="button" style="background-color:#6CBF87;border:#6CBF87" class="btn btn-success btn-xs" onclick="location.href=\'/online_shulong/admin/purchase/plant_purchase.php?onadd_sn='.$row['onadd_sn'].'\'">施做</button>
	        							    </span>
        									<span><button type="button" class="btn btn-danger btn-xs delay" data-onadd_sn="'.$row['onadd_sn'].'">延後</button>
        									</span>';
        									echo '</td></tr>';
        								}
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
    </body>
    </html>