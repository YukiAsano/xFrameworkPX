<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PHP Extender File
 *
 * PHP versions 5
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    SVN $Id: extender.php 1181 2010-01-06 03:27:06Z tamari $
 */

/**
 * Directory Separator Shorthand
 */
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

// {{{ normalize_path

/**
 * パス正規化関数
 *
 * @param string $path パス
 * @return string 正規化されたパス
 */
function normalize_path($path, $separator = DS)
{
    $path = str_replace($separator, '/', $path);
    $path = preg_replace(
        '/(\/+)/i',
        '/',
        str_replace('\\', '/', $path)
    );

    if ($separator !== '/') {
        $path = str_replace('/', $separator, $path);
    }

    return $path;
}

// }}}
// {{{ file_forceput_contents

/**
 * ファイル出力関数
 *
 * ディレクトリが存在しない場合に自動生成してファイルを出力します。
 *
 * @param string $filename データを書き込むファイルへのパス。
 * @param string $data 書き込むデータ。
 * @param string $flags フラグ
 * @param string $context コンテキストリソース。
 * @see http://php.net/manual/ja/function.file-put-contents.php
 * @return string 正規化されたパス
 */
function file_forceput_contents($filename, $data, $flags = 0, $context = null)
{
    // {{{ ディレクトリ作成

    makeDirectory(dirname($filename));

    // }}}

    return file_put_contents(
        $filename,
        $data,
        $flags,
        $context
    );

}


// {{{ makeDirectory

/**
 * ディレクトリ生成メソッド
 *
 * @param string $dir 生成ディレクトリ
 * @param integer $mode パーミッション値(省略時:0755)
 * @return bool true:成功,false:失敗
 */
function makeDirectory($dir, $mode = 0755)
{

    // {{{ ディレクトリが存在するか、生成成功の場合は終了

    if (is_dir($dir) || @mkdir($dir, $mode)) {
        return true;
    }

    // }}}
    // {{{ ディレクトリ生成

    makeDirectory(dirname($dir), $mode);

    // }}}

    return @mkdir($dir, $mode);

}

// }}}
// {{{ removeDirectory

/**
 * ディレクトリ削除関数
 *
 * @param string $dir 削除ディレクトリ
 * @return void
 */
function removeDirectory($dir)
{
    if ($dh = @opendir($dir)) {
        while (false !== ($item = @readdir($dh))) {

            if ($item != '.' && $item != '..') {

                if (is_dir("$dir/$item")) {
                    removeDirectory("$dir/$item");
                } else {
                    @unlink("$dir/$item");
                }
            }
        }

        @closedir($dh);
        @rmdir($dir);
    }
}

// }}}
// {{{ file_copy

/**
 * ファイルコピー関数
 *
 * @param string $src コピー元ファイルパス
 * @param string $dest コピー先ファイルパス
 * @return int バイト数、失敗した場合はfalseを返します。
 */
function file_copy($dest, $src)
{

    return file_forceput_contents($dest, file_get_contents($src));

}

// }}}
// {{{ file_change_owner

/**
 * ファイル所有者変更関数
 *
 * ファイルの所有者を変更します。
 *
 * @param string $filename 所有者を変更するファイルへのパス。
 * @param string $user 変更する所有者。
 * @see http://php.net/manual/ja/function.chown.php
 * @see http://php.net/manual/ja/function.fileowner.php
 * @return bool
 */
function file_change_owner($filename, $user = null)
{

    return @chown($filename, $user);

}
// }}}
// {{{ get_filename

/**
 * ファイル名取得関数
 *
 * @param string $file ファイルパス
 * @return string ファイル名
 */
function get_filename($file)
{

    $ret = '';

    if (defined('PATHINFO_FILENAME')) {

        $ret = pathinfo($file, PATHINFO_FILENAME);

    // @codeCoverageIgnoreStart
    } else if (strstr($file, '.')) {

        $ret = substr(
            pathinfo($file, PATHINFO_BASENAME),
            0,
            strrpos(
                pathinfo($file, PATHINFO_BASENAME),
                '.'
            )
        );

    }
    // @codeCoverageIgnoreEnd

    return $ret;

}

