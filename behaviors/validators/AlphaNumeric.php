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
 * validators_AlphaNumeric Class
 *
 * @category   Behavior
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

class validators_AlphaNumeric extends xFrameworkPX_Model_Behavior {

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
            'xFrameworkPX_Validation_AlphaNumeric',     //クラス名
            'xFrameworkPX/Validation/AlphaNumeric.php'  //ファイルパス
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

        // フレームワーク上の入力チェックを実行
        $empty = new xFrameworkPX_Validation_NotEmpty();
        return $empty->validate($target);

    }

    // }}}
    // {{{ _isAlphaNumeric

    /**
     * 英数チェック
     *
     * @access public
     * @param $target
     * @return boolean
     */
    private function _isAlphaNumeric ($target) {

        // フレームワーク上の入力チェックを実行
        $alphaNumeric = new xFrameworkPX_Validation_AlphaNumeric();
        return $alphaNumeric->validate($target);

    }

    // }}}
    // {{{ bindValidateAlphaNumericWithStr

    /**
     * 文字付き半角英数チェックメソッド
     * カンマやピリオドが混じった数値のチェックに使用
     *
     * @param string $target ターゲット値（使用しない）
     * @param array $opt
     *  array(
     *      ',', '.' // 取り除く文字列
     *  )
     * @return boolean
     */
    public function bindValidateAlphaNumericWithStr($target, $opt = array(','))
    {

        // 【お約束】空はチェックしない
        if (!$this->_NotEmpty($target)) {
            return true;
        }

        if (!empty($opt)) {
            // 文字を取り除く
            foreach ($opt as $str) {
                $target = str_replace($str, '', $target);
            }
        }

        // 半角英数数字チェック
        return $this->_isAlphaNumeric($target);

    }

    // }}}

}

// }}}
