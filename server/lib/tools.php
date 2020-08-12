<?php


/*
    업그레이드
*/

/// var_dump의 업그레이드 버전
function VarDump($data)
{

    echo "<pre>";
    var_dump($data);
    echo "</pre>";
}


/// fRead 업그레이드 버전
function BFRead($filePath)
{
    $fp = fopen($filePath, "r");
    
    $buffer = "";
    if (!$fp) {
        return false;
    }
    while (!feof($fp)) {
        $buffer .= fread($fp, 1024);
    }
    fclose($fp);

    return $buffer;
}

/// is_dir 업그레이드 버전
function IsDir($dir_name)
{
    return is_dir(iconv("UTF-8", "cp949", $dir_name));
}

/// is_file 업그레이드 버전
function IsFile($file_name)
{
    return is_file(iconv("UTF-8", "cp949", $file_name));
}










/*
    기능
*/



/// 0을 제거한 밀리초 가져오기
function GetMillisecond()
{
    $microTimes = explode(' ', microtime());
    $microTimes[0] = str_replace('.', '', $microTimes[0]);
    $microTimes[0] = substr($microTimes[0], 1, strlen($microTimes[0])-3);
    $millisecond = $microTimes[1] . $microTimes[0];

    return $millisecond;
}

/// 랜덤값을 포함한 밀리초 가져오기
// array key: millisecond, millisecond_with_rand 
function GetMillisecondArrayWithRand()
{
    $r = [];
    $millisecond = GetMillisecond();
    $randValue = rand(100, 999);
    $millisecond_with_rand = $millisecond . $randValue;

    $r["millisecond"] = $millisecond;
    $r["millisecondWithRand"] = $millisecond_with_rand;
    $r["rand"] = $randValue;
    return $r;
}

/// Byte를 크기에 따라 바꿔준다
function ByteConvert($bytes)
{
 
    $s = array('B', 'Kb', 'MB', 'GB', 'TB', 'PB');
 
    $e = floor(log($bytes)/log(1024));
 
     
 
    return sprintf('%.2f '.$s[$e], ($bytes/pow(1024, floor($e))));
}

/// 디렉토리 전체를 압축시킨다
function MakeZipByDirs($dir, $zipfile)
{
    $zip = new ZipArchive;
    $res = $zip->open($zipfile, ZipArchive::CREATE);
    if ($res === true) {
        _DirZip($zip, $dir);
        $zip->close();
    } else {
        return "에러 코드: ".$res;
    }
    return true;
}

/// MakeZipByDirs의 함수
$dirZipFuncOriDir = "";
function _DirZip($resource, $dir, $isFirst = true)
{
    global $dirZipFuncOriDir;

    $dir = str_replace("\\", "/", $dir);

    if ($dir[strlen($dir) - 1] == "/") {
        $dir = substr($dir, 0, strlen($dir) - 1);
    }
    if ($isFirst == true) {
        $dirZipFuncOriDir = $dir;
    }
    if (filetype($dir) === 'dir') {
        clearstatcache();

        if ($fp = @opendir($dir)) {
            while (false !== ($ftmp = readdir($fp))) {
                if (($ftmp !== ".") && ($ftmp !== "..") && ($ftmp !== "")) {
                    if ($isFirst) {
                        $newDir = "";
                    } else {
                        $newDir = str_replace($dirZipFuncOriDir . "/", "", $dir);
                    }
    
                    if (filetype($dir.'/'.$ftmp) === 'dir') {
                        clearstatcache();

                        // 디렉토리이면 생성하기
                        $resource->addEmptyDir($newDir.'/'.$ftmp);
                        set_time_limit(0);

                        _DirZip($resource, $dir .'/'.$ftmp, false);
                    } else {
                        // 파일이면 파일 압축하기
                        $resource->addFile($dir . "/".$ftmp, $newDir.'/'.$ftmp);
                    }
                }
            }
        }
        if (is_resource($fp)) {
            closedir($fp);
        }
    } else {
       // 파일이면 파일 압축하기
        $resource->addFile($dir);
    }
}

/// JSON 인지 확인하는 함수
function IsJson($string)
{
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}




/*
    파일 및 폴더
*/



/// 확장자만 가져오기
function GetExtension($path)
{
    $values = explode('.', $path);

    return $values[count($values)-1];
}

