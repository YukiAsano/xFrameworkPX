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
 * validators_Time Class
 *
 * @category   Behavior
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

class validators_Time extends xFrameworkPX_Model_Behavior {

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
            'xFrameworkPX_Validation_Number',     //クラス名
            'xFrameworkPX/Validation/Number.php'  //ファイルパス
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
    // {{{ _splitData

    /**
     * データを文字列で分割します。
     * @param $target
     * @param string $delim
     * @return array
     */
    private function _splitData($target, $delim = ':') {

        $result = explode($delim, $target);

        array_walk($result, function (&$value) {
            $value = trim($value);
        });

        return $result;

    }

    // }}}
    // {{{ _isNumber

    /**
     * 数字チェック
     *
     * @access public
     * @param $target
     * @return boolean
     */
    private function _isNumber($target) {

        // フレームワーク上の入力チェックを実行
        $alphaNumeric = new xFrameworkPX_Validation_Number();
        return $alphaNumeric->validate($target);

    }

    // }}}
    // {{{ _checkTime

    /**
     * 時間チェック
     * @param $hour
     * @param $min
     * @return bool
     */
    private function _checkTime($hour, $min) {

        if ($hour < 0 || $hour > 23){
            return false;
        }

        if ($min < 0 || $min > 59){
            return false;
        }

        return true;
    }

    // }}}
    // {{{ bindValidateTime

    /**
     * 時間の入力形式チェックメソッド
     * @param $target
     * @return bool
     */
    public function bindValidateTime($target) {

        $values = array();
        $hour = null;
        $min = null;

        // 【お約束】空はチェックしない
        if (!$this->_NotEmpty($target)) {
            return true;
        }

        $values = $this->_splitData($target);

        // 数字チェック
        foreach($values as $value) {

            if (!$this->_isNumber($value)) {
                return false;
            }
        }

        $hour = isset($values[0]) ? $values[0] : -1;
        $min = isset($values[1]) ? $values[1] : -1;

        return $this->_checkTime($hour, $min);

    }

    // }}}

}

// }}}
