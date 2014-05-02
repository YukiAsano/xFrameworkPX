<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * xFrameworkPX_Mobile Class File
 *
 * PHP versions 5
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    SVN $Id: Mobile.php 1436 2010-01-20 15:46:38Z kotsutsumi $
 */

// {{{ xFrameworkPX_Mobile

/**
 * xFrameworkPX_Mobile Class
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    Release: 3.5.0
 * @link       http://www.xframeworkpx.com/api/?class=xFrameworkPX_Mobile
 */
class xFrameworkPX_Mobile extends xFrameworkPX_Object
{

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

    /**
     * MOBYRENT API接続情報
     *
     * access private
     * var array
     */
    private $_api = array(
        'user'     => '',
        'pwd'      => '',
        'port'     => 443,
        'protocol' => 'https',
        'domain'   => 'www.mobyrent.jp',
        'uri'      => '/api/spec/agent/xml',
    );

    /**
     * APIデータタイプ
     *
     * access private
     * var array
     */
    private $_dataType = array(
        'profile',
        'display',
        'agent',
        'ssl',
        'bluetooth',
        'wifi',
        'share',
    );

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
            self::$_instance = new xFrameworkPX_Mobile();
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
    // {{{ isSmartPhone

    /**
     * スマートフォン判定メソッド
     *
     * @return boolean true:スマホ false:それ以外
     */
    public function isSmartPhone()
    {
        // MOBYRENTのAPIにうまくつながらないので、暫定対応
        $useragents = array(
            'iPhone',  // Apple iPhone
            'iPod',    // Apple iPod touch
            'Android', // 1.5+ Android
            'dream',   // Pre 1.5 Android
            'CUPCAKE', // 1.5+ Android
//             'blackberry9500', // Storm
//             'blackberry9530', // Storm
//             'blackberry9520', // Storm v2
//             'blackberry9550', // Storm v2
//             'blackberry9800', // Torch
//             'webOS', // Palm Pre Experimental
            'incognito', // Other iPhone browser
            'webmate' // Other iPhone browser
        );
        $pattern = '/'.implode('|', $useragents).'/i';
        return preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
//         return ($this->isiPhone() || $this->isAndroid() || $this->isXperia());

    }

    // }}}
    // {{{ isiPhone

    /**
     * iPhone判定メソッド
     *
     * @return boolean true:iPhone false:それ以外
     */
    public function isiPhone()
    {
        return strpos($_SERVER['HTTP_USER_AGENT'],'iPhone');
    }

    // }}}
    // {{{ isAndroid

    /**
     * Android判定メソッド
     *
     * @return boolean true:Android false:それ以外
     */
    public function isAndroid()
    {
        return strpos($_SERVER['HTTP_USER_AGENT'],'Android');
    }

    // }}}
    // {{{ getMobileInfo

    /**
     * 携帯端末情報取得メソッド（API使用）
     *
     * @return boolean true:Xperia false:それ以外
     */
    public function getMobileInfo()
    {
//         $request = array(
//             'method' => 'POST',
//             'port' => $this->_api['port'],
//             'domain' => $this->_api['domain'],
//             'uri' => $this->_api['uri'],
//             'data' => '',
//         );
//         $datas = $this->_request($request);
        // 現在はAPIに通信せず仮データを返却
        return array('hoge');
    }

    // }}}
    // {{{ _request

    /**
     * 携帯情報取得メソッド
     *
     * @access private
     * @param $sendData
     * array(
     *     'method' => 'GET' or 'POST',
     *     'port' => 80 など,
     *     'domain' => 'www.xxxxx.com'など,
     *     'uri' => '/hoge/foo/'など,
     *     'data' => キーバリユ配列か文字列,
     * )
     * @return array
     */

    private function _request($sendData, $time = 1000)
    {

        $port = $sendData['port'];
        $uri = $sendData['domain'].$sendData['uri'];
        $datas = $sendData['data'];
        $send = '';
        $length = 0;

        // データ形成
        if (is_array($datas)) {

            $tmp = array();
            foreach ($datas as $key => $val) {
                $tmp[] = $key."=".urlencode($val);
            }

            $send = join( '&', $arrRequest );

        } else {
            $send = $datas;
        }

        $length = $length + strlen($send);

        // POSTヘッダー生成
        $header  = "POST ".$sendData['uri']." HTTP/1.1\r\n";
        $header .= "Host: ".$sendData['domain']."\r\n";

        $header .= "Accept: parent::env('HTTP_ACCEPT')\r\n";
        $header .= "Accept-Language: parent::env('HTTP_ACCEPT_LANGUAGE')\r\n";
        $header .= "Accept-Encoding: parent::env('HTTP_ACCEPT_ENCODING')\r\n";
        $header .= "Accept-Charset: parent::env('HTTP_ACCEPT_CHARSET')\r\n";

        if ($this->_api['user'] && $this->_api['pwd']) {
            $header .= 'Authorization: Basic'.base64_encode($this->_api['user'].':'.$this->_api['pwd']);
        }

        $header .= "Connection: close\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: $length\r\n";
        $header .= "\r\n";

        // data以外の情報を結合
        $send = $header . $send;

        // APIにリクエスト

        $fp = fsockopen($uri, $port, $arr, $arr2, $time);

        // No Connection Error
        if (!$fp) {
            return false;
        }

        // data部の情報を送信
        fputs($fp, $send);

        // ヘッダーの末尾の改行を挿入
        $end = "\r\n\r\n";
        fputs($fp, $end);

        // APIからのレスポンスを取得
        $res = '';
        while (!feof($fp)) {
            $res .= fgets($fp);
        }

        fclose($fp);

        // chunkedチェック
        $bChunked = $this->chkChunked($res);

        // 本文の取得
        $res = $this->getHttpBody($res);

        // chunked trueで送られるBODY先頭の16進数部分を除去
        if( $bChunked ) {
            $rn = "\r\n";
            $target = strpos($res, $rn);
            $target = $target + strlen($rn);
            $res = substr( $objResponse, $targeet);
        }

        return $res;

    }

    // }}}
    // {{{ getHttpBody

    /**
     * レスポンスボディー取得メソッド
     *
     * httpヘッダーを取り除いた本文の値を取得します。
     *
     * @access public
     * @return string レスポンス本文
     * @param  $ret レスポンス文字列
     */
    public function getHttpBody ($ret) {
        $datas = explode("\r\n\r\n", $ret, 2);
        return $datas[1];
    }

    // }}}
    // {{{ getHttpHeader

    /**
     * レスポンスヘッダ取得メソッド
     *
     * レスポンス内のhttpヘッダーを取得します。
     *
     * @access public
     * @return string httpヘッダ
     * @param  $ret レスポンス文字列
     */
    public function chkChunked ($ret) {

        $datas = explode("\r\n\r\n", $ret, 2);
        $data = $datas[0];

        $tmp = explode("\r\n", $data);
        foreach ($tmp as $val) {

            if (strpos(strtolower($val), 'transfer-encoding') !== false) {

                if (strpos( strtolower( $val ), 'chunked') !== false) {
                    return true;
                }
            }
        }
        return false;
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
