<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * xFrameworkPX_Master Class File
 *
 * PHP versions 5
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX
 * @author     Yuki Asano <asano@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    SVN $Id: Yaml.php 1181 2010-01-06 03:27:06Z tamari $
 */

// {{{ xFrameworkPX_Master

/**
 * xFrameworkPX_Master Class
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX
 * @author     Yuki Asano <asano@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    Release: 3.5.0
 * @link       http://www.xframeworkpx.com/api/?class=xFrameworkPX_Yaml
 */
class xFrameworkPX_Master
{

    // {{{ props

    /**
     * 取得タイプ
     *
     * @access protected
     */
    protected $_type = 'master';

    /**
     * 取得YML名
     *
     * @access protected
     */
    protected $_name;

    /**
     * このクラスのインスタンス
     *
     * @access private
     */
    private static $_instance;

    /**
     * ベースマスタディレクトリ
     *
     * @access protected
     */
    protected $_masterDir;

    /**
     * ベースキャッシュディレクトリ
     *
     * @access protected
     */
    protected $_cacheDir;

    /**
     * マスタファイルの拡張子
     *
     * @access protected
     */
    protected $_masterExt = '.yml';

    /**
     * キャッシュファイルの拡張子
     *
     * @access protected
     */
    protected $_cacheExt = '.cache';

    /**
     * キャッシュ強制生成
     *
     * @access protected
     */
    protected $_forceCreateCache = false;

    /**
     * ヘッダー情報をキャッシュする配列
     *
     * @access protected
     */
    protected $_head;

    /**
     * 1度読み込んだマスタデータをキャッシュする配列
     *
     * @access protected
     */
    protected $_datas;

    /**
     * オリジナルのYAMLデータ
     */
    protected $_orgData;

    /**
     * ルート情報
     */
    protected $_route;

    // }}}
    // {{{ __construct

    /**
     * コンストラクタ（継承禁止）
     *
     * @access public
     */
    final public function __construct()
    {

        // ディレクトリ設定
        $this->_masterDir = sprintf('..%1$sconfigs%1$syml', DS);
        $this->_cacheDir = sprintf('..%1$scache%1$scache', DS);

    }

    // }}}
    // {{{ getInstance

    /**
     * インスタンス取得
     *
     * @access public
     * @param none
     * @return object インスタンス
     */
    public static function getInstance($type = null)
    {

        if (!self::$_instance) {
            self::$_instance = new self;
        }

        if (!is_null($type)) {
            self::$_instance->_type = $type;
        }

        return self::$_instance;
    }

    // }}}
    // {{{ get

    /**
     * Masterの値を取得する
     *
     * @access public
     * @param 第一パラメータ：取得するマスタ名、第二以降：マスタ内で取得するキーを指定
     * @return mixed マスタの値
     */
    public function get()
    {
        $args = func_get_args();
        return call_user_func_array(
            array($this, '_get'),
            $args
        );
    }

    // }}}
    // {{{ getRoute

    /**
     * ルート情報を取得する
     *
     * @access public
     * @param 取得するマスタ名
     * @return array ルート情報
     */
    public function getRoute($name)
    {
        return $this->_datas[$this->_type][$name]['__route'];
    }

    // }}}
    // {{{ setExt

    /**
     * マスタの拡張子を変更する
     *
     * @param string $ext
     * @return object インスタンス
     */
    public function setExt($ext)
    {
        $this->_masterExt = '.'.$ext;
        return $this;
    }

    // }}}
    // {{{ setDir

    /**
     * マスタのディレクトリを変更する
     *
     * @param unknown_type $dir
     * @return object インスタンス
     */
    public function setDir($dir)
    {
        $this->_masterDir = $dir;
        return $this;
    }

    // }}}
    // {{{ setCacheDir

    /**
     * キャッシュのディレクトリを変更する
     *
     * @param unknown_type $dir
     * @return object インスタンス
     */
    public function setCacheDir($dir)
    {
        $this->_cacheDir = $dir;
        return $this;
    }

    // }}}
    // {{{ forceCreateCache

    /**
     * キャッシュ強制生成切り替えメソッド
     *
     * @access public
     */
    public function forceCreateCache()
    {
        $this->_forceCreateCache = true;
        return $this;
    }

    // }}}
    // {{{ makeMaster

