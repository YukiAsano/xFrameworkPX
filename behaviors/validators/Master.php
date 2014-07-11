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
 * validators_Master Class
 *
 * @category   Behavior
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

class validators_Master extends xFrameworkPX_Model_Behavior {

    // {{{ __construct

    /**
     * コンストラクタ
     *
     * @access public
     */
    public function __construct($module) {

        parent::__construct($module);

    }

    // }}}
    // {{{ bindValidateMasterKeyExists

    /**
     * マスタのキー存在チェック
     *
     * @access public
     * @param string $target チェック対象文字列
     * @param array $opt 'master' => マスタ、またはマスタ名
     * @throws xFrameworkPX_Exception
     * @return boolean true: 正しい、false: 正しくない
     */
    public function bindValidateMasterKeyExists($target, $opt) {
        if ($target == '') {
            return true;
        }

        if (array_key_exists('master', $opt)) {
            $master = $opt['master'];
        } else {
            throw new xFrameworkPX_Exception('masterの指定がありません。');
        }

        if (!is_array($master)) {
            $values = array();
            if (array_key_exists('values', $opt)) {
                $values = $opt['values'];
            }
            $params = array_merge(array($master), $values);
            $master = call_user_func_array('getMaster', $params);
        }

        return array_key_exists($target, $master);
    }

    // }}}
    // {{{ bindValidateMasterKeyExistsMulti

    /**
     * マスタのキー存在チェック(チェックボックス用)
     *
     * @access public
     * @param array $targets チェック対象配列
     * @param array $opt 'master' => マスタ、またはマスタ名
     * @return boolean true: 正しい、false: 正しくない
     */
    public function bindValidateMasterKeyExistsMulti($targets, $opt) {

        if (!is_object($targets)) {
            // 配列(オブジェクト)で入ってこない場合はtrue
            return true;
        }
        foreach ($targets as $target) {
            if (!$this->bindValidateMasterKeyExists($target, $opt)) {
                return false;
            }
        }
        return true;

    }

    // }}}
}

// }}}
