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
 * validators_Number Class
 *
 * @category   Behavior
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

class validators_Number extends xFrameworkPX_Model_Behavior {

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

        //フレームワーク上の入力チェックを実行
        $empty = new xFrameworkPX_Validation_NotEmpty();
        return $empty->validate($target);

    }

    // }}}
    // {{{ _isNumber

    /**
     * 数値チェック
     *
     * @access public
     * @param $target
     * @return boolean
     */
    private function _isNumber ($target) {

        // フレームワーク上の入力チェックを実行
        $number = new xFrameworkPX_Validation_Number();
        return $number->validate($target);

    }

    // }}}
    // {{{ bindValidateNumberRange

    /**
     * 範囲チェック（最大値最小値を含みます）
     *
     * @access public
     * @param string $target 対象フィールド値
     * @param array $opt オプション（array('max' => '最大値', 'min' => '最小値')）
     * @throws xFrameworkPX_Exception
     * @return boolean true:正常値,false:異常値
     */
    public function bindValidateNumberRange ($target, $opt = array('max' => 1, 'min' => 0)) {

        if (!$this->_NotEmpty($target) || !$this->_isNumber($target)) {
            // ターゲット数値でない、または空はエラーとしない
            return true;
        }

        if (!isset($opt['max']) || !isset($opt['min'])) {

            // オプションを設定しない場合は例外
            throw new xFrameworkPX_Exception('最大値・最小値の指定がありません。');

        } else if (!preg_match('/(^[1-9][\d]*$)|(^0$)/', $opt['max']) || !preg_match('/(^[1-9][\d]*$)|(^0$)/', $opt['min'])) {

            // 数値を設定しない場合は例外
            throw new xFrameworkPX_Exception('最大値・最小値が数値ではありません。');

        }

        if ((float)$target <= (float)$opt['max'] && (float)$target >= (float)$opt['min']) {

            // 範囲内ならOK
            return true;

        }

        return false;

    }

    // }}}
    // {{{ bindValidateNumberWithStr

    /**
     * 文字付き数値チェックメソッド
     * カンマやピリオドが混じった数値のチェックに使用
     *
     * @param string $target ターゲット値
     *  array(
     *      ',', '.' // 取り除く文字列
     *  )
     * @param array $opt
     * @return boolean
     */
    public function bindValidateNumberWithStr($target, $opt = array(','))
    {

        // 【お約束】空はチェックしない
        if (!$this->_NotEmpty($target)) {
            return true;
        }

        if (is_array($opt)) {
            // 文字を取り除く
            foreach ($opt as $str) {
                $target = str_replace($str, '', $target);
            }
        }

        // 数字チェック
        return $this->_isNumber($target);

    }

    // }}}
    // {{{ bindValidateNumberFromTo

    /**
     * 数値FromToチェックメソッド
     *
     * @param string $target ターゲット値（使用しない）
     * @param array $opt オプション（array('from' => '開始値', 'to' => '終了値')）
     * @return boolean
     */
    public function bindValidateNumberFromTo ($target, $opt = array('from' => 'from', 'to' => 'to'))
    {

        // 【お約束】ローカル変数初期化
        $datas = null;
        $from = null;
        $to = null;

        // フィールド全データ取得
        $datas = $this->module->getTargetDatas();

        // チェックフィールド設定がなければ終了
        if (!isset($opt['from']) || !isset($opt['to'])) {
            return true;
        }

        // チェックできないので終了
        if (!isset($datas[$opt['from']]) || !isset($datas[$opt['to']])) {
            return true;
        }

        $from = $datas[$opt['from']];
        $to = $datas[$opt['to']];

        // 【お約束】空はチェックしない
        if (!$this->_NotEmpty($from) || !$this->_NotEmpty($to)) {
            return true;
        }

        // 数値ではない場合はチェックしない
        if (!is_numeric($from) || !is_numeric($to)) {
            return true;
        }

        // Fromが大きい場合はfalse
        if ($from > $to) {
            return false;
        }

        return true;

    }

    // }}}

}

// }}}