/// 파일 다운로드
function FileDownload($filePath)
{
    if (!IsFile($filePath)) {
        return false;
    }

    $filesize = filesize($filePath);
    $path_parts = pathinfo($filePath);
    $filename = $path_parts['basename'];
    $extension = $path_parts['extension'];

    header("Pragma: public");
    header("Expires: 0");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: $filesize");

    ob_clean();
    flush();
    readfile($filePath);
    return true;
}

/// FILE PATH TO URL PATH
function FilePathToUrlPath($filePath)
{
    $filePath =str_replace('\\', '/', $filePath);
    return str_replace($_SERVER['DOCUMENT_ROOT'], '', $filePath);
}

/// URL PATH TO FILE PATH
function UrlPathToFilePath($filePath)
{
    $r = $_SERVER['DOCUMENT_ROOT'] . $filePath;
    return str_replace('\\', '/', $r);
}

/// 파일 옮기기
function MoveFile($src, $dest)
{
    $r = false;
    @chmod($src, 0707);
    if (copy($src, $dest)) {
        @unlink($src);
        $r = true;
    }
    @chmod($src, 0606);

    return $r;
}

/// 폴더 안에 있는 파일들을 복사해서 옮긴다
function CopyDirectory($src_dir, $dest_dir)
{
    if ($src_dir == $dest_dir) {
        return false;
    }
 
    if (!IsDir($src_dir)) {
        return false;
    }
 
    if (!IsDir($dest_dir)) {
        @mkdir($dest_dir, 0707);
        @chmod($dest_dir, 0707);
    }
 
    $dir = opendir($src_dir);
    while (false !== ($filename = readdir($dir))) {
        if ($filename == "." || $filename == "..") {
            continue;
        }
 
        $files[] = $filename;
    }
 
    for ($i=0; $i<count($files); $i++) {
        $src_file = $src_dir.'/'.$files[$i];
        $dest_file = $dest_dir.'/'.$files[$i];
        if (IsFile($src_file)) {
            copy($src_file, $dest_file);
            @chmod($dest_file, 0606);
        }
    }
}

/// 폴더 삭제
function RmDirOk($dir)
{
    if (!is_dir($dir)) {
        return false;
    }
    $dirs = dir($dir);
    while (false !== ($entry = $dirs->read())) {
        if (($entry != '.') && ($entry != '..')) {
            if (IsDir($dir.'/'.$entry)) {
                RmDirOk($dir.'/'.$entry);
            } else {
                  @unlink($dir.'/'.$entry);
            }
        }
    }
    $dirs->close();
    @rmdir($dir);
}

/// 빈 디렉토리 만들기
function MakeEmptyDir($dir)
{
    RmDirOk($dir);
    if (!is_dir($dir)) {
        @mkdir($dir);
    }
}

/// 한 경로 위로 올려준다
function PathUp($filepath, $reverse = false)
{
    $filepath = str_replace("\\", "/", $filepath);
    if (!$reverse) {
        return dirname($filepath);
    } else {
        $pieces = explode("/", $filepath);
        $newPath = "";
        $first = true;
        foreach ($pieces as $piece) {
            if ($piece != "") {
                if ($first) {
                    $first =false;
                    continue;
                }
                $newPath .= $piece . "/";
            }
        }
        return substr($newPath, 0, -1);
    }
    
    if ($filepath[strlen($filepath)-1] == "/") {
        $filepath[strlen($filepath)-1] = "";
    }
    $isFile = IsFile($filePath);

    $paths = explode("/", $filepath);

    $newPath = "";
    if ($reverse) {
        for ($i = 0; $i < count($paths); $i++) {
        }
    } else {
        for ($i = 0; $i < count($paths)-1; $i++) {
            $newPath .= $paths[$i];
            $newPath .= "/";
        }
    }

    return $newPath;
}

/// 디렉토리에서 정보들을 가져온다
// dir, file, dircnt, filecnt
function GetDirDataOnDir($dir)
{
    if (!is_dir($dir)) {
        return false;
    }
    $entrys = array(); // 폴더내의 정보를 저장하기 위한 배열
    $dirs = dir($dir); // 오픈하기
    $entrys['dir'] = [];
    $entrys['file'] = [];
    while (false !== ($entry = $dirs->read())) { // 읽기
        
        if (($entry != '.') && ($entry != '..')) {
            if (is_dir($dir.'/'.$entry)) { // 폴더이면
                $entrys['dir'][] = iconv("euc-kr", "utf-8", $entry);
            } else { // 파일이면
                $entrys['file'][] = iconv("euc-kr", "utf-8", $entry);
            }
        }
    }
    $dirs->close(); // 닫기
   
    $dircnt = count($entrys['dir']); // 폴더 수
    $filecnt = count($entrys['file']); // 파일 수
   
    $entrys["dircnt"] = $dircnt;
    $entrys["filecnt"] = $filecnt;

    return $entrys;
}

