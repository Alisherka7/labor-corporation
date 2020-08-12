<?php

/**
 * 키
 * - 신청번호
 *
 * 신청서데이터
 * - 비고
 * - 회사명
 * - 회사주소
 * - ...
 *
 * 메타데이터
 * - index
 * - apply_time
 * - id
 * - receipt_name
 * - state
 * - ...
 */
class ReceiptState
{
    public static $APPLY = 'apply';
    public static $REFUSE = 'refuse';
    public static $CANCEL = 'cancel';
    public static $ACCEPT = 'accept';
}

if ($name == 'state_accept' || $name == 'state_apply' || $name == 'state_refuse' || $name == 'state_cancel') {
    if (!($name != 'state_cancel' && $id != 'admin')) {
        $index = $bag['index'];
        $now_state = $bag['now_state'];
        $receipt = $db_receipt->Get($index);
        if ($receipt['meta']['state'] == $now_state) {
            $toState = '';
            if ($name == 'state_accept') {
                $toState = ReceiptState::$ACCEPT;
            } else if ($name == 'state_apply') {
                $toState = ReceiptState::$APPLY;
            } else if ($name == 'state_refuse') {
                $toState = ReceiptState::$REFUSE;
            } else if ($name == 'state_cancel') {
                $toState = ReceiptState::$CANCEL;
            }

            $receipt['meta']['state'] = $toState;
            $r['success'] = true;
            $db_receipt->Set($index, $receipt);
        } else {
            $r['result'] = 'diffrent now state';
        }
    }
}

if ($id == 'admin') {

    switch ($name) {
        case 'get_all':
            $r['result'] = $db_receipt->GetAll();
            $r['success'] = true;
            break;
        case 'excel':
            include_once './lib/excel.php';
            $r['result'] = GetExcel($bag);
            $r['success'] = true;
            break;
        case 'cover':
            include_once './lib/excel.php';

            $r['result'] = GetCover($bag);
            $r['success'] = true;
            break;
        case 'remove_all_receipts':
            $allReceipt = $db_receipt->GetAll();
            $allUsers = $db_user->GetAll();

            $directory = "./receipt-uploads/";
            $handle = opendir($directory);
            while ($file = readdir($handle)) {
                @unlink($directory . $file);
            }
            closedir($handle);

            foreach ($allReceipt as $receipt) {
                // foreach ($receipt['meta']['file_fields'] as $field) {
                //     foreach ($receipt['data'][$field] as $path) {
                //         unlink($path);
                //     }
                // }
                $db_receipt->Remove($receipt['meta']['index']);
            }

            foreach ($allUsers as $key => $user) {
                $allUsers[$key]['신청서'] = [];
                $db_user->Set($key, $allUsers[$key]);
            }
            $r['success'] = true;
            break;
        case 'remove_accept_cancel_files':
            $settings = $db_setting->GetOrigin();
            if ($settings['remove_accept_cancel_files'] == 1) {
                
                $allReceipt = $db_receipt->GetAll();

                foreach ($allReceipt as $receipt) {
                    if ($receipt['meta']['removed'] == 1) {
                        continue;
                    }

                    if ($receipt['meta']['state'] == ReceiptState::$ACCEPT || $receipt['meta']['state'] == ReceiptState::$CANCEL) {
                        $receipt['meta']['removed'] = 1;
                        $db_receipt->Set($receipt['meta']['index'], $receipt);
                        // var_dump($receipt['data']);
                        foreach ($receipt['meta']['file_fields'] as $field) {
                            if ($field == '') {
                                continue;
                            }
                            foreach ($receipt['data'][$field] as $path) {
                                @unlink($path);
                            }
                        }
                    }

                }
            }

            $r['success'] = true;
            break;
        case 'set_message':
            $index = $bag['index'];
            $receipt = $db_receipt->Get($index);
            $receipt['meta']['message'] = $bag['message'];
            $r['success'] = true;
            $db_receipt->Set($index, $receipt);
            break;
    }

}

switch ($name) {

    case 'add':
        // 신청서의 키값은 신청인덱스 (format 과 같이 상승한다.)
        /** GENERAL에서 INDEX 가져온 후 +1 해서 셋팅한다.. */
        $general = $db_general->GetOrigin();
        $index = $general['receipt_index'];
        $general['receipt_index'] = $index + 1;
        $db_general->SetOrigin($general);

        $receipt_data = $bag['data'];
        $receipt_name = $bag['receipt_name'];
        $file_fields = $bag['file_fields'];
        $manage_company = $bag['manage_company'];
        /** uploads 에서 파일 이동 */
        $to_dir = './receipt-uploads/';
        foreach ($file_fields as $file_field) {
            if (key_exists($file_field, $receipt_data)) {
                $new_files = [];
                foreach ($receipt_data[$file_field] as $key => $origin_path) {
                    list($txt, $ext) = explode(".", basename($origin_path));
                    $to_basename = "$txt $index.$ext";
                    // error_log($to_basename, 0, "BASENAME");
                    // error_log($origin_path, 0, "ORIGINPATH");
                    $to_path = $to_dir . $to_basename;
                    $file_origin = fopen($origin_path, 'r');
                    $file_to = fopen($to_path, 'w');
                    $read = fread($file_origin, filesize($origin_path));
                    fwrite($file_to, $read);
                    fclose($file_origin);
                    fclose($file_to);
                    unlink($origin_path);

                    $new_files[] = $to_path;
                }
                $receipt_data[$file_field] = $new_files;
            }
        }
        $receipt_meta = [
            'index' => $index,
            'apply_time' => time(),
            'id' => $id,
            'receipt_name' => $receipt_name,
            'state' => ReceiptState::$APPLY,
            'message' => '',
            'file_fields' => $file_fields,
            'manage_company' => $manage_company,
            'removed' => 0,
        ];

        $receipt = [
            'data' => $receipt_data,
            'meta' => $receipt_meta,
        ];

        $db_receipt->Add($index);
        $db_receipt->Set($index, $receipt);

        $user = $db_user->Get($id);
        $user['신청서'][] = $index;
        $db_user->Set($id, $user);

        $r['success'] = true;
        $r['result'] = [
            'index' => $index,
        ];

        break;
    case 'get':

        $indexs = $bag['indexs'];
        $result = [];
        foreach ($indexs as $index) {

            $receipt = $db_receipt->Get($index);
            if ($id == 'admin' || $receipt['meta']['id'] == $id) {
                $result[$index] = $receipt;
            }
        }
        $r['success'] = true;
        $r['result'] = $result;

        break;
    case 'get_user':

        $receipts = [];
        $user = $db_user->Get($id);
        foreach ($user['신청서'] as $index) {
            $receipts[$index] = $db_receipt->Get($index);
        }
        $r['result'] = $receipts;
        $r['success'] = true;
        break;

}
/**
 * index,
 * managerContact
 * managerFUllName
 * corpFax
 * corpName
 * corpContact
 * pageCount
 * companyName
 * formName
 */
