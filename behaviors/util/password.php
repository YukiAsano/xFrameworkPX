<?php
/**
 *
 * passwordクラス
 *
 * @category   Behavior
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */

// {{{ password

/**
 * password Class
 *
 * @category   Behavior
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */

class util_password extends xFrameworkPX_Model_Behavior {

    // {{{ props

    private $_key = 'testhoge';

    // }}}

    // {{{ __construct

    /**
     * コンストラクタ
     *
     * @access public
     */
    public function __construct($module) {

        parent::__construct($module);

    }

    // }}}
    // {{{ bindGetPassword

    /**
     * パスワード生成メソッド
     *
     * @access public
     * @param int $length パスワードの長さ
     * @return mixed パスワード文字列
     */
    public function bindGetPassword($length = 8) {

        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'), 0, $length);

    }

    // }}}
    // {{{ bindEncrypt

    /**
     * 暗号化メソッド(要php-mcrypt)
     *
     * @access public
     * @param string $text 暗号対象テキスト
     * @return string|void
     */
    public function bindEncrypt($text) {

        if (!empty($text)) {
            return encrypt($this->_key, $text);
        }

        return '';

    }

    // }}}
    // {{{ bindDecrypt

    /**
     * 復号化メソッド(要php-mcrypt)
     *
     * @access public
     * @param string $text 復号対象テキスト
     * @return string
     */
    public function bindDecrypt($text) {

        if (!empty($text)) {
            return decrypt($this->_key, $text);
        }

        return '';

    }

    // }}}
    // {{{ bindGetCryptPass

    /**
     * 暗号化パスワード取得
     * sha1とmd5の組み合わせのパスワード使用時
     *
     * @access public
     * @param string $plain
     * @return string 暗号化パスワード
     */
    public function bindGetCryptPass($plain) {

        return sha1(md5($plain));

    }

    // }}}

}

// }}}
