<?php
error_reporting(E_ALL);
ini_set("log_errors", 1);
ini_set("error_log", "/php-error.log");
// ini_set('display_errors', 0);
// ini_set('display_errors', 1);

include_once './db.php';
include_once './tool.php';
session_cache_expire(144000);
session_start();
$r = [];
if (key_exists('type', $_POST)) {
    $type = $_POST['type'];
} else {
    $type = '';
}
if (key_exists('name', $_POST)) {
    $name = $_POST['name'];
} else {
    $name = '';
}
if (key_exists('bag', $_POST)) {
    $bag = $_POST['bag'];
} else {
    $bag = [];
}

if (key_exists('id', $_SESSION)) {
    $id = $_SESSION['id'];
} else {
    $id = '';
}

//////////////////////TEST INITER//////////////////////////

$settings = json_decode(file_get_contents("./settings.json"), true);

$dbHost = $settings['db_host'];
$dbId = $settings['db_id'];
$dbPassword = $settings['db_password'];
$dbName = $settings['db_name'];

$db_user = new DB($dbHost, $dbId, $dbPassword, $dbName, 'labor_user', false);
$db_format = new DB($dbHost, $dbId, $dbPassword, $dbName, 'labor_format', false);
$db_chat = new DB($dbHost, $dbId, $dbPassword, $dbName, 'labor_chat', false);
$db_receipt = new DB($dbHost, $dbId, $dbPassword, $dbName, 'labor_receipt', false);
$db_setting = new DB($dbHost, $dbId, $dbPassword, $dbName, 'labor_setting', true);
$db_general = new DB($dbHost, $dbId, $dbPassword, $dbName, 'labor_general', true);
$general = $db_general->GetOrigin();

if (!key_exists('init', $general)) {
    $db_setting->SetOrigin([
        'unvalid_formats' => ["php"],
        'file_max_size' => 20,
        'remove_accept_cancel_files' => 0,
        'receipt_manage_update_interval' => 60,
        'notice' => "",
    ]);
    $db_general->SetOrigin([
        'format_index' => 0,
        'receipt_index' => 0,
        'init' => 1,
    ]);
}
//////////////////////TEST INITER//////////////////////////

$r['success'] = false;
$r['result'] = '';
// $id = 'admin';

if (!$type) {
    if (count($_FILES) != 0) {
        include_once './ajax-form.php';
    } else {
        $r['result'] = 'no type';
    }
} else {
    switch ($type) {
        case 'user':
            include_once './ajax-user.php';
            break;
        case 'format':
            include_once './ajax-format.php';
            break;
        case 'chat':
            include_once './ajax-chat.php';
            break;
        case 'receipt':
            include_once './ajax-receipt.php';
            break;
        case 'setting':
            include_once './ajax-setting.php';
            break;
        case 'data':
            include_once './ajax-data.php';
            break;
        case 'other':
            include_once './ajax-other.php';
            break;
        default:
            $r['result'] = 'no_type_mathing';
            break;
    }
}

echo json_encode($r, JSON_UNESCAPED_UNICODE);
