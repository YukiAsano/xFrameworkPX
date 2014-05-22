<?php
/**
 *
 * SSL変換クラス
 * currentPageアクションとセットで使用する
 *
 * @category   common
 * @author     Satoshi Sasaki<sasaki@xenophy.com>
 * @version    Release: 1.0.0
 */

// {{{ convssl

/**
 * super_convssl Class
 *
 * @category   common
 * @author     Satoshi Sasaki<sasaki@xenophy.com>
 * @version    Release: 1.0.0
 */
class super_convssl extends xFrameworkPX_Controller_Action
{

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

        $uri = null;
        $currentPage = null;
        $get = null;

        // }}}
        // {{{ 現在のページのURL取得

        $uri = $this->get('currentPage');

        if (!$uri) {

            // 取得できない場合は終了
            return;

        }

        // }}}
        // {{{ URLからパス情報だけにする

        $basename = preg_replace('/\/$/', '', base_name());
        $uri = preg_replace('/'.preg_replace('/\//','\\/',$basename).'/', '', $uri);

        // }}}
        // {{{

        if (is_secure() && $this->_isHttpconv($uri)) {

            // {{{ httpsでアクセス、変換対象ディレクトリの場合

            // 非SSL変換
            if (startsWith($basename, 'https:')) {
                $basename = preg_replace('/^https:/', 'http:', $basename);
                $url = $basename . $uri;
                $this->redirect($url);
            }

            // }}}

        } else if ($this->_isHttpsconv($uri)) {

            // {{{ httpでアクセス、変換対象ディレクトリの場合

            // SSL変換
            if (startsWith($basename, 'http:')) {
                $basename = preg_replace('/^http:/', 'https:', $basename);
                $url = $basename . $uri;
                $this->redirect($url);
            }

            // }}}

        }

        // }}}

    }

    // }}}
    // {{{ _isHttpsconv

    /**
     * URLがSSLかチェック
     *
     * @access private
     * @param string $uri URI
     * @return boolean true:変換対象/false:変換対象外
     */
    private function _isHttpsconv($uri)
    {

        return $this->_iscontain(
            $uri,
            getSetting('env', getSetting('env', 'envType'), 'httpsconv')
        );

    }

    // }}}
    // {{{ _isHttpconv

    /**
     * URLが非SSLかチェック
     *
     * @access private
     * @param string $uri URI
     * @return boolean true:変換対象/false:変換対象外
     */
    private function _isHttpconv($uri)
    {

        return $this->_isContain(
            $uri,
            getSetting('env', getSetting('env', 'envType'), 'httpconv')
        );

    }

    // }}}
    // {{{ _isContain

    /**
     * リストに現在のパスが含まれているかチェック
     *
     * @param string $uri URI
     * @param array $list 変換対象リスト
     * @return boolean true:変換対象/false:変換対象外
     */
    private function _isContain($uri, $list)
    {

        if (isset($list) && is_array($list)) {

            foreach ($list as $data) {

                if ($data == '/') {

                    if ($uri == $data) {
                        return true;
                    }

                } else if (startsWith($uri, $data)) {

                    return true;

                }

            }

        }

        return false;

    }

    // }}}

}

// }}}
