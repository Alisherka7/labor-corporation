<?php

if ($id == 'admin') {
    switch ($name) {
        // ADMIN

        case 'remove':
            $db_user->Remove($bag['아이디']);
            $r['success'] = true;
            break;

        case 'get_all':
            $r['result'] = $db_user->GetAll();
            $r['success'] = true;
            break;

    }

}

switch ($name) {
    case 'id_check':
        if ($db_user->Get($bag['아이디'])) {
            $r['result'] = false;
        } else {
            $r['success'] = true;
        }
        break;

    case 'add':
        $bag['관리회사'] = [];
        $bag['신청서'] = [];
        $bag['메타'] = [];
        $bag['메타']['rooms'] = [];
        $bag['메타']['avatar'] = 0;
        if ($db_user->Add($bag['아이디'])) {
            $db_user->Set($bag['아이디'], $bag);
            $r['success'] = true;
        } else {
            $r['result'] = 'id_overlap';
        }
        // 아이디, 비밀번호가 bag에 포함되어있음.
        break;
    // MEMBER
    case 'login':
        $result = $db_user->Get($bag['아이디']);
        if ($result) {
            if ($result['비밀번호'] == $bag['비밀번호']) {
                $_SESSION['id'] = $result['아이디'];
                $r['success'] = true;
            } else {
                $r['result'] = 'password';
            }
        } else {
            $r['result'] = 'id';
        }

        break;
    case 'logout':
        $_SESSION['id'] = '';
        $r['success'] = true;
        break;
    case 'get':
        $r['result'] = $db_user->Get($id);
        $r['success'] = true;
        break;

    case 'modify_privacy':
        $privacys = $bag['privacys'];

        $datas = $db_user->Get($id);
        $unchangeables = ['아이디', '비밀번호', '관리회사'];
        foreach ($datas as $key => $data) {
            if (!in_array($key, $unchangeables)) {
                if (key_exists($key, $privacys)) {
                    $datas[$key] = $privacys[$key];
                }
            }
        }

        $db_user->Set($id, $datas);
        $r['success'] = true;
        break;

    case 'modify_password':
        $datas = $db_user->Get($id);
        $try_current_password = $bag['비밀번호'];
        $try_new_password = $bag['새비밀번호'];
        $correct_password = $datas['비밀번호'];

        if ($try_current_password === $correct_password) {
            $datas['비밀번호'] = $try_new_password;

            $db_user->Set($id, $datas);
            $r['success'] = true;

        } else {
            $r['result'] = 'unmatched';
        }

        break;
    case 'add_cm':
        $try_company_datas = $bag['datas'];
        $try_company_name = $try_company_datas['회사명'];

        $datas = $db_user->Get($id);
        if (key_exists($try_company_name, $datas['관리회사'])) {
            $r['result'] = 'exist';
        } else {
            $datas['관리회사'][$try_company_name] = $try_company_datas;
            $db_user->Set($id, $datas);
            $r['success'] = true;
        }

        break;
    case 'remove_cm':
        $try_company_name = $bag['company_name'];
        $datas = $db_user->Get($id);
        if (key_exists($try_company_name, $datas['관리회사'])) {
            unset($datas['관리회사'][$try_company_name]);
            $db_user->Set($id, $datas);
            $r['success'] = true;
        } else {
            $r['result'] = 'not_exist';
        }
        break;

    case 'set_meta':
        $metaName = $bag['name'];
        $metaValue = $bag['value'];
        $user = $db_user->Get($id);

        $user['메타'][$metaName] = $metaValue;

        $db_user->Set($id, $user);
        $r['success'] = true;
        break;

    case 'get_meta':
        $metaName = $bag['name'];
        $user = $db_user->Get($id);
        if (key_exists($metaName, $user['메타'])) {
            $r['result'] = $user['메타'][$metaName];
            $r['success'] = true;
        }
        break;

    case 'get_user_avatar':
        $id = $bag['id'];
        $user = $db_user->Get($id);
        $r['result'] = $user['메타']['avatar'];
        $r['success'] = true;
        break;
    default:
        break;

}
