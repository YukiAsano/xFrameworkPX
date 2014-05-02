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
 * validators_Phone Class
 *
 * @category   Behavior
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

class validators_Phone extends xFrameworkPX_Model_Behavior {

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
            'xFrameworkPX_Validation_Phone',            //クラス名
            'xFrameworkPX/Validation/Phone.php'         //ファイルパス
        );

        parent::__construct($module);

    }

    // }}}
    // {{{ _NotEmpty

    /**
     * 空チェック
     *
     * @access public
     * @return boolean
     */
    private function _NotEmpty ($target) {

        //フレームワーク上の入力チェックを実行
        $empty = new xFrameworkPX_Validation_NotEmpty();
        return $empty->validate($target);

    }

    // }}}
    // {{{ _isPhone

    /**
     * 電話番号形式チェック
     *
     * @access public
     * @return boolean
     */
    private function _isPhone ($target, $opt=array('mobile' => true)) {

        //フレームワーク上の入力チェックを実行
        $phone = new xFrameworkPX_Validation_Phone();
        return $phone->validate($target, $opt);

    }

    // }}}
    // {{{ bindValidatePhone3Field

    /**
     * 電話番号チェック
     * 3つのフィールドで入力させた時用
     *
     * @access public
     * @params $target チェック対象文字列(未使用)
     * @params $opt
     *  array(
     *      'target' => array(
     *          フィールド名1,
     *          フィールド名2,
     *          フィールド名3
     *      )
     *  )
     * @return boolean
     */
    public function bindValidatePhone3Field ($target, $opt) {

        // 【お約束】ローカル変数初期化
        // 引数の$targetは使わない
        $target = '';
        $datas = null;

        // 指定がない場合は終了
        if (!isset($opt['target'])) {
            return true;
        }

        $datas = $this->module->getTargetDatas();

        // ターゲットが存在しない場合はチェックしない
        foreach ($opt['target'] as $field) {
            if (isset($datas[$field])) {
                if ($target !== '') {
                    $target .= '-';
                }
                $target .= $datas[$field];
            }
        }

        // チェック実行
        return $this->_isPhone($target, $opt);

    }

    // }}}
    // {{{ bindValidatePhoneCond

    /**
     * 条件付き電話番号チェック
     *
     * @params $target 入力データ
     * @params $opt nameで指定したフィールドに
     *                 valueで指定した値がある場合にチェックします
     * array(
     *     'name' => フィールド名
     *     'value' => フィールドの値
     * )
     * @return boolean
     */
    public function bindValidatePhoneCond($target, $opt = array())
    {

        // 【お約束】ローカル変数初期化
        $datas = null;
        $chkData = null;

        // 【お約束】空はチェックしない
        /*
        if (!$this->_NotEmpty($target)) {
            return true;
        }
         */

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
            if (isset($opt['target'])) {
                // 3フィールドチェック
                return $this->bindValidatePhone3Field($target, $opt);
            } else {
                // 単一フィールドチェック
                return $this->_isPhone($target, $opt);
            }
        }

        return true;

    }

    // }}}

}

// }}}