    /**
     * マスタファイル生成メソッド
     *
     * @param string $name マスタ名
     * @param mixed $data データ
     * @param boolean $makeCache キャッシュ生成フラグ
     * @access public
     * @return boolean
     */
    public function makeMaster($name, $data, $makeCache = true)
    {
        $this->_name = $name;

        // マスタファイル生成
        $res = file_forceput_contents(
            $this->_getMasterFilePath($name),
            xFrameworkPX_Yaml::encode($data)
        );

        if ($res !== false && $makeCache) {
            // キャッシュ生成
            $ret = $this->makeCache($name, $data);
        }
        return !$res ? false : true;
    }

    // }}}
    // {{{ makeCache

    /**
     * キャッシュファイル生成メソッド
     *
     * @param string $name キャッシュ名
     * @param mixed $data データ
     * @access public
     * @return boolean
     */
    public function makeCache($name, $data)
    {
        $this->_name = $name;

        // ファイルタイムスタンプ情報追加
        $data['__masterfiletimestamp'] = time();

        // シリアライズデータファイル生成
        $res = file_forceput_contents(
            $this->_getCacheFilePath($name),
            serialize($data)
        );
        return !$res ? false : true;
    }

    // }}}
    // {{{ _get

    /**
     * Masterの値を取得する内部関数
     *
     * @access protected
     */
    protected function _get()
    {
        $args = func_get_args();

        if (count($args) < 1) {
            throw new Exception('パラメータの数が不正です');
        }

        $type = $this->_type;
        $data = null;
        $name = array_shift($args);
        $this->_name = $name;
        $ret = null;

        if (!isset($this->_datas[$type][$name])) {

            // ファイルからデータ取得
            $masterPath = $this->_getMasterFilePath($name);
            $cachePath = $this->_getCacheFilePath($name);

            if (!file_exists($cachePath) || $this->_forceCreateCache === true) {

                // ファイルからデータ取得・キャッシュ生成
                $data = $this->_getData($masterPath, $cachePath);

            } else {

                // キャッシュ・シリアライズされたデータ取得
                $data = unserialize(file_get_contents($cachePath));

                if (
                    isset($data['__masterfiletimestamp']) &&
                    file_exists($masterPath) &&
                    filemtime($masterPath) > $data['__masterfiletimestamp']
                ) {
                    // ファイルからデータ取得・キャッシュ生成
                    $data = $this->_getData($masterPath, $cachePath);
                }

            }

//             // タイムスタンプ情報削除
//             unset($data['__masterfiletimestamp']);

            // YAMLをヘッダーとデータに分解
            if (isset($data['__data']['head'])) {
                $head = $data['__data']['head'];
                $data = $data['__data']['data'];
            } else {
                $head = array();
                $data = $data;
            }

            $this->_heads[$type][$name] = $head;
            $this->_datas[$type][$name] = $data;

        }

        // フラグをfalseに
        $this->_forceCreateCache = false;

        if (!array_key_exists('__data', $this->_datas[$type][$name])) {
            $this->_datas[$type][$name] = array(
                '__data' => $this->_datas[$type][$name],
            );
        }
        $ret = $this->_datas[$type][$name]['__data'];

        foreach ($args as $key) {
            if (!isset($ret[$key])) {
                $ret = null;
                break;
            }
            $ret = $ret[$key];
        }
        return $ret;
    }

    // }}}
    // {{{ _getData

    /**
     * データ取得・キャッシュ生成
     *
     * @param string $mPath マスタファイルパス
     * @param string $mPath キャッシュファイルパス
     * @access protected
     * @return mixed ファイルから取得したデータ
     */
    protected function _getData($mPath, $cPath)
    {

        if (!file_exists($mPath)) {
            throw new Exception("マスターファイルが見つかりません({$mPath})");
        }

        $data = array();

        if (endsWith($mPath, 'yml')) {
            // YMLからデータ取得
            $this->_orgData = xFrameworkPX_Yaml::decode($mPath);

            // 継承処理
            $data['__data'] = $this->_getExtendsData();

        } else if (endsWith($mPath, 'xml')) {
            // XMLからデータ取得
            $data['__data'] = $this->_xmlToArray(
                simplexml_load_string(
                    file_get_contents($mPath)
                )
            );
        } else if (endsWith($mPath, 'json')) {
            // JSONからデータ取得
            $data['__data'] = json_decode(
                file_get_contents($mPath), true
            );
        }

        // ルート情報追加
        if (isset($this->_route[$this->_type][$this->_name])) {
            $data['__route'] = $this->_route[$this->_type][$this->_name];
        }

        // ファイルタイムスタンプ情報追加
        $data['__masterfiletimestamp'] = filemtime($mPath);

        // シリアライズデータファイル生成
        // {{{ バッチ経由では実行しない

        if ($_SERVER['SCRIPT_FILENAME'] != 'px.php') {
            file_forceput_contents(
                $cPath,
                serialize($data)
            );
        }

        // }}}

        return $data;

    }

