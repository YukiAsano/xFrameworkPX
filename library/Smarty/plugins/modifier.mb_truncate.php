<?php
/**
 *
 * マルチバイトトランケート関数
 *
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

// {{{ smarty_modifier_mb_truncate

/**
 * smarty_modifier_mb_truncate
 *
 * @access public
 * @param mixed $string 対象文字列
 * @param string $length 文字長さ
 * @param string $etc 切り取り後に付加する文字列
 * @param string $enc テキストエンコード
 * @return void
 */

function smarty_modifier_mb_truncate($string, $length = 80, $etc = '...', $enc = 'UTF-8') {
    if ($length == 0) {
        return '';
    }

    if (mb_strlen($string) > $length) {
        return mb_strimwidth($string, 0, $length, '', $enc).$etc;
    } else {
        return $string;
    }
}

// }}}
?>
