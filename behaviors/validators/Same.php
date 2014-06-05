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
 * validators_Same Class
 *
 * @category   Behavior
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

class validators_Same extends xFrameworkPX_Model_Behavior {

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
    // {{{ bindValidateSame

    /**u
     * 2フィールド同一チェック
     *
     * @access public
     * @param string $target チェック対象文字列
     * @param $opt
     *  array(
     *      'target' => もう1つのフィールド名,
     *      'force' => trueで空でもチェック
     *  )
     * @return boolean
     */
    public function bindValidateSame($target, $opt) {

        // 【お約束】ローカル変数初期化
        $datas = null;
        $chkData = null;

        // ターゲットフィールドの設定がない場合はチェックしない
        if (!isset($opt['target'])) {
            return true;
        }

        // ターゲットが存在しない場合はチェックしない
        $datas = $this->module->getTargetDatas();

        if (!isset($datas[$opt['target']])) {
            return true;
        }

        $checkData = $datas[$opt['target']];

        // ターゲットが空の場合はチェックしない
        if (
            !$this->_NotEmpty($checkData) &&
            (!isset($opt['force']) || !$opt['force'])
        ) {
            return true;
        }

        return ($target === $checkData);

    }

    // }}}
    // {{{ bindValidateSameCond

    /**
     * 条件付き一致チェック
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
    public function bindValidateSameCond($target, $opt = array())
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
            return $this->bindValidateSame($target, $opt);
        }

        return true;

    }

    // }}}

}

// }}}
