<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * xFrameworkPX_Environment Class File
 *
 * PHP versions 5
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    SVN $Id: Yaml.php 1181 2010-01-06 03:27:06Z tamari $
 */

// {{{ xFrameworkPX_Yaml

/**
 * xFrameworkPX_Environment Class
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    Release: 3.5.0
 * @link       http://www.xframeworkpx.com/api/?class=xFrameworkPX_Yaml
 */
class xFrameworkPX_Environment
{

    // {{{ const

    const ENV_TYPE_REAL   = 'real';    // 本番環境
    const ENV_TYPE_STAGE  = 'stage';   // ステージング環境
    const ENV_TYPE_STAGE2 = 'stage2';  // ステージング環境
    const ENV_TYPE_STAGE3 = 'stage3';  // ステージング環境
    const ENV_TYPE_TEST   = 'test';    // テスト環境
    const ENV_TYPE_DEV    = 'dev';     // 開発環境

    // }}}
    // {{{ props

    /**
     * このクラスのインスタンス
     *
     * @access private
     */
    private static $_instance;

    /**
     * 現在の環境（real/stage/test/dev）
     *
     * @access protected
     */
    protected $_envType;

    /**
     * 環境設定情報（全て）
     *
     * @access protected
     */
    protected $_envAll;

    /**
     * 環境設定情報（現在の環境のもののみ）
     *
     * @access protected
     */
    protected $_env;

    // }}}
    // {{{ getInstance

    /**
     * インスタンス取得
     *
     * @access public
     * @param none
     * @return object インスタンス
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    // }}}
    // {{{ __construct

    /**
     * コンストラクタ
     *
     * @access public
     */
    public function __construct()
    {
        $this->_envAll = getSetting('env');
        if (!isset($this->_envAll['envType'])) {
            throw new Exception('環境設定ファイルの記述が不正です。');
        }
        $this->_envType = $this->_envAll['envType'];
        $this->_env = $this->_envAll[$this->_envType];
    }

    // }}}
    // {{{ get

    /**
     * 環境設定情報を取得する
     *
     * @access public
     * @param 取得したいキーを第一引数から順次指定
     * @return mixed 環境設定情報
     */
    public function get()
    {
        $args = func_get_args();
        return call_user_func_array(array($this, '_get'), $args);
    }

    // }}}
    // {{{ getEnvType

    /**
     * 現在の環境を取得
     *
     * @access public
     */
    public function getEnvType()
    {
        return $this->_envType;
    }

    // }}}
    // {{{ isReal

    /**
     * 本番環境かどうか
     *
     * @access public
     * @param none
     * @return boolean 本番環境の場合：true
     */
    public function isReal()
    {
        if ($this->_envType == self::ENV_TYPE_REAL) {
            return true;
        }
        return false;
    }

    // }}}
    // {{{ isStage

    /**
     * ステージング環境かどうか
     *
     * @access public
     * @param none
     * @return boolean ステージング環境の場合：true
     */
    public function isStage()
    {
        if ($this->_envType == self::ENV_TYPE_STAGE  ||
            $this->_envType == self::ENV_TYPE_STAGE2 ||
            $this->_envType == self::ENV_TYPE_STAGE3) {
            return true;
        }
        return false;
    }

    // }}}
    // {{{ isTest

    /**
     * テスト環境かどうか
     *
     * @access public
     * @param none
     * @return boolean テスト環境の場合：true
     */
    public function isTest()
    {
        if ($this->_envType == self::ENV_TYPE_TEST) {
            return true;
        }
        return false;
    }

    // }}}
    // {{{ isDev

    /**
     * 開発環境かどうか
     *
     * @access public
     * @param none
     * @return boolean 開発環境の場合：true
     */
    public function isDev()
    {
        if ($this->_envType == self::ENV_TYPE_DEV) {
            return true;
        }
        return false;
    }

    // }}}
    // {{{ _get

    /**
     * 環境設定情報を取得する内部関数
     *
     * @access protected
     */
    protected function _get()
    {
        $args = func_get_args();

        $ret = $this->_env;
        foreach ($args as $key) {
            if (!is_array($ret) || !array_key_exists($key, $ret)) {
                $ret = null;
                break;
            }
            $ret = $ret[$key];
        }
        return $ret;
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
