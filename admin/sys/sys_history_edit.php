<?php
include_once("./func.php");
$op=GetParam('op');
if(!empty($op)) {
	$ret_code = 1;
	$ret_msg = '';
	$ret_data = array();
	switch ($op) {
		case 'export_excel':
			set_time_limit(300);
			header("Content-Type:text/html; charset=utf-8");
			include_once(WT_PATH_ROOT.'/lib/PHPExcel_1.8.0/PHPExcel.php');
			include_once(WT_PATH_ROOT.'/lib/PHPExcel_1.8.0/PHPExcel/Writer/Excel2007.php');
				
			// init excel
			$inputfilename = WT_PATH_ROOT.'/admin/sys/history_edit_report.xls';
			if(!file_exists($inputfilename)) exceptions("查無Excel巡檢表");
			$originalexcel = PHPExcel_IOFactory::load($inputfilename);
				
			$search_where = $_SESSION['query']['sys_edit_history'];
			$record_list = getHistoryEdit($search_where, 0, 1000000000);
			// printr($record_list);exit;
			// init data
			$add_date = date('Y/m/d H:i:s');
			$sheetname = '所有帳號執行修改次數';
		
			$sheet = $originalexcel->getSheetByName($sheetname);
				
			// 塞值
			$sheet->setCellValue('A1', "所有帳號執行修改次數");
			$sheet->setCellValue('A2', "製表時間：{$add_date}");
				
			foreach ($record_list as $i=>$v) {
				$row = $i+4;
				$sheet->setCellValue('A'.$row, $v['jsuser_sn']);
				$sheet->setCellValue('B'.$row, $v['jsuser_name']);
				$sheet->setCellValue('C'.$row, $v['count']);
			}

			$sheet->setTitle('所有帳號執行修改次數');
				
			// 產生檔案
			$excelextend = substr($inputfilename, strpos($inputfilename, "."));
			$filename="history_edit_".date("YmdHis");
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header("Content-Disposition: attachment;filename=".$filename.$excelextend);
			header('Cache-Control: max-age=0');
			if($excelextend == "xlsx")
				$objWriter = PHPExcel_IOFactory::createWriter($originalexcel, 'Excel2007');
			else
				$objWriter = PHPExcel_IOFactory::createWriter($originalexcel, 'Excel5');
			$objWriter->save('php://output');
				
			exit;
			break;
			
		default:
			$ret_msg = 'error!';
			break;
	}
	
	echo enclode_ret_data($ret_code, $ret_msg, $ret_data);
	exit;
} else {
	// search
	if(($start = GetParam('start'))) {
		$start_c = str2time($start);
		$search_where[] = "jshist_add_date>={$start_c}";
	}
	if(($end = GetParam('end'))) {
		$end_c = str2time($end);
		$search_where[] = "jshist_add_date<={$end_c}";
	}
	$search_where = isset($search_where) ? implode(' and ', $search_where) : '';
	$_SESSION['query']['sys_edit_history'] = $search_where;
	$search_query_string = isset($search_query_string) ? http_build_query($search_query_string) : '';
	
	// page
	$pg_page = GetParam('pg_page', 1);
	$pg_rows = 20;
	$pg_total = GetParam('pg_total')=='' ? getHistoryEditQty($search_where) : GetParam('pg_total');
	$pg_offset = $pg_rows * ($pg_page - 1);
	$pg_pages = $pg_rows == 0 ? 0 : ( (int)(($pg_total + ($pg_rows - 1)) /$pg_rows) );
	
	$history_list = getHistoryEdit($search_where, $pg_offset, $pg_rows);
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    	<?php include('./../htmlModule/head.php');?>
		<script src="./../../lib/jquery.twbsPagination.min.js"></script>
		
		<!-- bootstrap-datetimepicker -->
		<script src="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>
		<script src="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.zh-TW.js" charset="UTF-8"></script>
		<link rel="stylesheet" href="./../../lib/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
		
		<script type="text/javascript">
			$(document).ready(function() {
				<?php 
					//	init search parm
					if($status>=0) {
					}
				?>

		        $('#datetimepicker1').datetimepicker({
		            language:  'zh-TW',
		            format: 'yyyy-mm-dd hh:ii:ss',
		            useCurrent: false //Important! See issue #1075
		        });
		        $('#datetimepicker2').datetimepicker({
		            language:  'zh-TW',
		            format: 'yyyy-mm-dd hh:ii:ss',
		            useCurrent: false //Important! See issue #1075
		        });

				$("#export_excel_btn").click(function(e){
					window.open('./sys_history_edit.php?op=export_excel');
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
					<h3 class="text-center wt-block-title">所有帳號執行修改次數</h3>
					
    				<!-- nav toolbar -->
					<div class="navbar-collapse collapse pull-right" style="margin-bottom: 10px;">
					    <ul class="nav nav-pills pull-right toolbar">
					    </ul>
					</div>
					
    				<!-- search -->
    				<div id="search" style="clear:both;">
					    <form autocomplete="off" method="get" action="./sys_history_edit.php" id="search_form" class="form-inline alert alert-info" role="form">
					    	<div class="row">
							    <div class="col-md-12" style="margin-top: 10px;">
								    <div class="form-group">
								        <label for="datetimepicker1">開始時間</label>
								        <input type="text" class="form-control" id="datetimepicker1" name="start" value="<?php echo $start;?>" placeholder="">
								    </div>
								    <div class="form-group">
								        <label for="datetimepicker2">結束時間</label>
								        <input type="text" class="form-control" id="datetimepicker2" name="end" value="<?php echo $end;?>" placeholder="">
								    </div>
								</div>
							    <div class="col-md-12" style="margin-top: 10px;">
								    <button type="submit" class="btn btn-info" op="search">搜尋</button>
							    	<button class="btn btn-info" id="export_excel_btn">匯出Excel</button>
								</div>
						</form>
					</div>
					
    				<!-- content -->
					<table class="table table-striped table-hover table-condensed tablesorter">
						<thead>
							<tr>
								<th width="100px">#</th>
								<th width="300px">姓名</th>
								<th>次數</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($history_list as $row) {
									echo '<tr>';
									echo '<td>'.$row['jsuser_sn'].'</td>';
									echo '<td>'.$row['jsuser_name'].'</td>';
									echo '<td>'.$row['count'].'</td>';
									echo '</td></tr>';
								}
							?>
						</tbody>
					</table>
					
    				<?php include('./../htmlModule/page.php');?>
					
				</div>
			</div>
		</div>
    </body>
</html>