<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * xFrameworkPX_Db Class File
 *
 * PHP versions 5
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    SVN $Id: Debug.php 1396 2010-01-19 07:00:14Z kotsutsumi $
 */

// {{{ xFrameworkPX_Db

/**
 * xFrameworkPX_Db Class
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    Release: 3.5.0
 * @link       http://www.xframeworkpx.com/api/?class=xFrameworkPX_Log
 */
class xFrameworkPX_Db extends xFrameworkPX_Object
{
    // {{{ props

    /**
     * PDOオブジェクト
     *
     * @var array
     */
    protected $_db = array();

    /**
     * 実行クエリ文字列
     *
     * @var string
     */
    protected $_query = null;

    /**
     * xFrameworkPX設定オブジェクト
     *
     * @var array
     */
    protected $_pxconf;

    /**
     * インスタンス変数
     *
     * @var xFrameworkPX
     */
    protected static $_instance = null;

    // }}}
    // {{{ getInstance

    /**
     * インスタンス取得メソッド
     *
     * @return xFrameworkPXインスタンス
     */
    public static function getInstance($conf = null)
    {
        // インスタンス取得
        if (!isset(self::$_instance)) {
            self::$_instance = new xFrameworkPX_Db();
        }

        return self::$_instance;
    }

    // }}}
    // {{{ __construct

    /**
     * コンストラクタ
     *
     * @return void
     */
    protected function __construct()
    {
    }

    // }}}
    // {{{ __clone

    /**
     * インスタンス複製メソッド
     *
     * @return void
     */
    public final function __clone()
    {
        throw new xFrameworkPX_Config_Exception(
            sprintf(PX_ERR90001, get_class($this))
        );
    }

    // }}}
    // {{{ getConn

    public function getConn($connection)
    {

        if (isset($this->_db[$connection])) {
            return $this->_db[$connection];
        }

        return null;

    }

    // }}}
    // {{{ setConn

    public function setConn($connection, $pdo)
    {
        $this->_db[$connection] = $pdo;
    }

    // }}}
    // {{{ getQuery

    public function getQuery()
    {

        return $this->_query;

    }

    // }}}
    // {{{ setConn

    public function setQuery($query)
    {
        $this->_query = $query;
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