/// 폴더에서 중복이 없는 파일 이름을 하나 만든다
function GetNameNoOverlap($filePath)
{
    $pi = pathinfo($filePath);
    $fileBaseName = $pi["basename"];
    $fileExtension = $pi["extension"];
    $fileName = $pi["filename"];
    $folderPath = str_replace($fileBaseName, "", $filePath);


    $fileNames = GetDirDataOnDir($folderPath)["file"];

    $FileCounter = 0;
    $FinalFilename = "";
    while (file_exists( $folderPath . $FinalFilename )) {
        $FinalFilename = $fileName . '_' . $FileCounter++ . '.' . $fileExtension;
    }

    return $folderPath . $FinalFilename;
}

/// 폴더 전체용량
function DirSize($dir, $debug = false, &$fileCount = 0, &$folderCount = 0)
{
     
    if (!is_dir($dir)) {
        return false;
    }
     
    if (!preg_match("`/$`", $dir)) {
        $dir .= '/';
    }
     
    $get_size = 0;
     
    $d = dir($dir);
    while (false !== ($entry = $d->read())) {
        if (substr($entry, 0, 1) == '.') {
            continue;
        }
         
        if (IsFile($dir . $entry)) {
            $fileCount++;
            $get_size += filesize($dir . $entry);
            if ($debug == true) {
                echo $dir . $entry . ' ' . filesize($dir . $entry) . "<br>\n";
            }
        } elseif (is_dir($dir . $entry)) {
            $folderCount++;
            $get_size += dirsize($dir . $entry, $debug, $fileCount, $folderCount);
        } else {
            continue;
        }
    }
    $d->close();
     
    return $get_size;
}

/// JSON을 파일 경로에서 읽어서 배열로 반환한다
function GetJsonArrayByFile($filePath)
{
    if (filesize($filePath) == 0) {
        return false;
    }
    $tempReadData = BFRead($filePath);
    $read_data = json_decode($tempReadData, true);
    if (!is_array($read_data)) {
        Note1("파일 읽기 실패\n" . $tempReadData);
        return false;
    }


    return $read_data;
}

/// 파일 경로와 데이터 배열을 주면 JSON으로 저장한다
function SetJsonArrayToFile($filePath, $data)
{
    $f = fopen($filePath, 'w');
    fwrite($f, json_encode($data));
    fclose($f);
}








/*
    허브
*/


///허브파일로그에 로깅한다
function LogHubFileLog($fileBaseName)
{
    $hubPath = S_UPLOADS_HUB;
    $logPath = S_P_HUBFILELOG;

    
    $readData = GetJsonArrayByFile($logPath);
    $dateWithRand = GetMillisecondArrayWithRand()["millisecondWithRand"];
    $readData[$dateWithRand] = $fileBaseName;

    SetJsonArrayToFile($logPath, $readData);
}

/// 허브파일 로그에서 지난 시간에 따라 삭제한다
function ClearHubFileLog($diffTime)
{
    $hubPath = S_UPLOADS_HUB;
    $logPath = S_P_HUBFILELOG;

    $readData = GetJsonArrayByFile($logPath);
    
    if (!is_array($readData)) {
        // Note1("readData 가 배열이 아닙니다 [" . $readData . "]");
        return false;
    }
    $currentDateWithRand = GetMillisecondArrayWithRand()["millisecondWithRand"];
    

    $diffTime .= "000000";

    $indexs = [];
    $newReadData = [];
    foreach ($readData as $dateWtihRand => $fileBaseName) {
        if ($currentDateWithRand - $dateWtihRand > $diffTime) {
            @unlink($hubPath . $fileBaseName);
        } else {
            $newReadData[$dateWtihRand] = $fileBaseName;
        }
    }

    SetJsonArrayToFile($logPath, $newReadData);
}

/// 준비와 함께 허브내의 중복 없는 주소를 가져온다
function GetHubPathNameWithReady($fileBaseName)
{
    ClearHubFileLog(60000);
    
    $path = S_UPLOADS_HUB . $fileBaseName;
    $newPath = GetNameNoOverlap($path);
    LogHubFileLog(basename($newPath));
    
    return $newPath;
}

