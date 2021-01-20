<?php
include_once("./func_plant_setting.php");
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

$permmsion = $_SESSION['user']['jsuser_admin_permit'];

$op=GetParam('op');
if(!empty($op)) {
	$ret_code = 1;
	$ret_msg = '';
	$ret_data = array();
	switch ($op) {
		case 'add':
		$onchba_add_date=GetParam('onchba_add_date');//建立日期
		$onchba_mod_date=GetParam('onchba_mod_date');//修改日期
		$onchba_size=GetParam('onchba_size');//品號
		$onchba_cycle=GetParam('onchba_cycle');//品名
		$onchba_status=GetParam('onchba_status');//狀態 1 啟用 0 刪除
		$jsuser_sn = GetParam('supplier');//編輯人員

		if(empty($onchba_cycle)){
			$ret_msg = "*為必填！";
		} else { 
			$user = getUserByAccount($onchba_size);
			$onadd_planting_date = str2time($onadd_planting_date);
			$now = time();
			$conn = getDB();
				$sql = "INSERT INTO online_change_basin (onchba_add_date, onchba_mod_date, onchba_size, onchba_cycle, onchba_status, jsuser_sn) " .
				"VALUES ('{$now}', '{$now}', '{$onchba_size}', '{$onchba_cycle}', '1', '{$jsuser_sn}');";
				if($conn->query($sql)) {
					$ret_msg = "新增成功！";
				} else {
					$ret_msg = "新增失敗！";
				}
			$conn->close();
		}
		break;

		case 'get':
		$onsp_sn=GetParam('onsp_sn');
		$ret_data = array();
		if(!empty($onsp_sn)){
			$ret_code = 1;
			$ret_data = getSpace($onsp_sn);
		} else {
			$ret_code = 0;
		}

		break;

		case 'get_fa':
		$ret_data = array();
		$ret_code = 1;
		$ret_data = getFacilityAllocation();
		break;

		case 'upd-fa':
		$fa_number=GetParam('fa_number');

		if($fa_number == ""){
			$ret_msg = "*為必填！";
		} else { 
			$now = time();
			$conn = getDB();
				$sql = "UPDATE `js_config` SET `jsc_value`='{$fa_number}' WHERE jsc_key like 'FacilityAllocation'";
				if($conn->query($sql)) {
					$ret_msg = "修改成功！";
				} else {
					$ret_msg = "修改失敗！";
				}
			$conn->close();
		}
		break;

		case 'upd':
		$onsp_sn=GetParam('onsp_sn');
		$onsp_space=GetParam('onsp_space');


		if(empty($onsp_sn)){
			$ret_msg = "*為必填！";
		} else { 
			$now = time();
			$conn = getDB();
				$sql = "UPDATE online_space SET onsp_space='{$onsp_space}' WHERE onsp_sn='{$onsp_sn}'";
				if($conn->query($sql)) {
					$ret_msg = "修改成功！";
				} else {
					$ret_msg = "修改失敗！";
				}
			$conn->close();
		}
		break;

		case 'del':
		$onchba_sn=GetParam('onchba_sn');

		if(empty($onchba_sn)){
			$ret_msg = "刪除失敗！";
		}else{
			$now = time();
			$conn = getDB();
			$sql = "DELETE FROM online_change_basin WHERE onchba_sn='{$onchba_sn}'";
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
	if(($onchba_status = GetParam('onsp_space', -1))>=0) {
		$search_where[] = "onsp_space='{$onsp_space}'";
		$search_query_string['onsp_space'] = $onsp_space;
	}

	$search_where = isset($search_where) ? implode(' and ', $search_where) : '';
	$search_query_string = isset($search_query_string) ? http_build_query($search_query_string) : '';

	// page
	$pg_page = GetParam('pg_page', 1);
	$pg_rows = 20;
	$pg_total = GetParam('pg_total')=='' ? getSpaceQty($search_where) : GetParam('pg_total');
	$pg_offset = $pg_rows * ($pg_page - 1);
	$pg_pages = $pg_rows == 0 ? 0 : ( (int)(($pg_total + ($pg_rows - 1)) /$pg_rows) );

	$space_list = getSpace();
	$facilityAllocation = getFacilityAllocation();
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
			// print "$('#search [name=onchba_status] option[value={$onchba_status}]').prop('selected','selected');";
			?>

			$('button.upd').on('click', function(){
				$('#upd-modal').modal();
				$('#upd_form')[0].reset();
				$.ajax({
					url: './space_setting.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get", onsp_sn:$(this).data('onsp_sn')},
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
			                	$('#upd_form input[name=onsp_sn]').val(d[0].onsp_sn);
			                	$('#upd_form input[name=onsp_space]').val(d[0].onsp_space);
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			});

			$('button.upd-facilityallocation').on('click', function(){
				$('#upd-fa-modal').modal();
				$('#upd-fa_form')[0].reset();
				$.ajax({
					url: './space_setting.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get_fa"},
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
			                	$('#upd-fa_form input[name=fa_number]').val(d);
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });
			});

			bootbox.setDefaults({
				locale: "zh_TW",
			});

			$('button.del').on('click', function(){
				onchba_sn = $(this).data('onchba_sn')
				bootbox.confirm("確認刪除？", function(result) {
					if(result) {
						$.ajax({
							url: './plant_purchase.php',
							type: 'post',
							dataType: 'json',
							data: {op:"del", onchba_sn:onchba_sn},
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

			$('#add_form, #upd_form, #upd_form1, #upd_form2, #upd-fa_form').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					e.preventDefault();
					var param = $(this).serializeArray();

					$(this).parents('.modal').modal('hide');
					$(this)[0].reset();
					 	// console.table(param);

					 	$.ajax({
					 		url: './space_setting.php',
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
        				<h4>園區空間設定</h4>
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
        						<h4 class="modal-title">修改</h4>
        					</div>
        					<div class="modal-body">
        						<div class="row">
        							<div class="col-md-12">
        								<input type="hidden" name="op" value="upd">
        								<input type="hidden" name="onsp_sn">
        								<div class="form-group">
        									<label for="addModalInput1" class="col-sm-2 control-label">空間大小<font color="red">*</font></label>
        									<div class="col-sm-10">
        										<input type="text" class="form-control" id="addModalInput1" name="onsp_space" placeholder="" required minlength="1" maxlength="32">
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

        	<!-- modal -->
        	<div id="upd-fa-modal" class="modal upd-fa-modal" tabindex="-1" role="dialog">
        		<div class="modal-dialog modal-lg">
        			<div class="modal-content">
        				<form autocomplete="off" method="post" action="./" id="upd-fa_form" class="form-horizontal" role="form" data-toggle="validator">
        					<div class="modal-header">
        						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        						<h4 class="modal-title">設施攤提成本修改</h4>
        					</div>
        					<div class="modal-body">
        						<div class="row">
        							<div class="col-md-12">
        								<input type="hidden" name="op" value="upd-fa">

        								<div class="form-group">
        									<label for="addModalInput1" class="col-sm-2 control-label">成本金額<font color="red">*</font></label>
        									<div class="col-sm-10">
        										<input type="text" class="form-control" id="addModalInput1" name="fa_number" placeholder="" required minlength="1" maxlength="32">
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

        	<!-- container -->
        	<div class="container-fluid">
        		<div class="row">
        			<div class="col-md-12">

        				<!-- content -->
        				<table class="table table-striped table-hover table-condensed tablesorter">
        					<thead>
        						<tr>
        							<th style="width: 90%;">目前園區所有空間</th>
        							<?php if($permmsion == 0){ ?>
        								<th>操作</th>
        							<?php } ?>
        						</tr>
        					</thead>
        					<tbody>
        						<?php
        						echo '<tr>';
        						echo '<td>'.$space_list[0]['onsp_space'].'</td>';//品名
        						if($permmsion == 0){ 
        							echo '<td><button type="button" class="btn btn-primary btn-xs upd" data-onsp_sn="'.$space_list[0]['onsp_sn'].'">修改</button>&nbsp;';
        						}
        						echo '</td></tr>';

        						?>
        					</tbody>
        				</table>

        				<!-- content -->
        				

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

    </html>?>