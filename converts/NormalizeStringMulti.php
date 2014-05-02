<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * convNormalizeStringMulti Class File
 *
 * PHP versions 5
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX_Validation
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    SVN $Id: ZenkakuKana.php 1178 2010-01-05 15:13:08Z tamari $
 */

// {{{ convNormalizeStringMulti

/**
 * convNormalizeStringMulti Class
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX_Validation
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    Release: 3.5.0
 * @link       http://www.xframeworkpx.com/api/?class=xFrameworkPX_Validation_ZenkakuKana
 */
class convNormalizeStringMulti
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
    public function convert($datas, $enc = 'UTF-8')
    {
        require_once('NormalizeString.php');
        $convNormalizeString = new convNormalizeString();

        $returnDatas = array();

        foreach ($datas as $key => $data) {
            $data = $convNormalizeString->convert($data, $enc);

            $returnDatas[$key] = $data;
        }

        return $returnDatas;

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
