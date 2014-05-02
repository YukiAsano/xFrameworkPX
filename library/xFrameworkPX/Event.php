<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * xFrameworkPX_Event Class File
 *
 * PHP versions 5
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX
 * @author     Kazuhiro Yasunaga <yasunaga.weic@gmail.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    SVN $Id: Debug.php 1396 2010-01-19 07:00:14Z kotsutsumi $
 */

// {{{ xFrameworkPX_Event

/**
 * xFrameworkPX_Event Class
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX
 * @author     Kazuhiro Yasunaga <yasunaga.weic@gmail.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    Release: 3.5.0
 * @link       http://www.xframeworkpx.com/api/?class=xFrameworkPX_Log
 */
class xFrameworkPX_Event extends xFrameworkPX_Util_Observable
{
    // {{{ props

    /**
     * イベント名配列
     *
     * @var array
     */
    protected $_events = array();

    /**
     * 設定オブジェクト
     *
     * @var xFrameworkPX_Util_MixedCollection
     */
    protected $_conf;

    /**
     * コントローラクラス接尾辞
     *
     * @var string
     */
    protected $_suffix = "";

    /**
     * ユーザー定義コントローラー
     */
    private $_userController = array();

    /**
     * インスタンス変数
     *
     * @var xFrameworkPX
     */
    protected static $_instance = null;

    /**
     * アクション名変数
     *
     * @var string
     */
    protected $_actionname = null;

    /**
     * イベント設定配列
     *
     * @var array
     */
    protected $_setting = array();

    // }}}
    // {{{ getInstance

