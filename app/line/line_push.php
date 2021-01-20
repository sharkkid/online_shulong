<?php
  include_once(dirname(__FILE__).'/func.php');

  $CheckList = getCheckLogList();
  $NotPlantList = getNotPlantBill();
  

  for ($i=0; $i < count($CheckList); $i++) { 
     $text = "\n污染率查看通知
於 ".$CheckList[$i]['onld_add_time']." , 製作的組培項目：
批號：".$CheckList[$i]['data']['sn']."
品名：".$CheckList[$i]['data']['onadd_part_no']."
品號：".$CheckList[$i]['data']['onadd_part_name']."
製作已滿三天，請研發人員進行污染確認，謝謝。";
     line_notify($text);     
  }
 
  for ($i=0; $i < count($NotPlantList); $i++) { 
     $text = "\n訂單通知
".$NotPlantList[$i]['onbuda_add_date']." , 有一筆訂單資料新增：
批號：".$NotPlantList[$i]['onbuda_sn']."
品名：".$NotPlantList[$i]['onbuda_part_no']."
品號：".$NotPlantList[$i]['onbuda_part_name']."
材料：".$NotPlantList[$i]['onbuda_part_material_type']."
訂單需求：請研發人員進行相關作業，謝謝";
     line_notify($text);     
  }

  $ShipAndBasinList = getWorkListByMonth();
    for ($i=0; $i < count($ShipAndBasinList); $i++) { 
        if($ShipAndBasinList[$i]['onadd_planting_date_unix'] >= $ShipAndBasinList[$i]['expected_date_unix']){
          if($ShipAndBasinList[$i]['isSell'] == 9){
            $expected_title = "預計出貨日：";
            $event = "已經超過出貨日期，請安排作業";
          }
          else{
            $expected_title = "預計成長日：";
            $event = "已經超過換盆日期，請安排作業";
          }            
        }
        else{
          if($ShipAndBasinList[$i]['isSell'] == 9){
            $expected_title = "預計出貨日：";
            $event = "即將到達出貨日期";
          }
          else{
            $expected_title = "預計成長日：";
            $event = "即將到達換盆日期";
          }
        }
        if($ShipAndBasinList[$i]['onadd_quantity'] > 0){
$text = "\n".$event."查看通知
於 ".$ShipAndBasinList[$i]['onadd_planting_date']." , 下種的苗株項目：
批號：".$ShipAndBasinList[$i]['sn']."
品名：".$ShipAndBasinList[$i]['onadd_part_no']."
品號：".$ShipAndBasinList[$i]['onadd_part_name']."
數量：".$ShipAndBasinList[$i]['onadd_quantity']."
提醒事項：".$event."，謝謝。";

     line_notify($text);
        }   
  }
 ?>
