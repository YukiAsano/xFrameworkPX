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
 * validators_Ng Class
 *
 * @category   Behavior
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

class validators_Ng extends xFrameworkPX_Model_Behavior {

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
    // {{{ bindValidateNGWordExists

    /**
     * NGワード存在チェック
     *
     * @access public
     * @params string $target チェック対象文字列
     * @params array $opt オプション
     * @return boolean
     */
    public function bindValidateNGWordExists($target, $opt = array()) {

        // 【お約束】ローカル変数初期化
        $ngWords = null;

        // 【お約束】空はチェックしない
        if (!$this->_NotEmpty($target)) {
            return true;
        }

        // NGワードのリスト
        $ngWords = getMaster('ng_word');

        foreach ($ngWords as $key => $ngWord) {

            // チェック
            if (strpos($target, $ngWord["name"]) !== FALSE) {

                //NGワードがヒット
                return false;

            }

        }

        return true;

    }

    // }}}
    // {{{ bindValidateNGWordExistsMulti

    /**
     * 複数用NGワード存在チェック
     *
     * @access public
     * @params $target チェック対象文字列
     * @return boolean
     */
    public function bindValidateNGWordExistsMulti ($targets, $opt = array()) {

        if (!is_array($targets)) {

            // 配列で入ってこない場合はtrue
            return true;

        }

        foreach ($targets as $target) {

            if (!$this->bindValidateNGWordExists($target, $opt)) {
                return false;
            }

        }

        return true;

    }

    // }}}

}

// }}}