/// 준비와 함께 복사한다
// GetHubPathNameWithReady 를 포함한다
function CopyToHubWithReady($oriPath)
{
    if (!IsFile($oriPath)) {
        Note1("oriPath가 파일이 아닙니다." . "[$oriPath]");
        return false;
    }
    $fileBaseName = basename($oriPath);
    $filePath = GetHubPathNameWithReady($fileBaseName);

    if (!copy($oriPath, $filePath)) {
        return false;
    }

    return $filePath;
}


 




/*
    구분자
*/



/// 배열 속에서 구분자로 값을 가져온다
function GetValueBySeparator($array, $separator)
{

    $result = false;
    foreach ($array as $key => $value) {
        if (strpos($key, $separator)) {
            $result = $value;
            break;
        }
    }
    return $result;
}



/// 구분자를 제외한 값을 가져온다
function GetWithoutSeparator($data)
{
    return preg_replace("/__.*\__/", "", $data);
}

/// 구분자는 그대로 두고 키만 바꾼다
function ChangeKey($key, $change_key)
{
    return GetOnlySeparatorWithValue($key) . $change_key;
}

/// 괄호를 포함한 구분자만 가져온다
function GetOnlySeparatorWithValue($data)
{
    preg_match("/\__[^()]+\__/", $data, $c);
    if (isset($c[0])) {
        return $c[0];
    } else {
        return "";
    }
}








/*
    엑셀
*/



/// Excel Index FROM Excel English
// 1부터 시작한다
// EE == Excel English
// EI == Excel Index
function GetEIFromEE($eng)
{
    if (ctype_alpha($eng) == false) {
        Note1("영문이 아닙니다. [$eng]");
        return;
    }
    if (strlen($eng) > 3) {
        Note1("영문이 너무 깁니다. [$eng]");
        return;
    } elseif (strlen($eng) <= 0) {
        Note1("영문이 너무 짧습니다. [$eng]");
        return;
    }
    $alphabet = 'A';
    
    for ($i = 0; $i < 18278; $i++) {
        if ($alphabet == $eng) {
            break;
        }
        $alphabet++;
    }
    
    return $i + 1;
}

/// Excel English From Excel Index
// EE == Excel English
// EI == Excel Index
function GetEEFromEI($index)
{
    if (is_numeric($index) == false) {
        Note1("숫자가 아닙니다. [$index]");
        return;
    }
    if ($index < 1) {
        Note1("1 이상이여야 합니다. [$index]");
        var_dump(debug_backtrace());
        return false;
    }
    $alphabet = 'A';

    for ($i = 0; $i < $index - 1; $i++) {
        $alphabet++;
    }

    return $alphabet;
}








/*
    워드프레스
*/

function WPGetUserData($userId, $userData)
{
    if ($userId == 0) {
        return false;
    }
    $userInfo = get_userdata($userId);
    return $userInfo->$userData;
}

/// 유저의 이메일을 가져온다
function WPGetUserEmail($userId)
{
    return WPGetUserData($userId, "user_email");
}

/// 업로드 디렉토리에 플러그인 이름에 따라 폴더를 만든다
function WPMakeFolderInUploadDir($pluginName)
{
    $upload_dir = wp_upload_dir();
    $cusotm_dir = $upload_dir['basedir'] . '/' . $pluginName;
    if (! file_exists($cusotm_dir )) {
        wp_mkdir_p($cusotm_dir);
        @chmod($cusotm_dir, 0606);
    }
}

/// 플러그인 이름에 따라 폴더 경로를 가져온다
function WPGetUploadDirPath($pluginName)
{
    $upload_dir = wp_upload_dir();
    return $upload_dir['basedir'] . '/' . $pluginName;
}

/// 유저의 메타데이터를 가져온다
function WPGetUserMeta($userId, $key)
{
    return get_user_meta($userId, $key, true);
}
/// 유저의 메타데이터를 설정한다
// 없으면 생성한다
function WPSetUserMeta($userId, $metaValue, $data)
{
    return update_user_meta($userId, $metaValue, $data);
}

/// 유저의 메타데이터를 배열로 추가시키거나 배열을 추가시킨다
function WPAddArrayUserMeta($userId, $metaValue, $addValue, $isMany = false, $overlap = true)
{
    if ($addValue == "") {
        return false;
    }

    $metaDatas = WPGetUserMeta($userId, $metaValue);
    if (!is_array($metaDatas)) {
        $metaDatas = [];
    }
            
    if (!$isMany) {
        $newMetaDatas = AddDataAtArray($metaDatas, $addValue, $overlap);
    } else {
        $newMetaDatas = array_merge($metaDatas, $addValue);
    }
    WPSetUserMeta($userId, $metaValue, $newMetaDatas);
    return true;
}

