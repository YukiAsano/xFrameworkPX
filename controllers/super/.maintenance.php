<?php
/**
 *
 * メンテナンスリダイレクトアクション
 *
 * @category   common
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

// {{{ maintenance

/**
 * super_maintenance Class
 *
 * @category   common
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

class super_maintenance extends xFrameworkPX_Controller_Action
{

    // {{{ execute

    /**
     * execute
     *
     * @return void
     */
    public function execute() {

        // 環境設定ファイルからメンテナンスフラグを読み取る
        $env = xFrameworkPX_Environment::getInstance()->get('maintenance');

        // true → メンテナンスページの有無確認
        if ($env === true) {

            $page = xFrameworkPX_Environment::getInstance()->get('maintenance_page');

            // あればリダイレクトで終了
            if (file_exists($page)) {
                $this->redirect(base_name().$page);
            }
        }

    }

    // }}}

}

// }}}
