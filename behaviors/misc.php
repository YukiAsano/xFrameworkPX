<?php
/**
 *
 * miscクラス
 *
 * @category   Behavior
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */

// {{{ misc

/**
 * misc Class
 *
 * @category   Behavior
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */

class misc extends xFrameworkPX_Model_Behavior {

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
    // {{{ bindConvertArray

    /**
     * 配列変換メソッド(再帰)
     *
     * @access public
     * @param xFrameworkPX_Util_MixedCollection $mix xFrameworkPX_Util_MixedCollectionオブジェクト
     * @return array
     */
    public function bindConvertArray($mix)
    {
        $ret = array();
        if (($mix instanceof xFrameworkPX_Util_MixedCollection)) {
            $ret = $mix->getArrayCopy();
        } else {
            $ret = $mix;
        }

        if (!is_array($ret)) {
            return $ret;
        }
        foreach ($ret as &$value) {
            if (($value instanceof xFrameworkPX_Util_MixedCollection)) {
                $value = $this->bindConvertArray($value);
            }
        }

        return $ret;

    }

    // }}}
    // {{{ bindCreateQuery

    /**
     * 送信パラメータ取得メソッド
     *
     * @access public
     * @param array $datas パラメータ配列
     * @param array $filter 除去パラメータ名配列
     * @param boolean $enc urlencodeフラグ
     * @return string クエリ文字列
     */
    public function bindCreateQuery(
        $datas,
        $filter = array('cp', '_method'),
        $enc = false
    )
    {

        $ret = '';
        $query = array();

        if (!is_array($datas)) {
            return $ret;
        }
        $query = $this->_createQuery($datas, $filter);

        $ret = implode('&', $query);

        if ($enc) {
            $ret = urlencode($ret);
        }

        return $ret;

    }

    // }}}
    // {{{ _createQuery

    /**
     * 送信パラメータ取得メソッド(再帰)
     *
     * @param array $datas パラメータ配列
     * @param array $filter 除去パラメータ名配列
     * @access private
     * @param string $name  パラメータが配列構成時のkey
     * @param array $names パラメータが配列構成時のkey配列
     * @return string クエリ文字列
     */
    public function _createQuery($datas, $filter = array(), $name = '', $names = array())
    {

        $ret = array();

        if (!is_array($datas)) {
            return $ret;
        }

        if ($name != '') {
            $names[] = $name;
        }

        foreach ($datas as $key => $data) {

            if (in_array($key, $filter)) {
                continue;
            }

            if (is_array($data)) {
                $ret = array_merge(
                    $ret,
                    $this->_createQuery($data, $filter, $key, $names)
                );
            } else {
                if (count($names)) {
                    $str = '';
                    for ($i=0; $i<count($names); ++$i) {
                        if ($i == 0) {
                            $str .= $names[$i];
                        } else {
                            $str .= '['.$names[$i].']';
                        }
                    }
                    $ret[] = $str.'['.$key.']'.'='.$data;
                } else {
                    $ret[] = rawurlencode($key).'='.rawurlencode($data);
                }
            }

        }

        return $ret;

    }

    // }}}
    // {{{ bindGetPager

    /**
     * ページャー情報取得メソッド
     *
     * @access public
     * @param int $page ページ情報
     * @param int $limit 1ページ当たりの表示件数
     * @param int $count 総件数
     * @param int $number 表示ページ数
     * @param string $cond 検索条件
     * @return array ページャー情報配列
     */
    public function bindGetPager($page, $limit, $count, $number = 5, $cond = '')
    {

        $ret = array();
        $pager = array();
        $current = 0;

        if (!is_numeric($page) || !is_numeric($count)) {
            return $ret;
        }

        $max = ceil($count/$limit);

        $leftNum =  floor(($number - 1) / 2);
        $rightNum =  ceil(($number - 1) / 2);

        if ($max <= ($page + $rightNum)) {

            if (($page - $leftNum) <= 1) {
                $sta = 0;
                $end = $number-1;
            } else {
                $sta = $max - $number;
                $end = $count;
            }
        } else if (($page - $leftNum) < 1){
            $sta = 0;
            $end = $number-1;
        } else {
            $sta = ($page - $leftNum);
            $end = ($page + $rightNum);
        }

        $next = false;
        for ($i=$sta; $i<$max; ++$i) {

            if ($i > $end) {
                break;
            }
            $pager[$i] = array(
                'next' => (($count/$limit) - 1 > $i),
                'current' => ($page == $i),
                'prev' => (($page - 1) >= 0),
                'prevpage' => $page - 1,
                'nextpage' => $page + 1,
                'search' => $cond
            );

            if ($page == $i) {
                $current = $page;
                $next = $pager[$i]['next'];
            }

        }

        $ret['pager'] = $pager;
        $ret['first'] = 0;
        $ret['last'] = $max - 1;
        $ret['current'] = $current;
        $ret['disp_current'] = $current + 1;
        $ret['max'] = $count;
        $ret['limit'] = $limit;
        $ret['next'] = $next;
        $ret['under'] = $current * $limit + 1;
        $ret['upper'] = ($limit < ($count - $ret['under'] + 1))
                        ? ($current + 1) * $limit
                        : $count;
        return $ret;

    }