/// 유저의 메타데이터를 배열로 삭제한다
function WPSubArrayUserMeta($userId, $metaValue, $subValue)
{
    if ($subValue == "") {
        return false;
    }
    $metaDatas = WPGetUserMeta($userId, $metaValue);
    
    if (!is_array($metaDatas)) {
        $metaDatas = [];
    }
    $newMetaDatas = SubDataAtArray($metaDatas, $subValue);
    update_user_meta($userId, $metaValue, $newMetaDatas);
    return true;
}

/// 유저 권한 확인
function WPUserAuth($admin, $login = true)
{
    $isLogin = is_user_logged_in();
    $isAdmin = current_user_can( 'edit_pages' );
    
    if ($login) {
        if ($admin) {
            if ($isLogin && $isAdmin) {
                return true;
            }
            return false;
        }
        if ($isLogin) {
            return true;
        }
        return false;
    }
}





/*
    배열
*/


/// 배열에 데이터를 추가한다
// 배열이 아니면 새로운 배열을 만들어서 반환한다
function AddDataAtArray($array, $data, $overlap = true)
{
    if (!is_array($array)) {
        Note1("배열이 아닙니다\n" . $array);
        return false;
    }
    if ($overlap) {
        if (!in_array($data, $array)) {
            $array[] = $data;
        }
    } else {
        $array[] = $data;
    }
    return $array;
}

/// 배열에서 입력된 데이터값을 뺀다
function SubDataAtArray($array, $data)
{
    
    $r = [];
    foreach ($array as $key => $value) {
        if ($data != $value) {
            $r[] = $value;
        }
    }
    return $r;
}

/// 딕셔너리 키값을 배열 밸류값에서 뺀다
function SubDicKeyByArrayValue($dics, $subKeys)
{
    if (!is_array($dics) || !is_array($subKeys)) {
        Note1("배열이 아닙니다");
    }

    $newSubKeys = [];
    foreach ($subKeys as $key) {
        $newSubKeys[$key] = "";
    }

    $r = array_diff_key($dics, $newSubKeys);

    return $r;
}

/// 딕셔너리 키값을 배열 밸류값에서 가져온다
function GetDicKeyByArrayValue($dics, $subKeys, $subSeparator = true)
{
    if (!is_array($dics) || !is_array($subKeys)) {
        Note1("배열이 아닙니다");
    }


    $r = [];
    foreach ($dics as $key => $value) {
        foreach ($subKeys as $subKey) {
            if ($subSeparator) {
                $subKey = GetWithoutSeparator($subKey);
                $key = GetWithoutSeparator($key);
            }
            if ($key == $subKey) {
                $r[$key] = $value;
            }
        }
    }

    return $r;
}







/*
    보안
*/
/// '<' 와 '>' 를 변환한다
function XSSBlock($str)
{
    $newStr = "";
    for ($i = 0; $i < strlen($str); $i++) {
        $addCh = $str[$i];
        if ($addCh == "<") {
            $addCh = "&lt";
        } elseif ($str == ">") {
            $addCh = "&gt";
        }
        $newStr .= $addCh;
    }
    return $newStr;
}





/*
    노트
*/



/// 에러 발생시의 노트
function Note1($message)
{
    _LeaveNote("Note_1.txt", $message, false);
}

/// 경고 발생시의 노트
function Note2($message)
{
    _LeaveNote("Note_2.txt", $message, false);
}

/// 알림 발생시의 노트
function Note3($message)
{
    _LeaveNote("Note_3.txt", $message, false);
}

/// echo를 하는 노트 ( 저장 안함 )
function NoteEcho($message)
{
    _LeaveNote("", $message, true);
}

/// 노트 함수
function _LeaveNote($noteBaseName, $message, $doEcho)
{

    if (is_array($message)) {
        $message = var_export($message, true);
    }
    $backTraceMessage = _GetPrettyBackTrace();
    
    $resultMessage = "";
    
    if ($doEcho) {
        $resultMessage .=  "<table border='1px' style='border-collapse: collapse;'>";
        $resultMessage .=  "<tr><td>";
        $resultMessage .=  "에러 메세지" ;
        $resultMessage .=  "<br/>";
        $resultMessage .=  "위치: " . $backTraceMessage;
        $resultMessage .=  "<br/>";
        $resultMessage .=  "메세지: " . $message;
        $resultMessage .=  "</tr></td><br/></table>";
        echo $resultMessage;
    } else {
        $resultMessage .=  "\n";
        $resultMessage .=  "위치: " . $backTraceMessage;
        $resultMessage .=  "\n";
        $resultMessage .=  "메세지: " . $message;
        $resultMessage .=  "\n";
        $logPath = S_NOTE . $noteBaseName;
        $time = date( 'Y/m/d H:i:s', time() );
        error_log("[".$time."]" . $resultMessage . "\n", 3, $logPath);
    }
}

