<?php
require_once dirname(__FILE__) . './../_setting.php';

function wt_download($source, $headers=array()) {
    if(!file_exists($source) || !is_readable($source))
        return false;
        
    // Encode file name
    $filename = basename($source);
    $h_list['Content-Disposition'] = 'attachment; filename="' . $filename . '"';
    $content_length = filesize($source);
    
    // Set headers
    $h_list['Content-Type'] = 'application/octet-stream';
    $h_list['Content-Length'] = $content_length;
    $h_list['Content-Transfer-Encoding'] = 'binary';
    $h_list['Expires'] = 0;
    $h_list['Cache-Control'] = 'must-revalidate, post-check=0, pre-check=0';
    $h_list['Pragma'] = 'public';
    
    // Merge customized headers
    $h_list = array_merge($h_list, $headers);
    
    // Set headers
    ob_clean();
    foreach($h_list as $h_name=>$h_value)
        header("{$h_name}: {$h_value}");
        
    // Transfer file in 1024 byte chunks to save memory usage.
    set_time_limit(300 + $h_list['Content-Length']*8/1024/100);
    if($fd = fopen($source, 'rb'))
    {
        while(!feof($fd))
            print fread($fd, 1024);
            fclose($fd);
    }
    ob_flush();
    
    return true;
}


if (substr(php_uname(), 0, 7) == "Windows") {
    $uploads_path = WT_PATH_ROOT . '\\uploads\\';
} else {
    $uploads_path = WT_PATH_ROOT . '//uploads//';
}

$path = GetParam('p');
$filename = GetParam('f');

$headers = array('Content-Disposition'=>"attachment; filename=\"".$filename."\"");
wt_download($uploads_path.$path, $headers);
exit;