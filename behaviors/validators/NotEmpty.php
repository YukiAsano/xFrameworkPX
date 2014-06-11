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
 * validators_NotEmpty Class
 *
 * @category   Behavior
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

class validators_NotEmpty extends xFrameworkPX_Model_Behavior {

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
    // {{{ bindValidateNotEmptyMulti

    /**
     * 複数用空チェック
     * チェックボックス用空チェック
     *
     * @access public
     * @param array $targets チェック対象文字列配列
     * @return boolean
     */
    public function bindValidateNotEmptyMulti($targets) {

        if (!is_array($targets)) {

            // 配列で入ってこない場合はtrue
            return true;

        }

        foreach ($targets as $target) {

            if (!$this->_NotEmpty($target)) {
                return false;
            }

        }

        return true;

    }

    // }}}
    // {{{ bindValidateNotEmptyMultiField

    /**
     * 複数フォームの空チェック
     *
     * @access public
     * @param $target チェック対象文字列(未使用)
     * @param $opt オプション（array('target' => array('フィールド名'...))）
     * @return boolean
     */
    public function bindValidateNotEmptyMultiField($target, $opt) {

        $datas = $this->module->getTargetDatas();

        if (!isset($opt['target'])) {
            return true;
        }

        foreach ($opt['target'] as $field) {
            if (isset($datas[$field])) {
                if ($datas[$field] !== '') {
                    return true;
                }
            }
        }

        return false;

    }

    // }}}
    // {{{ bindValidateNotEmptyWithSpace

    /**
     * 空文字チェック（空白文字だけの場合もエラー）
     *
     * @access public
     * @param $target 対象フィールド値
     * @return boolean true:正常値,false:異常値
     */
    public function bindValidateNotEmptyWithSpace($target) {

        // 空白文字を取り除いたデータが空の場合エラー
        return $this->_NotEmpty(preg_replace('/^\r\n|\r|\n|\s|　+/i', '', $target));

    }

    // }}}
    // {{{ bindValidateNotEmptyWithStr

    /**
     * 区切り文字つき空チェック（区切り文字だけの場合もエラー）
     *
     * @access public
     * @param $target 対象フィールド値
     * @param $opt
     *  array(
     *      ',', '.' // 取り除く文字列
     *  )
     * @return boolean true:正常値,false:異常値
     */
    public function bindValidateNotEmptyWithStr($target, $opt=array(',')) {

        if (is_array($opt)) {
            // 文字を取り除く
            foreach ($opt as $str) {
                $target = str_replace($str, '', $target);
            }
        }

        // 区切り文字を取り除いたデータが空の場合エラー
        return $this->_NotEmpty($target);

    }

    // }}}
    // {{{ bindValidateNotEmptyCond

    /**
     * 条件付き空チェック
     *
     * @param string $target 入力データ
     * @param array $opt nameで指定したフィールドに
     *              valueで指定した値がある場合にチェックします
     * array(
     *     'name' => フィールド名
     *     'value' => フィールドの値
     * )
     * @return boolean
     */
    public function bindValidateNotEmptyCond($target, $opt = array())
    {

        // 【お約束】ローカル変数初期化
        $datas = null;
        $chkData = null;

        // 全データ取得
        $datas = $this->module->getTargetDatas();

        if (
            !isset($datas[$opt['name']]) ||
            !$this->_NotEmpty($opt['name'])
        ) {
            // ターゲットとなるものが空ならtrueを返す
            return true;
        }

        if ($opt['value'] == $datas[$opt['name']]) {

            // 対象の値が設定した値なら、チェック実行
            return $this->_NotEmpty($target);
        }

        return true;

    }

    // }}}
    // {{{ bindValidateNotEmptyCondMulti

    /**
     * 複数用空チェック
     * チェックボックス用空チェック
     *
     * @access public
     * @param array $targets チェック対象文字列配列
     * @return boolean
     */
    public function bindValidateNotEmptyCondMulti($targets) {

        if (!is_array($targets)) {

            // 配列で入ってこない場合はtrue
            return true;

        }

        foreach ($targets as $target) {

            if (!$this->_NotEmpty($target)) {
                return false;
            }

        }

        return true;

    }

    // }}}

}

// }}}