    // }}}
    // {{{ _getExtendsData

    /**
     * 継承処理
     */
    protected function _getExtendsData()
    {
        $ret = array();

        // 各値の継承処理
        foreach ($this->_orgData as $key => $val) {
            $realKeyName = $this->_getRealKeyName($key);
            $ret[$realKeyName] = $this->_getExtendsDataRecursive($key, $key);
        }
        return $ret;
    }

    // }}}
    // {{{ _getExtendsDataRecursive

    /**
     * 継承処理を再帰的に行う
     */
    protected function _getExtendsDataRecursive($key, $routeKey = null)
    {
        static $_routeKey = null;

        $val = null;

        if ($routeKey !== null) {
            $_routeKey = $routeKey;
        }

        // 自身の値を取得
        if (array_key_exists($key, $this->_orgData)) {
            $val = $this->_orgData[$key];
        }

        // 継承対象であれば継承処理を行う
        if (preg_match('/ extends /', $key)) {
            $realKeyName = $this->_getRealKeyName($key);
            $extKeyName = $this->_getExtendsKeyName($key);
            $this->_route[$this->_type][$this->_name][$this->_getRealKeyName($_routeKey)][] = $extKeyName;

            // 自身のデータがある場合はマージする
            if ($val) {
                $val = array_merge((array)$this->_getExtendsDataRecursive($this->_getOriginalKeyName($extKeyName)), (array)$val);
            } else {
                $val = $this->_getExtendsDataRecursive($this->_getOriginalKeyName($extKeyName));
            }
        }
        return $val;
    }

    // }}}
    // {{{ _getOriginalKeyName

    /**
     * オリジナルのキー名を取得
     */
    protected function _getOriginalKeyName($key)
    {
        $ret = $key;
        $keys = array_keys($this->_orgData);
        if (!preg_match('/ extends /', $key) && !in_array($key, $keys)) {
            $exp = "/^{$key} extends .*$/";
            foreach ($keys as $val) {
                if (preg_match($exp, $val)) {
                    $ret = $val;
                    break;
                }
            }
        }
        return $ret;
    }
    // }}}
    // {{{ _getRealKeyName

    /**
     * 実際に使用するキー名を取得
     */
    protected function _getRealKeyName($key)
    {
        return trim(preg_replace('/ extends .*$/', '', $key));
    }

    // }}}
    // {{{ _getExtendsKeyName

    /**
     * 継承先のキー名を取得
     */
    protected function _getExtendsKeyName($key)
    {
        return trim(preg_replace('/^.* extends /', '', $key));
    }

    // }}}
    // {{{ _getDirName

    /**
     * ディレクトリ名を返却
     *
     * @access protected
     */
    protected function _getDirName()
    {
        return $this->_masterDir;
    }

    // }}}
    // {{{ _getMasterFilePath

    /**
     * マスタファイルのパスを生成する
     *
     * @access protected
     * @param string $masterName マスタ名
     * @return string マスタファイルパス
     */
    protected function _getMasterFilePath($masterName)
    {
        return sprintf(
            '%1$s%2$s%3$s%4$s%5$s%6$s',
            $this->_getDirName(),
            DS,
            $this->_type,
            DS,
            $masterName,
            $this->_masterExt
        );
    }

    // }}}
    // {{{ _getCacheFilePath

    /**
     * キャッシュファイルのパスを生成する
     *
     * @access protected
     * @param string $cacheName キャッシュ名
     * @return string キャッシュファイルパス
     */
    protected function _getCacheFilePath($cacheName)
    {
        return sprintf(
            '%1$s%2$s%3$s%4$s%5$s%6$s',
            $this->_cacheDir,
            DS,
            $this->_type,
            DS,
            $cacheName,
            $this->_cacheExt
        );
    }

    // }}}
    // {{{ _xmlToArray

    /**
     * XML→配列処理変換メソッド
     *
     * @access protected
     * @params object XMLオブジェクト
     * @return array 配列
     */
    protected function _xmlToArray($obj) {

        $res = array();
        $list = null;

        if (is_object($obj)) {

            $list = get_object_vars($obj);

            while(list($k,$v) = each($list)) {
                $res[$k] = $this->_xmlToArray($v);
            }

        } else if (is_array($obj)) {

            while (list($k,$v) = each($obj)) {
                $res[$k] = $this->_xmlToArray($v);
            }

        } else {

            return $obj;

        }

        return $res;

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
