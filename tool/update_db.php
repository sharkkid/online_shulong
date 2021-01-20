<?php
require_once dirname(__FILE__) . './../_setting.php';

function getFileContent($path) {
    if(file_exists($path)) {
        $handle = fopen($path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                $sqls[] = $line;
            }
            
            fclose($handle);
            $position_data = file_get_contents($path);
        }
    } else {
        $sqls = array();
    }
    
    return $sqls;
}

$path = empty($_GET['p']) ? '' : $_GET['p'];
if(empty($path)) {
    die('Path not found!');
}

if(!file_exists($path)) {
    die('Update completed!');
}

$sqls = getFileContent($path);
$conn = getDB();
try {
    $conn->autocommit(false);
    foreach($sqls as $k=>$v) {
        if(empty($v) || $v=='' || strlen($v)==0) {
            continue;
        }
        
        if($conn->query($v)) {
            echo 'Update ' . ($k+1) . '/' . count($sqls) . ' success<br>';
            file_put_contents('update_db_log.txt', date('Y-m-d H:i:s') . ' success, sql: ' . $v, FILE_APPEND);
        } else {
            file_put_contents('update_db_log.txt', date('Y-m-d H:i:s') . ' failed,  sql: ' . $v . ' error: ' . mysqli_error($conn), FILE_APPEND);
            throw new Exception('error');
        }
    }
    $conn->commit();
    echo 'Update completed'.PHP_EOL;
    file_put_contents('update_db_log.txt', '--------------------'.PHP_EOL, FILE_APPEND);
    unlink($path);
} catch (Exception $e) {
    $conn->rollback();
    echo 'Update failed'.PHP_EOL;
    file_put_contents('update_db_log.txt', '--------------------'.PHP_EOL, FILE_APPEND);
}
$conn->close();
?>