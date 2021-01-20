<?php
include_once("./func.php");
$yn_mapping = array(0=>'<font color="red">否</font>', 1=>'<font color="blue">是</font>');
$status_mapping = array(0=>'<font color="red">關閉</font>', 1=>'<font color="blue">啟用</font>');
$permissions_mapping = array(
    1=>'<font color="red">系統管理員</font>', // Admin
    2=>'<font color="blue">員工</font>' // WT_user
);

$jsuser_admin_permit_mapping = array(
    1=>'系統管理員', // Admin
    2=>'員工'// WT_user
);
$DEVICE_SYSTEM= array(
    1=>'系統管理員', // Admin
    2=>'查看權限',// WT_user
    3=>'資料建立權限',// WT_user
    4=>'編輯權限',// WT_user
    5=>'查看區域權限',// WT_user
    6=>'查看成本權限',// WT_user
    7=>'業務管理權限',// WT_user
    8=>'查看可供量權限',// WT_user
    9=>'客戶管理權限',// WT_user
    10=>'帳號管理',// WT_user
    11=>'統計圖表'// WT_user
);
$op=GetParam('op');
if(!empty($op)) {
	$ret_code = 1;
	$ret_msg = '';
	$ret_data = array();
	switch ($op) {
		case 'add':
		$jsuser_account=GetParam('jsuser_account');
		$jsuser_password=GetParam('jsuser_password');
		$jsuser_name=GetParam('jsuser_name');
		$jsuser_email=GetParam('jsuser_email');
		$jsuser_admin_permit=GetParam('jsuser_admin_permit');
		$jsuser_status=GetParam('jsuser_status');

		if(empty($jsuser_account)||empty($jsuser_password)||empty($jsuser_name)||empty($jsuser_email)||$jsuser_status==''){
			$ret_msg = "*為必填！";
		} else if (!preg_match("/^([A-Za-z0-9]+)$/", $jsuser_account)) {
			$ret_msg = "帳號只能為英文或數字組成！";
		} else {
			$user = getUserByAccount($jsuser_account);
			$now = time();
			$conn = getDB();
			if(empty($user)) {
				$sql = "INSERT INTO js_user (jsuser_account, jsuser_password, jsuser_name, jsuser_email, jsuser_add_date, jsuser_mod_date, jsuser_status) " .
				"VALUES ('{$jsuser_account}', '{$jsuser_password}', '{$jsuser_name}', '{$jsuser_email}', '{$now}', '{$now}'," .
				"'{$jsuser_status}');";
				if($conn->query($sql)) {
					$ret_msg = "新增成功！";
				} else {
					$ret_msg = "新增失敗！";
				}
			} else {
				$ret_msg = "帳號重複，新增失敗！";
			}
			$conn->close();
		}
		break;

		case 'get':
		$jsuser_sn=GetParam('jsuser_sn');
		$ret_data = array();
		if(!empty($jsuser_sn)){
			$ret_code = 1;
			$ret_data = getUserBySn($jsuser_sn);
		} else {
			$ret_code = 0;
		}

		break;

		case 'upd':
		$jsuser_sn=GetParam('jsuser_sn');
		$jsuser_password=GetParam('jsuser_password');
		$jsuser_name=GetParam('jsuser_name');
		$jsuser_email=GetParam('jsuser_email');
		// $jsuser_admin_permit=GetParam('jsuser_admin_permit');
		$jsuser_status=GetParam('jsuser_status');

		if(empty($jsuser_sn)||empty($jsuser_password)||empty($jsuser_name)||empty($jsuser_email)||$jsuser_status==''){
			$ret_msg = "*為必填！";
		} else {
			$now = time();
			$conn = getDB();
			$sql = "UPDATE js_user SET jsuser_password='{$jsuser_password}', jsuser_name='{$jsuser_name}', jsuser_email='{$jsuser_email}', jsuser_mod_date='{$now}', " .
			"jsuser_status='{$jsuser_status}' WHERE jsuser_sn='{$jsuser_sn}'";
			if($conn->query($sql)) {
				$ret_msg = "修改完成！";
			} else {
				$ret_msg = "修改失敗！";
			}
			$conn->close();
		}
		break;

		case 'del':
		$jsuser_sn=GetParam('jsuser_sn');

		if(empty($jsuser_sn)){
			$ret_msg = "刪除失敗！";
		}else{
			$now = time();
			$conn = getDB();
			$sql = "DELETE FROM js_user WHERE jsuser_sn='{$jsuser_sn}'";
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
	if(($jsuser_account = GetParam('jsuser_account'))) {
		$search_where[] = "jsuser_account like '%{$jsuser_account}%'";
		$search_query_string['jsuser_account'] = $jsuser_account;
	}
	if(($jsuser_name = GetParam('jsuser_name'))) {
		$search_where[] = "jsuser_name like '%{$jsuser_name}%'";
		$search_query_string['jsuser_name'] = $jsuser_name;
	}
	if(($jsuser_email = GetParam('jsuser_email'))) {
		$search_where[] = "jsuser_email like '%{$jsuser_email}%'";
		$search_query_string['jsuser_email'] = $jsuser_email;
	}
	if(($jsuser_admin_permit = GetParam('jsuser_admin_permit', -1))>=0) {
		$search_where[] = "jsuser_admin_permit='{$jsuser_admin_permit}'";
		$search_query_string['jsuser_admin_permit'] = $jsuser_admin_permit;
	}
	if(($jsuser_status = GetParam('jsuser_status', -1))>=0) {
		$search_where[] = "jsuser_status='{$jsuser_status}'";
		$search_query_string['jsuser_status'] = $jsuser_status;
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
	<script type="text/javascript">
		$(document).ready(function() {
			<?php
					//	init search parm
			print "$('#search [name=jsuser_admin_permit] option[value={$jsuser_admin_permit}]').prop('selected','selected');";
			print "$('#search [name=jsuser_status] option[value={$jsuser_status}]').prop('selected','selected');";
			?>

			$('button.upd').on('click', function(){
				$('#upd-modal').modal();
				$('#upd_form')[0].reset();

				$.ajax({
					url: './sys_user.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get", jsuser_sn:$(this).data('jsuser_sn')},
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
			                	$('#upd_form input[name=jsuser_sn]').val(d.jsuser_sn);
			                	$('#upd_form .jsuser_account').text(d.jsuser_account);
			                	$('#upd_form input[name=jsuser_password]').val(d.jsuser_password);
			                	$('#upd_form input[name=jsuser_name]').val(d.jsuser_name);
			                	$('#upd_form input[name=jsuser_email]').val(d.jsuser_email);
			                	$('#upd_form [name=jsuser_admin_permit] option[value='+d.jsuser_admin_permit+']').prop('selected','selected');
			                	$('#upd_form [name=jsuser_status] option[value='+d.jsuser_status+']').prop('selected','selected');
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
				jsuser_sn = $(this).data('jsuser_sn')
				bootbox.confirm("確認刪除？", function(result) {
					if(result) {
						$.ajax({
							url: './sys_user.php',
							type: 'post',
							dataType: 'json',
							data: {op:"del", jsuser_sn:jsuser_sn},
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

			$('#add_form, #upd_form').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					e.preventDefault();
					var param = $(this).serializeArray();

					$(this).parents('.modal').modal('hide');
					$(this)[0].reset();

					 	// console.table(param);

					 	$.ajax({
					 		url: './sys_user.php',
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
		});
	</script>
</head>

<body>
	<?php include('./../htmlModule/nav.php');?>
	
        <section class="main-content">



        	<!--page header start-->
        	<div class="page-header">
        		<div class="row">
        			<div class="col-sm-6">
        				<h4>帳號管理</h4>
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
        						<h4 class="modal-title">新增使用者</h4>
        					</div>
        					<div class="modal-body">
        						<div class="row">
        							<div class="col-md-12">
        								<input type="hidden" name="op" value="add">
        								<div class="form-group">
        									<label for="addModalInput1" class="col-sm-2 control-label">帳號<font color="red">*</font></label>
        									<div class="col-sm-10">
        										<input type="text" class="form-control" id="addModalInput1" name="jsuser_account" placeholder="" required minlength="2" maxlength="32">
        										<div class="help-block with-errors"></div>
        									</div>
        								</div>
        								<div class="form-group">
        									<label for="addModalInput2" class="col-sm-2 control-label">密碼<font color="red">*</font></label>
        									<div class="col-sm-10">
        										<input type="text" class="form-control" id="addModalInput2" name="jsuser_password" placeholder="" required minlength="2" maxlength="32">
        										<div class="help-block with-errors"></div>
        									</div>
        								</div>
        								<div class="form-group">
        									<label for="addModalInput3" class="col-sm-2 control-label">姓名<font color="red">*</font></label>
        									<div class="col-sm-10">
        										<input type="text" class="form-control" id="addModalInput3" name="jsuser_name" placeholder="" required minlength="2" maxlength="32">
        										<div class="help-block with-errors"></div>
        									</div>
        								</div>
        								<div class="form-group">
        									<label for="addModalInput5" class="col-sm-2 control-label">電子郵件<font color="red">*</font></label>
        									<div class="col-sm-10">
        										<input type="email" class="form-control" id="addModalInput5" name="jsuser_email" placeholder="" required minlength="4" maxlength="64">
        										<div class="help-block with-errors"></div>
        									</div>
        								</div>
        								<div class="form-group">
        										<label class="col-sm-2 control-label">權限設定<font color="red">*</font></label>
        										<div class="col-sm-10">
        										<?php foreach ($DEVICE_SYSTEM as $k=>$v) {?>
        									        <div class="form-check">
        									        	<label class="form-check-label">
        									            	<input class="form-check-input upd_dema_system<?php echo $k;?>" type="checkbox" name="dema_system[<?php echo $k;?>]" value="<?php echo $k;?>"> <?php echo $v;?>
        									        	</label>
        									        </div>
        									    <?php }?>
        										</div>
        									</div>
        								<div class="form-group">
        									<label for="addModalInput14" class="col-sm-2 control-label">帳號狀態<font color="red">*</font></label>
        									<div class="col-sm-10">
        										<select class="form-control" id="addModalInput14" name="jsuser_status">
        											<option value="0">關閉</option>
        											<option selected="selected" value="1">啟用</option>
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

        	<div id="upd-modal" class="modal upd-modal" tabindex="-1" role="dialog">
        		<div class="modal-dialog modal-lg">
        			<div class="modal-content">
        				<form autocomplete="off" method="post" action="./" id="upd_form" class="form-horizontal" role="form" data-toggle="validator">
        					<div class="modal-header">
        						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        						<h4 class="modal-title">使用者修改</h4>
        					</div>
        					<div class="modal-body">
        						<div class="row">
        							<div class="col-md-12">
        								<input type="hidden" name="op" value="upd">
        								<input type="hidden" name="jsuser_sn">
        								<div class="form-group">
        									<label for="updModalInput1" class="col-sm-2 control-label">帳號</label>
        									<div class="col-sm-10">
        										<div class="form-control-static jsuser_account"></div>
        									</div>
        								</div>
        								<div class="form-group">
        									<label for="updModalInput2" class="col-sm-2 control-label">密碼<font color="red">*</font></label>
        									<div class="col-sm-10">
        										<input type="text" class="form-control" id="updModalInput2" name="jsuser_password" placeholder="" required minlength="2" maxlength="32">
        										<div class="help-block with-errors"></div>
        									</div>
        								</div>
        								<div class="form-group">
        									<label for="updModalInput3" class="col-sm-2 control-label">姓名<font color="red">*</font></label>
        									<div class="col-sm-10">
        										<input type="text" class="form-control" id="updModalInput3" name="jsuser_name" placeholder="" required minlength="2" maxlength="32">
        										<div class="help-block with-errors"></div>
        									</div>
        								</div>
        								<div class="form-group">
        									<label for="updModalInput5" class="col-sm-2 control-label">電子郵件<font color="red">*</font></label>
        									<div class="col-sm-10">
        										<input type="email" class="form-control" id="updModalInput5" name="jsuser_email" placeholder="" required minlength="4" maxlength="64">
        										<div class="help-block with-errors"></div>
        									</div>
        								</div>
        								<div class="form-group">
        										<label class="col-sm-2 control-label">權限設定<font color="red">*</font></label>
        										<div class="col-sm-10">
        										<?php foreach ($DEVICE_SYSTEM as $k=>$v) {?>
        									        <div class="form-check">
        									        	<label class="form-check-label">
        									            	<input class="form-check-input upd_dema_system<?php echo $k;?>" type="checkbox" name="dema_system[<?php echo $k;?>]" value="<?php echo $k;?>"> <?php echo $v;?>
        									        	</label>
        									        </div>
        									    <?php }?>
        										</div>
        									</div>
        								<div class="form-group">
        									<label for="updModalInput14" class="col-sm-2 control-label">帳號狀態<font color="red">*</font></label>
        									<div class="col-sm-10">
        										<select class="form-control" id="updModalInput14" name="jsuser_status">
        											<option value="0">關閉</option>
        											<option selected="selected" value="1">啟用</option>
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

        	<!-- container -->
        	<div class="container-fluid">
        		<div class="row">
        			<div class="col-md-12">

        				<!-- nav toolbar -->
        				<div class="navbar-collapse collapse pull-right" style="margin-bottom: 10px;">
        					<ul class="nav nav-pills pull-right toolbar">
        						<li><button data-parent="#toolbar" data-toggle="modal" data-target=".add-modal" class="accordion-toggle btn btn-primary"><i class="glyphicon glyphicon-plus"></i> 新增</button></li>
        					</ul>
        				</div>

        				<!-- search -->
        				<div id="search" style="clear:both;">
        					<form autocomplete="off" method="get" action="./sys_user.php" id="search_form" class="form-inline alert alert-info" role="form">
        						<div class="row">
        							<div class="col-md-12">
        								<div class="form-group">
        									<label for="searchInput1">帳號</label>
        									<input type="text" class="form-control" id="searchInput1" name="jsuser_account" value="<?php echo $jsuser_account;?>" placeholder="">
        								</div>
        								<div class="form-group">
        									<label for="searchInput2">姓名</label>
        									<input type="text" class="form-control" id="searchInput2" name="jsuser_name" value="<?php echo $jsuser_name;?>" placeholder="">
        								</div>
        								<div class="form-group">
        									<label for="searchInput4">電子郵件</label>
        									<input type="text" class="form-control" id="searchInput4" name="jsuser_email" value="<?php echo $jsuser_email;?>" placeholder="">
        								</div>
        								<div class="form-group">
        									<label for="searchInput13">帳號狀態</label>
        									<select class="form-control" id="searchInput13" name="jsuser_status">
        										<option selected="selected" value="-1">全部</option>
        										<option value="0">關閉</option>
        										<option value="1">啟用</option>
        									</select>
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
        							<th>#</th>
        							<th>帳號</th>
        							<th>密碼</th>
        							<th>姓名</th>
        							<th>電子郵件</th>
        							<th width="55px">帳號<br>狀態</th>
        							<th>操作</th>
        						</tr>
        					</thead>
        					<tbody>
        						<?php
        						foreach ($user_list as $row) {
        							echo '<tr>';
        							echo '<td>'.$row['jsuser_sn'].'</td>';
        							echo '<td>'.$row['jsuser_account'].'</td>';
        							echo '<td>'.$row['jsuser_password'].'</td>';
        							echo '<td>'.$row['jsuser_name'].'</td>';
        							echo '<td>'.$row['jsuser_email'].'</td>';
        							echo '<td>'.$status_mapping[$row['jsuser_status']].'</td>';
        							echo '<td><button type="button" class="btn btn-primary btn-xs upd" data-jsuser_sn="'.$row['jsuser_sn'].'">修改</button>&nbsp;';
        							if($row['jsuser_account']!='admin')
        								echo '<button type="button" class="btn btn-danger btn-xs del" data-jsuser_sn="'.$row['jsuser_sn'].'">刪除</button>';
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
    </html>