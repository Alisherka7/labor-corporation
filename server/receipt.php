<?php
include_once './db.php';

$r = [];
$r['success'] = false;
$r['result'] = '';
$db_receipt = new DB('localhost', 'root', 'autoset', 'vision', 'vision_receipt', false);

$getKeys = ['key', 'type'];
foreach ($getKeys as $getKey) {
    if (key_exists($getKey, $_GET) == false) {
        $r['result'] = $getKey . '이(가) 없습니다';
        break;
    }
    if ($getKey == 'key') {
        if ($_GET['key'] != 'LVPAJpmLnCREv73dYAB2DfuzvWsVJQ4K') {
            $r['result'] = "키값이 올바르지 않습니다.";
            break;
        }
    }
    if ($getKey == 'type') { 
        if ($_GET['type'] == 'index' && key_exists('index', $_GET) == false) {
            $r['result'] = '인덱스가 없습니다.';
            break;
        }
    }

}

if ($r['result'] == '') {
    $type = $_GET['type'];
    switch ($type) {
        case 'all':
            $r['result'] = $db_receipt->GetAll();
            $r['success'] = true;
            break;
        case 'index':
            $index = $_GET['index'];
            $geted = $db_receipt->Get($index);
            // if ($geted == null) {
            // $r['result'] = '인덱스에 맞는 정보가 존재하지 않습니다.';
            // } else {
            $r['result'] = $geted;
            $r['success'] = true;
            // }
            break;
    }
}

echo json_encode($r, JSON_UNESCAPED_UNICODE);
