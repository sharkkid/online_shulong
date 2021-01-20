<?php
include_once("./func.php");

$status_mapping = array(0=>'<font color="red">關閉</font>', 1=>'<font color="blue">啟用</font>');
$permissions_mapping = array(
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

$op=GetParam('op');
if(!empty($op)) {
	$ret_code = 1;
	$ret_msg = '';
	$ret_data = array();
	switch ($op) {
		case 'add':
		$onsd_name=GetParam('onsd_name');
		$onsd_address=GetParam('onsd_address');
		$onsd_phone=GetParam('onsd_phone');
		$onsd_mail=GetParam('onsd_mail');
		
		if(empty($onsd_name)){
			$ret_msg = "*為必填！";
		} else { 
			$ret_msg = addSupplierData($onsd_name,$onsd_address,$onsd_phone,$onsd_mail);
		}
		break;

		case 'get':
		$onsd_sn=GetParam('onsd_sn');
		$ret_data = array();
		if(!empty($onsd_sn)){
			$ret_code = 1;
			$ret_data = getSupplierDataBySn($onsd_sn);
		} else {
			$ret_code = 0;
		}

		break;

		case 'adjust':
		$onsd_sn=GetParam('onsd_sn');
		$onsd_name=GetParam('onsd_name');
		$onsd_address=GetParam('onsd_address');
		$onsd_phone=GetParam('onsd_phone');
		$onsd_mail=GetParam('onsd_mail');
		
		if(empty($onsd_sn)){
			$ret_msg = "*錯誤";
		} else { 
			$now = time();
			$conn = getDB();
			$sql = "UPDATE `onliine_supplier_data` SET `onsd_name`='{$onsd_name}',`onsd_phone`='{$onsd_phone}',`onsd_mail`='{$onsd_mail}',`onsd_address`='{$onsd_address}' WHERE `onsd_sn`= $onsd_sn";			
			if($conn->query($sql)) {
				$ret_msg = "修改成功！";
			} else {
				$ret_msg = "修改失敗！";
			}
			$conn->close();
		}
		break;

		case 'del':
		$onsd_sn=GetParam('onsd_sn');
		
		if(empty($onsd_sn)){
			$ret_msg = "*為必填！";
		} else { 
			$conn = getDB();
			$sql = "UPDATE `onliine_supplier_data` SET onsd_status = 0 WHERE onsd_sn = $onsd_sn ";			
			if($conn->query($sql)) {
				$ret_msg = "刪除成功！";
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
	if(($onsd_name = GetParam('onsd_name'))) {
		$search_where[] = "onsd_name like '%{$onsd_name}%'";
		$search_query_string['onsd_name'] = $onsd_name;
	}
	if(($onsd_phone = GetParam('onsd_phone'))) {
		$search_where[] = "onsd_phone like '%{$onsd_phone}%'";
		$search_query_string['onsd_phone'] = $onsd_phone;
	}
	if(($onsd_address = GetParam('onsd_address'))) {
		$search_where[] = "onsd_address like '%{$onsd_address}%'";
		$search_query_string['onsd_address'] = $onsd_address;
	}
	if(($onadd_mail = GetParam('onadd_mail', -1))>=0) {
		$search_where[] = "onadd_mail='{$onadd_mail}'";
		$search_query_string['onadd_mail'] = $onadd_mail;
	}

	$search_where = isset($search_where) ? implode(' and ', $search_where) : '';
	$search_query_string = isset($search_query_string) ? http_build_query($search_query_string) : '';

	// page
	$pg_page = GetParam('pg_page', 1);
	$pg_rows = 20;
	$pg_total = GetParam('pg_total')=='' ? getSupplierDataQty($search_where) : GetParam('pg_total');
	$pg_offset = $pg_rows * ($pg_page - 1);
	$pg_pages = $pg_rows == 0 ? 0 : ( (int)(($pg_total + ($pg_rows - 1)) /$pg_rows) );

	$supplier_list = getSupplierData($search_where, $pg_offset, $pg_rows);

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

			bootbox.setDefaults({
				locale: "zh_TW",
			});

			$('button.del').on('click', function(){
				onsd_sn = $(this).data('onsd_sn')
				bootbox.confirm("確認刪除？", function(result) {
					if(result) {
						$.ajax({
							url: './sys_supplier.php',
							type: 'post',
							dataType: 'json',
							data: {op:"del", onsd_sn:onsd_sn},
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

			//延後-----------------------------------------------------------
			$('button.adjust').on('click', function(){
				$('#adjust-modal').modal();
				$('#adjust_form')[0].reset();				
				var onsd_sn = $(this).data('onsd_sn');

				$.ajax({
					url: './sys_supplier.php',
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
						var data = ret.data;
						$('#adjust_form input[name=onsd_sn]').val(data.onsd_sn);	
						$('#adjust_form input[name=onsd_name]').val(data.onsd_name);	
						$('#adjust_form input[name=onsd_phone]').val(data.onsd_phone);	
						$('#adjust_form input[name=onsd_address]').val(data.onsd_address);	
						$('#adjust_form input[name=onsd_mail]').val(data.onsd_mail);	
					},
					error: function (xhr, ajaxOptions, thrownError) {
			        	// console.log('ajax error');
			             // console.log(xhr);
			         }
			    });		
			});

			$('#add_form, #adjust_form').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					e.preventDefault();
					var param = $(this).serializeArray();

					$(this).parents('.modal').modal('hide');
					$(this)[0].reset();
					 	$.ajax({
					 		url: './sys_supplier.php',
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
					<h4>供應商管理</h4>
				</div>
			</div>
		</div>

		<!-- modal -->
		<div id="add-modal" class="modal add-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="add_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="schedule_title">新增供應商</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="add">

									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">供應商名稱<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onsd_name" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">供應商電話</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onsd_phone" placeholder=""  maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">供應商地址</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onsd_address" placeholder="" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">供應商信箱</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onsd_mail" placeholder="" maxlength="32">
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
		<!-- modal -->
		<div id="adjust-modal" class="modal adjust-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="adjust_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title" id="schedule_title">供應商修改</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="adjust">
									<input type="hidden" name="onsd_sn">

									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">供應商名稱<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onsd_name" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">供應商電話</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onsd_phone" placeholder="" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">供應商地址</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onsd_address" placeholder="" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">供應商信箱</label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="onsd_mail" placeholder="" maxlength="32">
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

		<!-- container -->
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="navbar-collapse collapse pull-right" style="margin-bottom: 10px;">
						<ul class="nav nav-pills pull-right toolbar">							
							<li><button data-parent="#toolbar" data-toggle="modal" id="todo_export" data-target=".add-modal" class="accordion-toggle btn btn-info"><i class="glyphicon glyphicon-plus"></i> 新增供應商</button></li>
						</ul>
					</div>
					<!-- search -->
					<div id="search" style="clear:both;">
						<form autocomplete="off" method="get" action="./sys_supplier.php" id="search_form" class="form-inline alert alert-info" role="form">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="searchInput1">供應商名稱</label>
										<input type="text" class="form-control" id="searchInput1" name="onsd_name" value="<?php echo $onsd_name;?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="searchInput4">供應商電話</label>
										<input type="text" class="form-control" id="searchInput4" name="onsd_phone" value="<?php echo $onsd_phone;?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="searchInput4">供應商地址</label>
										<input type="text" class="form-control" id="searchInput4" name="onsd_address" value="<?php echo $onsd_address;?>" placeholder="">
									</div>
									<div class="form-group">
										<label for="searchInput4">供應商信箱</label>
										<input type="text" class="form-control" id="searchInput4" name="onsd_mail" value="<?php echo $onsd_mail;?>" placeholder="">
									</div>

									<button type="submit" class="btn btn-info" op="search">搜尋</button>
								</div>
							</div>
						</form>
					</div>

					<!-- content -->
					<table class="table table-striped table-hover table-condensed tablesorter">
						<thead>
							<tr style="font-size: 1.1em">
								<th style="text-align: center;">供應商編號</th>
								<th style="text-align: center;">供應商名稱</th>
								<th style="text-align: center;">供應商電話</th>
								<th style="text-align: center;">供應商地址</th>
								<th style="text-align: center;">供應商信箱</th>
								<?php if($permmsion == 0){ ?>
									<th style="text-align: center;">操作</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($supplier_list as $row) {
								echo '<tr>';
									echo '<td style="vertical-align: middle;text-align: center;">'.$row['onsd_sn'].'</td>';
									echo '<td style="vertical-align: middle;text-align: center;">'.$row['onsd_name'].'</td>';
									echo '<td style="vertical-align: middle;text-align: center;">'.$row['onsd_phone'].'</td>';
									echo '<td style="vertical-align: middle;text-align: center;">'.$row['onsd_address'].'</td>';
									echo '<td style="vertical-align: middle;text-align: center;">'.$row['onsd_mail'].'</td>';
        							if($permmsion == 0){
        								echo '<td style="vertical-align: middle;text-align: center;">
        								<button type="button" class="btn btn-primary btn-xs adjust" data-onsd_sn="'.$row['onsd_sn'].'">修改</button>
        								<button type="button" class="btn btn-danger btn-xs del" data-onsd_sn="'.$row['onsd_sn'].'">刪除</button>
        								</td>';
        							}
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
    </body>
    </html>