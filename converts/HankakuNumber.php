<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * convHankakuNumber Class File
 *
 * PHP versions 5
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX_Validation
 * @author     Atsushi Yamaguchi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    SVN $Id: ZenkakuKana.php 1178 2010-01-05 15:13:08Z tamari $
 */

// {{{ convHankakuNumber

/**
 * convHankakuNumber Class
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX_Validation
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    Release: 3.5.0
 * @link       http://www.xframeworkpx.com/api/?class=xFrameworkPX_Validation_ZenkakuKana
 */
class convHankakuNumber
{

    private $_option = 'n';

    // {{{ __construct

    /**
     * 全角数字変換メソッド
     *
     * @access public
     * @param string 対象データ
     * @return string コンバート後データ
     */
    public function __construct ($opt = 'n')
    {
        $this->_option = $opt;
    }

    // }}}
    // {{{ convert

    /**
     * convert
     *
     * 全角数字変換メソッド
     *
     * @access public
     * @param string 対象データ
     * @return string コンバート後データ
     */
    public function convert($data, $enc = 'UTF-8')
    {
        if ($data === '') {
            return $data;
        }
        return (int)mb_convert_kana($data, $this->_option, $enc);
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
