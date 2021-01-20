<?php
include_once("./func_plant_purchase_details.php");
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



$op=GetParam('op');
if(!empty($op)) {
	$ret_code = 1;
	$ret_msg = '';
	$ret_data = array();
	switch ($op) {
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
		$onadd_quantity_cha=GetParam('onadd_quantity_cha');//換盆數量
		$onadd_quantity_cha123 =($onadd_quantity - $onadd_quantity_cha);
		if($onadd_quantity_cha123<=0) {
			$onadd_status = -1;
		} else {
			$onadd_status = 1;
		}
		$onadd_growing=GetParam('onadd_growing');//預計成長大小
		$onadd_change_basin=GetParam('onadd_change_basin');//換盆週期
		// $onadd_status=GetParam('onadd_status');//狀態 1 啟用 0 刪除
		$jsuser_sn = GetParam('supplier');//編輯人員

		if(empty($onadd_planting_date)||empty($onadd_quantity)){
			$ret_msg = "*為必填！";
		} else { 
			$user = getUserByAccount($onadd_part_no);
			$onadd_planting_date = str2time($onadd_planting_date);
			$now = time();
			$conn = getDB();
				$sql = "INSERT INTO onliine_add_data (onadd_add_date, onadd_mod_date, onadd_part_no, onadd_part_name, onadd_color, onadd_size, onadd_height, onadd_pot_size, onadd_supplier, onadd_planting_date, onadd_quantity, onadd_growing, onadd_change_basin, onadd_status, jsuser_sn) " .
				"VALUES ('{$now}', '{$now}', '{$onadd_part_no}', '{$onadd_part_name}', '{$onadd_color}', '{$onadd_size}', '{$onadd_height}', '{$onadd_pot_size}', '{$onadd_supplier}', '{$onadd_planting_date}', '{$onadd_quantity_cha}', '{$onadd_growing}', '{$onadd_change_basin}', '1', '{$jsuser_sn}');";

				$sql1 = "UPDATE onliine_add_data SET onadd_quantity='{$onadd_quantity_cha123}', onadd_status='{$onadd_status}' WHERE onadd_sn='{$onadd_sn}'";
				if($conn->query($sql) && $conn->query($sql1)) {
					$ret_msg = "新增成功！";
				} else {
					$ret_msg = "新增失敗！";
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
		if($onadd_quantity_del123<=0) {
			$onadd_status = -1;
		} else {
			$onadd_status = 1;
		}

		if(empty($onadd_quantity_del)){
			$ret_msg = "*為必填！";
		} else {
			$now = time();
			$conn = getDB();
			$sql1 = "UPDATE onliine_add_data SET onadd_quantity='{$onadd_quantity_del123}', onadd_status='{$onadd_status}' WHERE onadd_sn='{$onadd_sn}'";
			if($conn->query($sql1)) {
				$ret_msg = "修改完成！";
			} else {
				$ret_msg = "修改失敗！";
			}
		}

		if(empty($onelda_reason)){
			$ret_msg = "*為必填！";
		} else {
			$now = time();
			$conn = getDB();
			$sql = "INSERT INTO online_elimination_data (onelda_add_date, onelda_mod_date, onelda_quantity, onelda_reason, onadd_sn, onadd_part_no, onadd_part_name) " .
				"VALUES ('{$now}', '{$now}', '{$onadd_quantity_del}', '{$onelda_reason}', '{$onadd_sn}', '{$onadd_part_no}', '{$onadd_part_name}');";
			if($conn->query($sql)) {
				$ret_msg = "修改完成！";
			} else {
				$ret_msg = "修改失敗！";
			}			
			$conn->close();
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
		$onadd_quantity_shi=GetParam('onadd_quantity_shi');//汰除數量
		$onshda_client=GetParam('onshda_client');//汰除數量
		$onadd_quantity_shi123 = ($onadd_quantity - $onadd_quantity_shi);
		if($onadd_quantity_shi123<=0) {
			$onadd_status = -1;
		} else {
			$onadd_status = 1;
		}

		if(empty($onadd_quantity_shi)){
			$ret_msg = "*為必填！";
		} else {
			$now = time();
			$conn = getDB();
			$sql = "UPDATE onliine_add_data SET onadd_quantity='{$onadd_quantity_shi123}', onadd_status='{$onadd_status}' WHERE onadd_sn='{$onadd_sn}'";
			if($conn->query($sql)) {
				$ret_msg = "修改完成！";
			} else {
				$ret_msg = "修改失敗！";
			}
			$conn->close();
		}

		if(empty($onadd_quantity_shi)){
			$ret_msg = "*為必填！";
		} else {
			$now = time();
			$conn = getDB();
			$sql = "INSERT INTO online_shipment_data (onshda_add_date, onshda_mod_date, onshda_client, onshda_quantity, onadd_sn, onadd_part_no, onadd_part_name) " .
				"VALUES ('{$now}', '{$now}', '{$onshda_client}', '{$onadd_quantity_shi}', '{$onadd_sn}', '{$onadd_part_no}', '{$onadd_part_name}');";
			if($conn->query($sql)) {
				$ret_msg = "修改完成！";
			} else {
				$ret_msg = "修改失敗！";
			}			
			$conn->close();
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
	if(($onproduct_part_no = GetParam('onproduct_part_no'))) {
		$search_where[] = "onproduct_part_no like '%{$onproduct_part_no}%'";
		$search_query_string['onproduct_part_no'] = $onadd_part_no;
	}
	if(($onproduct_part_name = GetParam('onproduct_part_name'))) {
		$search_where[] = "onproduct_part_name like '%{$onproduct_part_name}%'";
		$search_query_string['onproduct_part_name'] = $onadd_part_name;
	}

	$search_where = isset($search_where) ? implode(' and ', $search_where) : '';
	$search_query_string = isset($search_query_string) ? http_build_query($search_query_string) : '';

	// page
	$pg_page = GetParam('pg_page', 1);
	$pg_rows = 8;
	$pg_total = GetParam('pg_total')=='' ? getUserQty($search_where) : GetParam('pg_total');
	$pg_offset = $pg_rows * ($pg_page - 1);
	$pg_pages = $pg_rows == 0 ? 0 : ( (int)(($pg_total + ($pg_rows - 1)) /$pg_rows) );

	// echo $pg_pages;
	// exit();
	$AllProductData = getAllProductData_page($search_where,$pg_offset,$pg_rows);

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
	<link rel="stylesheet" href="amazeui/css/amazeui.min.css">
	<link rel="stylesheet" href="default/style.css">
	<script src="amazeui/js/jquery.min.js"></script>
	<script src="amazeui/js/amazeui.min.js"></script>
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

	<script type="text/javascript">
		$(document).ready(function() {
			$('#Upload_Image_Modal-modal').modal();
			<?php
					//	init search parm
			// print "$('#search [name=onadd_status] option[value={$onadd_status}]').prop('selected','selected');";
			// print "$('#search [name=onadd_growing] option[value={$onadd_growing}]').prop('selected','selected','selected','selected','selected','selected','selected');";
			?>

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
			                	$('#upd_form input[name=onadd_change_basin]').val(d.onadd_change_basin);			                	
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

			$('#add_form, #upd_form, #upd_form1, #upd_form2').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					e.preventDefault();
					var param = $(this).serializeArray();

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
			                     // console.log(xhr);
			                 }
			             });
					 }
					});
				// $('#datetimepicker1').datetimepicker({
		  //       	minView: 2,
		  //           language:  'zh-TW',
		  //           format: 'yyyy-mm-dd',
		  //           useCurrent: false
		  //       });
		        
		  //       $('#datetimepicker2').datetimepicker({
		  //       	minView: 2,
		  //           language:  'zh-TW',
		  //           format: 'yyyy-mm-dd',
		  //           useCurrent: false
		  //       });
		  //       $('button.cancel').on('click', function() {
				// 	location.href = "./../";
				// });

		});
	function upd_btn_click(onproduct_sn) {
	  	$('#Upload_Image_Modal').modal('show');
	  	$('#onproduct_sn').val(onproduct_sn);
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
					<h4>品種資料</h4>
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
						    <input type="hidden" id="onproduct_type" name="onproduct_type" value="1">
						    <input type="hidden" id="parameters" name="parameters" value="<?php echo "plant_purchase_pdetails.php"; ?>">
						    <!-- accept 限制上傳檔案類型 -->
						    <input type="file" name="myFile" accept="image/jpeg,image/jpg,image/gif,image/png">

						    <input type="submit" value="上傳檔案">
						</form>
					</div>	
				</div>		
			</div>			
		</div>

		<!-- container -->
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">

					<!-- nav toolbar -->

					<!-- search -->
					<div id="search" style="clear:both;">
						<form autocomplete="off" method="get" action="./plant_purchase_details.php" id="search_form" class="form-inline alert alert-info" role="form">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="searchInput1">品號</label>
										<input type="text" class="form-control" id="searchInput1" name="onproduct_part_no" value="<?php echo $onadd_part_no;?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="searchInput4">品名</label>
										<input type="text" class="form-control" id="searchInput4" name="onproduct_part_name" value="<?php echo $onadd_part_name;?>" placeholder="">
									</div>

									<button type="submit" class="btn btn-info" op="search">搜尋</button>
								</div>
							</div>
						</form>
					</div>
        				<!--foreach ($user_list as $row) {
        						// 	echo '<tr>';
        						// 	echo '<td><button type="button" class="btn btn-info btn-xs" onclick="location.href=\'./details_table.php?onadd_part_no='.$row['onadd_part_no'].'&onadd_growing='.$row['onadd_growing'].'&onadd_quantity_del='.$row['onadd_quantity_del'].'&start='.$start.'&end='.$end.'\'">查看</button></td>';
        						// 	echo '<td>'.$row['onadd_part_no'].'</td>';//品號
        						// 	echo '<td>'.$row['onadd_part_name'].'</td>';//品名
        						// 	echo '</tr>';
        						// }
        					-->

        				</div>
        			</div>
        		</div>
        		<div class="" ass="am-cf am-g">
        			<ul class="am-avg-sm-2 my-shop-product-list">
        				<?php
        				$produce_image = '';
        				$image_btn_name = '';
        				for($i = 0;$i < count($AllProductData);$i++){        					
        					if(!empty($AllProductData[$i]['onproduct_pic_url'])){
        						$produce_image = $AllProductData[$i]['onproduct_pic_url'];
        						$image_btn_name = '更新照片';
        					}else{
        						$produce_image = './images/nopic.png';
        						$image_btn_name = '上傳照片';
        					}
        					$product_size_n = getSizeQtyBySn($AllProductData[$i]['onproduct_part_no'],$AllProductData[$i]['onproduct_part_name']);
        					// printr($product_size_n);
        					// exit();
        					echo '<li>
		        					<div class="am-panel am-panel-default">
		        						<div class="am-panel-bd">
		        						<button class="btn btn-info" onclick="upd_btn_click('.$AllProductData[$i]['onproduct_sn'].')">'.$image_btn_name.'</button>
		        							<img class="am-img-responsive" src="'.$produce_image.'" height="170" width="150">
		        							<h3 style="text-align:center"><a href="details_ptable.php?onadd_part_no='.$AllProductData[$i]['onproduct_part_no'].'&onadd_growing='.$AllProductData[$i]['onproduct_growing'].'&onadd_quantity_del=2019">'.$AllProductData[$i]['onproduct_part_name'].'</a></h3>
		        							<h4 style="text-align: center; display:block;">'.$AllProductData[$i]['onproduct_part_no'].'</h4>
		        							';
		        								for($n=0;$n<6;$n++){
		        									if($product_size_n[$n]['onadd_growing'] != null){
		        										echo '<span style="text-align: center; display:block;">'.$permissions_mapping[$product_size_n[$n]['onadd_growing']].'寸：'.$product_size_n[$n]['sum'].' 株</span><br>';
		        									}		        									
		        								}

		        			echo			'        						
		        						</div>
		        					</div>
		        				</li>';
        				}
        				?>

        			</ul>

        		</div>
        		<?php include('./../htmlModule/page.php');?>
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