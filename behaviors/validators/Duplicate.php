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
 * validators_Duplicate Class
 *
 * @category   Behavior
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

class validators_Duplicate extends xFrameworkPX_Model_Behavior {

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
    // {{{ bindValidateDuplicate

    /**
     * データ重複チェック
     *
     * @access public
     * @params $target チェック文字列
     * @params $opt
     * array(
     *     'target' => フィールド名 // 対象DBカラム
     *     'where'  => WHERE句       // 追加WHERE句（ステータスを見るとか。ex."AND status <> 9"）
     * )
     * @return boolean 重複あり:false、重複なし:false
     */
    public function bindValidateDuplicate($target, $opt)
    {

        // 【お約束】ローカル変数初期化
        $data = null;
        $pk = null;
        $query = null;
        $binds = array();
        $ret   = null;
        $pkCol = $this->primaryKey;

        // 【お約束】空はチェックしない
        if (!$this->_NotEmpty($target)) {
            return true;
        }

        if (!isset($opt['target'])) {
            throw new xFrameworkPX_Exception('ターゲットの指定がありません。');
        }

        // 全データ取得
        $datas = $this->module->getTargetDatas();

        // PK取得
        $pk = isset($datas[$this->primaryKey]) ? $datas[$this->primaryKey] : '';

        $col = $opt['target']; // チェックするカラム
        $val = $target;           // チェックする値
        $pk = $pk;                // プライマリキー

        // 存在チェック
        $query  = '';
        $query .= 'SELECT ';
        $query .= $col.' ';
        $query .= 'FROM ';
        $query .= $this->usetable.' ';
        $query .= 'WHERE ';

        if (!is_null($pk) && $pk != '') {
            $query .= $pkCol.' <> :'.$pkCol.' AND ';
            $binds[':'.$pkCol] = $pk;
        }

        $query .= $col.' = :'.$col;

        if (isset($opt['where'])) {

            // 追加のWHERE句
            $query .= ' ';
            $query .= $opt['where'];
            $query .= ' ';

        }

        $binds[':'.$col] = $val;

        // 結果取得
        $ret = $this->module->row(
            array(
                'query' => $query,
                'bind' => $binds,
            )
        );

        return $ret === false;

    }

    // }}}

}

// }}}
