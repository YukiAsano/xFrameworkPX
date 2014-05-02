<?php

// {{{ Set Library Path

set_include_path('../library/' . PATH_SEPARATOR . get_include_path());

// }}}
// {{{ Include xFrameworPX

require_once 'xFrameworkPX/Yaml.php';
require_once 'xFrameworkPX/Master.php';
require_once 'xFrameworkPX/Environment.php';

include '../locales/ja.php';
include 'xFrameworkPX/Loader/Core.php';

// }}}
// {{{ xFrameworkPX Run

$bForce = false;

// メンテナンスページが設定されていたら、強制的にコントローラを実行させる
//$bForce = (xFrameworkPX_Environment::getInstance()->get('maintenance') == '1');

//if ($bForce == false) {
// プレサイトページが設定されていたら、強制的にコントローラを実行させる
//$bForce = (xFrameworkPX_Environment::getInstance()->get('presite') == '1');
//}

xFrameworkPX_Dispatcher::getInstance()->run(
    array(
        'DEBUG' => 2,
        'FORCE_CONTROLLER_EXECUTE' => $bForce,
    )
);

// }}}
