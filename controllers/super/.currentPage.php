<?php
/**
 *
 * カレントページ情報取得クラス
 *
 * @category   common
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */

// {{{ currentPage

/**
 * super_currentPage Class
 *
 * @category   common
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */
class super_currentPage extends xFrameworkPX_Controller_Action
{

    // {{{ props

    public $modules = array(
        'common'
    );

    // }}}
    // {{{ execute

    /**
     * コントローラーメソッド
     *
     * @access public
     * @return void
     */
    public function execute()
    {
        // {{{ 変数初期化

        $info = null;
        $currentPage = null;
        $get = null;

        // }}}
        // {{{ ページ情報取得

        $info = $this->common->getCurrentPageInfo();
        $currentPage = $info['currentPage'];

        // }}}
        // {{{ 動的URL用にGETパラメータをつける

        if (!matchesIn($currentPage, '?')) {

            $get = $this->common->convertArray($this->get);
            unset($get['cp']);

            if (count($get) > 0){
                $currentPage = $currentPage.'?'.http_build_query($get);
            }

        }

        // }}}
        // {{{ セット

        $this->set('currentPage', $currentPage);
        $this->set('baseName', $info['baseName']);
        $this->set('httpBase', $info['httpBase']);
        $this->set('sslBase', $info['sslBase']);
        $this->set('uri', $info['uri']);
        $this->set('isReal', xFrameworkPX_Environment::getInstance()->isReal());

        // }}}

    }

    // }}}

}

// }}}
