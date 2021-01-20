<?php
  include_once(dirname(__FILE__).'./../config.php');
  $GLOBALS['size'] = array(
    1=>"1.7",
    2=>"2.5",
    3=>"2.8",
    4=>"3.0",
    5=>"3.5",
    6=>"3.6",
    7=>"其他",
    8=>"瓶苗開瓶",
    9=>"出貨"
    // 1.7, 2.5, 2.8, 3.0, 3.5, 3.6 其他
);
  function line_notify($message){
    $get_text = $message;
    $access_token = array();
    // $access_token[]="lPuzbsvo2oZo28hVuBHtA2ZXAkqP9XyKAmT0aRo1YoQ";
    // $access_token[]="cMxfnrv7yJyQETF3cy72L1vnuQ272lYK00gLNaRNxSz";
    $access_token[]="xL3gE41cDc42Bgb4XeN7kv9D41tQkn6fqZq1ZRh0QQX";
    
    $message=$get_text;
    $TargetCount = count($access_token);
    $Push_Content['message'] = $message;
    // $Push_Content['imageThumbnail'] = "http://img2.ali213.net/picfile/News/2019/06/13/584_32cea05b73fca687d9d8286f11a416e4.jpg";
    // $Push_Content['imageFullsize'] = "http://img2.ali213.net/picfile/News/2019/06/13/584_32cea05b73fca687d9d8286f11a416e4.jpg";
    // $Push_Content['stickerPackageId'] = "3";
    // $Push_Content['stickerId'] = "180";
    for ($i=0;$i<$TargetCount;$i++) {
     $ch = curl_init("https://notify-api.line.me/api/notify");
     curl_setopt($ch, CURLOPT_POST, true);
     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
     curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($Push_Content));
     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/x-www-form-urlencoded',
      'Authorization: Bearer '.$access_token[$i]
     ));
     $response_json_str = curl_exec($ch);
     curl_close($ch);
     // echo $response_json_str."<br>\r\n";
     // {"status":400,"message":"LINE Notify account doesn't join group which you want to send."}
     // {"status":401,"message":"Invalid access token"}
     // {"status":400,"message":"message: must not be empty"}
     $response = json_decode($response_json_str, true);
     // print_r($response);
     // echo "<hr>";
     if ( (!isset($response['status'])) || (!isset($response['message'])) ) {
      // echo "Request failed";
      exit;
     };
     if ( ($response['status'] != 200) || ($response['message'] != 'ok') ) {
      // echo "Request failed";
      exit;
     };
     if (!isset($response['access_token'])) {
      $ch = curl_init("https://notify-api.line.me/api/status");
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
       'Authorization: Bearer '.$access_token[$i]
      ));
      $response_json_str = curl_exec($ch);
      curl_close($ch);
          // echo $response_json_str."<hr>";
     } else if (preg_match('/[^a-zA-Z0-9]/u', $response['access_token'])) {
      // echo 'Got wired access_token: '.$response['access_token']."<br>";
      // echo 'http_response_header'.$http_response_header."<br>";
      // echo 'response_json'.$response_json_str."<br>";
     } else {
      // echo 'access_token: '.$response['access_token'];
     }
     usleep(6000); // microseconds * 1000 = miliseconds
    };
  }

  function getCheckLogList() {
      $ret_data = array();
      $conn = getDB();
      $now = time();
      $sql="SELECT b.onbuda_sn,b.onadd_sn,a.onlog_sn,b.onlog_add_person,a.onld_add_time,c.jsstaff_number,c.jsstaff_name,a.onld_notify_time FROM `online_log_detail` a
      left join onliine_log b on a.onlog_sn = b.onlog_sn
      left join js_staff c on a.jsstaff_sn = c.jsstaff_sn
      WHERE onld_status = 1"; 

      $qresult = $conn->query($sql);
      if ($qresult->num_rows > 0) {
        while($row = $qresult->fetch_assoc()) { 
          if($row['onld_notify_time'] > 0 && $now >= $row['onld_notify_time']){
            $date = date('Y-m-d H:i:s',$row['onld_notify_time']);
            $next_notify_time = strtotime(date('Y-m-d H:i:s',strtotime($date . "+3 days")));
            $row['onld_add_time'] = date('Y-m-d',$row['onld_add_time']);
            
            if($row['onbuda_sn'] != 0)
              $sql2="SELECT onadd_sn,onbuda_sn,onadd_part_no,onadd_part_name,onadd_add_date FROM `onliine_add_data` WHERE onbuda_sn = ".$row['onbuda_sn']." and onadd_status = 1";  
            else
              $sql2="SELECT onadd_sn,onbuda_sn,onadd_part_no,onadd_part_name,onadd_add_date FROM `onliine_add_data` WHERE onadd_sn = ".$row['onadd_sn']." and onadd_status = 1";  

            $qresult2 = $conn->query($sql2);
            if ($qresult2->num_rows > 0) {
              while($row2 = $qresult2->fetch_assoc()) { 
                if($row['onbuda_sn'] != 0)
                  $sn = $row['onbuda_sn'];
                else
                  $sn = $row['onadd_sn'];

                $row2['sn'] = "O".date('Y',$row2['onadd_add_date']).'-'.str_pad($sn,5,"0",STR_PAD_LEFT);
                $row['data'] = $row2;
              }
            }
            $ret_data[] = $row;

            $sql="UPDATE `online_log_detail` SET onld_notify_time = '{$next_notify_time}' WHERE onld_sn = ".$row['onld_sn']." and onld_status = 1";
            $conn->query($sql); 
          }
        }
        $qresult->free();
      }
      $conn->close();
      return $ret_data;
  }

  function getNotPlantBill() {
    $ret_data = array();
    $conn = getDB();
    $sql="SELECT onbuda_sn FROM `onliine_business_data` WHERE onbuda_status = 1"; 
    $qresult = $conn->query($sql);
    if ($qresult->num_rows > 0) {
      while($row = $qresult->fetch_assoc()) { 
        $sql2="SELECT * FROM `onliine_add_data` WHERE onbuda_sn = ".$row['onbuda_sn'];  
        $qresult2 = $conn->query($sql2);
        if ($qresult2->num_rows <= 0) {
          $sql3="SELECT onbuda_part_no,onbuda_part_name,onbuda_add_date,onbuda_part_material_type FROM `onliine_business_data` WHERE onbuda_sn = ".$row['onbuda_sn']; 
          $qresult3 = $conn->query($sql3);
          if ($qresult->num_rows > 0) {
            while($row3 = $qresult3->fetch_assoc()) { 
              $row3['onbuda_sn'] = "O".date('Y',$row3['onbuda_add_date']).'-'.str_pad($row['onbuda_sn'],5,"0",STR_PAD_LEFT);
              $row3['onbuda_add_date'] = date('Y-m-d',$row3['onbuda_add_date']);
              $ret_data[] = $row3;
            }
          }       
        }
      }
      $qresult->free();
    }
    $conn->close();
    return $ret_data;
  }



?>