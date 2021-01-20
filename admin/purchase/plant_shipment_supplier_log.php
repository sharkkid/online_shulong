<?php
include_once("./func_plant_shipment.php");
// printr(getCustomerSoldLog());
// exit;
$export_error = GetParam('export_error');
$status_mapping = array(0=>'<font color="red">關閉</font>', 1=>'<font color="blue">啟用</font>');
$DEVICE_SYSTEM = array(
		1=>"1.7",
		2=>"2.5",
		3=>"2.8",
		4=>"3.0",
		5=>"3.5",
		6=>"3.6",
		7=>"其他",
		8=>"瓶苗開瓶"
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
// printr(getShipListBySn(3));
// exit;
$op=GetParam('op');
if(!empty($op)) {
	$ret_code = 1;
	$ret_msg = '';
	$ret_data = array();
	switch ($op) {
		case 'get_ship':
			$onshda_sn=GetParam('onshda_sn');
			$ret_data = array();
			if(!empty($onshda_sn)){
				$ret_code = 1;
				$ret_data = getShipListBySn($onshda_sn);
			} else {
				$ret_code = 0;
			}

		break;

		case 'adjust':
			$onshda_sn=GetParam('onshda_sn');
			$onshda_real_price=GetParam('onshda_real_price');
			$ret_msg = $onshda_sn;
			if(!empty($onshda_sn)){
				$now = time();
				$conn = getDB();
				$sql = "UPDATE online_shipment_data SET onshda_real_price='{$onshda_real_price}' WHERE onshda_sn='{$onshda_sn}';";
				if($conn->query($sql)) {
					$ret_code = 1;
					$ret_msg = "修改成功！";
				} else {
					$ret_msg = "修改失敗！";
				}			
			} else {
				$ret_code = 0;
			}

		break;

		case 'export_all':	
			$ship_list = $_SESSION['ship_list'];
			// printr($User_forExcel);
			// exit;
		    ob_end_clean(); //  避免亂碼
		    header("Content-Type:text/html; charset=utf-8");
		    include_once(WT_PATH_ROOT.'/lib/PHPExcel_1.8.0/PHPExcel.php');
		    include_once(WT_PATH_ROOT.'/lib/PHPExcel_1.8.0/PHPExcel/Writer/Excel2007.php');

		    // init excel
		    $inputfilename = WT_PATH_ROOT.'/admin/purchase/supplier_log_temp.xls';

		    if(!file_exists($inputfilename)) exceptions("查無Excel巡檢表");
		    $originalexcel = PHPExcel_IOFactory::load($inputfilename);
		    // init data
		    $add_date = date('Y/m/d H:i:s');
		    $sheetname = 'data';
		    $sheet = $originalexcel->getSheetByName($sheetname);
		    $sheet->freezePane('A3');
		    $sheet->setTitle("供應商理紀錄_".date("Y-m-d"));

		    // 塞值
		    $n = 3;
		    $i = 0;
		    foreach ($ship_list as $key => $value) {
		    	foreach ($value as $key2 => $value2) {
			    	//客戶名稱
				    $sheet->setCellValue('A'.($n+$i), $key);
				    //交易日期
				    $sheet->setCellValue('B'.($n+$i), date('Y-m-d',$value2['onadd_add_date']));
				    //品號
				    $sheet->setCellValue('C'.($n+$i), $value2['onadd_part_no']);
				    //品名
				    $sheet->setCellValue('D'.($n+$i), $value2['onadd_part_name']);
				    //交易數量
				    $sheet->setCellValue('E'.($n+$i), $value2['onadd_quantity'].'株 ('.$DEVICE_SYSTEM[$value2['onadd_cur_size']].'寸)');
				    //育成率
				    $sheet->setCellValue('F'.($n+$i), $value2['livability']);
				    //需求
				    $sheet->setCellValue('G'.($n+$i), $value2['onadd_type']);

			        $i++;	
		        }     
		    }
		    // 產生檔案
		    $excelextend = substr($inputfilename, strpos($inputfilename, "."));
		    $filename="供應商理紀錄_".date("YmdHis");
		    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		    header("Content-Disposition: attachment;filename=".$filename.$excelextend);
		    header('Cache-Control: max-age=0');
		    // $objWriter = PHPExcel_IOFactory::createWriter($originalexcel, 'Excel2007');
		    // $objWriter->setIncludeCharts(TRUE);
		    // $objWriter->save('php://output');
		    if($excelextend == "xlsx")
		        $objWriter = PHPExcel_IOFactory::createWriter($originalexcel, 'Excel2007');
		    else
		        $objWriter = PHPExcel_IOFactory::createWriter($originalexcel, 'Excel5');
		    $objWriter->save('php://output');
			exit;
		break;

		case 'export_individual':	
			if($onshda_client = GetParam('customer')){
				$ship_list = $_SESSION['ship_list'];
				foreach ($ship_list as $key => $value) {
					if($key != $onshda_client)
						unset($ship_list[$key]);
				}
			    ob_end_clean(); //  避免亂碼
			    header("Content-Type:text/html; charset=utf-8");
			    include_once(WT_PATH_ROOT.'/lib/PHPExcel_1.8.0/PHPExcel.php');
			    include_once(WT_PATH_ROOT.'/lib/PHPExcel_1.8.0/PHPExcel/Writer/Excel2007.php');

			    // init excel
			    $inputfilename = WT_PATH_ROOT.'/admin/purchase/supplier_log_temp.xls';

			    if(!file_exists($inputfilename)) exceptions("查無Excel巡檢表");
			    $originalexcel = PHPExcel_IOFactory::load($inputfilename);
			    // init data
			    $add_date = date('Y/m/d H:i:s');
			    $sheetname = 'data';
			    $sheet = $originalexcel->getSheetByName($sheetname);
			    $sheet->freezePane('A3');
			    $sheet->setTitle("供應商理紀錄_{$onshda_client}_".date("Y-m-d"));

			    // 塞值
			    $n = 3;
			    $i = 0;
			    foreach ($ship_list as $key => $value) {
			    	foreach ($value as $key2 => $value2) {
				    	//客戶名稱
				        $sheet->setCellValue('A'.($n+$i), $key);
				        //交易日期
				        $sheet->setCellValue('B'.($n+$i), date('Y-m-d',$value2['onadd_add_date']));
				        //品號
				        $sheet->setCellValue('C'.($n+$i), $value2['onadd_part_no']);
				        //品名
				        $sheet->setCellValue('D'.($n+$i), $value2['onadd_part_name']);
				        //交易數量
				        $sheet->setCellValue('E'.($n+$i), $value2['onadd_quantity'].'株 ('.$DEVICE_SYSTEM[$value2['onadd_cur_size']].'寸)');
				        //育成率
				        $sheet->setCellValue('F'.($n+$i), $value2['livability']);
				        //需求
				        $sheet->setCellValue('G'.($n+$i), $value2['onadd_type']);

				        $i++;	
			        }     
			    }
			    // 產生檔案
			    $excelextend = substr($inputfilename, strpos($inputfilename, "."));
			    $filename="供應商理紀錄_{$onshda_client}_".date("YmdHis");
			    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			    header("Content-Disposition: attachment;filename=".$filename.$excelextend);
			    header('Cache-Control: max-age=0');
			    // $objWriter = PHPExcel_IOFactory::createWriter($originalexcel, 'Excel2007');
			    // $objWriter->setIncludeCharts(TRUE);
			    // $objWriter->save('php://output');
			    if($excelextend == "xlsx")
			        $objWriter = PHPExcel_IOFactory::createWriter($originalexcel, 'Excel2007');
			    else
			        $objWriter = PHPExcel_IOFactory::createWriter($originalexcel, 'Excel5');
			    $objWriter->save('php://output');
			}
			exit;
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

	if(($customer = GetParam('customer')) && $customer != '1') {
		if($customer == "未輸入")
			$search_where[] = "onadd_supplier like ''";
		else
			$search_where[] = "onadd_supplier like '%{$customer}%'";
		$search_query_string['customer'] = $customer;
	}	

	if(($start = GetParam('start',""))) {
		$start_c = str2time($start ." 00:00");
		$search_where[] = "onadd_add_date>={$start_c}";
		$search_query_string['start'] = $start;
	} else {
		$start_c = time() - 30 * 86400;
		$start = date('Y-m-d 00:00', $start_c);
		$search_where[] = "onadd_add_date>={$start_c}";
		$search_query_string['start'] = $start;
		$start = date('Y-m-d', $start_c);
	}

	if(($end = GetParam('end',""))) {
		$end_c = str2time($end ." 23:59");
		$search_where[] = "onadd_add_date<={$end_c}";
		$search_query_string['end'] = $end;
	} else {
		$end_c = time();
		$end = date("Y-m-d 23:59", $end_c);
		$search_where[] = "onadd_add_date<={$end_c}";
		$search_query_string['end'] = $end;
		$end = date("Y-m-d", $end_c);
	}
	
	$search_where = isset($search_where) ? implode(' and ', $search_where) : '';
	$search_query_string = isset($search_query_string) ? http_build_query($search_query_string) : '';

	// page
	// $pg_page = GetParam('pg_page', 1);
	// $pg_rows = 20;
	// $pg_total = GetParam('pg_total')=='' ? getUserQty($search_where) : GetParam('pg_total');
	// $pg_offset = $pg_rows * ($pg_page - 1);
	// $pg_pages = $pg_rows == 0 ? 0 : ( (int)(($pg_total + ($pg_rows - 1)) /$pg_rows) );
	// printr($search_where);
	$ship_list = getSupplierBoughtLog($search_where);
	$_SESSION['ship_list'] = $ship_list;
	$supplier_list = getAllSupplier();
	// printr($supplier_list);
	// exit;
	// printr($ship_list_2);
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
	<style>
		./*panel-heading {
		  border-radius:10px;
		}*/
	</style>
	<script type="text/javascript">
		$(document).ready(function() {
			<?php
					//	init search parm
			// print "$('#search [name=onadd_status] option[value={$onadd_status}]').prop('selected','selected');";
			if(!empty($customer))
				print "$('#search [name=customer] option[value={$customer}]').prop('selected','selected','selected','selected','selected','selected','selected');";
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

			$('button.adjust').on('click', function(){
				var onshda_sn = $(this).data('onshda_sn');
				var sn = $(this).data('sn');
				var cost_money = $(this).data('cost_money');
				$.ajax({
					url: './plant_shipment_detail.php',
					type: 'post',
					dataType: 'json',
					data: {op:"get_ship", onshda_sn:onshda_sn},
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
			                	$('#adjust_form input[name=sn]').val(sn);
			                	$('#adjust_form input[name=onadd_sn]').val(d.onadd_sn);
			                	$('#adjust_form input[name=onshda_sn]').val(d.onshda_sn);
			                	$('#adjust_form input[name=onadd_part_no]').val(d.onadd_part_no);
			                	$('#adjust_form input[name=onadd_part_name]').val(d.onadd_part_name);
			                	$('#adjust_form input[name=onshda_add_date]').val(d.onshda_add_date_formated);
			                	$('#adjust_form input[name=onshda_real_price]').val(d.onshda_real_price);
			                	$('#adjust_form input[name=total_cost_week]').val(d.TotalWeeks);
			                	$('#adjust_form input[name=total_cost_shipment]').val(d.CostBase);

			                	$('#adjust-modal').modal('show');
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

			$('#adjust_form').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					e.preventDefault();
					var param = $(this).serializeArray();

					$(this).parents('.modal').modal('hide');
					$(this)[0].reset();

					 	console.table(param);

					 	$.ajax({
					 		url: './plant_shipment.php',
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

				$('#datetimepicker1,#onshda_add_date,#datetimepicker2').datetimepicker({
		        	minView: 2,
		            language:  'zh-TW',
		            format: 'yyyy-mm-dd',
		            useCurrent: false
		        });

				$('button.export_excel').on('click', function(){
            	    window.open("plant_shipment_supplier_log.php?op=export_all");
	
            	});

            	$('button.export_individual').on('click', function(){
            		var customer = $(this).data('customer');
            		window.open("plant_shipment_supplier_log.php?op=export_individual&customer="+customer);
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
        				<h4>供應商管理紀錄</h4>
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
        					<form autocomplete="off" method="get" action="./plant_shipment_supplier_log.php" id="search_form" class="form-inline alert alert-info" role="form">
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
        								<div class="form-group">
        									<label for="searchInput4">供應商</label>
        									<select  class="form-control" name="customer" required>
												<?php 
													foreach ($supplier_list as $key => $value) {  
														echo '<option selected="selected" value="'.$value.'">'.$value.'</option>';
													}  
												?>
												<option selected="selected" value="1">全部</option>
											</select>
        								</div>
        								<div class="form-group">
        									<div class="input-group">
											    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
											    <input type="text" class="form-control" id="datetimepicker1" name="start" placeholder="" required="" minlength="1" maxlength="32" value="<?php echo $start;?>">
											</div>
        								</div>
        								<div class="form-group">
        									<label for="datetimepicker2">~</label>
        									<div class="input-group">
											    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
											    <input type="text" class="form-control" id="datetimepicker1" name="end" placeholder="" required="" minlength="1" maxlength="32" value="<?php echo $end;?>">
											</div>        										
        								</div>

        								<button type="submit" class="btn btn-info" op="search">搜尋</button>
        								<div class="form-group">
                                            <button class="btn btn-info export_excel">匯出出貨報表</button>
                                        </div>
        							</div>
        						</div>
        					</form>
        				</div>
        				<?php
        				// printr($ship_list);
        					$x = 0;
        					foreach ($ship_list as $key => $value) {        					
        				?>
						<div class="panel-group" id="accordion" >
							<div class="panel panel-default" style="border-radius:10px">
								<div class="panel-heading" style="padding-bottom: 15px;">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $key;?>">
										   <div class="col-sm-11" style="text-align:left;font-size:3.5rem;padding-left: 0px;"><?php echo $key;?></div>
										   <button class="btn btn-info export_individual" data-customer="<?php echo $key;?>">匯出</button>
										</a>
									</h4>
								</div>
								<div id="collapse<?php echo $key;?>" class="panel-collapse collapse <?php if($x==0){echo "in";}?>">
									<div class="panel-body">
										<table class="table table-hover table-condensed tablesorter">
				        					<thead>
				        						<tr style="font-size: 1.1em">
				        							<th style="text-align: center;">交易日期</th>
				        							<!-- <th style="text-align: center;">類型</th> -->
				        							<th style="text-align: center;">品號</th>
				        							<th style="text-align: center;">品名</th>
				        							<th style="text-align: center;">交易數量</th>
				        							<th style="text-align: center;">育成率</th>   
				        							<th style="text-align: center;">需求</th>   
				        						</tr>
				        					</thead>
				        					<tbody>
				        						<?php
				        							
				        							foreach ($value as $key2 => $value2) {
				        								// if($value2['onadd_plant_st'] == 1)
				        								// 	$onadd_plant_st = "苗株";
				        								// else
				        								// 	$onadd_plant_st = "瓶苗";
				        								echo '<tr>';
				        								echo '<td style="vertical-align: middle;text-align: center;">'.Date('Y-m-d',$value2['onadd_add_date']).'</td>'; 
				        								// echo '<td style="vertical-align: middle;text-align: center;">'.$onadd_plant_st.'</td>'; 
				        								echo '<td style="vertical-align: middle;text-align: center;">'.$value2['onadd_part_no'].'</td>'; 
				        								echo '<td style="vertical-align: middle;text-align: center;">'.$value2['onadd_part_name'].'</td>'; 
				        								echo '<td style="vertical-align: middle;text-align: center;">'.$value2['onadd_quantity'].'株 ('.$DEVICE_SYSTEM[$value2['onadd_cur_size']].'寸)</td>'; 
				        								echo '<td style="vertical-align: middle;text-align: center;">'.getLivability($value2['onadd_sn']).'</td>'; 
				        								echo '<td style="vertical-align: middle;text-align: center;">'.$value2['onadd_type'].'</td>'; 
				        								echo '</tr>';
				        							}
				        							
				        						?>
				        					</tbody>
				        				</table>
									</div>
								</div>
							</div>
						</div>
						<?php
							$x++;
        					}
        				?>
        				<!-- content -->
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