// }}}
// {{{ get_filelist

/**
 * ファイルリスト取得関数
 *
 * @param string $dir ディレクトリ
 * @param string $filter フィルター
 * @return array ファイルリスト
 */
function get_filelist($dir, $filter = null)
{
    $ret = array();

    if (!is_dir($dir)) {
        return $ret;
    }

    $iterator = new RecursiveDirectoryIterator($dir);

    foreach (
        new RecursiveIteratorIterator(
            $iterator,
            RecursiveIteratorIterator::CHILD_FIRST
        ) as $item
    ) {

        if (!$item->isDir()) {
            if (is_null($filter)) {
                $ret[] = $item->getPathname();
            } else {
                $valid = true;
                if (isset($filter['ext'])) {
                    if (
                        pathinfo(
                            $item->getPathname(),
                            PATHINFO_EXTENSION
                        ) !== $filter['ext']
                    ) {
                        $valid = false;
                    }
                }

                if (isset($filter['filename'])) {
                    if (
                        get_filename($item->getPathname())
                        !== $filter['filename']
                    ) {
                        $valid = false;
                    }
                }

                if ($valid === true) {
                    $ret[] = $item->getPathname();
                }
            }
        }
    }

    return $ret;
}

// }}}
// {{{ get_relative_url

/**
 * URL相対パス取得関数
 *
 * @param string $base ベースパス
 * @param string $target ターゲットパス
 * @return string 相対パス
 */
function get_relative_url($base, $target)
{
    $ret = '';
    $baseTemp   = explode('/', $base);
    $targetTemp = explode('/', $target);

    do {
        if (empty($baseTemp) || empty($targetTemp)) {
            break;
        }
        $to = array_shift($baseTemp);
        $from = array_shift($targetTemp);
    } while ($to  == $from);

    return str_repeat('../', count($baseTemp));
}

// }}}
// {{{ stripslashes_deep

/**
 * クォート削除関数
 *
 * @param mixed $values クォート削除対象オブジェクト
 * @return mixed クォート削除後オブジェクト
 */
function stripslashes_deep($values)
{
    if (is_array($values)) {
        foreach ($values as $key => $value) {
            $values[$key] = stripslashes_deep($value);
        }
    } else {
        $values = stripslashes($values);
    }

    return $values;
}

// }}}
// {{{ sys_get_temp_dir

if (!function_exists('sys_get_temp_dir')) {

// @codeCoverageIgnoreStart
/**
 * 一時ファイル用ディレクトリパス取得関数
 *
 * @return string 一時ディレクトリのパス
 */
function sys_get_temp_dir()
{
    if (!empty($_ENV['TMP'])) {
        return realpath($_ENV['TMP']);
    } else if (!empty($_ENV['TMPDIR'])) {
        return realpath($_ENV['TMPDIR']);
    } else if (!empty($_ENV['TEMP'])) {
        return realpath($_ENV['TEMP']);
    } else {
        $tempfile = tempnam(md5(uniqid(rand(), true)), '');
        if ($tempfile) {
            $tempdir = realpath(dirname($tempfile));
            unlink($tempfile);
            return $tempdir;
        } else {
            return false;
        }
    }
}
// @codeCoverageIgnoreEnd

}

// }}}
// {{{ mb_convert_encoding_deep

/**
 * 多階層mb_convert_encoding関数
 *
 * @param $values 対象変換文字列、または配列
 * @param $to 変換文字コード
 * @param $from 現在の文字コード
 * @return mixed 変換後の値
 */
