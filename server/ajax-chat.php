<?php

switch ($name) {
    // 실시간 채팅 X
    // 문의사항 디자인( 실시간이 아닌 것 처럼 )
    case 'add':
        $room = $bag['room'];
        $talk = $bag['talk'];
        $chars = $bag['chars'];

        $db_chat->Add($room);
        $talks = $db_chat->Get($room);
        $talks[] = [
            'char' => $id,
            'talk' => $talk,
        ];
        $db_chat->Set($room, $talks);

        $r['success'] = true;
        $r['result'] = [
            'room' => $room,
        ];

        //////// OBJSERBER ////////
        // 받는 사람 (room) 에서의 메타 {rooms:{church:3}}를 + 1
        foreach ($chars as $char) {
            if ($char['self'] == 'false') {
                $oppoId = $char['id'];
                $oppoUser = $db_user->Get($oppoId);

                if (!key_exists($room, $oppoUser['메타']['rooms'])) {
                    $oppoUser['메타']['rooms'][$room] = 0;
                }
                $oppoUser['메타']['rooms'][$room] += 1;

                $db_user->Set($oppoId, $oppoUser);
            }
        }

        break;
    case 'get':
        $room = $bag['room'];
        $r['result'] = $db_chat->Get($room);
        $r['success'] = true;

        //////// OBJSERBER ////////
        // GET 하는 사람의 room 에서의 메타 0
        $user = $db_user->Get($id);
        $user['메타']['rooms'][$room] = 0;
        $db_user->Set($id, $user);

        break;
    case 'clear':

        $room = $bag['room'];
        $db_chat->Set($room, []);

        $r['success'] = true;
        $r['result'] = [
            'room' => $room,
        ];

        $user = $db_user->Get($id);
        $user['메타']['rooms'][$room] = 0;
        $db_user->Set($id, $user);
        break;
}
