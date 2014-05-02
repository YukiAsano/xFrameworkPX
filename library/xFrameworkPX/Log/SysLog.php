<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * xFrameworkPX_Log_SysLog Class File
 *
 * PHP versions 5
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    SVN $Id: Dispatcher.php 1475 2010-02-27 23:45:20Z kotsutsumi $
 */

// {{{ xFrameworkPX_Log_SysLog

/**
 * xFrameworkPX_Dispatcher Class
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    Release: 3.5.0
 * @link       http://www.xframeworkpx.com/api/?class=xFrameworkPX_Dispatcher
 */
class xFrameworkPX_Log_SysLog extends xFrameworkPX_Object
{
    // {{{ props

    /**
     * ログレベル
     *
     * @var int
     */
    protected $_loglevel = xFrameworkPX_LOG::INFO;

    /**
     * コントローラオブジェクト
     *
     * @var int
     */
    protected $_controller = null;

    // }}}
    // {{{ __construct

    /**
     * コンストラクタ
     *
     * @return void
     */
    public function __construct($param, $ctrl = null)
    {

        if (isset($param['level']) && is_numeric($param['level'])) {
            // ログレベル設定
            $this->_loglevel = $param['level'];
        }

        if ($ctrl) {
            $this->_controller = $ctrl;
        }

    }

    // }}}
    // {{{ debug

    /**
     * DEBUGログメソッド
     *
     * @param string $message ロガーメッセージ
     * @return void
     */
    public function debug($message)
    {

        foreach (debug_backtrace() as $stack) {
            $location = $stack;
            break;
        }

        $this->_putLog($message, $location, xFrameworkPX_Log::DEBUG);
    }

    // }}}
    // {{{ info

    /**
     * INFOログメソッド
     *
     * @param string $message ロガーメッセージ
     * @return void
     */
    public function info($message)
    {

        foreach (debug_backtrace() as $stack) {
            $location = $stack;
            break;
        }

        $this->_putLog($message, $location, xFrameworkPX_Log::INFO);
    }

    // }}}
    // {{{ warning

    /**
     * WARNINGログメソッド
     *
     * @param string $message ロガーメッセージ
     * @return void
     */
    public function warning($message)
    {

        foreach (debug_backtrace() as $stack) {
            $location = $stack;
            break;
        }

        $this->_putLog($message, $location, xFrameworkPX_Log::WARNING);
    }

    // }}}
    // {{{ error

    /**
     * ERRORログメソッド
     *
     * @param string $message ロガーメッセージ
     * @return void
     */
    public function error($message)
    {

        foreach (debug_backtrace() as $stack) {
            $location = $stack;
            break;
        }

        $this->_putLog($message, $location, xFrameworkPX_Log::ERROR);
    }

    // }}}
    // {{{ fatal

    /**
     * FATALログメソッド
     *
     * @param string $message ロガーメッセージ
     * @return void
     */
    public function fatal($message)
    {

        foreach (debug_backtrace() as $stack) {
            $location = $stack;
            break;
        }

        $this->_putLog($message, $location, xFrameworkPX_Log::FATAL);
    }

    // }}}
    // {{{ putLog

    /**
     * ログ出力メソッド
     *
     * @param string $message ログメッセージ
     * @param array $location ロケーション情報配列
     * @param string $channel チャンネル名
     * @param int $level ログレベル
     * @return void
     */
    private function _putLog($message, $location, $level)
    {

        // ログレベル設定にあわせて出力
        if ($this->_loglevel <= $level) {

            $this->execute($message, $level, $location);

        }

    }

    // }}}
    // {{{ getLoglevel

    /**
     * ログレベル取得メソッド
     *
     * @return int
     */
    public function getLoglevel ()
    {

        return $this->_loglevel;

    }

    // }}}
    // {{{ execute

    /**
     * ロギング実行メソッド
     *
     * @param $message ログメッセージ
     * @param $level ログレベル文字列
     * @param $location ロケーション情報
     * @return void
     */
    public function execute($message, $level, $location)
    {
        $buffer = '';
        $buffer = $message . "\n";

        // Windowsの場合は出力するメッセージをSJISで出力
        // メッセージは、管理ツール>イベントビューアで確認できる。
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $buffer = mb_convert_encoding($buffer, 'SJIS');
        }

        // 本番環境だけはfacilityを切り替える。
        $facility = LOG_USER;

        if (xFrameworkPX_Environment::getInstance()->isReal()) {
            $facility = LOG_LOCAL1;
        }

        openlog($this->_putName, LOG_PID, $facility);

        switch ($level) {

            case xFrameworkPX_Log::DEBUG:
                syslog(LOG_DEBUG, $buffer);
            break;

            case xFrameworkPX_Log::INFO:
                syslog(LOG_INFO, $buffer);
            break;

            case xFrameworkPX_Log::WARNING:
                syslog(LOG_WARNING, $buffer);
            break;

            case xFrameworkPX_Log::ERROR:
                syslog(LOG_ERR, $buffer);
            break;

            case xFrameworkPX_Log::FATAL:
                syslog(LOG_ALERT, $buffer);
            break;
        }

        // システムログの接続を切断
        closelog();
    }

    // }}}
    // {{{ convertLevelString

    /**
     * ログレベル文字列変換
     *
     * @param $level ログレベル
     * @return ログレベル文字列
     */
    public function convertLevelString($level)
    {
        $logLevel = '';

        switch ($level) {
            case xFrameworkPX_Log::TRACE:
                $logLevel = 'TRACE';
                break;

            case xFrameworkPX_Log::DEBUG:
                $logLevel = 'DEBUG';
                break;

            case xFrameworkPX_Log::INFO:
                $logLevel = 'INFO';
                break;

            case xFrameworkPX_Log::WARNING:
                $logLevel = 'WARNING';
                break;

            case xFrameworkPX_Log::ERROR:
                $logLevel = 'ERROR';
                break;

            case xFrameworkPX_Log::FATAL:
                $logLevel = 'FATAL';
                break;
        }

        return $logLevel;
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
