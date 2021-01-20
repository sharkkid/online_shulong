<?php
include_once("./func_plant_purchase.php");
// echo $_COOKIE['onadd_sn'];
// exit();
if($_COOKIE['onadd_sn'] != null){
	$onadd_sn=$_COOKIE['onadd_sn'];
	$ret_data = array();
	if(!empty($onadd_sn)){
		$ret_code = 1;
		$ret_data = getDetailsBySn($onadd_sn);
	} else {
		$ret_code = 0;
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

	<style>
		.work{
		  border:1px #000 solid;font-size:13px;padding:10px;
		  float:left;
		  width: 5.5cm;
		  height: 7cm;
		  margin-bottom: 10px;
		  margin-right: 10%;
		  text-align: center;
		}
		page[size="A4"] {  
		  width: 21cm;
		  height: 29.7cm; 
		}
		page[size="A4"][layout="landscape"] {
		  width: 29.7cm;
		  height: 21cm;  
		}
		page[size="A3"] {
		  width: 29.7cm;
		  height: 470px;
		}
		page[size="A3"][layout="landscape"] {
		  width: 470px;
		  height: 29.7cm;  
		}
		page[size="A5"] {
		  width: 14.8cm;
		  height: 21cm;
		}
		page[size="A5"][layout="landscape"] {
		  width: 21cm;
		  height: 14.8cm;  
		}
		@media print {
		  body, page {
		    margin: 0;
		    box-shadow: 0;
		  }
		}
	</style>
	<script src="./../../js1/jquery.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$.ajax({
				url: './plant_purchase.php',
				type: 'post',
				dataType: 'json',
				data: {op:"get", onadd_sn:<?php echo $_COOKIE['onadd_sn'];?>},
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
			            	// console.log(d);
			            	if(d.img_url != "")
			            		$('.product_img').attr("src",d.img_url);
			            	else
			            		$('.product_img').attr("src","./images/nopic.png");

			            	$('.onadd_sn').html("產品編號："+"<?php echo $_COOKIE['qr_sn']?>");
			            	console.log("<?php echo $_COOKIE['qr_sn']?>");
			            	$('.part_no').html("品號："+d.onadd_part_no);
			            	$('.part_name').html("品名："+d.onadd_part_name);
			            	$('.plant_date').html("下種日期："+d.onadd_planting_date);
							$('.location').html("位置："+d.onadd_location);
							if("<?php echo $_COOKIE['plant_sn'];?>" == "1")
			            		var src = $('#qr_img_example').attr('src');
			            	else
			            		var src = $('#qr_img_example_flask').attr('src');
			            	$('.qr_img').attr('src',src+"onadd_sn="+d.onadd_sn);
			            	setTimeout(function(){
			            		window.print();
			            	},500);
			            	
			            }
			        },
			        error: function (xhr, ajaxOptions, thrownError) {
		            	// console.log('ajax error');
		                // console.log(xhr);
		            }
		        });


			// $('.product_img').attr("src","<?php echo GetParam('img_src');?>");
			// $('.onadd_sn').html("<?php echo GetParam('qr_sn');?>");
			// $('.part_no').html("<?php echo GetParam('qr_part_no');?>");
			// $('.part_name').html("<?php echo GetParam('qr_part_name');?>");
			// $('.plant_date').html("<?php echo GetParam('qr_plant_date');?>");
			// $('.location').html("<?php echo GetParam('qr_location');?>");
			// $('.qr_img').attr("src","<?php echo GetParam('qr_src');?>")
		});

			   //圖片長寬取得
			    var img     = document.getElementById('img_print');
			    var width   = img.clientWidth;
			    var height  = img.clientHeight;
			    alert(width);
			    alert(height);
		
	</script>
	<style type="text/css">
		#img_print{
			max-height:10vh;
			max-width:15vw;
			min-height:5vh;
			min-width:15vw;
		}
		#QR{
			max-height:9vh;
			max-width: 9vw;
		}
		/*body{
			width: 820px;
			height:1160px;
		}*/
	</style>
</head>

<body>

	<page size="A4">

		<div style="text-align: center;width: 595px">
			<img id="qr_img_example" style="margin-left: 20px;padding-left: 10px;display:none;" src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/purchase/plant_purchase_m.php?">
			<img id="qr_img_example_flask" style="margin-left: 20px;padding-left: 10px;display:none;" src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo WT_SERVER;?>/admin/flask/plant_flask.php?">
			<?php 
				for ($i=0; $i < 6; $i++) { ?>
					<div class="work">
					<img id='img_print' style="" src="" class="product_img">
					<div style="text-align:cen;font-size: 14px;margin: auto;" class="onadd_sn">產品編號:1419-17</div>
					<div style="text-align:cen;font-size: 14px;margin: auto;" class="part_no">品號:P1015</div>
					<div style="text-align:cen;font-size: 14px;margin: auto;" class="part_name">品名:維維安Vivian (2號)</div>
					<div style="text-align:cen;font-size: 14px;margin: auto;" class="plant_date">下種日期:1419-08-01</div>
					<div style="text-align:cen;font-size: 14px;margin: auto;" class="location">位置:A5</div>
					<div style="text-align:right;"><img id='QR' style="margin-left: 0px;" src="" class="qr_img"></div>	
			</div>

			<?php 	}	?>
		</div>
	</page>


    </body>
    </html>