    // }}}
    // {{{ bindClean

    /**
     * ディレクトリクリーンメソッド
     * ディレクトリ内のファイルを削除します
     *
     * @param string $dir 対象ディレクトリ
     * @param string $time 対象経過時間
     * @param string $timeType 対象経過時間のタイプ(H, i, s)
     * @return void
     */
    public function bindClean($dir, $time, $timeType = 'H')
    {

        if ($dir == '/' || !$dir) {
            return false;
        }
        clearstatcache();
        if (!is_dir($dir)) {
            return false;
        }

        if ($dp = opendir($dir)) {

            while (($filename = readdir($dp)) !== false) {

                $fileNames[] = $filename;
            }

            closedir($dp);

            if (is_array($fileNames)) {

                foreach ($fileNames as $name) {

                    if (is_file($dir . $name)) {

                        if (!$time || $this->_checkDate($dir . $name, $time, $timeType)) {
                            unlink($dir . $name);
                        }

                    } else if (
                        is_dir($dir . $name) && ($name != '.' && $name != '..')
                    ) {

                        $this->bindClean($dir.$name.'/', $time, $timeType);

                    }
                }
            }
        }

    }

    // }}}
    // {{{ _checkDate

    /**
     * ファイル生成時間チェックメソッド
     *
     * 指定した時間以前に生成されているかをチェックします。
     *
     * @param string $filename ファイル名
     * @param string $time 対象時間
     * @param string $timeType 対象時間タイプ（H,i,s）
     * @return true 対象時間以前に生成されたファイル false
     */
    public function _checkDate($filename, $time, $timeType = 'H')
    {

        if (!file_exists($filename)) {
            return false;
        }

        $isFileTime = filemtime($filename);
        if ($timeType === 'H') {
            $target = time() - ($time * 60 * 60);
        } else if ($timeType === 'i') {
            $target = time() - ($time * 60);
        } else if ($timeType === 's') {
            $target = time() - ( $time );
        }

        return ($isFileTime <= $target);
    }

    // }}}
    // {{{ bindCopyDirectory

    /**
     * ディレクトリコピーメソッド
     *
     * @param string $baseDir コピー元ディレクトリ
     * @param string $copyDir コピー先ディレクトリ
     * @return bool
     */
    public function bindCopyDirectory($baseDir, $copyDir)
    {

        try {

            $handle = @opendir($baseDir);

            //コピー元ディレクトリが無ければ、コピーしないで正常終了
            if ($handle === false) {
                return true;
            }

            // 保存先ディレクトリ作成
            makeDirectory($copyDir);

            while ($filename = readdir($handle)) {
                if (strcmp($filename,".") != 0 && strcmp($filename,"..") != 0) {
                    if (is_dir($baseDir.$filename)) {
                        if (!empty($filename) && !file_exists($copyDir.$filename)) {
                            mkdir($copyDir.$filename);
                        }
                        copyDirectory($baseDir.$filename, $copyDir.$filename);
                    } else {
                        if (file_exists($copyDir.$filename)) {
                            unlink($copyDir.$filename);
                        }
                        copy($baseDir.$filename, $copyDir.$filename);
                    }
                }
            }

        } catch (xFrameworkPX_Exception $e) {
            return false;
        }

        return true;

    }

    // }}}
    // {{{ bindGetContentsUrl

    /**
     * 設置ディレクトリ取得メソッド
     *
     * @param $type
     * @param bool $ssl
     * @return string
     */
    public function bindGetContentsUrl($type, $ssl = false)
    {

        $ret = '';

        $info = xFrameworkPX_Environment::getInstance()->get('categorysetting', $type);

        $protocol = ($ssl)? 'https://' : 'http://';

        if (isset($info['domain']) && isset($info['userdir']) && isset($info['dir'])) {
            $ret = $protocol.str_replace('//', '/', implode('/', $info));
        }

        return $ret;

    }

    // }}}
    // {{{ bindGetCurrentPageInfo