    /**
     * インスタンス取得メソッド
     *
     * @return xFrameworkPXインスタンス
     */
    public static function getInstance($conf = array())
    {
        // インスタンス取得
        if (!isset(self::$_instance)) {
            self::$_instance = new xFrameworkPX_Event($conf);
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
    public function __construct($conf = array())
    {
        $this->_conf = $conf;
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
    // {{{ setEvent

    public function setEvent($name, $actPath, $setting = array())
    {

        // 同名があっても上書き
        $this->_events[$name] = $actPath;

        // イベント設定
        $this->_setting[$name] = $setting;

    }

    // }}}
    // {{{ removeEvent

    public function removeEvent($name)
    {
        if (isset($this->_events[$name])) {
            unset($this->_events[$name]);
        }
    }

    // }}}
    // {{{ execute

    public function execute($name = null)
    {

        if ($name) {

            $this->_execute($name);
            // 実行したら消去する
            $this->removeEvent($name);

        } else {

            $events = $this->_events;
            foreach ($events as $key => $val) {
                $this->_execute($key);
                // 実行したら消去する
                $this->removeEvent($name);
            }

        }

    }

    // }}}
    // {{{ _execute

    public function _execute ($name)
    {

        // イベントに設定されていなければ終了
        if (!isset($this->_events[$name])) {
            return false;
        }

        // コントローラ設定
        $this->setUp($name, $this->_events[$name]);

        // コントローラ実行
        $this->invoke();

    }

    // }}}
    // {{{ setUp

    public function setUp ($clsName, $includefilename)
    {

        // ファイル存在確認
        if (!file_exists($includefilename)) {
            throw new xFrameworkPX_Controller_Exception(
                sprintf(PX_ERR41000, $includefilename)
            );
        }

        // 読み込み
        include_once $includefilename;

        // クラス存在確認
        if (!class_exists($clsName)) {
            throw new xFrameworkPX_Controller_Exception(
                sprintf(PX_ERR41001, $includefilename, $clsName)
            );
        }

        // コントローラーオブジェクト生成
        $cls = new $clsName($this->_conf);

        $this->setActionName($clsName);

        // イベントリスナー追加
        if (method_exists($cls, 'execute')) {
            $this->addEvents($clsName);
            $this->_userController[$clsName] = $cls;
            $this->on($clsName, array($cls, 'execute'));
        }

        // モジュール生成
        foreach ($cls->modules as $name => $value) {

            if (is_string($value)) {
                $name = $value;
                if (isset($cls->forceConnect)) {
                    $value = array('conn' => $cls->forceConnect);
                } else {
                    $value = array('conn' => 'default');
                }
            }

            $clsPath = str_replace('_', DS, $name);
            $modulePath = normalize_path(
                implode(
                    DS,
                    array($this->_conf['pxconf']['MODULE_DIR'], $clsPath . '.php')
                )
            );

            if (file_exists($modulePath)) {

                // モジュールクラスファイル読み込み
                include_once $modulePath;
            }

            // 設定オブジェクト生成
            $conf = $this->mix($value);

            // データベース設定格納
            $conf->database = $this->_conf->dbconf;

            // PX動作設定格納
            $conf->px = $this->_conf['pxconf'];

            // 実行パス設定
            $conf->execpath = $this->_conf->execpath;

            // コンテンツパス設定
            $conf->contentpath = $this->getContentPath();

            // モジュールオブジェクト生成
            $cls->modules[$name] = new $name($conf, $this);
        }

        // モジュール一覧設定
        foreach ($cls->modules as $name => $value) {

            if (is_string($value)) {
                $name = $value;
            }

            $cls->modules[$name]->modules = $cls->modules;
        }

    }

    // }}}
    // {{{ invoke

    /**
     * 呼び出しメソッド
     *
     * @return void
     */
    public function invoke()
    {
        // 接尾辞を追加
        $actionName = $this->getActionName() . $this->_suffix;

        // イベントディスパッチ
        if ($this->hasListener($actionName)) {

            if ($this->_conf->pxconf['DEBUG'] >= 2) {
                $startUserTime = microtime(true);
            }

            $this->dispatch($actionName);

            // ビュー設定があればビューで出力
            if (
                isset($this->_setting[$actionName]) &&
                isset($this->_setting[$actionName]['view'])
            ) {

                // レイアウト設定取得＆設定
                xFrameworkPX_View::getInstance()->setLayout(
                    $this->getLayout($this->_setting[$actionName])
                 );

                // ビューディスパッチ
                if (!xFrameworkPX_View::getInstance()->dispatch('beforerender')) {
                    return;
                }
                if (!xFrameworkPX_View::getInstance()->dispatch('render')) {
                    return;
                }
                if (!xFrameworkPX_View::getInstance()->dispatch('afterrender')) {
                    return;
                }

            }

            if ($this->_conf->pxconf['DEBUG'] >= 2) {
                xFrameworkPX_Debug::getInstance()->addProfileData(
                    get_class($this),
                    get_class($this->_userController[$actionName]),
                    'execute',
                    'Controller',
                    microtime(true) - $startUserTime
                );
            }

            // ビュー出力後に続行設定がなければ終了
            if (
                isset($this->_setting[$actionName]) &&
                isset($this->_setting[$actionName]['view']) &&
                !isset($this->_setting[$actionName]['continue'])
            ) {
                exit(0);
            }

        }

    }

    // }}}
    // {{{ setActionName

    /**
     * アクション名設定メソッド
     *
     * @param $name アクション名
     * @return void
     */
    public function setActionName($name)
    {
        $this->_actionname = $name;
    }

    // }}}
    // {{{ getActionName

    /**
     * アクション名取得メソッド
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->_actionname;
    }

    // }}}
    // {{{ getLayout

    /**
     * レイアウト取得メソッド
     *
     * @return xFrameworkPX_Util_MixedCollection レイアウト情報オブジェクト
     */
    protected function getLayout($setting)
    {
        // デバッグ用計測開始
        if ($this->_conf->pxconf['DEBUG'] >= 2) {
            $startTime = microtime(true);
        }

        $pxconf = $this->_conf->pxconf;
        $templatefile = '';

        $info = pathinfo($setting['view']);

        $templatefile = $info['basename'];
        $cp = preg_replace('/'.preg_quote($pxconf['WEBROOT_DIR'], '/').'/', '', $info['dirname']);
        $cp = preg_replace('/'.preg_quote('\\').'/', '/', $cp);

        $relpath =  str_repeat(
            '../',
            count(
                explode('/', $this->getContentPath())
            ) - 1
        );

        $ret = $this->mix(
            array(
                'file' => $templatefile,
                'path' => $info['dirname'],
                'cp' => $cp,
                'relpath' => $relpath,
            )
        );

        if ($this->_conf->pxconf['DEBUG'] >= 2) {
            xFrameworkPX_Debug::getInstance()->addProfileData(
                get_class($this),
                'xFrameworkPX_Controller_Web',
                'getLayout',
                'Controller',
                microtime(true) - $startTime
            );
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
