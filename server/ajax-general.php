<?php

switch ($name) {
    case 'get':
        $r['result'] = $db_general->GetOrigin();
        $r['success'] = true;
        break;
}
