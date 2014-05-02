<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * xFrameworkPX_Controller_Component_Mail Class File
 *
 * PHP versions 5
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX_Controller_Component
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    SVN $Id: Mail.php 912 2009-12-23 20:00:22Z kotsutsumi $
 */

// {{{ xFrameworkPX_Controller_Component_Mail

/**
 * xFrameworkPX_Controller_Component_Mail Class
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX_Controller_Component
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    Release: 3.5.0
 * @link       http://www.xframeworkpx.com/api/?class=xFrameworkPX_Controller_Component_Mail
 */
class xFrameworkPX_Controller_Component_Mail
extends xFrameworkPX_Controller_Component
{
    // {{{ send

    /**
     * 送信メソッド
     *
     * @param xFrameworkPX_util_MixedCollection $conf 設定オブジェクト
     * @return boolean
     */
    public function send($conf, $debug = true)
    {

        if ($debug && xFrameworkPX_Environment::getInstance()->get('mail', 'replace_flg') == true) {

            $prev = array(
                'to' => '',
                'subject' => '',
            );
            // {{{ TO設定があれば、強制的にそちらを使用する

            $to = xFrameworkPX_Environment::getInstance()->get('mail', 'to');
            if ($to) {
                if (is_array($conf['to'])) {
                    $prev['to'] = implode(',', $conf['to']);
                } else {
                    $prev['to'] = $conf['to'];
                }
                $conf['to'] = $to;
            }

            // }}}
            // {{{ CC設定があれば、強制的にCCを変更する(ない場合は削除)

            $cc = xFrameworkPX_Environment::getInstance()->get('mail', 'cc');

            //CCの送信設定がされている場合、メール本文に表示する変数に追加
            if (isset($conf['cc'])) {
                if (is_array($conf['cc'])) {
                    $prev['cc'] = implode(',', $conf['cc']);
                } else {
                    $prev['cc'] = $conf['cc'];
                }
                //evn.ymlの設定がある場合、上書き
                if ($cc) {
                    $conf['cc'] = $cc;
                } else {
                    //env.ymlの設定がない場合、CCを削除
                    unset($conf['cc']);
                }
            }

            // }}}
            // {{{ subjectに接頭辞をつける

            $prefix = xFrameworkPX_Environment::getInstance()->getEnvType();

            if ($prefix != 'real') {
                if (isset($conf['subject'])) {
                    $prev['subject'] = $conf['subject'];
                    $conf['subject'] = '['.$prefix.']'.$conf['subject'];
                }
                // {{{ 本文に変換前の情報を載せる

                $addBody = '';
                $addBody = "\n\n\n";
                $addBody .= "==============================\n";
                $addBody .= "変換前の情報\n";
                $addBody .= 'to:'.$prev['to']."\n";
                if (isset($prev['cc'])) {
                    $addBody .= 'cc:'.$prev['cc']."\n";
                }
                $addBody .= 'subject:'.$prev['subject']."\n";
                $addBody .= "==============================\n";

                $conf['body'] .= $addBody;

                // }}}
            }

            // }}}

        }

        $mail = new xFrameworkPX_Mail();
        return $mail->send($conf);
    }

    // }}}

}

// }}}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
