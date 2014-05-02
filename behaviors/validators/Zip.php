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
 * validators_Zip Class
 *
 * @category   Behavior
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

class validators_Zip extends xFrameworkPX_Model_Behavior {

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
     * @return boolean
     */
    private function _NotEmpty ($target) {

        //フレームワーク上の入力チェックを実行
        $empty = new xFrameworkPX_Validation_NotEmpty();
        return $empty->validate($target);

    }

    // }}}
    // {{{ bindValidateZipSingle

    /**
     * 半角英数（デフォルトはハイフン許可）チェック
     *
     * @params $datas 入力データ配列
     * @params $opt removeに正規表現で除外するものを指定します
     * @return boolean
     */
    public function bindValidateZipSingle($target, $opt = array('remove' => '/-/'))
    {

        // 【お約束】空はチェックしない
        if (!$this->_NotEmpty($target)) {
            return true;
        }

        if (isset($opt['remove'])) {

            // 指定した記号を取り除く
            $target = preg_replace($opt['remove'], '', $target);

        }

        return preg_match('/^[0-9]{7}$/', $target);

    }

    // }}}
    // {{{ bindValidateZip

    /**
     * 郵便番号チェック
     *
     * @access public
     * @params $target チェック対象文字列(未使用)
     * @params $opt オプション（array('first' => '郵便番号3桁フィールド名', 'second' => '郵便番号4桁フィールド名')）
     * @return boolean
     */
    public function bindValidateZip ($target, $opt) {

        // 【お約束】ローカル変数初期化
        $datas = null;
        $chkData = null;

        // チェックフィールド設定がなければ終了
        if (!isset($opt['first']) || !isset($opt['second'])) {
            return true;
        }

        $datas = $this->module->getTargetDatas();

        // チェックできないので終了
        if (!isset($datas[$opt['first']]) || !isset($datas[$opt['second']])) {
            return true;
        }

        return $this->bindValidateZipSingle($datas[$opt['first']].$datas[$opt['second']], $opt);

    }

    // }}}

}

// }}}
