<?php

function CopyFile($from_path, $to_path)
{
    if (!IsFile($from_path)) {
        return false;
    }

    list($txt, $ext) = explode(".", basename($from_path));
    $file_origin = fopen($from_path, 'r');
    $file_to = fopen($to_path, 'w');
    $read = fread($file_origin, filesize($from_path));
    fwrite($file_to, $read);
    fclose($file_origin);
    fclose($file_to);
    // unlink($from_path);
    return true;
}
function IsDir($dir_name)
{
    return is_dir(iconv("UTF-8", "cp949", $dir_name));
}

function IsFile($file_name)
{
    return is_file(iconv("UTF-8", "cp949", $file_name));
}


function mh_sanitize_koran_chars( $filename ){
	return mh_hantoeng( $filename );
}
function myEach(&$arr) {
    $key = key($arr);
    $result = ($key === null) ? false : [$key, current($arr), 'key' => $key, 'value' => current($arr)];
    next($arr);
    return $result;
}
function mh_hantoeng($str = null){
	$LCtable = array("ㄱ","ㄲ","ㄴ","ㄷ","ㄸ","ㄹ","ㅁ","ㅂ","ㅃ","ㅅ","ㅆ","ㅇ","ㅈ","ㅉ","ㅊ","ㅋ","ㅌ","ㅍ","ㅎ");
	$MVtable = array("ㅏ","ㅐ","ㅑ","ㅒ","ㅓ","ㅔ","ㅕ","ㅖ","ㅗ","ㅘ","ㅙ","ㅚ","ㅛ","ㅜ","ㅝ","ㅞ","ㅟ","ㅠ","ㅡ","ㅢ","ㅣ");
	$TCtable = array("","ㄱ","ㄲ","ㄳ","ㄴ","ㄵ","ㄶ","ㄷ","ㄹ","ㄺ","ㄻ","ㄼ","ㄽ","ㄾ","ㄿ","ㅀ","ㅁ","ㅂ","ㅄ","ㅅ","ㅆ","ㅇ","ㅈ","ㅊ","ㅋ","ㅌ","ㅍ","ㅎ");

	$LCetable = array("k","kk","n","d","tt","l","m","b","pp","s","ss","","j","jj","ch","k","t","p","h");
	$MVetable = array("a","ae","ya","yae","eo","e","yeo","ye","o","wa","wae","oe","yo","u","wo","we","wi","yu","eu","ui","i");
	$TCetable = array("","k","k","k","n","n","n","t","l","l","l","l","l","l","l","l","m","p","p","t","t","ng","t","t","k","t","p","h");

	$result = mh_utf8_to_unicode($str);
	$cchr = '';
	while (list($key, $val) = myEach($result)) {
		if($val >= 44032 && $val <= 55203) {
			$chr = "";
			$code = "";
			$temp1 = "";
			$code = $val;
			$temp1 = $code - 44032;
			$T = (int) $temp1 % 28;
			$temp1 /= 28;
			$V = (int) $temp1 % 21;
			$temp1 /= 21;
			$L = (int) $temp1;
			$chr = $LCetable[$L].$MVetable[$V].$TCetable[$T];
		} else {
			$temp2 = array();
			$temp2 = array($result[$key]);
			$chr = mh_unicode_to_utf8($temp2);
			if (ord($chr) == 32){
				$chr = "-";
			}
		}
		$cchr .= $chr;
	}
	return $cchr;
}
function mh_utf8_to_unicode( $str ) {
	$unicode = array();
	$values = array();
	$lookingFor = 1;

	for ($i = 0; $i < strlen( $str ); $i++ ) {
		$thisValue = ord( $str[ $i ] );
			if ( $thisValue < 128 ) $unicode[] = $thisValue;
			else {
					if ( count( $values ) == 0 ) $lookingFor = ( $thisValue < 224 ) ? 2 : 3;
					$values[] = $thisValue;
					if ( count( $values ) == $lookingFor ) {
						$number = ( $lookingFor == 3 ) ?
						( ( $values[0] % 16 ) * 4096 ) + ( ( $values[1] % 64 ) * 64 ) + ( $values[2] % 64 ):
						( ( $values[0] % 32 ) * 64 ) + ( $values[1] % 64 );

						$unicode[] = $number;
						$values = array();
						$lookingFor = 1;
			}// if
		} // if
	} // for
	return $unicode;
}
function mh_unicode_to_utf8( $str ) {
	$utf8 = '';
		foreach( $str as $unicode ) {
		if ( $unicode < 128 ) {
			$utf8.= chr( $unicode );
		} elseif ( $unicode < 2048 ) {
			$utf8.= chr( 192 + ( ( $unicode - ( $unicode % 64 ) ) / 64 ) );
			$utf8.= chr( 128 + ( $unicode % 64 ) );
		} else {
			$utf8.= chr( 224 + ( ( $unicode - ( $unicode % 4096 ) ) / 4096 ) );
			$utf8.= chr( 128 + ( ( ( $unicode % 4096 ) - ( $unicode % 64 ) ) / 64 ) );
			$utf8.= chr( 128 + ( $unicode % 64 ) );
		} // if
	} // foreach
	return $utf8;
}
?>