/// 백트레이스를 심플하게 바꿔준다
function _GetPrettyBackTrace()
{
    $backDatas = debug_backtrace();
    $r = "";
    $count = 0 ;
    foreach ($backDatas as $backData) {
        if ($count == 0) {
            //pass
        } elseif ($count == 2) {
            $r .= $backData["file"] ."(". $backData["line"].") \n";
        } elseif ($count == 3) {
            $r .= $backData["function"] ."\n";
        }
        $count ++;
    }
    return $r;
}

/// 타입에 따라 노트한다
function NoteByType($type, $data = "", $message = "", $echo = true, $echoValue = -1)
{
    if (is_array($data)) {
        $data = var_export($data, true);
    }
    if ($type == "lack") {
        Note1("설정되지 않은 값이 있습니다 ($data)");
    } elseif ($type == "array") {
        Note1("값이 배열이 아닙니다 ($data)");
    } elseif ($type == "cannotArray") {
        Note1("값이 배열입니다 ($data)");
    } elseif ($type == "login") {
        Note1("로그인되어있지 않습니다 ($data)");
    } elseif ($type == "auth") {
        Note1("권한이 없습니다 ($data)");
    } elseif ($type == "tooMany") {
        Note1("값이 너무 많습니다 ($data)");
    } elseif ($type == "findFail") {
        Note1("값을 찾지 못했습니다 ($data)");
    } elseif ($type == "false") {
        Note1("값이 false 입니다 ($data)");
    } elseif ($type == "null") {
        Note1("값이 null 입니다 ($data)");
    } elseif ($type == "type") {
        Note1("타입이 일치하지 않습니다 ($data)");
    } elseif ($type == "message") {
        Note1($message . "($data)");
    } elseif ($type == "value") {
        Note1("값이 적당하지 않습니다" . "($data)");
    } elseif ($type == "nofile") {
        Note1("파일이 없습니다" . "($data)");
    } else {
        Note1("타입이 없습니다");
    }
    if ($echo) {
        echo -1;
    }
}

function IsOverlapMeta($metas)
{
    foreach ($metas as $meta) {
        $userData = get_user_by($meta["meta_key"], $meta["meta_value"]);
        if (!$userData) {
            // 값이 한개라도 다를 때
            return false;
        }
    }
}

// 한자리 숫자만 가능함
// middle 띄어쓰기 직접 넣어야함
function GetNumSumByArray($array, $middle)
{
    $r = [];
    // 키 -> ori
    // 값 -> plus
    // 번역과 슬러그가 같은 경우 숫자를 없애고 넣는다
    foreach ($array as $key => $value) {
        $num = substr($key, -1);
        if (is_numeric($num)) {
            $isSameSeparator = ItHasSeparator($value, "__" . GetWithoutSeparator($value) . "__");
            $ori = substr($key, 0, -1);
            if ($isSameSeparator) {
                $ori = preg_replace("/[0-9]/", "", $ori);
            }
            if (!isset($r[$ori])) {
                $r[$ori] = "";
                $r[$ori] = $r[$ori] . $value;
            } else {
                $add = $r[$ori] . $middle . $value;
                $rep = str_replace($middle, "", $add);
                if($rep == ""){
                    $add = $rep;
                }
                    $r[$ori] = $add;
                
            }
        } else {
            $r[$key] = $value;
        }

    }
    return $r;
}

function DivideDataForSolicitudExcel($array)
{
    $v = GetValueBySolicitudSeparator($array, "excel_workday");
    if(!$v)return $array;

    $dayData = json_decode($v, true);
    for ($i=1; $i<32; $i++) {
        $str = "__data__" . $i . "day";
        $array[$str] = "";
    }
    foreach ($dayData as $value) {
        $day = preg_replace("/[^0-9]*/s","", $value);
        $array["__data__" . $day . "day"] = 1;
    }

    return $array;
}


function ItHasSeparator($str, $separator)
{
    if (strpos($str, $separator) !== false) {
        return true;
    } else {
        return false;
    }
}
