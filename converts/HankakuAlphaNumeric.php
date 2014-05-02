<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * convHankakuAlphaNumeric Class File
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

// {{{ convHankakuAlphaNumeric

/**
 * convHankakuAlphaNumeric Class
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX_Validation
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    Release: 3.5.0
 * @link       http://www.xframeworkpx.com/api/?class=xFrameworkPX_Validation_ZenkakuKana
 */
class convHankakuAlphaNumeric
{

    private $_option = 'a';

    // {{{ __construct

    /**
     * 全角英数字変換メソッド
     *
     * @access public
     * @param string 対象データ
     * @return string コンバート後データ
     */
    public function __construct ($opt = 'a')
    {
        $this->_option = $opt;
    }

    // }}}
    // {{{ convert

    /**
     * convert
     *
     * 全角英数字変換メソッド
     *
     * @access public
     * @param string 対象データ
     * @return string コンバート後データ
     */
    public function convert($data, $enc = 'UTF-8')
    {
        return mb_convert_kana($data, $this->_option, $enc);
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
