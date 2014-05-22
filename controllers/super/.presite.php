<?php
/**
 *
 * プレサイト表示アクション
 *
 * @category   common
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

// {{{ presite

/**
 * super_presite Class
 *
 * @category   common
 * @author     Yuki Asano <asano@xenophy.com>
 * @version    Release: 1.0.0
 */

class super_presite extends xFrameworkPX_Controller_Action
{

    // {{{ execute

    /**
     * execute
     *
     * @return void
     */
    public function execute() {

        // 環境設定ファイルからプレサイトフラグを読み取る
        $env = xFrameworkPX_Environment::getInstance()->get('presite');

        // true → プレサイトページの有無確認
        if ($env === true) {

            $ipList = xFrameworkPX_Environment::getInstance()->get('presite-skip-ip');
            $ip = $_SERVER["REMOTE_ADDR"];

            // アクセスを許可するIPのリストにない場合、プレサイトページを表示
            if (array_search($ip, $ipList) === FALSE) {

                $page = xFrameworkPX_Environment::getInstance()->get('presite_page');

                // あればファイルをを読み込んで終了
                if (file_exists($page)) {
                    require_once($page);
                    exit;
                }
            }
        }

    }

    // }}}

}

// }}}
