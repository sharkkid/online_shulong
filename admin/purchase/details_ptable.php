<?php
include_once("./func_plant_purchase.php");
// printr(getExpectedShipByMonth(2019,'PA2',2));
// exit();
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

// printr(getProductData(1));
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
		$onbuda_part_no = GetParam('onbuda_part_no');
		$onbuda_part_name = GetParam('onbuda_part_name');
		$onbuda_quantity = GetParam('onbuda_quantity');
		$onbuda_size = GetParam('onbuda_size');
		$onbuda_date = strtotime (GetParam('onbuda_date'));
		$onbuda_client = GetParam('onbuda_client');
		$onbuda_year = substr(GetParam('onbuda_date'),0,4);
		$onbuda_month = substr(GetParam('onbuda_date'),6,-3);
		$now = time();
		$conn = getDB();

		$sql = "INSERT INTO `onliine_business_data`(`onbuda_add_date`, `onbuda_mod_date`, `onbuda_status`, `onbuda_part_no`, `onbuda_part_name`, `onbuda_date`, `onbuda_quantity`, `onbuda_size`, `onbuda_client`, `onbuda_year`, `onbuda_day`) ".
			"VALUES('{$now}', '{$now}', '1','{$onbuda_part_no}','{$onbuda_part_name}','{$onbuda_date}','{$onbuda_quantity}','{$onbuda_size}','{$onbuda_client}','{$onbuda_year}','{$onbuda_month}');";
		if($conn->query($sql)) {
			$ret_msg = "完成！";
		} else {
			$ret_msg = "失敗！";
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

		default:
		$ret_msg = 'error!';
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
	$onadd_growing = GetParam('onadd_growing');
	$onadd_quantity_del = GetParam('onadd_quantity_del');
	$user_list = getExpectedShipByMonth($onadd_quantity_del,$onadd_part_no);
	$business_data = getBusinessData($onadd_part_no,$onadd_quantity_del);
	// printr($business_data);
	// exit;
	$data_list = getDataDetails($onadd_part_no,$onadd_growing);
	// $user_list = getDetails($onadd_part_no,$onadd_growing,$onadd_quantity_del);
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
	$pg_total = GetParam('pg_total')=='' ? getUserQty($search_where) : GetParam('pg_total');
	$pg_offset = $pg_rows * ($pg_page - 1);
	$pg_pages = $pg_rows == 0 ? 0 : ( (int)(($pg_total + ($pg_rows - 1)) /$pg_rows) );
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
	<script type="text/javascript">
		$(document).ready(function() {
			<?php
					//	init search parm
			print "$('#search [name=onadd_status] option[value={$onadd_status}]').prop('selected','selected');";
			print "$('#search [name=onadd_growing] option[value={$onadd_growing}]').prop('selected','selected','selected','selected','selected','selected','selected');";
			?>

			//汰除-----------------------------------------------------------
			$('button.upd1').on('click', function(){
				$('#upd-modal1').modal();
				$('#upd_form1')[0].reset();
				var onproduct_sn = $("#onproduct_sn").html()
				$.ajax({
					url: './details_table.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get", onproduct_sn:onproduct_sn},
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
			                	$("input[name=onbuda_part_no]").val(d[0].onproduct_part_no);
			                	$("input[name=onbuda_part_name]").val(d[0].onproduct_part_name);
			                }
			                else{
			                	console.log("error");
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

			$('#upd_form1').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					e.preventDefault();
					var param = $(this).serializeArray();


					$(this).parents('.modal').modal('hide');
					$(this)[0].reset();

					 	// console.table(param);

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
			                	// console.log('ajax error');
			                     // console.log(xhr);
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
					$('#month_customers_cotent').html('<div class="col-md-12"><div class="col-sm-10"><label for="addModalInput1" class="col-sm-2 control-label">客戶名稱</label><label for="addModalInput1" class="col-sm-2 control-label">預計出貨數量</label><label for="addModalInput1" class="col-sm-2 control-label">預計出貨日期</label><label for="addModalInput1" class="col-sm-2 control-label">預計出貨尺寸</label><label for="addModalInput1" class="col-sm-2 control-label">該筆新增日期</label></div></div>');
					$.each(ret.data, function(key,value){	
						if(key < ret.data.length){										
							$('#month_customers_cotent').html($('#month_customers_cotent').html()+'<div class="col-md-12"><div class="col-sm-10"><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbuda_client+'</label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbuda_quantity+'</label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbuda_date+'</label></label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbuda_size+'吋</label><label for="addModalInput1" class="col-sm-2 control-label">'+value.onbuda_add_date+'</label></div></div>');								
						}

					});
					
				},
				error: function (xhr, ajaxOptions, thrownError) {
			   	console.log('ajax error');
			        // console.log(xhr);
			    }
			});
			// $('#month_customers_cotent').html($('#month_customers_cotent').html()+'<div class="col-md-12"><div class="col-sm-10"><label for="addModalInput1" class="col-sm-2 control-label">客戶名稱</label><label for="addModalInput1" class="col-sm-2 control-label">預計出貨數量</label><label for="addModalInput1" class="col-sm-2 control-label">預計出貨日期</label><label for="addModalInput1" class="col-sm-2 control-label">該筆新增日期</label></div></div>');
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
		<div class="page-header">
			<div class="row">
				<div class="col-sm-6">
					<h4>品種資料</h4>
				</div>
			</div>
		</div>

		<div class="navbar-collapse collapse pull-right" style="margin-bottom: 10px;">
			<ul class="nav nav-pills pull-right toolbar">
				<li><button type="button" class="btn btn-primary btn-xs" onClick="upd_btn_click(<?php echo $data_list[0]['onproduct_sn'];?>)"><i class="glyphicon glyphicon-plus"></i>新增更多圖片</button></li>
				<!-- <li><button type="button" class="btn btn-primary btn-xs upd1"><i class="glyphicon glyphicon-plus"></i>預計出貨資料</button></li> -->
			</ul>
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
						    <input type="hidden" id="parameters" name="parameters" value="<?php echo "details_ptable.php?onadd_part_no=".GetParam('onadd_part_no')."&onadd_growing=".GetParam('onadd_growing').'&onadd_quantity_del='.GetParam('onadd_quantity_del'); ?>">
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

		<!--預計出貨----------------------------------------------------------->
		<div id="upd-modal1" class="modal upd-modal1" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd_form1" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">新增預計出貨</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd1">
									<input type="hidden" name="onadd_sn">
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">預計出貨數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="hidden" name="onbuda_part_no">
											<input type="hidden" name="onbuda_part_name">
											<input type="text" class="form-control" id="addModalInput1" name="onbuda_quantity" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div> 
									<div class="form-group">
										<label class="col-sm-2 control-label">預計出貨日期&nbsp;</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="datetimepicker1" name="onbuda_date" placeholder="">
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

		<!-- Page Content -->
		<div  class="container-fluid">
			<div class="row">
				<div class="col-md-4">
					<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
						<!-- Indicators -->
						<ol class="carousel-indicators">
							<?php
							$pics = getPic($data_list[0]['onproduct_sn']);
							for($i=0;$i<count($pics);$i++){
								if($i==0)
									echo '<li data-target="#carousel-example-generic" data-slide-to="'.$i.'" class="active"></li>';
								else
									echo '<li data-target="#carousel-example-generic" data-slide-to="'.$i.'"></li>';
							}
							?>
						</ol>
						<div class="carousel-inner">
							<?php
								if(!empty($data_list[0]['onproduct_pic_url'])){									
									for($i=0;$i<count($pics);$i++){
										if($i==0){
											echo '<div class="item active">';
												echo "<img src='".$pics[$i]['onpic_img_path']."'>";
											echo '</div>';
										}
										else{
											echo '<div class="item">';
												echo "<img src='".$pics[$i]['onpic_img_path']."'>";
											echo '</div>';
										}
									}
								}
								else
									echo "<img src='images/nopic.png' >";
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
			</div>
		</div></br>

		<div class="col-md-10">


			<?php
			foreach ($data_list as $row) {
				echo '<div style="display:none" id="onproduct_sn">'.$row['onproduct_sn'].'</div>';
				echo '<h3>'.$onproduct_part_no.'</h3>';
				echo '<p>'. '品號(Part no.) : '. $row['onproduct_part_no'].'</p>';
				echo '<p>'. '品名(Part name.) : '. $row['onproduct_part_name'].'</p>';
				echo '<p>'. '花色 (Flower Color) : '. $row['onproduct_color'].'</p>';
				echo '<p>'. '花徑 (Flower Size) : '. $row['onproduct_size'].'</p>';
				echo '<p>'. '高度 (Plant Height) : '. $row['onproduct_height'].'</p>';
				echo '<p>'. '適合開花盆徑 (Suitable flowering pot size) : '. $row['onproduct_pot_size'].'</p>';
			}
			?> 
		</div>

		<!-- container -->
		<div  class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<?php
					$href = './details_ptable.php?onadd_part_no='.$onadd_part_no.'&onadd_growing='.$onadd_growing.'&onadd_quantity_del='.'2020'.'&end='.$end;
					?>
					<ul class="nav nav-tabs">
						<?php
						$font_size = '';
						for($i=0;$i<5;$i++){
							$n = (2019+$i);
							if($n == GetParam('onadd_quantity_del')){
								echo '<li class="active"><a style="color:#000000;">'.$n.'</a></li>';
							}
							else{
								echo '<li class="active"><a style="color:#23b7e5;" href="'.WT_URL_ROOT.'/admin/purchase/details_ptable.php?onadd_part_no='.GetParam('onadd_part_no').'&onadd_growing='.GetParam('onadd_growing').'&onadd_quantity_del='.$n.'">'.$n.'</a></li>';
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
        			<table id="table_summary" class="table table-striped table-hover table-condensed table-bordered">
        				<thead>
        					<tr>
        						<th rowspan="2">出售</br>尺寸</th>
        						<th colspan="12" class="tableheader" align="center">可供出售月份(系統計算)</th>
        					</tr>
        					<tr>
        						<th>一月</th>
        						<th>二月</th>
        						<th>三月</th>
        						<th>四月</th>
        						<th>五月</th>
        						<th>六月</th>
        						<th>七月</th>
        						<th>八月</th>
        						<th>九月</th>
        						<th>十月</th>
        						<th>十一月</th>
        						<th>十二月</th>
        					</tr>
        				</thead>
        				<tbody>
        					<?php
        					for($i=0;$i<8;$i++){
        						$n = 0;
        						for($j=1;$j<=12;$j++){
        							$n += $user_list[$i][$j];
        						}
        						if($n != 0){
        							echo '<tbody>';
        							echo '<td>'.$permissions_mapping[$i].'寸'.'</td>';
		        					for($j = 1 ;$j <= 12;$j++){
		        						echo '<td>'.$user_list[$i][$j].'</td>';//預計成熟月份數量
		                            }
		                            echo '</tbody>';
        						}

        					}
                             ?>
                         </tbody>
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