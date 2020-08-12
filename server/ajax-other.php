<?php
switch ($name) {
    case 'get_all':
        $result = [];
        if ($id == 'admin') {
            // $result['chat'] = $db_chat->GetAll();
            $result['users'] = $db_user->GetAll();
            $result['receipts'] = $db_receipt->GetAll();
            $result['settings'] = $db_setting->GetOrigin();
        } else {
            $result['users'] = [
                $id => $db_user->Get($id),
            ];
            $receipts = [];
            foreach ($result['user']['신청서'] as $index) {
                $receipts[$index] = $db_receipt->Get($index);
            }
            $result['receipts'] = $receipts;
        }
        $result['id'] = $id;
        $result['formats'] = $db_format->GetAll();

        $r['result'] = $result;
        $r['success'] = true;

        break;
    case 'default':
        $r['result'] = 'no_name_mathing';
}
