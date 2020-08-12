<?php

switch ($name) {
    case 'get':
        include_once './lib/excel.php';

        $name = $bag['name'];
        if ($name == 'corporations') {
            $r['success'] = true;
            $r['result'] = GetExcelAbc('./resources/corporations.xls');
        }
        break;

}
