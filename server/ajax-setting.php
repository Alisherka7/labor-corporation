<?php
if ($id != 'admin') {
    return;
}

switch ($name) {
    case 'set':
        $origin = $db_setting->GetOrigin();


        foreach($bag as $key=> $value){
            $origin[$key] = $bag[$key];
        }
        // foreach ($origin as $key => $value) {
        //     if (key_exists($key, $bag)) {
        //         $origin[$key] = $bag[$key];
        //     }
        // }
        $db_setting->SetOrigin($origin);
        $r['success'] = true;
        break;
    case 'get':
        $r['result'] = $db_setting->GetOrigin();
        $r['success'] = true;
        break;
    default:
        break;
}
