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
 * validators_Date Class
 *
 * @category   Behavior
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

class validators_Date extends xFrameworkPX_Model_Behavior {

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
            'xFrameworkPX_Validation_Date',             //クラス名
            'xFrameworkPX/Validation/Date.php'          //ファイルパス
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
    // {{{ _Date

    /**
     * 日付チェック
     *
     * @access public
     * @param $target
     * @return boolean
     */
    private function _Date($target) {

        //フレームワーク上の入力チェックを実行
        $date = new xFrameworkPX_Validation_Date();
        return $date->validate($target);

    }

    // }}}
    // {{{ bindValidateDateMulti

    /**
     * 複数用日付チェック
     *
     * @access public
     * @param array $targets チェック対象文字列
     * @return boolean
     */
    public function bindValidateDateMulti($targets) {

        if (!is_array($targets)) {

            // 配列で入ってこない場合はtrue
            return true;

        }

        foreach ($targets as $target) {

            if (!$this->_Date($target)) {
                return false;
            }

        }

        return true;

    }

    // }}}
    // {{{ bindValidateDateRange

    /**
     * 日付の範囲チェック（最大値最小値を含みます）
     *
     * @access public
     * @param string $target 対象フィールド値
     * @param array $opt オプション（array('max' => '最大値', 'min' => '最小値')）
     * @throws xFrameworkPX_Exception
     * @return boolean true:正常値,false:異常値
     */
    public function bindValidateDateRange($target, $opt)
    {

        // 【お約束】ローカル変数初期化
        $targetTime = null;
        $minTime = null;
        $maxTime = null;

        // 【お約束】空はチェックしない
        if (!$this->_NotEmpty($target)) {
            return true;
        }

        if (!isset($opt['max']) || !isset($opt['min'])) {
            // オプションを設定しない場合は例外
            throw new xFrameworkPX_Exception('最大値・最小値の指定がありません。');
        }

        $targetTime = date('Ymd', strtotime($target));
        $minTime = preg_replace('/[\/\-]/', '', $opt['min']);
        $maxTime = preg_replace('/[\/\-]/', '', $opt['max']);

        if ($minTime <= $targetTime && $targetTime <= $maxTime) {
            // 範囲内ならOK
            return true;
        }

        return false;

    }

    // }}}
    // {{{ bindValidateDateFromTo

    /**
     * 日付FromToチェックメソッド
     * 日付のチェックは終わっているものとする
     *
     * @param string $target ターゲット値（使用しない）
     * @param array $opt オプション（array('from' => '開始日時', 'to' => '終了日時')）
     * @return boolean
     */
    public function bindValidateDateFromTo($target, $opt = array('from' => 'from', 'to' => 'to'))
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

        // 【お約束】空はチェックしない
        if (!$this->_NotEmpty($datas[$opt['from']]) || !$this->_NotEmpty($datas[$opt['to']])) {
            return true;
        }

        $from = strtotime($datas[$opt['from']]);
        $to = strtotime($datas[$opt['to']]);

        // 日付フォーマットではない場合はチェックしない
        if ($from === false || $to === false) {

            return true;

        }

        // Fromが大きい場合はfalse
        if ($from > $to) {

            return false;

        }

        return true;

    }

    // }}}
    // {{{ bindValidateMultipleDate

    /**
     * 年月日フィールド分割日付チェックメソッド
     *
     * @param string $target ターゲット値（使用しない）
     * @param array $opt オプション（array('year' => '年フィールド', 'month' => '月フィールド', 'day' => '日フィールド')）
     * @return boolean
     */
    public function bindValidateMultipleDate($target, $opt = array('year' => 'year', 'month' => 'month', 'day' => 'day'))
    {

        // 【お約束】ローカル変数初期化
        $datas = null;
        $from = null;
        $to = null;

        // フィールド全データ取得
        $datas = $this->module->getTargetDatas();

        // チェックフィールド設定がなければ終了
        if (!isset($opt['year']) || !isset($opt['month']) || !isset($opt['day'])) {
            return true;
        }

        // チェックできないので終了
        if (!isset($datas[$opt['year']]) || !isset($datas[$opt['month']]) || !isset($datas[$opt['day']])) {
            return true;
        }

        // 【お約束】空はチェックしない
        if (!$this->_NotEmpty($datas[$opt['year']]) ||
            !$this->_NotEmpty($datas[$opt['month']]) ||
            !$this->_NotEmpty($datas[$opt['day']])) {
            return true;
        }

        $year = $datas[$opt['year']];
        $month = $datas[$opt['month']];
        $day = $datas[$opt['day']];
        $date = $year . '/' . $month . '/'  . $day;


        if (!$this->_Date($date)) {
            return false;
        }

        return true;

    }

    // }}}

}

// }}}
