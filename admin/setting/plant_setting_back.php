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
		$onchba_sn=GetParam('onchba_sn');
		$ret_data = array();
		if(!empty($onchba_sn)){
			$ret_code = 1;
			$ret_data = getUserBySn($onchba_sn);
		} else {
			$ret_code = 0;
		}

		break;

		case 'upd':
		$onchba_sn=GetParam('onchba_sn');
		$onchba_add_date=GetParam('onchba_add_date');//建立日期
		$onchba_mod_date=GetParam('onchba_mod_date');//修改日期
		$onchba_size=GetParam('onchba_size');//品號
		$onchba_cycle=GetParam('onchba_cycle');//品名
		$jsuser_sn = GetParam('supplier');//編輯人員

		if(empty($onchba_cycle)){
			$ret_msg = "*為必填！";
		} else { 
			$user = getUserByAccount($onchba_size);
			$now = time();
			$conn = getDB();
				$sql = "UPDATE online_change_basin SET onchba_cycle='{$onchba_cycle}' WHERE onchba_sn='{$onchba_sn}'";
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
	if(($onchba_size = GetParam('onchba_size'))) {
		$search_where[] = "onchba_size like '%{$onchba_size}%'";
		$search_query_string['onchba_size'] = $onchba_size;
	}
	if(($onchba_cycle = GetParam('onchba_cycle'))) {
		$search_where[] = "onchba_cycle like '%{$onchba_cycle}%'";
		$search_query_string['onchba_cycle'] = $onchba_cycle;
	}
	if(($onchba_status = GetParam('onchba_status', -1))>=0) {
		$search_where[] = "onchba_status='{$onchba_status}'";
		$search_query_string['onchba_status'] = $onchba_status;
	}

	$search_where = isset($search_where) ? implode(' and ', $search_where) : '';
	$search_query_string = isset($search_query_string) ? http_build_query($search_query_string) : '';

	// page
	$pg_page = GetParam('pg_page', 1);
	$pg_rows = 20;
	$pg_total = GetParam('pg_total')=='' ? getUserQty($search_where) : GetParam('pg_total');
	$pg_offset = $pg_rows * ($pg_page - 1);
	$pg_pages = $pg_rows == 0 ? 0 : ( (int)(($pg_total + ($pg_rows - 1)) /$pg_rows) );

	$user_list = getUser($search_where, $pg_offset, $pg_rows);
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
			print "$('#search [name=onchba_status] option[value={$onchba_status}]').prop('selected','selected');";
			?>

			$('button.upd').on('click', function(){
				$('#upd-modal').modal();
				$('#upd_form')[0].reset();

				$.ajax({
					url: './plant_setting.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get", onchba_sn:$(this).data('onchba_sn')},
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
			                	$('#upd_form input[name=onchba_sn]').val(d.onchba_sn);
			                	$('#upd_form input[name=onchba_size]').val(d.onchba_size);
			                	$('#upd_form input[name=onchba_cycle]').val(d.onchba_cycle);		                	
			                	$('#upd_form [name=onchba_status] option[value='+d.onchba_status+']').prop('selected','selected');
			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
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

			$('#add_form, #upd_form, #upd_form1, #upd_form2').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					e.preventDefault();
					var param = $(this).serializeArray();

					$(this).parents('.modal').modal('hide');
					$(this)[0].reset();

					 	// console.table(param);

					 	$.ajax({
					 		url: './plant_setting.php',
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
        				<h4>種植週期設定</h4>
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
        								<input type="hidden" name="onchba_sn">
        								<div class="form-group">
        									<label for="addModalInput1" class="col-sm-2 control-label">週期(日)<font color="red">*</font></label>
        									<div class="col-sm-10">
        										<input type="text" class="form-control" id="addModalInput1" name="onchba_cycle" placeholder="" required minlength="1" maxlength="32">
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
        							<th>尺寸</th>
        							<th>週期(日)</th>
        							<th>操作</th>
        						</tr>
        					</thead>
        					<tbody>
        						<?php
        						foreach ($user_list as $row) {
        							echo '<tr>';
        							echo '<td>'.$row['onchba_size'].'</td>';//品號
        							echo '<td>'.$row['onchba_cycle'].'</td>';//品名
        							echo '<td><button type="button" class="btn btn-primary btn-xs upd" data-onchba_sn="'.$row['onchba_sn'].'">修改</button>&nbsp;';
        							echo '</td></tr>';
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
    </html>?>