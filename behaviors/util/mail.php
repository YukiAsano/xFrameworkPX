<?php
/**
 *
 * mailクラス
 *
 * @category   Behavior
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */

// {{{ mail

/**
 * mail Class
 *
 * @category   Behavior
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */

class util_mail extends xFrameworkPX_Model_Behavior {

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

        xFrameworkPX_Loader_Auto::register(
            'xFrameworkPX_Validation_Email',            //クラス名
            'xFrameworkPX/Validation/Email.php'         //ファイルパス
        );

        parent::__construct($module);

    }
    // }}}
    // {{{ _isEmail

    /**
     * Email形式チェック
     *
     * @access public
     * @return boolean
     */
    private function _isEmail ($target) {

        //フレームワーク上の入力チェックを実行
        $email = new xFrameworkPX_Validation_Email();
        return $email->validate($target);

    }

    // }}}
    // {{{ bindGetSendMailStr

    /**
     * メール文字列取得用メソッド
     *
     * @param $target 対象文字列
     * @param $option オプション
     * @return string
     */
    public function bindGetSendMailStr ($target, $option = array())
    {

        //初期化
        $ret = "";
        //オプションの規定値を設定
        $option += array(
            'length' => 70, //分割文字数
        );

        //文字列以外はそのまま返却
        if (!is_string($target)) {
            return $target;
        }

        //内部エンコードの切り替え
        $enc = mb_internal_encoding();
        mb_internal_encoding('UTF-8');


        //改行ごとに配列に分割
        $tmpArray = explode("\r\n", $target);

        //改行で分割した行数分、ループ
        foreach ($tmpArray as $line) {
            //初期化
            $str_array = array();

            if ($line != "") {
                //行に対し、末尾まで計算
                for ( $i=0; $i < mb_strlen($line); $i+=$option['length'] ) {
                    //分割する文字数ごとに配列に追加
                    $str_array[] = mb_substr($line,$i,$option['length'],'UTF-8');
                }
            } else {
                $str_array[] = ""; //改行のみの場合、空文字列の配列を追加
            }

            $str_array[] = ""; //末尾の改行用に空文字を追加
            //分割した行を改行コードで結合し、返却用の文字列に追加
            $ret .= implode("\r\n", $str_array);
        }

        //内部エンコードを元に戻す
        mb_internal_encoding($enc);

        return $ret;

    }

    // }}}
    // {{{ bindGetMailFrom

    /**
     * メールのFromの取得
     *
     * @access public
     * @return boolean
     */
    public function bindGetMailFrom($from = null)
    {

        //初期化
        $retFrom = null;

        $env = xFrameworkPX_Environment::getInstance();

        // テスト環境の場合はFromを固定で返却
        if ($env->isDev() || $env->isTest()) {
            return 'testhoge@example.com';
        }

        //env.yml上のFromを取得
        $retFrom = $env->get('mail', 'from');

        //引数のFromが有効の場合
        if ($from && $this->_isEmail($from)) {
            //引数のFromに上書き
            $retFrom = $from;
        }

        return $retFrom;

    }

    // }}}

}

// }}}
