<?php
include_once("./func.php");
$op=GetParam('op');
if(!empty($op)) {
	$ret_code = 1;
	$ret_msg = '';
	$ret_data = array();
	switch ($op) {
		case 'passwd':
			$new_password=GetParam('new_password');
			$confirm_password=GetParam('confirm_password');
			$jsuser_sn = $_SESSION['user']['jsuser_sn'];

			if(empty($new_password)||empty($confirm_password)||empty($jsuser_sn)) {
				$ret_msg = "*為必填！";
			} else if($new_password!=$confirm_password) {
				$ret_msg = "新密碼與確認新密碼不一樣！";
			} else {
				$user = getUserBySn($jsuser_sn);
				$now = time();
				$conn = getDB();
				$sql = "UPDATE js_user SET jsuser_password='{$new_password}', jsuser_mod_date='{$now}' WHERE jsuser_sn='{$jsuser_sn}'";

				if($conn->query($sql)) {
					$ret_msg = "修改完成！";
				} else {
					$ret_msg = "修改失敗！";
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
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include('./../htmlModule/head.php');?>
<script src="./../../lib/jquery.twbsPagination.min.js"></script>
<script type="text/javascript">
		$(document).ready(function() {
			bootbox.setDefaults({
				locale: "zh_TW",
			});

			$('#f').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					e.preventDefault();
					var param = $(this).serializeArray();
					
				  	$(this).parents('.modal').modal('hide');
				  	$(this)[0].reset();
				  	
				 	// console.table(param);
					
					$.ajax({
					    url: './sys_passwd.php',
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
		                    bootbox.confirm(ret.msg, function(result) {});
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

	<!-- container -->
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">

				<!-- title -->
				<h3 class="text-center wt-block-title">變更密碼</h3>

				<!-- content -->
				<div class="row">
					<div class="col-sm-offset-2 col-sm-8">
						<form id="f" class="form-horizontal" method="post" autocomplete="off"
							novalidate="novalidate">
							<fieldset class="well">
				        		<input type="hidden" name="op" value="passwd">
								<div class="form-group">
									<label class="col-sm-3 control-label">新密碼<font color="red">*</font>
									</label>
									<div class="col-sm-9">
										<input type="password" name="new_password" id="new_password"
											class="form-control required" size="35" maxlength="64"
											minlength="4" tabindex="2" aria-required="true">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label">確認新密碼<font color="red">*</font>
									</label>
									<div class="col-sm-9">
										<input type="password" name="confirm_password"
											id="confirm_password" class="form-control required"
											equalto="input[name=new_password]" size="35" maxlength="64"
											minlength="4" tabindex="3" aria-required="true">
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label hidden-xs"></label>
									<div class="col-sm-9">
										<input class="btn" type="reset" value="重設">&nbsp;<input
											class="btn btn-primary" type="submit" class="passwd" value="送出">
									</div>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