function mb_convert_encoding_deep($values, $to, $from = 'auto')
{
    if (is_array($values)) {

        foreach ($values as $key => $target) {
            $values[$key] = mb_convert_encoding_deep($target, $to, $from);
        }

    } elseif (!empty($values) && is_string($values)) {
        $values = mb_convert_encoding($values, $to, $from);
    }

    return $values;
}

// }}}
// {{{ startsWith

/**
 * 前方一致確認関数
 * $checkが$targetから始まるか判定します。
 *
 * @param string $check チェック文字列
 * @param string $target 対象文字列
 * @return boolean true:一致:false:不一致
 */
function startsWith($check, $target)
{
    return strpos($check, $target, 0) === 0;
}

// }}}
// {{{ endsWith

/**
 * 後方一致確認関数
 * $checkが$targetで終わるか判定します。
 *
 * @param string $check チェック文字列
 * @param string $target 対象文字列
 * @return boolean true:一致:false:不一致
 */
function endsWith($check, $target)
{
    // {{{ 文字列長が足りていない場合はFALSEを返します。

    $len = (strlen($check) - strlen($target));
    if ($len < 0) {
        return false;
    }

    // }}}

    return strpos($check, $target, $len) !== false;

}

// }}}
// {{{ matchesIn

/**
 * 部分一致確認関数
 * $checkの中に$targetが含まれているか判定します。
 *
 * @param string $check チェック文字列
 * @param string $target 対象文字列
 * @return boolean true:一致:false:不一致
 */
function matchesIn($check, $target)
{

    return strpos($check, $target) !== false;

}

// }}}
// {{{ lcfirst

if (!function_exists('lcfirst')) {

// @codeCoverageIgnoreStart
/**
 * 先頭文字を小文字にする関数
 *
 * @param string $target 対象文字列
 * @return string 変換後の文字列
 */
function lcfirst($target)
{
    if (!empty($target) && is_string($target)) {
        $target{0} = strtolower($target{0});
    }
    return $target;
}
// @codeCoverageIgnoreEnd
}

// }}}
// {{{ get_status_code

/**
 * ステータスコード取得関数
 *
 * @param number $code コード番号
 * @return string ステータスコード
 */
function get_status_code($code)
{

    $codes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out'
    );

    if (isset($codes[$code])) {
        return $codes[$code];
    }

    return null;

}

// }}}
// {{{ is_secure

/**
 * SSL接続判定メソッド
 *
 * @return true:SSL接続,false:非SSL接続
 */
function is_secure()
{

    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        return true;
    } else {
        return false;
    }

}

// }}}
// {{{ base_name

/**
 * 基底URL取得メソッド
 *
 * @return string 基底URL
 */
function base_name($https=false)
{
    $protocol = is_secure() ? 'https://' : 'http://';

    if ($https===true) {
        $protocol = 'https://';
    }

    $serverName = $_SERVER['SERVER_NAME'];
    $serverPort = '';

    if($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443') {
        $serverPort = ':' . $_SERVER["SERVER_PORT"];
    }

    //$path = str_replace('index.php', '', $_SERVER['PHP_SELF']);
    $path = str_replace('bootstrap.php', '', $_SERVER['PHP_SELF']);

    return $protocol . $serverName . $serverPort . $path;

}

// }}}
// {{{ get_ip

/**
 * IPアドレス取得関数
 *
 * @return string IPアドレス
 */
function get_ip()
{
    return getenv('REMOTE_ADDR');
}

// }}}
// {{{ is_ipv6

/**
 * IPv6判定関数
 *
 * @return boolean true:IPv6,false:IPv4
 */
function is_ipv6($ip = '')
{
    if ($ip === '') {
        $ip = get_ip();
    }

    if (substr_count($ip, ':') > 0 && substr_count($ip, '.') == 0) {
        return true;
    } else {
        return false;
    }
}

// }}}
// {{{ uncompress_ipv6

/**
 * IPv6アドレス展開関数
 *
 * @param string IPv6アドレス
 * @return string 展開アドレス
 */
