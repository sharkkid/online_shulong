<?php
include_once("./func_KPI.php");
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
			$onpp_year = explode("/",GetParam('add_date'))[0];
			$onpp_month = explode("/",GetParam('add_date'))[1];
			$onpp_date = strtotime($onpp_year."-".$onpp_month);
			$onpp_plant_staff = GetParam('onpp_plant_staff');
			$plant_size = array();
			$target_number = array();
			foreach ($DEVICE_SYSTEM as $key => $value) {
				$json[$key]['plant_size'] = $value;
				$json[$key]['target_number'] = GetParam('T_'.$key);
			}
			$onpp_target_number = urldecode(json_encode($json,JSON_UNESCAPED_UNICODE));
			
			$conn = getDB();
			if ($onpp_plant_staff < 0) {
				$ret_msg = "新增失敗，請選擇種植人員！";
			}else{							
				$sql = "INSERT INTO online_planting_performance (onpp_date,onpp_year, onpp_month, onpp_plant_staff, onpp_target_number) VALUES ({$onpp_date},{$onpp_year}, {$onpp_month}, '{$onpp_plant_staff}', '{$onpp_target_number}');";
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

		//修改---------------------------------------------
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
				$sql = "UPDATE online_cost_data SET onadd_quantity='{$onadd_quantity_shi123}', oncoda_status='{$oncoda_status}' WHERE oncoda_sn='{$oncoda_sn}'";
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
		//刪除---------------------------------------------

		case 'del':
			$oncoda_sn=GetParam('oncoda_sn');

			if(empty($oncoda_sn)){
				$ret_msg = "刪除失敗！";
			}else{
				$now = time();
				$conn = getDB();
				$sql = "DELETE FROM online_cost_data WHERE oncoda_sn='{$oncoda_sn}'";
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
	$Staffs = get_Staffs();
	if($onpp_plant_staff = GetParam('onpp_plant_staff')) {
		if ($onpp_plant_staff != -1) {
			$search_where[] = "onpp_plant_staff = '{$onpp_plant_staff}'";
			$search_query_string['onpp_plant_staff'] = $onpp_plant_staff;
		}
		// else{
		// 	$onpp_plant_staff = "";
		// 	foreach ($Staffs as $key => $value) {
		// 		$onpp_plant_staff .= $value['jsuser_sn'].',';
		// 	}
		// 	$onpp_plant_staff = substr($onpp_plant_staff,0,-1);
		// 	$search_where[] = "onpp_plant_staff IN ({$onpp_plant_staff})";
		// 	$search_query_string['onpp_plant_staff'] = $onpp_plant_staff;
		// }
	}
	if($start = GetParam('start')) {
		$start_year = explode("/",$start)[0];
		$start_month = explode("/",$start)[1];
		$search_where[] = "FROM_UNIXTIME(a.onadd_planting_date, '%Y/%m') BETWEEN '{$start_year}/{$start_month}'";
		$search_query_string['start'] = $start;
	}
	if($end = GetParam('end')) {	
		$end_year = explode("/",$end)[0];
		$end_month = explode("/",$end)[1];
		if ($end_month == 12) {
			$end_year = $end_year+1;
			$end_month = 1;
		}else{
			$end_month = $end_month+1;
		}		
		$search_where[] = "'{$end_year}/{$end_month}'";
		$search_query_string['end'] = $end;
	}

	if($onpp_up_to_standard = GetParam('onpp_up_to_standard')) {
		if ($onpp_up_to_standard != -1) {
			$search_where[] = "onpp_up_to_standard = '{$onpp_up_to_standard}'";
			$search_query_string['onpp_up_to_standard'] = $onpp_up_to_standard;
		}		
	}
	$search_where = isset($search_where) ? implode(' and ', $search_where) : '';
	$search_query_string = isset($search_query_string) ? http_build_query($search_query_string) : '';
	$ppData_table = get_ppData($search_where,$start_year,$end_year);
	// printr($ppData_table);
	// exit;

	$export_error = GetParam('export_error');
	switch ($export_error) {
		case '1':	
			$data_year = GetParam('data_year');
			$data_month = GetParam('data_month');
			$expect_data = $ppData_table[GetParam('data_year')][GetParam('data_month')];
			$expect_data = array_reverse($expect_data);

	        ob_end_clean(); //  避免亂碼
	        header("Content-Type:text/html; charset=utf-8");
	        include_once(WT_PATH_ROOT.'/lib/PHPExcel_1.8.0/PHPExcel.php');
	        include_once(WT_PATH_ROOT.'/lib/PHPExcel_1.8.0/PHPExcel/Writer/Excel2007.php');

	        // init excel
	        $inputfilename = WT_PATH_ROOT.'/admin/KPI/kpi_performance.xls';
	        if(!file_exists($inputfilename)) exceptions("查無Excel巡檢表");
	        $originalexcel = PHPExcel_IOFactory::load($inputfilename);
	        // init data

	        $add_date = date('Y/m/d H:i:s');
	        $sheetname = 'data';
	        $sheet = $originalexcel->getSheetByName($sheetname);
	        $sheet->freezePane('A2');
	        $sheet->setTitle("種植績效報表_".$data_year."-".$data_month."月");
	    	// 塞值
	        $n = 3;
	        $staff_position = 3;
	        $new_key = 0;
	        $onpp_actual_number = array();
	        for($i=0;$i<count($expect_data);$i++){
	        	$onpp_target_number = json_decode($expect_data[$i]['onpp_target_number'], true);
	        	foreach ($onpp_target_number as $DS_key => $DS_value) {
					if (empty($DS_value['target_number'])) {
						$onpp_target_number[$DS_key]['target_number'] = 0;
					}
				}
				$onpp_month_S =  $data_month;
				$onpp_month_E = $onpp_month_S+1;
				$onpp_year_S = $onpp_year_E = $data_year;				
				$conn = getDB();
				if ($onpp_month_S == 12) {
					$onpp_year_E = $onpp_year_E+1;
					$onpp_month_E = 1;
				}
				$sql_quantity = "SELECT onadd_cur_size,onadd_quantity FROM `onliine_add_data` WHERE onadd_plant_staff = '".$expect_data[$i]['onpp_plant_staff']."' AND `onadd_planting_date` BETWEEN UNIX_TIMESTAMP('$onpp_year_S/$onpp_month_S/01 00:00:00')  AND (UNIX_TIMESTAMP('$onpp_year_E/$onpp_month_E/01 00:00:00')-1);";
				$qresult = $conn->query($sql_quantity);
				$actual_number = array();
				if ($qresult->num_rows > 0) {
					while($row = $qresult->fetch_assoc()) {
						$actual_number[$row['onadd_cur_size']]['plant_size'] = $DEVICE_SYSTEM[$row['onadd_cur_size']];
						$actual_number[$row['onadd_cur_size']]['target_number'] = $row['onadd_quantity'];							
					}
					$qresult->free();
				}
				$conn->close();	

	        	for ($ji=1; $ji <= count($DEVICE_SYSTEM); $ji++) {        			
					$new_actual_number = array();
					foreach ($onpp_target_number as $DS_key => $DS_value) {						
						if (!empty($actual_number[$DS_key])) {
							$new_actual_number[$DS_key] = $actual_number[$DS_key];									
						}else{
							$new_actual_number[$DS_key]['plant_size'] = $DS_value['plant_size'];
							$new_actual_number[$DS_key]['target_number'] = 0;
						}											
					}       			
        		}
	            
        		// 合併儲存格
        		$sheet->mergeCells('A'.$staff_position.':A'.($staff_position+count($onpp_target_number)-1)); 
				//人員
	            $sheet->setCellValue('A'.$staff_position, get_Staff_name($expect_data[$i]['onpp_plant_staff']));
	            $staff_position = $staff_position+count($onpp_target_number);
	            
	            foreach ($onpp_target_number as $onpp_t_key => $onpp_t_value) {
	            	$gap = $onpp_t_value['target_number']-$new_actual_number[$onpp_t_key]['target_number'];          	
					if ($gap <= 0) {
						if ($onpp_t_value['target_number'] != 0 || $new_actual_number[$onpp_t_key]['target_number'] !=0) {
							$status = '達標';
						}else{
							$status = "-";
						}
					}else{
						$status = '未達標';	 
					}
	            	//目標值
	            	$sheet->setCellValue('B'.($n+$new_key), "（".$onpp_t_value['plant_size']."）".$onpp_t_value['target_number']);
	            	//實際值	
	            	$sheet->setCellValue('C'.($n+$new_key), "（".$new_actual_number[$onpp_t_key]['plant_size']."）".$new_actual_number[$onpp_t_key]['target_number']);
	            	//未達數	
	            	$sheet->setCellValue('D'.($n+$new_key), "（".$onpp_t_value['plant_size']."）".$gap);
	            	//達成率	
	           		$sheet->setCellValue('E'.($n+$new_key), @round(100*(($new_actual_number[$onpp_t_key]['target_number']/$onpp_t_value['target_number'])),2)."％");
	           		//狀態
	            	$sheet->setCellValue('F'.($n+$new_key), $status);
	            	$new_key+=1;
	            }	            
	        }
	    	// 產生檔案
	        $excelextend = substr($inputfilename, strpos($inputfilename, "."));
	        $filename="種植績效報表_".$data_year."-".$data_month."月";
	        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	        header("Content-Disposition: attachment;filename=".$filename.$excelextend);
	        header('Cache-Control: max-age=0');
	        // $objWriter = PHPExcel_IOFactory::createWriter($originalexcel, 'Excel2007');
	        // $objWriter->setIncludeCharts(TRUE);
	        // $objWriter->save('php://output');
	        if($excelextend == "xlsx"){
	            $objWriter = PHPExcel_IOFactory::createWriter($originalexcel, 'Excel2007');
	        }else{
	            $objWriter = PHPExcel_IOFactory::createWriter($originalexcel, 'Excel5');
	        }
	        $objWriter->save('php://output');

	        exit;
		    
			break;
		case '2':			
			$expect_data = $ppData_table;
	        ob_end_clean(); //  避免亂碼
	        header("Content-Type:text/html; charset=utf-8");
	        include_once(WT_PATH_ROOT.'/lib/PHPExcel_1.8.0/PHPExcel.php');
	        include_once(WT_PATH_ROOT.'/lib/PHPExcel_1.8.0/PHPExcel/Writer/Excel2007.php');

	        // init excel
	        $inputfilename = WT_PATH_ROOT.'/admin/KPI/kpi_performance_year.xls';
	        if(!file_exists($inputfilename)) exceptions("查無Excel巡檢表");
	        $originalexcel = PHPExcel_IOFactory::load($inputfilename);
	        // init data

	        $add_date = date('Y/m/d H:i:s');
	        $sheetname = 'data';
	        $sheet = $originalexcel->getSheetByName($sheetname);
	        $sheet->freezePane('A2');
	        $sheet->setTitle("種植績效報表");
	    	// 塞值
	        $n = 3;
	        $staff_position = 3;
	        $new_key = 0;
	        $onpp_actual_number = array();
	        $exl_year = "";
			foreach ($expect_data as $ALL_key => $ALL_value) {
				$exl_year .= $ALL_key."-";
				foreach ($ALL_value as $month_key => $month_value) {
					if (!empty($month_value)) 
					foreach ($month_value as $each_key => $each_value) {
						$onpp_target_number = json_decode($each_value['onpp_target_number'], true);
			        	foreach ($onpp_target_number as $DS_key => $DS_value) {
							if (empty($DS_value['target_number'])) {
								$onpp_target_number[$DS_key]['target_number'] = 0;
							}
						}
						$actual_number = array();
		        		$actual_number[$each_value['onadd_cur_size']]['plant_size'] = $DEVICE_SYSTEM[$each_value['onadd_cur_size']];
		        		$actual_number[$each_value['onadd_cur_size']]['target_number']  = $each_value['onadd_quantity'];
						
			        	for ($ki=1; $ki <= count($DEVICE_SYSTEM); $ki++) {        			
							$new_actual_number = array();
							foreach ($onpp_target_number as $DS_key => $DS_value) {	
								if (!empty($actual_number[$DS_key])) {
									$new_actual_number[$DS_key] = $actual_number[$DS_key];									
								}else{
									$new_actual_number[$DS_key]['plant_size'] = $DS_value['plant_size'];
									$new_actual_number[$DS_key]['target_number'] = 0;
								}											
							}       			
		        		}
		        		// 合併儲存格
		        		$sheet->mergeCells('A'.$staff_position.':A'.($staff_position+count($onpp_target_number)-1));
		        		$sheet->mergeCells('B'.$staff_position.':B'.($staff_position+count($onpp_target_number)-1)); 
		        		// DATE
			            $sheet->setCellValue('A'.$staff_position, $each_value['onpp_year']."年".$each_value['onpp_month']."月");
						//人員
			            $sheet->setCellValue('B'.$staff_position, get_Staff_name($each_value['onpp_plant_staff']));
			            $staff_position = $staff_position+count($onpp_target_number);
			            
			            foreach ($onpp_target_number as $onpp_t_key => $onpp_t_value) {
			            	$gap = $onpp_t_value['target_number']-$new_actual_number[$onpp_t_key]['target_number'];   
							if ($gap <= 0) {
								if ($onpp_t_value['target_number'] != 0 || $new_actual_number[$onpp_t_key]['target_number'] !=0) {
									$status = '達標';
								}else{
									$status = "-";
								}
							}else{
								$status = '未達標';	 
							}
			            	//目標值
			            	$sheet->setCellValue('C'.($n+$new_key), "（".$onpp_t_value['plant_size']."）".$onpp_t_value['target_number']);
			            	//實際值	
			            	$sheet->setCellValue('D'.($n+$new_key), "（".$new_actual_number[$onpp_t_key]['plant_size']."）".$new_actual_number[$onpp_t_key]['target_number']);
			            	//未達數	
			            	$sheet->setCellValue('E'.($n+$new_key), "（".$onpp_t_value['plant_size']."）".$gap);
			            	//達成率	
			           		$sheet->setCellValue('F'.($n+$new_key), @round(100*(($new_actual_number[$onpp_t_key]['target_number']/$onpp_t_value['target_number'])),2)."％");
			           		//狀態
			            	$sheet->setCellValue('G'.($n+$new_key), $status);
			            	$new_key+=1;
			            }	
					}					
				}				
			}
			$exl_year = substr($exl_year,0,-1);
	    	// 產生檔案
	        $excelextend = substr($inputfilename, strpos($inputfilename, "."));
	        $filename="種植績效報表_".$exl_year."年";
	        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	        header("Content-Disposition: attachment;filename=".$filename.$excelextend);
	        header('Cache-Control: max-age=0');
	        // $objWriter = PHPExcel_IOFactory::createWriter($originalexcel, 'Excel2007');
	        // $objWriter->setIncludeCharts(TRUE);
	        // $objWriter->save('php://output');
	        if($excelextend == "xlsx"){
	            $objWriter = PHPExcel_IOFactory::createWriter($originalexcel, 'Excel2007');
	        }else{
	            $objWriter = PHPExcel_IOFactory::createWriter($originalexcel, 'Excel5');
	        }
	        $objWriter->save('php://output');

	        exit;
			break;
		default:
			# code...
			break;
	}
	
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
    <script src="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.zh-TW.js" charset="UTF-8"></script>
	<link rel="stylesheet" href="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
	<script type="text/javascript">		
		$(document).ready(function() {
			<?php
					//	init search parm
			// print "$('#search [name=onadd_status] option[value={$onadd_status}]').prop('selected','selected');";
			// print "alert($onpp_plant_staff);";
			print "$('#search_form [name=onpp_plant_staff] option[value={$onpp_plant_staff}]').prop('selected','selected');";
			?>
			$('button.upd').on('click', function(){
				$('#upd-modal').modal();
				$('#upd_form')[0].reset();

				$.ajax({
					url: './kpi_planting_performance.php',
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

			bootbox.setDefaults({
				locale: "zh_TW",
			});

			$('button.del_table').on('click', function(){
				oncost_sn = $(this).data('oncost_sn')
				bootbox.confirm("確認刪除？", function(result) {
					if(result) {
						$.ajax({
							url: './plant_business.php',
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
							url: './kpi_planting_performance.php',
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

			$('#add_form, #upd_form').validator().on('submit', function(e) {
				if (!e.isDefaultPrevented()) {
					e.preventDefault();
					var param = $(this).serializeArray();

					$(this).parents('.modal').modal('hide');
					$(this)[0].reset();

					 	console.table(param);

					 	$.ajax({
					 		url: './kpi_planting_performance.php',
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
			$('button.export_excel_each').on('click', function(){
				var data_array = new Array();
　				var data_array = $(this).data('export_excel_each_data').split("_");
				var data_year = data_array[0];
				var data_month = data_array[1];
				window.open("kpi_planting_performance.php?export_error=1&data_year="+data_year+
					"&data_month="+data_month+
					"&onpp_plant_staff="+
					<?PHP echo '"'.GetParam('onpp_plant_staff').
        	    	"&start=".GetParam('start').
        	    	"&end=".GetParam('end').
        	    	"&onpp_up_to_standard=".GetParam('onpp_up_to_standard').'"';
        	    	?>);
        	});
        	$('button.export_excel').on('click', function(){
        	    window.open("kpi_planting_performance.php?export_error=2&onpp_plant_staff="+
					<?PHP echo '"'.GetParam('onpp_plant_staff').
        	    	"&start=".GetParam('start').
        	    	"&end=".GetParam('end').
        	    	"&onpp_up_to_standard=".GetParam('onpp_up_to_standard').'"';
        	    	?>);
        	});


			$('#datetimepickerADD').datetimepicker({				
				startView:3,
			    minView:3,
			    autoclose:'true',
			    language:'zh-TW',
                format: 'yyyy-mm',
			});

			$('#datetimepicker1,#datetimepicker2').datetimepicker({
				startView:3,
			    minView:3,
			    autoclose:'true',
			    format:'yyyy/mm',
			    language:"zh-TW",
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
					<h4 style="font-size: 25px">種植績效管理</h4>
				</div>
			</div>
		</div>

		<!-- ADD modal -->
		<div id="add-modal" class="modal add-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="add_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">新增</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="add">
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">日期(年/月)<font color="red">*</font></label>
										<div class="col-md-3">
											<div class="input-group">
												<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
												<input type="text" class="form-control" id="datetimepicker1" name="add_date" value="<?php echo date('Y/m',time());?>" placeholder="">
											</div>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="col-md-12">
										<table class="table">
											<thead>
												<tr>
													<th style='text-align: center;width: 6vw;'>人員</th>
													<?php 
													foreach ($DEVICE_SYSTEM as $key => $value) {
														echo "<th style='text-align: center;'>".$value." 目標</th>";
													}
													?>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td style='text-align: center;'>
														<select class="form-control" name="onpp_plant_staff">
				    										<option value="-1">請選擇</option>
				        									<?php 
				        									foreach ($Staffs as $key2 => $value2) {
				        										echo "<option value=".$value2['jsuser_sn'].">".$value2['jsuser_name']."</option>";
				        									}
				        									?>
				    									</select>
													</td>
													<?php 
													foreach ($DEVICE_SYSTEM as $key => $value) {
														echo "<td style='text-align: center;'><input class='form-control' type='text' name='T_".$key."'></td>";
													}
													?>
												</tr>
											</tbody>
										</table>
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

		<!-- update modal -->
		<div id="upd-modal" class="modal upd-modal" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<form autocomplete="off" method="post" action="./" id="upd_form" class="form-horizontal" role="form" data-toggle="validator">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
							<h4 class="modal-title">新增</h4>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<input type="hidden" name="op" value="upd">
									<div class="form-group">
										<label for="addModalInput1" class="col-md-2 control-label">日期(年/月)<font color="red">*</font></label>
										<div class="col-md-2">
											<div class="input-group">
												<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
												<input type="text" class="form-control" id="datetimepicker1" name="add_date" value="<?php echo date('Y/m',time());?>" placeholder="">
											</div>
											<div class="help-block with-errors"></div>
										</div>
									</div>
									<div class="col-md-12">
										<table class="table">
											<thead>
												<tr>
													<th style='text-align: center;width: 6vw;'>人員</th>
													<?php 
													foreach ($DEVICE_SYSTEM as $key => $value) {
														echo "<th style='text-align: center;'>".$value." 目標</th>";
													}
													?>
												</tr>
											</thead>
											<tbody>
												<tr>
													<td style='text-align: center;'>
														<select class="form-control" name="onpp_plant_staff">
				    										<option value="-1">請選擇</option>
				        									<?php 
				        									foreach ($Staffs as $key2 => $value2) {
				        										echo "<option value=".$value2['jsuser_sn'].">".$value2['jsuser_name']."</option>";
				        									}
				        									?>
				    									</select>
													</td>
													<?php 
													foreach ($DEVICE_SYSTEM as $key => $value) {
														echo "<td style='text-align: center;'><input class='form-control' type='text' name='T_".$key."'></td>";
													}
													?>
												</tr>
											</tbody>
										</table>
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

		<!-- container -->
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-12">
					<div class="navbar-collapse collapse pull-right" style="margin-bottom: 10px;">
						<ul class="nav nav-pills pull-right toolbar">							
							<li>
								<button data-parent="#toolbar" data-toggle="modal" data-target=".add-modal" class="accordion-toggle btn btn-primary">
									<i class="glyphicon glyphicon-plus"></i> 新增
								</button>
							</li>
							<li class="form-group">								
                                <button type="submit" class="btn btn-info export_excel">
                                	<i class="glyphicon glyphicon-save-file"></i> 總報表匯出
                                </button>
                            </li>
						</ul>
					</div>
					<!-- search -->
        				<div id="search" style="clear:both;">
        					<form autocomplete="off" method="get" action="./kpi_planting_performance.php" id="search_form" class="form-inline alert alert-info" role="form">
        						<div class="row">
    								<div class="form-group" style="padding-left: 2rem;">
    									<label for="searchInput1">種植人員</label>
    									<select class="form-control" name="onpp_plant_staff">
    										<option value="-1">全部</option>
        									<?php 
        									foreach ($Staffs as $key => $value) {
        										// if ($onpp_plant_staff == $value['jsuser_sn']) {
        										// 	echo "<option value=".$value['jsuser_sn']." selected>".$value['jsuser_name']."</option>";
        										// }else{
        											echo "<option value=".$value['jsuser_sn'].">".$value['jsuser_name']."</option>";
        										// }
        									}
        									?>
    									</select>
    								</div>
    								<div class="form-group" style="padding-left: 2rem;">
										<label for="datetimepicker2">日期&nbsp;</label>
										<div class="input-group">
											<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
											<input type="text" class="form-control" id="datetimepicker2" name="start" value="<?php echo $start;?>" placeholder="">
										</div>
									</div>
									<div class="form-group">
										<label for="datetimepicker2"> ～ </label>
										<div class="input-group">
											<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
											<input type="text" class="form-control" id="datetimepicker2" name="end" value="<?php echo $end;?>" placeholder="">
										</div>
									</div>
    								<div class="form-group" style="padding-left: 2rem;">
    									<?php $standard_select = array('-1'=>'全部','1'=>'達標','2'=>'未達標');
    									?>
    									<select class="form-control" name="onpp_up_to_standard">
    										<?php 
        									foreach ($standard_select as $key => $value) {
        										if ($onpp_up_to_standard == $key) {
        											echo "<option value=".$key." selected>".$value."</option>";
        										}else{
        											echo "<option value=".$key.">".$value."</option>";
        										}
        									}
        									?>
    									</select>
    								</div>
    								<div  class="form-group" style="float: right;padding-right: 2rem;">
	    								<button type="submit" class="btn btn-info" op="search">搜尋</button>      									
    								</div>
        						</div>
        					</form>
        				</div>
					<!-- content -->
					<?php 				
						foreach ($ppData_table as $key => $value) {							
							foreach ($value as $key_m => $value_m) {				
								if (!empty($value_m)) {
									$newid = $key.'_'.$key_m;
					?>					
							<div class="col-md-12" style="">
								<div class="panel panel-info">
							      	<div class="panel-heading">
							      	 	<h3 class="panel-title">
								      	    <a data-toggle="collapse" data-parent="#accordion"  href="#<?php echo $newid;?>">
								      	    	<h3 color="#000000"><?php echo $key."/".$key_m." 月";?></h3>
								      	    </a>
								      	    <!-- <button type="button" class="btn btn-default  btn-warning btn-sm upd" style="float: right;margin-top: -3.5rem;margin-right: 7.5rem;" data-export_excel_data="<?php echo $newid;?>">
								      	    	<i class="glyphicon glyphicon-edit"></i> 修改
								      	    </button> -->
								      	    <button type="button" class="btn btn-default btn-sm export_excel_each" style="float: right;margin-top: -3.5rem;" data-export_excel_each_data="<?php echo $newid;?>">
								      	    	<i class="glyphicon glyphicon-save-file"></i> 匯出
								      	    </button>
							      	  	</h3>
							      	</div>
							      	<div id="<?php echo $newid;?>" name="collapse" class="panel-collapse collapse">
							      	  	<div class="panel-body">
					<?php
									foreach ($value_m as $key_data => $value_data) {
										$onpp_month_S =  $value_data['onpp_month'];
										$onpp_month_E = $onpp_month_S+1;
										$onpp_year_S = $onpp_year_E =  $value_data['onpp_year'];
										
										$onpp_target_number = json_decode($value_data['onpp_target_number'], True);
										$conn = getDB();
										if ($onpp_month_S == 12) {
											$onpp_year_E = $onpp_year_E+1;
											$onpp_month_E = 1;
										}
										$sql_quantity = "SELECT onadd_cur_size,onadd_quantity FROM `onliine_add_data` WHERE onadd_plant_staff = '{$value_data['onpp_plant_staff']}' AND `onadd_planting_date` BETWEEN UNIX_TIMESTAMP('$onpp_year_S/$onpp_month_S/01 00:00:00')  AND (UNIX_TIMESTAMP('$onpp_year_E/$onpp_month_E/01 00:00:00')-1);";
										$qresult = $conn->query($sql_quantity);
										$actual_number = array();
										if ($qresult->num_rows > 0) {
											while($row = $qresult->fetch_assoc()) {
												$actual_number[$row['onadd_cur_size']]['plant_size'] = $DEVICE_SYSTEM[$row['onadd_cur_size']];
												$actual_number[$row['onadd_cur_size']]['target_number'] = $row['onadd_quantity'];
											}
											$qresult->free();
										}
										$conn->close();
										$new_actual_number = array();
										foreach ($onpp_target_number as $DS_key => $DS_value) {
											if (isset($actual_number[$DS_key])) {
												$new_actual_number[$DS_key] = $actual_number[$DS_key];									
											}else{
												$new_actual_number[$DS_key]['plant_size'] = $DS_value['plant_size'];
												$new_actual_number[$DS_key]['target_number'] = 0;
											}											
										}
					?>
											<table class="table table-striped">
							      	  			<thead>
							      	  				<tr>
							      	  					<th style="vertical-align: middle;text-align: center;font-size: 2rem;width: 18%">人員</th>
							      	  					<th style="vertical-align: middle;text-align: center;font-size: 2rem;width: 18%">目標值</th>
							      	  					<th style="vertical-align: middle;text-align: center;font-size: 2rem;width: 18%">實際值</th>
							      	  					<th style="vertical-align: middle;text-align: center;font-size: 2rem;width: 18%">未達數</th>
							      	  					<th style="vertical-align: middle;text-align: center;font-size: 2rem;width: 18%">達成率</th>
							      	  					<th style="vertical-align: middle;text-align: center;font-size: 2rem;">狀態</th>
							      	  				</tr>
							      	  			</thead>
							      	  			<tbody>
							      	  				<?PHP
						      	  						foreach ($onpp_target_number as $tn_key => $tn_value) {
						      	  							$colors = '';
															$status = '';
						      	  							$gap = ($tn_value['target_number']-$new_actual_number[$tn_key]['target_number']);

						      	  								if ($gap <= 0) {
						      	  									if ($tn_value['target_number'] != 0 || $new_actual_number[$tn_key]['target_number'] !=0) {
								      	  								$conn = getDB();
								      	  								$sql_up_to_standard = "UPDATE `online_planting_performance` SET `onpp_up_to_standard` = 1 WHERE `onpp_sn` = {$value_data['onpp_sn']}";
																		$qresult = $conn->query($sql_up_to_standard);
																		$conn->close();
								      	  								$status = "達標";
								      	  								$colors = 'color:blue;';
								      	  							}else{
								      	  								$colors = 'color:#000000;';
								      	  								$status = "-";
								      	  								
								      	  							}
							      	  							}elseif ($gap > 0) {
							      	  								$status = "未達標";
							      	  								$colors = 'color:red;';
							      	  								$conn = getDB();
							      	  								$sql_up_to_standard = "UPDATE `online_planting_performance` SET `onpp_up_to_standard` = 2 WHERE `onpp_sn` = {$value_data['onpp_sn']}";
																	$qresult = $conn->query($sql_up_to_standard);
																	$conn->close();
							      	  							}
						      	  							
						      	  							
						      	  							// printr($status.$colors);	
						      	  							echo "<tr>";
						      	  							if ($tn_key == 1) {
						      	  								echo "<td rowspan='7' style='vertical-align: middle;text-align: center;'><h4>".get_Staff_name($value_data['onpp_plant_staff'])."</h4></td>";
						      	  							}
						      	  							// 目標數量
						      	  							echo "<td style='vertical-align: middle;text-align: center;'>".$tn_value['plant_size']."（".$tn_value['target_number']."）</td>";
						      	  							// 實際數量
						      	  							echo "<td style='vertical-align: middle;text-align: center;'>".$new_actual_number[$tn_key]['plant_size']."（".$new_actual_number[$tn_key]['target_number']."）</td>";
						      	  							// 未達數							
						      	  							echo "<td style='vertical-align: middle;text-align: center;".$colors."'>".$tn_value['plant_size']."（".$gap."）</td>";
						      	  							// 達成率						      	  							
						      	  							echo "<td style='vertical-align: middle;text-align: center;'>".@round($new_actual_number[$tn_key]['target_number']/$tn_value['target_number']*100,2)."％</td>";
						      	  							// 狀態
						      	  							echo "<td style='vertical-align: middle;text-align: center;".$colors."'>".$status."</td>";
						      	  							echo "</tr>";
						      	  						}
						      	  					?>	
							      	  				
							      	  			</tbody>
							      	  		</table>
					<?php
									}							
					?>						
							      		</div>
							      	</div>
							    </div>
							</div>	
					
					<?php
									
								}
							} 
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
    </body>
    </html>