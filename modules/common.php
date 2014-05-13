<?php
/**
 *
 * 共通モジュールクラス
 * miscを使いたいけど、使用するモジュールも特にない場合に。
 *
 * @package    common
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */

// {{{ common

/**
 * common Class
 *
 * @package    common
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */
class common extends xFrameworkPX_Model {

    // {{{ props

    /**
     * 使用テーブル設定
     * var string
     */
    public $usetable = false;

    /**
     * ビヘイビア設定
     * var array
     */
    public $behaviors = array(
        'misc',
    );

    // }}}

}

// }}}
