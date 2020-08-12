<?php

$path = "./uploads/";

$general = $db_setting->GetOrigin();

$unvalid_formats = $general['unvalid_formats'];
$max_size = $general['file_max_size'] * 1000 * 1000;
$result = [];
if (count($_FILES) == 0) {
    $r['result'][] = 'no_file';
    $r['result'][] = $type;
    $r['result'][] = $name;
    $r['result'][] = $bag;
}
$count = 0;
foreach ($_FILES as $key => $file) {
    $count++;
    $name = $file['name'];
    $size = $file['size'];
    $tmp = $file['tmp_name'];

    list($txt, $ext) = explode(".", $name);

    if (!strlen($name)) {
        $r['result'] = 'name';
        break;
    }

    if (in_array($ext, $unvalid_formats)) {
        $r['result'] = 'format';
        break;
    }

    if ($size > $max_size) {
        $r['result'] = 'size';
        break;
    }
    // $div = rand(0, 999);
    $div = $count;
    $txt = mh_sanitize_koran_chars($txt);
    $file_name = "$txt $div.$ext";
    $entire_path = $path . $file_name;
    if (!move_uploaded_file($tmp, $entire_path)) {
        $r['result'] = 'upload';
        break;
    }
    $result[$key] = $entire_path;
    $r['success'] = true;
}

if ($r['success']) {
    $r['result'] = $result;
}
