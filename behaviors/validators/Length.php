<?php
/**
 *
 * validatorsクラス
 *
 * @category   Behavior
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

// {{{ validators

/**
 * validators_Length Class
 *
 * @category   Behavior
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

class validators_Length extends xFrameworkPX_Model_Behavior {

    // {{{ __construct

    /**
     * コンストラクタ
     *
     * @access public
     */
    public function __construct($module) {

        //フレームワーク上のValidationを呼び出すための設定をする
        xFrameworkPX_Loader_Auto::register(
            'xFrameworkPX_Validation_NotEmpty',         //クラス名
            'xFrameworkPX/Validation/NotEmpty.php'      //ファイルパス
        );
        xFrameworkPX_Loader_Auto::register(
            'xFrameworkPX_Validation_TextLength',       //クラス名
            'xFrameworkPX/Validation/TextLength.php'    //ファイルパス
        );

        parent::__construct($module);

    }

    // }}}
    // {{{ _NotEmpty

    /**
     * 空チェック
     *
     * @access public
     * @param $target
     * @return boolean
     */
    private function _NotEmpty($target) {

        //フレームワーク上の入力チェックを実行
        $empty = new xFrameworkPX_Validation_NotEmpty();
        return $empty->validate($target);

    }

    // }}}
    // {{{ _TextLength

    /**
     * 文字列長チェック
     *
     * @access public
     * @param $target
     * @param $opt
     * @return boolean
     */
    private function _TextLength($target, $opt) {

        //フレームワーク上の入力チェックを実行
        $phone = new xFrameworkPX_Validation_TextLength();
        return $phone->validate($target, $opt);

    }

    // }}}
    // {{{ bindValidateTextLengthWithStr

    /**
     * 特定文字付き文字数チェックメソッド
     * カンマやピリオドが混じった数値の文字数チェックに使用
     *
     * @param $target ターゲット値（使用しない）
     * @param $opt 
     *  array(
     *      'remove' => array(
     *          ',', '.' // 取り除く文字列
     *      ),
     *      'maxlength' => 最大値,
     *      'minlength' => 最小値
     *  )
     *
     * @return boolean
     */
    public function bindValidateTextLengthWithStr($target, $opt = array())
    {

        // 【お約束】空はチェックしない
        if (!$this->_NotEmpty($target)) {
            return true;
        }

        if (isset($opt['remove']) && is_array($opt['remove'])) {
            // 文字を取り除く
            foreach ($opt['remove'] as $str) {
                $target = str_replace($str, '', $target);
            }
        }

        // 文字長チェック実行
        return $this->_TextLength($target, $opt);

    }

    // }}}
    // {{{ bindValidateTextLengthMulti

    /**
     * 複数用文字数チェック
     *
     * @access public
     * @param array $targets チェック対象文字列配列
     * @param array $opt オプション
     * @return boolean
     */
    public function bindValidateTextLengthMulti($targets, $opt = array()) {

        if (!is_array($targets)) {

            // 配列で入ってこない場合はtrue
            return true;

        }

        foreach ($targets as $target) {

            if (!$this->bindValidateTextLengthWithStr($target, $opt)) {
                return false;
            }

        }

        return true;

    }

    // }}}
    // {{{ bindValidateTextLengthCond

    /**
     * 条件付き文字列長チェック
     *
     * @param string $target 入力データ
     * @param array $opt nameで指定したフィールドに
     *                 valueで指定した値がある場合にチェックします
     * array(
     *     'name' => フィールド名
     *     'targetValue' => フィールドの値
     * )
     * @return boolean
     */
    public function bindValidateTextLengthCond($target, $opt = array())
    {

        // 【お約束】ローカル変数初期化
        $datas = null;
        $chkData = null;

        // 【お約束】空はチェックしない
        if (!$this->_NotEmpty($target)) {
            return true;
        }

        // 全データ取得
        $datas = $this->module->getTargetDatas();

        if (
            !isset($datas[$opt['name']]) ||
            $this->_NotEmpty($opt['name'])
        ) {
            // ターゲットとなるものが空ならtrueを返す
            return true;
        }

        $chkData = $datas[$opt['name']];

        if (is_object($chkData)) {

            // オブジェクトなら変換
            $chkData = $this->module->convertArray($datas[$opt['name']]);

        } else if (is_string($chkData)) {

            // 文字列なら配列化
            $chkData = array(
                $chkData
            );

        }

        if (array_search($opt['value'], $chkData) !== false) {
            // 対象の値が設定した値なら、チェック実行
            return $this->bindValidateTextLengthWithStr($target, $opt);
        }

        return true;

    }

    // }}}
    // {{{ bindByteLength

    /**
     * バイト長チェック
     *
     * @access public
     * @param $target
     * @param $opt
     * @return boolean
     */
    public function bindValidateByteLength($target, $opt) {

        // バイト単位での長さチェック

        // 【お約束】ローカル変数初期化
        $byte = null;

        // 【お約束】空はチェックしない
        if (!$this->_NotEmpty($target)) {
            return true;
        }

        // エンコード種別設定
        $encoding = (isset($opt['encode']))
                   ? (string)$opt['encode']
                   : 'utf-8';

        // バイト取得
        $byte = strlen(bin2hex($target)) / 2;

        // 文字数チェック
        if (isset($opt['maxlength']) &&
            $byte > (int)$opt['maxlength']
        ) {
            return false;
        }

        if (
            isset($opt['minlength']) &&
            $byte < (int)$opt['minlength']
        ) {
            return false;
        }

        return true;

    }

    // }}}
    // {{{ bindValidateByteLengthCond

    /**
     * 条件付きバイト長チェック
     *
     * @param string $target 入力データ
     * @param array $opt nameで指定したフィールドに
     *              valueで指定した値がある場合にチェックします
     * array(
     *     'name' => フィールド名
     *     'targetValue' => フィールドの値
     * )
     * @return boolean
     */
    public function bindValidateByteLengthCond($target, $opt = array()) {

        // 【お約束】ローカル変数初期化
        $datas = null;
        $chkData = null;

        // 【お約束】空はチェックしない
        if (!$this->_NotEmpty($target)) {
            return true;
        }

        // 全データ取得
        $datas = $this->module->getTargetDatas();

        if (
            !isset($datas[$opt['name']]) ||
            $this->_NotEmpty($opt['name'])
        ) {
            // ターゲットとなるものが空ならtrueを返す
            return true;
        }

        $chkData = $datas[$opt['name']];

        if (is_object($chkData)) {

            // オブジェクトなら変換
            $chkData = $this->module->convertArray($datas[$opt['name']]);

        } else if (is_string($chkData)) {

            // 文字列なら配列化
            $chkData = array(
                $chkData
            );

        }

        if (array_search($opt['value'], $chkData) !== false) {
            // 対象の値が設定した値なら、チェック実行
            return $this->bindValidateByteLength($target, $opt);
        }

        return true;

    }

    // }}}

}

// }}}
