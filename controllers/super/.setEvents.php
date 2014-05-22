<?php
/**
 *
 * イベント追加アクション
 *
 * @category   common
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */

// {{{ setEvents

/**
 * super_setEvents Class
 *
 * @category   common
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */
class super_setEvents extends xFrameworkPX_Controller_Action {

    // {{{ props

    private $_setting = array(
        // サイト情報取得アクション
        'siteinfo' => array(
            'clsName' => 'super_siteinfo',
            'dir'     => 'controllers',
        ),
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

        // アクション設定
        foreach ($this->_setting as $name => $values) {

            $path   = '';
            $action = '';
            if (!isset($values['clsName']) || !isset($values['dir'])) {
                continue;
            }

            $action = $values['clsName'];
            if ($values['dir'] == 'controllers') {
                $path = $this->_conf['pxconf']['CONTROLLER_DIR'];
            } else if ($values['dir'] == 'webapp') {
                $path = $this->_conf['pxconf']['WEBROOT_DIR'];
            }

            if (!$action || !$path) {
                throw new xFrameworkPX_Exception('イベント設定に失敗しました。');
            }

            if (matchesIn($action, '_')) {
                $dirs = explode('_', $action);
                $name = $this->_conf['pxconf']['CONTROLLER_PREFIX'].array_pop($dirs);
                $name = $name.$this->_conf['pxconf']['CONTROLLER_EXTENSION'];
                $path = normalize_path($path.'/'.implode('/', $dirs).'/'.$name);
            } else {
                $name = $this->_conf['pxconf']['CONTROLLER_PREFIX'].$action;
                $name = $name.$this->_conf['pxconf']['CONTROLLER_EXTENSION'];
                $path = normalize_path($path.'/'.$name);
            }

            xFrameworkPX_Event::getInstance($this->_conf)->setEvent(
                $action,
                $path
            );

        }

    }

    // }}}

}

// }}}
