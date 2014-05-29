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
 * validators_Email Class
 *
 * @category   Behavior
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

class validators_Email extends xFrameworkPX_Model_Behavior {

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
            'xFrameworkPX_Validation_Email',            //クラス名
            'xFrameworkPX/Validation/Email.php'         //ファイルパス
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
    private function _NotEmpty ($target) {

        //フレームワーク上の入力チェックを実行
        $empty = new xFrameworkPX_Validation_NotEmpty();
        return $empty->validate($target);

    }

    // }}}
    // {{{ _isEmail

    /**
     * Email形式チェック
     *
     * @access public
     * @param $target
     * @return boolean
     */
    private function _isEmail ($target) {

        //フレームワーク上の入力チェックを実行
        $email = new xFrameworkPX_Validation_Email();
        return $email->validate($target);

    }

    // }}}
    // {{{ bindValidateEmailMulti

    /**
     * 複数用メールアドレスチェック
     *
     * @access public
     * @param $targets
     * @param array $opt
     * @internal param \チェック対象文字列 $target
     * @return boolean
     */
    public function bindValidateEmailMulti ($targets, $opt = array()) {

        if (!is_array($targets)) {

            // 配列で入ってこない場合はtrue
            return true;

        }

        foreach ($targets as $target) {

            if (!$this->_isEmail($target)) {
                return false;
            }

        }

        return true;

    }

    // }}}
    // {{{ bindValidateEmailCond

    /**
     * 条件付きメールアドレスチェック
     *
     * @param string $target 入力データ
     * @param array $opt nameで指定したフィールドに
     *                 valueで指定した値がある場合にチェックします
     * array(
     *     'name' => フィールド名
     *     'targetValue' => フィールドの値
     * )
     * @param bool $bCond
     * @return boolean
     */
    public function bindValidateEmailCond($target, $opt = array(), $bCond = true)
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
            return $this->_isEmail($target);
        }

        return true;

    }

    // }}}

}

// }}}
