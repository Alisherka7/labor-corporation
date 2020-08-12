<?php

if ($id == 'admin') {

    switch ($name) {
        case 'add':
            /** GENERAL에서 INDEX 가져온 후 +1 해서 셋팅한다.. */
            $general = $db_general->GetOrigin();
            $index = $general['format_index'];
            $general['format_index'] = $index + 1;
            $db_general->SetOrigin($general);

            /** FORMAT 게시판에 하나 추가한다. */
            $title = $bag['title'];
            $body = $bag['body'];
            $files = $bag['files'];
            $effect = $bag['effect'];

            $new_files = [];
            $to_dir = '/tmp/labor/format-uploads/';
            foreach ($files as $path) {

                $basename = basename($path);

                list($txt, $ext) = explode(".", $basename);
                $to_basename = "$txt $index.$ext";
                $to_path = $to_dir . $to_basename;

                $file_origin = fopen($path, 'r');
                $file_to = fopen($to_path, 'w');
                $read = fread($file_origin, filesize($path));
                fwrite($file_to, $read);
                fclose($file_origin);
                fclose($file_to);
                unlink($path);

                $new_files[] = $to_path;
            }

            $datas = [
                'index' => $index,
                'title' => $title,
                'body' => $body,
                'files' => $new_files,
                'effect' => $effect,
            ];

            $db_format->Add($index);
            $db_format->Set($index, $datas);

            $r['success'] = true;
            break;
    }
}
switch ($name) {
    case 'get':
        $r['result'] = $db_format->GetAll();
        $r['success'] = true;
        break;
    case 'remove':
        $index = $bag['index'];
        $format = $db_format->Get($index);
        foreach ($format['files'] as $path) {
            unlink($path);
        }
        $db_format->Remove($index);

        $r['success'] = true;
        break;
}
