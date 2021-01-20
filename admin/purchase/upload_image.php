<?php
include_once(dirname(__FILE__).'/../config.php');
/**
 * 表單接收頁面
 */

// 網頁編碼宣告（防止產生亂碼）
header('content-type:text/html;charset=utf-8');
// 封裝好的單一檔案上傳 function
include_once("./func_plant_purchase_details.php");
// 取得 HTTP 文件上傳變數
$fileInfo = $_FILES['myFile'];

switch ($_POST['onproduct_type']) {
	// 1 : 更新封面   2 : 新增更多圖片
	case '1':
		$path = $_POST['parameters'];
		$newName = uploadFile($fileInfo);		
		if(update_image_url($newName,$_POST['onproduct_sn'])){
			echo '<script type="text/javascript">
					if(!alert("更新成功！")) {
				   		window.history.back();
				 	}
				 </script>';
		}
		else{
			echo '<script type="text/javascript">
					if(!alert("更新失敗！'.$_POST['onproduct_sn'].'")) {
				   		window.location.href="'.WT_SERVER."/admin/purchase/".$path.'";
				 	}
				 </script>';
		}
		//重定向瀏覽器 
		// header("Location: ".WT_SERVER."/admin/purchase/".$path); 
		break;
	
	case '2':
		$path = $_POST['parameters'];
		$newName = uploadFile($fileInfo);
		if(add_image_url($newName,$_POST['onproduct_sn'])){
			echo '<script type="text/javascript">
					if(!alert("新增成功！")) {
				   		window.location.href="'.WT_SERVER."/admin/purchase/".$path.'";
				 	}
				 </script>';
		}
		else{
			echo '<script type="text/javascript">
					if(!alert("新增失敗！")) {
				   		window.location.href="'.WT_SERVER."/admin/purchase/".$path.'";
				 	}</script>';
		}
		break;

	case '3':
		$path = $_POST['parameters'];
		$newName = uploadFile($fileInfo);
		add_image_url($newName,$_POST['onproduct_sn']);
		// //重定向瀏覽器 
		header("Location: ".WT_SERVER."/admin/purchase/".$path); 

	case '4':
		$newName = uploadFile($fileInfo);
		echo $newName;
		break;
}


//確保重定向後，後續代碼不會被執行 
exit;
?>
