<?php
/**
 *
 * サイト情報取得アクション
 *
 * @category   common
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */

// {{{ siteinfo

/**
 * super_siteinfo Class
 *
 * @category   common
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */
class super_siteinfo extends xFrameworkPX_Controller_Action {

    // {{{ props

    /**
     * モジュール指定
     *
     */
    public $modules = array(
        'site',
    );

    // }}}
    // {{{ execute

    /**
     * execute
     *
     * @access public
     * @return void
     */
    public function execute() {

        $siteinfo = $this->site->getSiteInfo($this->get('uri'));

        $title   = (isset($siteinfo['title']))? $siteinfo['title'] : '';
        $o_title = (isset($siteinfo['original_title']))? $siteinfo['original_title'] : '';
        $desc    = (isset($siteinfo['description']))? $siteinfo['description'] : '';
        $keyword = (isset($siteinfo['keyword']))? $siteinfo['keyword'] : '';

        $this->set('head_title', $title);
        $this->set('original_title', $o_title);
        $this->set('head_description', $desc);
        $this->set('head_keyword', $keyword);

    }

    // }}}

}

// }}}
