<?php
include_once("./func_plant_business.php");
$status_mapping = array(0=>'<font color="red">關閉</font>', 1=>'<font color="blue">啟用</font>');
$DEVICE_SYSTEM = array(
	1=>"1月",
	2=>"2月",
	3=>"3月",
	4=>"4月",
	5=>"5月",
	6=>"6月",
	7=>"7月",
	8=>"8月",
	9=>"9月",
	10=>"10月",
	11=>"11月",
	12=>"12月",
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
		$oncost_name=GetParam('oncost_name');//成本名稱
		$oncost_note=GetParam('oncost_note');//成本說明

		if(empty($oncost_name)||empty($oncost_note)){
			$ret_msg = "*為必填！";
		} else { 
			$conn = getDB();
			$sql = "INSERT INTO onliine_cost_sum_table (oncost_status, oncost_name, oncost_note) " .
			"VALUES ('1', '{$oncost_name}', '{$oncost_note}');";
			if($conn->query($sql)) {
				$ret_msg = "新增成功！";
			} else {
				$ret_msg = "新增失敗！";
			}
			$conn->close();
		}
		break;

		case 'get':
		$oncoda_sn=GetParam('oncoda_sn');
		$ret_data = array();
		if(!empty($oncoda_sn)){
			$ret_code = 1;
			$ret_data = getUserBySn($oncoda_sn);
		} else {
			$ret_code = 0;
		}

		break;

		case 'add_detail':
		$oncost_sn=GetParam('oncost_sn');//成本種類
		$oncoda_name=GetParam('oncoda_name');//成本名稱
		$oncoda_cost_size =GetParam('oncoda_cost_size');//盆栽大小		
		$oncoda_num=GetParam('oncoda_num');//成本說明
		$oncoda_cost=GetParam('oncoda_cost');//成本金額

		$conn = getDB();
		$sql = "INSERT INTO `online_cost_sum_data`(`oncost_sn`,`oncoda_status`, `oncoda_name`, `oncoda_cost_size`, `oncoda_cost`, `oncoda_num`) VALUES ('{$oncost_sn}','1','{$oncoda_name}','{$oncoda_cost_size}','{$oncoda_cost}','{$oncoda_num}')";

		if($conn->query($sql)) {
			$ret_msg = "新增成功！";
		} else {
			$ret_msg = "新增失敗！".$sql;
		}
		$conn->close();
		break;

		case 'upd_detail':
		$oncoda_sn=GetParam('oncoda_sn');//成本種類
		$oncoda_name=GetParam('oncoda_name');//成本名稱
		$oncoda_num=GetParam('oncoda_num');//成本說明
		$oncoda_cost=GetParam('oncoda_cost');//成本金額

		$conn = getDB();
		$sql = "UPDATE `online_cost_sum_data` SET `oncoda_name`= '{$oncoda_name}',`oncoda_cost`= '{$oncoda_cost}',`oncoda_num`= '{$oncoda_num}' WHERE oncoda_sn = {$oncoda_sn}";

		if($conn->query($sql)) {
			$ret_msg = "更新成功！";
		} else {
			$ret_msg = "更新失敗！";
		}
		$conn->close();
		break;

		//移除成本細項---------------------------------------------
		case 'del_table':
		$oncost_sn=GetParam('oncost_sn');//成本細項編號

		if(empty($oncost_sn)){
			$ret_msg = "刪除失敗！";
		}else{
			$now = time();
			$conn = getDB();
			$sql = "DELETE FROM onliine_cost_sum_table WHERE oncost_sn='{$oncost_sn}'";
			if($conn->query($sql)) {
				$ret_msg = "刪除完成！";
			} else {
				$ret_msg = "刪除失敗！";
			}
			$conn->close();
		}
		break;

		//汰除---------------------------------------------

		//出貨---------------------------------------------
		case 'upd2':
		$oncoda_sn=GetParam('oncoda_sn');
		$list = getUserBySn($oncoda_sn);
		$oncoda_grass = $list['oncoda_grass'];
		$oncoda_labor = $list['oncoda_labor'];
		$onadd_quantity=GetParam('onadd_quantity');//下種數量
		$oncoda_soft = GetParam('supplier');//編輯人員
		$onadd_plant_year=GetParam('onadd_plant_year');//汰除數量
		$onshda_client=GetParam('onshda_client');//汰除數量
		$onadd_quantity_shi123 = ($onadd_quantity - $onadd_plant_year);
		if($onadd_quantity_shi123<=0) {
			$oncoda_status = -1;
		} else {
			$oncoda_status = 1;
		}

		if(empty($onadd_plant_year)){
			$ret_msg = "*為必填！";
		} else {
			$now = time();
			$conn = getDB();
			$sql = "UPDATE online_cost_sum_data SET onadd_quantity='{$onadd_quantity_shi123}', oncoda_status='{$oncoda_status}' WHERE oncoda_sn='{$oncoda_sn}'";
			if($conn->query($sql)) {
				$ret_msg = "修改完成！";
			} else {
				$ret_msg = "修改失敗！";
			}
			$conn->close();
		}

		if(empty($onadd_plant_year)){
			$ret_msg = "*為必填！";
		} else {
			$now = time();
			$conn = getDB();
			$sql = "INSERT INTO online_shipment_data (onshda_add_date, onshda_mod_date, onshda_client, onshda_quantity, oncoda_sn, oncoda_grass, oncoda_labor) " .
			"VALUES ('{$now}', '{$now}', '{$onshda_client}', '{$onadd_plant_year}', '{$oncoda_sn}', '{$oncoda_grass}', '{$oncoda_labor}');";
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
		$oncoda_sn=GetParam('oncoda_sn');

		if(empty($oncoda_sn)){
			$ret_msg = "刪除失敗！";
		}else{
			$now = time();
			$conn = getDB();
			$sql = "DELETE FROM online_cost_sum_data WHERE oncoda_sn='{$oncoda_sn}'";
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
	$cost_table = get_SUM_CostTable();
	// printr($cost_table);
	// exit;
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
			// print "$('#search [name=oncoda_status] option[value={$oncoda_status}]').prop('selected','selected');";
			// print "$('#search [name=onadd_growing] option[value={$onadd_growing}]').prop('selected','selected','selected','selected','selected','selected','selected');";
			?>

			$('button.upd').on('click', function(){
				$('#upd-modal').modal();
				$('#upd_form')[0].reset();

				$.ajax({
					url: './plant_business_summary.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get", oncoda_sn:$(this).data('oncoda_sn')},
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
			                	$('#upd_form input[name=oncoda_sn]').val(d.oncoda_sn);
			                	$('#upd_form input[name=oncoda_grass]').val(d.oncoda_grass);
			                	$('#upd_form input[name=oncoda_labor]').val(d.oncoda_labor);
			                	$('#upd_form input[name=oncoda_water]').val(d.oncoda_water);
			                	$('#upd_form input[name=oncoda_electricity]').val(d.oncoda_electricity);
			                	$('#upd_form input[name=onadd_height]').val(d.onadd_height);
			                	$('#upd_form input[name=onadd_pot_size]').val(d.onadd_pot_size);
			                	$('#upd_form input[name=onadd_supplier]').val(d.onadd_supplier);
			                	// $('#upd_form input[name=onadd_planting_date]').val(d.onadd_planting_date);
			                	$('#upd_form input[name=onadd_quantity]').val(d.onadd_quantity);
			                	// $('#upd_form input[name=onadd_growing]').val(d.onadd_growing);		                	
			                	$('#upd_form [name=oncoda_status] option[value='+d.oncoda_status+']').prop('selected','selected');
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
				$('#upd_form1 input[name=oncost_sn]').val($(this).data('oncost_sn'));

			});
			//汰除-----------------------------------------------------------

			//出貨-----------------------------------------------------------
			$('button.upd2').on('click', function(){
				$('#upd-modal2').modal();
				$('#upd_form2')[0].reset();

				$.ajax({
					url: './plant_business_summary.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get", oncoda_sn:$(this).data('oncoda_sn')},
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
			                	$('#upd_form2 input[name=oncoda_sn]').val(d.oncoda_sn);
			                	$('#upd_form2 input[name=oncoda_grass]').val(d.oncoda_grass);
			                	$('#upd_for2 input[name=oncoda_soft]').val(d.oncoda_soft);
			                	$('#upd_form2 input[name=oncoda_labor]').val(d.oncoda_labor);
			                	$('#upd_form2 input[name=oncoda_water]').val(d.oncoda_water);
			                	$('#upd_form2 input[name=oncoda_electricity]').val(d.oncoda_electricity);
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

			$('button.upd_detail').on('click', function(){
				$('#upd-detail-modal').modal();

				oncoda_sn = $(this).data('oncoda_sn');

				$.ajax({
					url: './plant_business_summary.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get", oncoda_sn:$(this).data('oncoda_sn')},
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
			                	$('#upd_detail_form1 input[name=oncoda_sn]').val(d.oncoda_sn);
			                	$('#upd_detail_form1 input[name=oncoda_name]').val(d.oncoda_name);
			                	$('#upd_detail_form1 input[name=oncoda_cost_size ]').val(d.oncoda_cost_size );
			                	$('#upd_detail_form1 input[name=oncoda_num]').val(d.oncoda_num);
			                	$('#upd_detail_form1 input[name=oncoda_cost]').val(d.oncoda_cost);

			                }
			            },
			            error: function (xhr, ajaxOptions, thrownError) {
		                	// console.log('ajax error');
		                    // console.log(xhr);
		                }
		            });

			});

			$('button.del_table').on('click', function(){
				oncost_sn = $(this).data('oncost_sn')
				bootbox.confirm("確認刪除？", function(result) {
					if(result) {
						$.ajax({
							url: './plant_business_summary.php',
							type: 'post',
							dataType: 'json',
							data: {op:"del_table", oncost_sn:oncost_sn},
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

			$('button.del').on('click', function(){
				oncoda_sn = $(this).data('oncoda_sn')
				bootbox.confirm("確認刪除？", function(result) {
					if(result) {
						$.ajax({
							url: './plant_business_summary.php',
							type: 'post',
							dataType: 'json',
							data: {op:"del", oncoda_sn:oncoda_sn},
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

			$('#add_form, #upd_form, #upd_form1, #upd_form2, #upd_detail_form1').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					e.preventDefault();
					var param = $(this).serializeArray();

					$(this).parents('.modal').modal('hide');
					$(this)[0].reset();

					 	console.table(param);

					 	$.ajax({
					 		url: './plant_business_summary.php',
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
					<h4 style="font-size: 25px">成本總表</h4>
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
							<h4 class="modal-title">新成本種類建立作業</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="add">
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">年分<font color="red">*</font></label>
										<div class="col-sm-10">
											<!-- Single Year -->						                    					                    
											<select  class="form-control"  id="Select_Year" name="oncost_name" required>
												<?php 
													foreach ($DEVICE_SYSTEM as $key => $value) {
														echo "<option>".$value."</option>";
													}
												?>												
											</select>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<!-- <div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">說明<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="oncost_note" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div> -->
									
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<!-- <button type="reset" class="btn btn-default">清空</button> -->
							<button type="submit" class="btn btn-primary">新增</button>
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
							<h4 class="modal-title">新增成本細項</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="add_detail">
									<input type="hidden" name="oncost_sn">
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">月份<font color="red">*</font></label>
										<div class="col-sm-10">
											<select  class="form-control" name="oncoda_cost_size" required>
												<option value="12">12月</option>
												<option value="11">11月</option>
												<option value="10">10月</option>
												<option value="9">9月</option>
												<option value="8">8月</option>
												<option value="7">7月</option>
												<option value="6">6月</option>
												<option value="5">5月</option>
												<option value="4">4月</option>
												<option value="3">3月</option>
												<option value="2">2月</option>
												<option selected="selected" value="1">1月</option>
											</select>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">項目<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="oncoda_name" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<!-- <div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">單位<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="oncoda_unit" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>	 -->
									<!-- <div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="oncoda_num" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>	 -->
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">成本金額<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="oncoda_cost" placeholder="" required minlength="1" maxlength="32">
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
		<!--汰除----------------------------------------------------------->
		<div id="upd-detail-modal" class="modal upd-modal1" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd_detail_form1" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">修改成本細項</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd_detail">
									<input type="hidden" name="oncoda_sn">
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">項目<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="oncoda_name" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<!-- <div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">單位<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="oncoda_unit" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>	 -->
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">數量<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="oncoda_num" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>	
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">成本金額<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="oncoda_cost" placeholder="" required minlength="1" maxlength="32">
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
		<!--汰除----------------------------------------------------------->
		<div id="upd-modal2" class="modal upd-modal2" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd_form2" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">成本修改</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="add_detail">
									<input type="hidden" name="oncoda_sn">
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">水費<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="oncoda_water" placeholder="" required minlength="1" maxlength="32">
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="form-group">
										<label for="addModalInput1" class="col-sm-2 control-label">電費<font color="red">*</font></label>
										<div class="col-sm-10">
											<input type="text" class="form-control" id="addModalInput1" name="oncoda_electricity" placeholder="" required minlength="1" maxlength="32">
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
		<!--汰除----------------------------------------------------------->

		<!-- container -->
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="navbar-collapse collapse pull-right" style="margin-bottom: 10px;">
						<ul class="nav nav-pills pull-right toolbar">
							<li><button data-parent="#toolbar" data-toggle="modal" data-target=".add-modal" class="accordion-toggle btn btn-primary"><i class="glyphicon glyphicon-plus"></i> 成本種類建立</button></li>
						</ul>
					</div>
					<!-- content -->
					<?php 
						foreach ($cost_table as $key => $value) {
					?>
						<div id="search" style="clear:both;" class="form-inline alert alert-info">
							<div class="row">
								<div class="col-md-12">
									<div class="h3 page-header text-center offset-bottom" style="margin-left: 0px;margin-right: 0px;background:#bce7f1 ">
										<font color="#000000"><?php echo $value['oncost_name']?></font>
									</div>
								</div>	
							</div>
							<div class="col-md-12" style="text-align: right">
								<button type="button" class="btn btn-info btn-xs upd1" data-oncost_sn="<?php echo $value['oncost_sn'];?>">新增細項</button>
								<button type="button" class="btn btn-danger btn-xs del_table" data-oncost_sn="<?php echo $value['oncost_sn'];?>">刪除此區</button>
							</div>
							<!-- content -->
							<table class="table table-striped table-hover table-condensed tablesorter" style="font-size: 1.5rem">
								<thead>
									<th style="text-align: center;color:#52565e; border-bottom:1px #b0b0b0 solid;"></th>
								</thead>
								<tbody >
									<?PHP foreach ($DEVICE_SYSTEM as $key => $val) { 
										$cost_detail = get_SUM_CostDetail($value['oncost_sn'],$key);
										if (!empty($cost_detail)){
											if ($key>1) {?>
												<tr style="font-size: 2rem;border-top:1rem solid #d1f1fa;">
													<td  style="background-color: #fefcf6;text-align: center;color:#52565e; vertical-align: middle;text-align: center;" colspan="4"><?PHP echo $val;?>
													</td>
												</tr>	
										<?PHP }else{ ?>
												<tr style="font-size: 2rem;">
													<td  style="background-color: #fefcf6;text-align: center;color:#52565e; vertical-align: middle;text-align: center;" colspan="4"><?PHP echo $val;?>
													</td>
												</tr>	
										<?PHP } ?>
										<tr style="font-size: 1.3em">	
											<th style="text-align: center;color:#52565e; border-bottom:1px #b0b0b0 solid;">項目</th>
											<th style="text-align: center;color:#52565e; border-bottom:1px #b0b0b0 solid;">成本金額</th>
											<th style="text-align: center;color:#52565e; border-bottom:1px #b0b0b0 solid;">操作</th>
										</tr>
									<?php 
									// printr($cost_detail);
											foreach ($cost_detail as $key2 => $value2) {									
									?>									
											<tr style="font-size: 1.1em;">
												<td  style="text-align: center;color:#52565e; vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;"><?php echo $value2['oncoda_name']; ?></td>
												<td  style="text-align: center;color:#52565e; vertical-align: middle;border-right:0.1rem #BEBEBE dashed;text-align: center;"><?php echo number_format($value2['oncoda_cost'],1)." NT"; ?></td>
												<td  style="text-align: center;color:#52565e; vertical-align: middle;text-align: center;">
													<button type="button" class="btn btn-primary btn-xs upd_detail" data-oncoda_sn="<?php echo $value2['oncoda_sn'];?>" data-oncost_sn="<?php echo $value['oncost_sn'];?>" style="background-color:#A46B62;border:#A46B62">修改</button>
													<button type="button" class="btn btn-danger btn-xs del" data-oncoda_sn="<?php echo $value2['oncoda_sn'];?>" data-oncost_sn="<?php echo $value['oncost_sn'];?>" style="background-color:#E94653;">刪除</button>
												</td>
											</tr>									
									<?php 
											}
										}
									}
									?>
								</tbody>
							</table>
						</div> 
					<?php 
						}
					?>
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
        <script>
		var Select_Year = document.getElementById("Select_Year")
		var today = new Date();
		var thisYear = today.getFullYear(); //今年
		var Year_Start = thisYear-6; //起始年
		var Year_End = thisYear+6; //結束年
		var Year_Len = Year_End - Year_Start + 1;
		Select_Year.options.length = Year_Len;
		for (var i = 0; i < Year_Len; i++) {
			Select_Year.options[i].text = Select_Year.options[i].value = Year_Start + i + " 年"; 
			if (thisYear == Year_Start + i) {
				Select_Year.options[i].selected = true;
			}
		}
		</script>
    </body>
    </html>