function uncompress_ipv6($ip = '')
{
    if ($ip === '') {
        $ip = get_ip();
    }

    if (strstr($ip, '::')) {
        $i = 0;
        $e = explode(":", $ip);
        $s = 8 - sizeof($e) + 1;

        foreach ($e as $key => $val) {
            if ($val == '') {
                for (; $i <= $s; $i++) {
                    $newip[] = 0;
                }
            } else {
                $newip[] = $val;
            }
        }
        $ip = implode(':', $newip);
    }

    return $ip;
}

// }}}
// {{{ uncompress_ipv6

/**
 * IPv6アドレス圧縮関数
 *
 * @param string IPv6アドレス
 * @return string 圧縮アドレス
 */
function compress_ipv6($ip ='')
{
    if ($ip === '') {
        $ip = get_ip();
    }

    if (!strstr($ip, '::')) {

        $e = explode(':', $ip);
        $zeros = array(0);
        $result = array_intersect($e, $zeros);

        if (sizeof($result) >= 6) {

            if ($e[0]==0) {
                $newip[] = "";
            }

            foreach ($e as $key=>$val) {
                if ($val !=='0') {
                    $newip[] = $val;
                }
            }

            $ip = implode('::', $newip);
        }

    }

    return $ip;
}

// }}}
// {{{ encrypt

/**
 * 暗号化メソッド
 *
 * @access public
 * @params $text 暗号対象テキスト
 * @return void
 */
function encrypt($key, $text)
{
    $rc4 = _encrypt($key, $text);
    $crc = pack("L", crc32($rc4));
    return base64_encode($crc.$rc4);
}

// }}}
// {{{ decrypt

/**
 * 復号化メソッド
 *
 * @access public
 * @params $text 暗号対象テキスト
 * @return string
 */
function decrypt($key, $text)
{
    $crc_rc4 = base64_decode($text);
    if (strlen($crc_rc4) < 4) {
        return null;
    }
    $crc = unpack("L", $crc_rc4);
    $crc = $crc[1];
    $rc4 = substr($crc_rc4, 4);
    if (crc32($rc4) != $crc) return null;

    return _decrypt($key, $rc4);
}

// }}}
// {{{ _encrypt

/**
 * 暗号化メソッド
 *
 * @param string $pwd Key to encrypt with (can be binary of hex)
 * @param string $data Content to be encrypted
 * @param bool $ispwdHex Key passed is in hexadecimal or not
 * @access public
 * @return string
 */