    /**
     * 現在ページの情報取得メソッド
     *
     * @return string
     */
    public function bindGetCurrentPageInfo()
    {

        $base = base_name();
        $uri = $this->env('REQUEST_URI');

        // {{{ ユーザーディレクトリ対応

        $userDir = '';
        if (substr($uri,0, 2) == '/~') {
            $userDir = substr($uri, 0, strpos($uri, '/', strlen('/~')));
        }
        if ($userDir) {
            $uri = preg_replace('/'.preg_quote($userDir, '/').'/', '', $uri);
            if (strlen($uri) > 1 && startsWith($uri, '/')) {
                $uri = substr($uri, 1);
            }
        }

        // }}}
        // {{{ ドメインをセット

        $httpBase = '';
        $sslBase  = '';

        if (preg_match('/^http:/', $base)) {
            $httpBase = $base;
            $sslBase = preg_replace('/^http:/', 'https:', $base);
        } else if (preg_match('/^https:/', $base)) {
            $httpBase = preg_replace('/^https:/', 'http:', $base);
            $sslBase = $base;
        }

//        if ($uri == '/') {
        if (startsWith($uri, '/')) {
//            $uri = '';
            $uri = preg_replace('/^\//', '', $uri);
        }

        $ret = array(
            'currentPage' => $base.$uri,
            'baseName'    => $base,
            'httpBase'    => $httpBase,
            'sslBase'     => $sslBase,
            'uri'         => $uri,
        );

        return $ret;

    }

    // }}}
    // {{{ bindNormalizeKeyword

    /**
     * キーワード正規化メソッド
     *
     * @param string $target 対象キーワード
     * @param string $enc エンコード
     * @return string
     */
    public function bindNormalizeKeyword ($target, $enc = 'UTF-8')
    {

        $this->_load();

        $ret = '';

        $convString = new convNormalizeString();
        $ret = $convString->convert($target, $enc);

        $convZenKana = new convZenkaku('KVA');
        $ret = $convZenKana->convert($ret, $enc);

        return $ret;

    }

    // }}}
    // {{{ _load

    /**
     * キーワード正規化メソッド
     *
     */
    private function _load ()
    {

        // {{{ 使用する時に自動でrequireするように設定

        xFrameworkPX_Loader_Auto::register(
            'convNormalizeString', //クラス名
            realpath('../converts/NormalizeString.php')   //ファイルパス
        );

        xFrameworkPX_Loader_Auto::register(
            'convZenkaku',
            realpath('../converts/Zenkaku.php')
        );

        // }}}

    }

    // }}}
    // {{{ bindIsStringValid

    /**
     * 有効文字列判定メソッド
     *
     * true, false, yes, noの文字列をbooleanで返します。
     *
     * @param $target 対象文字列
     * @param $valid (true:true, yesの場合にtrueを返す, false:false,noの場合にtrueを返す)
     * @return boolean
     */
    public function bindIsStringValid($target, $valid = true)
    {

        $target = strtolower($target);

        if ($valid) {
            return ($target === 'true' || $target === 'yes');
        }

        return ($target === 'false' || $target === 'no');

    }

    // }}}
    // {{{ bindDateChk

    /**
     * 日付フォーマット簡易チェックメソッド
     *
     * @param $date 日付
     * @return boolean 日付ならtrue
     */
    public function bindDateChk($date)
    {

        $tmp = null;
        if (!is_null($date) && $date != '') {
            // 区切り文字を念のため変える
            $date = str_replace('-', '/', $date);
            $tmp = explode('/', $date);
        } else {
            return false;
        }
        return isset($tmp[0], $tmp[1], $tmp[2]) && is_numeric($tmp[0].$tmp[1].$tmp[2]);

    }

    // }}}
    // {{{ bindGetTax

    /**
     * 消費税取得メソッド
     *
     * 指定した年月日の消費税を取得します（tax.ymlを使用）
     *
     * @param $target 指定年月日（デフォルトはnullで現在の年月日）
     *                YYYY/mm/ddかYYYY-mm-ddの形で指定します。
     * @return float
     */
    public function bindGetTax($target = null)
    {

        $tmp = array();
        $ret = null;

        if (is_null($target)) {
            $target = date('Y-m-d');
        }

        // {{{ データチェック

        if (strpos($target, '-')) {
            $tmp = explode('-', $target);
        } else if (strpos($target, '/')) {
            $tmp = explode('/', $target);
        } else {
            return $ret;
        }

        if (count($tmp) != 3) {
            return $ret;
        }

        $year = $tmp[0];
        $month = $tmp[1];
        $day = $tmp[2];

        if (!checkdate($month, $day, $year)) {
            return $ret;
        }

        // }}}

        $time = strtotime($target);

        $master = getSetting('tax');

        $datas = array();
        foreach ($master as $k => $v) {
            $datas[strtotime($k)] = $v;
        }

        ksort($datas);

        $prev = null;
        foreach ($datas as $k => $v) {
            if ($k > $time) {
                break;
            }
            $prev = $k;
        }

        if (!isset($datas[$prev])) {
            return $ret;
        }

        $ret = $datas[$prev]['tax'];

        return $ret;

    }

    // }}}

}

// }}}
