<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * convNormalizeString Class File
 *
 * PHP versions 5
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX_Validation
 * @author     Yuki Asano <asano@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    SVN $Id: ZenkakuKana.php 1178 2010-01-05 15:13:08Z tamari $
 */

// {{{ convNormalizeString

/**
 * convNormalizeString Class
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX_Validation
 * @author     Yuki Asano <asano@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    Release: 3.5.0
 * @link       http://www.xframeworkpx.com/api/?class=xFrameworkPX_Validation_ZenkakuKana
 */
class convNormalizeString
{

    // {{{ convert

    /**
     * convert
     *
     * 文字列正規化メソッド
     *
     * @access public
     * @param string 対象データ
     * @param string コンバート前文字コード
     * @return string コンバート後データ
     */
    public function convert($data, $enc = 'UTF-8')
    {
        // 変換001用テーブル
        $convert_table = array(
            '①' => '(1)',
            '②' => '(2)',
            '③' => '(3)',
            '④' => '(4)',
            '⑤' => '(5)',
            '⑥' => '(6)',
            '⑦' => '(7)',
            '⑧' => '(8)',
            '⑨' => '(9)',
            '⑩' => '(10)',
            '⑪' => '(11)',
            '⑫' => '(12)',
            '⑬' => '(13)',
            '⑭' => '(14)',
            '⑮' => '(15)',
            '⑯' => '(16)',
            '⑰' => '(17)',
            '⑱' => '(18)',
            '⑲' => '(19)',
            '⑳' => '(20)',
            'Ⅰ' => 'I',
            'Ⅱ' => 'II',
            'Ⅲ' => 'III',
            'Ⅳ' => 'IV',
            'Ⅴ' => 'V',
            'Ⅵ' => 'VI',
            'Ⅶ' => 'VII',
            'Ⅷ' => 'VIII',
            'Ⅸ' => 'IX',
            'Ⅹ' => 'X',
            '㍉' => 'ミリ',
            '㌔' => 'キロ',
            '㌢' => 'センチ',
            '㍍' => 'メートル',
            '㌘' => 'グラム',
            '㌧' => 'トン',
            '㌃' => 'アール',
            '㌶' => 'ヘクタール',
            '㍑' => 'リットル',
            '㍗' => 'ワット',
            '㌍' => 'カロリー',
            '㌦' => 'ドル',
            '㌣' => 'セント',
            '㌫' => 'パーセント',
            '㍊' => 'ミリバール',
            '㌻' => 'ページ',
            '㎜' => 'mm',
            '㎝' => 'cm',
            '㎞' => 'km',
            '㎎' => 'mg',
            '㎏' => 'kg',
            '㏄' => 'cc',
            '㎡' => '平方メートル',
            '㍻' => '平成',
            '№' => 'No.',
            '㏍' => 'K.K.',
            '℡' => 'TEL',
            '㊤' => '(上)',
            '㊥' => '(中)',
            '㊦' => '(下)',
            '㊧' => '(左)',
            '㊨' => '(右)',
            '㈱' => '(株)',
            '㈲' => '(有)',
            '㈹' => '(代)',
            '㍾' => '明治',
            '㍽' => '大正',
            '㍼' => '昭和'
        );

        // 置換パターン作成
        $patterns = array();
        foreach($convert_table as $key => $value) {
            $patterns[ '/' . $key . '/u' ] = $value;
        }

        // 変換001に従って変換
        $data = preg_replace(
            array_keys($patterns),
            array_values($patterns),
            $data
        );

        // 一度CP932に変換して下駄変換を適用する
        $string_cp932 = mb_convert_encoding($data, 'CP932', $enc);
        mb_regex_encoding('CP932');
        mb_internal_encoding('CP932');
        // 13区(0x8740~0x879E)
        // 89区(0xED40~0xED9E)
        // 90区(0xED9F~0xEDFC)
        // 91区(0xEE40~0xEE9E)
        // 92区(0xEE9F~0xEEFC)
        // 115区(0xFA40~0xFA9E)
        // 116区(0xFA9F~0xFAFC)
        // 117区(0xFB40~0xFB9E)
        // 118区(0xFB9F~0xFBFC)
        // 119区(0xFC40~0xFC4B)
        $pattern = '/(?:[\x87][\x40-\x9F]|[\xED-\xEE][\x40-\xFF]|[\xFA-\xFB][\x40-\xFF]|[\xFC][\x40-\x4B])/';
        $chg_str = mb_convert_encoding('〓', 'CP932', $enc);
        $str2 = '';
        for ($cnt1 = 0; $cnt1 < mb_strlen($string_cp932); $cnt1++){
            $bit = mb_substr($string_cp932, $cnt1, 1);
            if (preg_match($pattern, $bit, $matches, PREG_OFFSET_CAPTURE)) {
                $flag = true;
            }
            $bit = preg_replace($pattern, $chg_str, $bit);
            $str2 .= $bit;
        }
        $string_cp932 = $str2;
        mb_regex_encoding($enc);
        mb_internal_encoding($enc);

        // 再度元のコードに変換して返す
        return mb_convert_encoding($string_cp932, $enc, 'CP932');

    }

    // }}}

}

// }}}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