function _encrypt($pwd, $data, $ispwdHex = 0)
{

    if ($ispwdHex) {
        $pwd = @pack('H*', $pwd);
    }

    $key = array();
    $box = array();

    $key[]  = '';
    $box[]  = '';
    $cipher = '';

    $pwd_length = strlen($pwd);
    $data_length = strlen($data);

    for ($i = 0; $i < 256; $i++) {
        $key[$i] = ord($pwd[$i % $pwd_length]);
        $box[$i] = $i;
    }
    for ($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $key[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    for ($a = $j = $i = 0; $i < $data_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        $k = $box[(($box[$a] + $box[$j]) % 256)];
        $cipher .= chr(ord($data[$i]) ^ $k);
    }

    return $cipher;

}

// }}}
// {{{ _decrypt

/**
 * 複合化メソッド
 *
 * @param string $pwd Key to decrypt with (can be binary of hex)
 * @param string $data Content to be decrypted
 * @param bool $ispwdHex Key passed is in hexadecimal or not
 * @access public
 * @return string
 */
function _decrypt($pwd, $data, $ispwdHex = 0)
{

    return _encrypt($pwd, $data, $ispwdHex);

}

// }}}
// {{{ move_file

/**
 * ファイルアップロードメソッド
 *
 * @param $fieldname input type=imageのname
 * @param $filename 移動先ファイル名
 * @param $fileinfo $_FILES配列
 * @param $filemode ファイル移動時のパーミッション指定
 * @param $create 移動先ディレクトリの自動作成指定 true:作成する false:作成しない
 * @param $dirmode ディレクトリ自動作成時のパーミッション指定
 * @return boolean true:正常移動 false:移動失敗
 */
function move_file(
    $fieldname,
    $filename,
    $fileinfo,
    $filemode = 0666,
    $create = true,
    $dirmode = 0777
)
{

    if (
        ($fileinfo['error'][$fieldname] === UPLOAD_ERR_OK) &&
        ($fileinfo['size'][$fieldname] > 0) &&
        @is_uploaded_file($fileinfo['tmp_name'][$fieldname])
    ) {

        $dir = dirname($filename);

        if (!@is_readable($dir)) {

            if ($create && !makeDirectory($dir, $dirmode)) {
                $error = 'Failed to create the upload directory. "%s"';
                $error = sprintf($error, $dir);
                trigger_error($error, E_USER_ERROR);
            }

            @chmod($dir, $dirmode);

        } else if (!@is_dir($dir)) {

            $error = 'Upload file path does not exist. "%s"';
            $error = sprintf($error, $dir);
            trigger_error($error, E_USER_ERROR);

        } else if (!@is_writable($dir)) {

            $error = 'Not have permissions to write to the directory. "%s"';
            $error = sprintf($error, $dir);
            trigger_error($error, E_USER_ERROR);

        }

        if (@move_uploaded_file($fileinfo['tmp_name'][$fieldname], $filename)) {
            @chmod($filename, $filemode);
            return true;
        }
    }

    return false;

}

// }}}
// {{{ getFileType

/**
 * ファイルの拡張子を取得します
 *
 * @param string $path ファイル パス
 * @return string 拡張子
 */
function getFileType($path) {
    $path = explode('.', $path);
    if (count($path) == 1) {
        return '';
    }
    else {
        return '.'.end($path);
    }
}

// }}}
// {{{ setFileType

/**
 * ファイルの拡張子を設定します
 *
 * @param string $path ファイル パス
 * @param string $type ファイル拡張子
 * @return string ファイル パス
 */
function setFileType($path, $type) {
    $path = explode('.', $path);
    if (count($path) != 1) {
        array_pop($path);
    }
    $path = implode('.', $path);
    if ($type != '') {
        $path .= $type;
    }
    return $path;
}

// }}}
// {{{ getFileList

/**
 * ファイルの一覧を取得します
 *
 * @param string $dir ディレクトリ
 * @param array $options オプション
 * @return mixed array: 一覧、string: エラー メッセージ
 */
function getFileList($dir, $options = array()) {
    $options += array(
        'depth' => 0,     //1以上: 深さ、0: 死ぬまで
        'clean' => false, //true: 変なディレクトリはないはず
        'base'  => false, //true: ファイル名のみ
    );
    $result = array();
    $handle = @opendir($dir);
    if ($handle === false) {
        if (isset($php_errormsg)) {
            return $php_errormsg;
        }
        else {
            return 'オープンできませんでした。';
        }
    }
    while (($file = @readdir($handle)) != false) {
        if (($file == '')
        ||  ($file == '.')
        ||  ($file == '..')) {
            continue;
        }
        $path = $dir.DS.$file;
        if (is_dir($path)) {
            if ($options['depth'] != 1) {
                $items = getFileList($path, array(
                    'depth' => ($options['depth'] - 1),
                    'clean' => $options['clean'],
                    'base'  => $options['base'],
                ));
                if (is_array($items)) {
                    $result = array_merge($result, $items);
                }
                else {
                    if ($options['clean']) {
                        $result = $items;
                        break;
                    }
                }
            }
        }
        else {
            if ($options['base']) {
                $result[] = $file;
            }
            else {
                $result[] = $path;
            }
        }
    }
    closedir($handle);
    return $result;
}

// }}}
// {{{ getMaster

/**
 * ymlによるマスター取得メソッド
 *
 * @param $path マスターファイルパス(/configs/yml/masterをルートとする)
 * @param 第一パラメータ：取得するマスタ名、第二以降：マスタ内で取得するキーを指定
 * @return mixed
 */
function getMaster()
{

    $args = func_get_args();
    return call_user_func_array(
        array(xFrameworkPX_Master::getInstance('master'), 'get'),
        $args
    );

}

// }}}
// {{{ getSetting

/**
 * ymlによるマスター取得メソッド
 *
 * @param $path マスターファイルパス(/configs/yml/settingsをルートとする)
 * @param 第一パラメータ：取得するマスタ名、第二以降：マスタ内で取得するキーを指定
 * @return mixed
 */
function getSetting()
{

    $args = func_get_args();
    return call_user_func_array(
        array(xFrameworkPX_Master::getInstance('settings'), 'get'),
        $args
    );

}

// }}}
// {{{ getEnvVal

/**
 * 現在の環境毎に、envの値を取得するメソッド
 *
 * @param マスターファイルパス(/configs/yml/settings/envをルートとする)
 * @param 第一パラメータ：取得するマスタ名、第二以降：マスタ内で取得するキーを指定
 * @return mixed
 */
function getEnvVal()
{

    $args = func_get_args();
    $file = 'env/'.array_shift($args);
    array_unshift($args, xFrameworkPX_Environment::getInstance()->getEnvType());
    array_unshift($args, $file);
    return call_user_func_array(
        array(xFrameworkPX_Master::getInstance('settings'), 'get'),
        $args
    );

}

// }}}
// {{{ dd

/**
 * デバッグ情報表示
 * settings/env.yml の debugmode がtrueの場合にのみ表示されます
 * dBugがあればdBug使用
 *
 * @param mixed $val 表示したい値
 * @param boolean $showHtml HTML表示する場合（dBugが無かった場合に有効）
 * @param boolean $showFrom 呼び出した場所を表示するか否か合（dBugが無かった場合に有効）
 * @return none
 */
function dd($val = null, $showHtml = false, $showFrom = true)
{
    $env = xFrameworkPX_Environment::getInstance();
    if ($env->get('debugmode')) {
        if ($showFrom) {
            $calledFrom = debug_backtrace();
            echo '<strong>' . $calledFrom[0]['file'] . '</strong>';
            echo ' (line <strong>' . $calledFrom[0]['line'] . '</strong>)';
        }

        if (file_exists(dirname(dirname(__FILE__)) . DS . 'misc' . DS . 'dBug.php')) {
            require_once('misc' . DS . 'dBug.php');
            new dBug($val);
        } else {
            echo "\n<pre>\n";
            $var = print_r($var, true);
            if ($showHtml) {
                $var = str_replace('<', '&lt;', str_replace('>', '&gt;', $var));
            }
            echo $var . "\n</pre>\n";
        }
    }
}

// }}}
// {{{ dt

/**
 * デバッグトレース
 * settings/env.yml の debugmode がtrueの場合にのみ表示されます
 *
 * @param number $depth 表示する深さ
 * @return none
 */
function dt($depth = 5)
{
    $env = xFrameworkPX_Environment::getInstance();
    if ($env->get('debugmode')) {
        $a = debug_backtrace();
        for ($i = 1; $i <= $depth; $i++) {
            if (!isset($a[$i])) {return;}
            $file = (isset($a[$i]['file'])) ? $a[$i]['file'] : null;
            $class = (isset($a[$i]['class'])) ? $a[$i]['class'] : null;
            $function = (isset($a[$i]['function'])) ? $a[$i]['function'] : null;
            $line = (isset($a[$i]['line'])) ? $a[$i]['line'] : null;
            echo "{$file}:{$line} ({$class}::{$function})\n";
        }
    }
}

// }}}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
