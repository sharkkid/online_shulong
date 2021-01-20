<?php
include_once("./func.php");
$sys_type_mapping = array(0=>'系統', 1=>'設備');
$op_type_mapping = array(0=>'其它', 1=>'新增', 2=>'修改', 3=>'刪除', 4=>'後臺登入');
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
			$inputfilename = WT_PATH_ROOT.'/admin/sys/history_report.xls';
			if(!file_exists($inputfilename)) exceptions("查無Excel巡檢表");
			$originalexcel = PHPExcel_IOFactory::load($inputfilename);
				
			$search_where = $_SESSION['query']['sys_history'];
			$record_list = getHistory($search_where, 0, 100000000);
			// printr($record_list);exit;
			// init data
			$add_date = date('Y/m/d H:i:s');
			$sheetname = '系統操作歷史紀錄';
		
			$sheet = $originalexcel->getSheetByName($sheetname);
				
			// 塞值
			$sheet->setCellValue('A1', "系統操作歷史紀錄");
			$sheet->setCellValue('A2', "製表時間：{$add_date}");
				
			foreach ($record_list as $i=>$v) {
				$row = $i+4;
				$sheet->setCellValue('A'.$row, $v['jshist_sn']);
				$sheet->setCellValue('B'.$row, date('Y-m-d H:i', $v['jshist_add_date']));
				$sheet->setCellValue('C'.$row, $sys_type_mapping[$v['jshist_sys_type']]);
				$sheet->setCellValue('D'.$row, $v['jshist_sys_type_desc']);
				$sheet->setCellValue('E'.$row, $v['jsuser_name']);
				$sheet->setCellValue('F'.$row, $op_type_mapping[$v['jshist_op_type']]);
				$sheet->setCellValue('G'.$row, $v['jshist_op_desc']);
				$sheet->setCellValue('H'.$row, $v['jshist_ip']);
			}
				
			$sheet->setTitle('系統操作歷史紀錄');
				
			// 產生檔案
			$excelextend = substr($inputfilename, strpos($inputfilename, "."));
			$filename="history_".date("YmdHis");
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
	if(($jshist_sys_type = GetParam('jshist_sys_type', -1))>=0) {
		$search_where[] = "jshist_sys_type='{$jshist_sys_type}'";
		$search_query_string['jshist_sys_type'] = $jshist_sys_type;
	}
	if(($jshist_sys_type_desc = GetParam('jshist_sys_type_desc'))) {
		$search_where[] = "jshist_sys_type_desc like '%{$jshist_sys_type_desc}%'";
		$search_query_string['jshist_sys_type_desc'] = $jshist_sys_type_desc;
	}
	if(($jshist_user = GetParam('jshist_user'))) {
		$search_where[] = "b.jsuser_name like '%{$jshist_user}%'";
		$search_query_string['jshist_user'] = $jshist_user;
	}
	if(($jshist_op_type = GetParam('jshist_op_type', -1))>=0) {
		$search_where[] = "jshist_op_type='{$jshist_op_type}'";
		$search_query_string['jshist_op_type'] = $jshist_op_type;
	}
	if(($jshist_ip = GetParam('jshist_ip'))) {
		$search_where[] = "a.jshist_ip like '%{$jshist_ip}%'";
		$search_query_string['jshist_ip'] = $jshist_ip;
	}
	if(($start = GetParam('start'))) {
		$start_c = str2time($start);
		$search_where[] = "jshist_add_date>={$start_c}";
	}
	if(($end = GetParam('end'))) {
		$end_c = str2time($end);
		$search_where[] = "jshist_add_date<={$end_c}";
	}
	$search_where = isset($search_where) ? implode(' and ', $search_where) : '';
	$_SESSION['query']['sys_history'] = $search_where;
	$search_query_string = isset($search_query_string) ? http_build_query($search_query_string) : '';
	
	// page
	$pg_page = GetParam('pg_page', 1);
	$pg_rows = 20;
	$pg_total = GetParam('pg_total')=='' ? getHistoryQty($search_where) : GetParam('pg_total');
	$pg_offset = $pg_rows * ($pg_page - 1);
	$pg_pages = $pg_rows == 0 ? 0 : ( (int)(($pg_total + ($pg_rows - 1)) /$pg_rows) );
	
	$history_list = getHistory($search_where, $pg_offset, $pg_rows);
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
						print "$('#search [name=jshist_sys_type] option[value={$jshist_sys_type}]').prop('selected','selected');";
						print "$('#search [name=jshist_op_type] option[value={$jshist_op_type}]').prop('selected','selected');";
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
					window.open('./sys_history.php?op=export_excel');
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
					<h3 class="text-center wt-block-title">系統操作歷史紀錄</h3>
					
    				<!-- nav toolbar -->
					<div class="navbar-collapse collapse pull-right" style="margin-bottom: 10px;">
					    <ul class="nav nav-pills pull-right toolbar">
					    </ul>
					</div>
					
    				<!-- search -->
    				<div id="search" style="clear:both;">
					    <form autocomplete="off" method="get" action="./sys_history.php" id="search_form" class="form-inline alert alert-info" role="form">
					    	<div class="row">
							    <div class="col-md-12">
								    <div class="form-group">
								        <label for="searchInput1">類別</label>
								        <select class="form-control" id="searchInput11" name="jshist_sys_type">
								            <option selected="selected" value="-1">全部</option>
								            <?php 
								            foreach ($sys_type_mapping as $k=>$v) {
												echo '<option value="'.$k.'">'.$v.'</option>';
											}
								            ?>
								        </select>
								    </div>
								    <div class="form-group">
								        <label for="searchInput2">描述</label>
								        <input type="text" class="form-control" id="searchInput2" name="jshist_sys_type_desc" value="<?php echo $jshist_sys_type_desc;?>" placeholder="">
								    </div>
								    <div class="form-group">
								        <label for="searchInput3">操作者</label>
								        <input type="text" class="form-control" id="searchInput3" name="jshist_user" value="<?php echo $jshist_user;?>" placeholder="">
								    </div>
								    <div class="form-group">
								        <label for="searchInput4">操作</label>
								        <select class="form-control" id="searchInput4" name="jshist_op_type">
								            <option selected="selected" value="-1">全部</option>
								            <?php 
								            foreach ($op_type_mapping as $k=>$v) {
												echo '<option value="'.$k.'">'.$v.'</option>';
											}
								            ?>
								        </select>
								    </div>
								</div>
							    <div class="col-md-12" style="margin-top: 10px;">
								    <div class="form-group">
								        <label for="searchInput3">IP</label>
								        <input type="text" class="form-control" id="searchInput3" name="jshist_ip" value="<?php echo $jshist_ip;?>" placeholder="">
								    </div>
								</div>
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
								<th width="10px">#</th>
								<th width="130px">時間</th>
								<th width="70px">類別</th>
								<th width="200px">描述</th>
								<th width="80px">操作者</th>
								<th width="90px">操作</th>
								<th>內容</th>
								<th width="100px">IP</th>
							</tr>
						</thead>
						<tbody>
							<?php
								foreach ($history_list as $row) {
									echo '<tr>';
									echo '<td>'.$row['jshist_sn'].'</td>';
									echo '<td>'.date('Y-m-d H:i', $row['jshist_add_date']).'</td>';
									echo '<td>'.$sys_type_mapping[$row['jshist_sys_type']].'</td>';
									echo '<td>'.$row['jshist_sys_type_desc'].'</td>';
									echo '<td>'.$row['jsuser_name'].'</td>';
									echo '<td>'.$op_type_mapping[$row['jshist_op_type']].'</td>';
									echo '<td>'.$row['jshist_op_desc'].'</td>';
									echo '<td>'.$row['jshist_ip'].'</td>';
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