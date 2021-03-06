<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * xFrameworkPX_Model_AllTests Class File
 *
 * PHP versions 5
 *
 * @category   Tests
 * @package    xFrameworkPX_Model
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    SVN $Id: AllTests.php 951 2009-12-25 11:40:13Z tamari $
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'xFrameworkPX_Model_AllTests::main');
}

// {{{ requires

set_include_path(get_include_path() . PATH_SEPARATOR . '../../');

/**
 * Require Files
 */
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'tests/Model/RapidDriveTest.php';
require_once 'tests/Model/Behavior/AllTests.php';
require_once 'tests/Model/Adapter/AllTests.php';

// }}}
// {{{ xFrameworkPX_Model_AllTests

/**
 * xFrameworkPX_Model_AllTests Class
 *
 * @category   Tests
 * @package    xFrameworkPX_Model
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    Release: @package_version@
 * @link       http://www.xframeworkpx.com/api/
 */
class xFrameworkPX_Model_AllTests
{

    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(
            self::suite(),
            array(
                'configuration' => '_files/config.xml',
                'coverageSource' => './TestResult/coverage-report',
                'reportDirectory' => './TestResult/report',
                'reportYUI' => true,
                'reportCharset' => 'UTF-8'
            )
        );
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite(
            'xFrameworkPX , xFrameworkPX_Model'
        );

        // {{{ xFrameworkPX_Model_Adapter_AllTests

        $suite->addTestSuite(xFrameworkPX_Model_Adapter_AllTests::suite());

        // }}}
        // {{{ xFrameworkPX_Model_Behavior_AllTests

        $suite->addTestSuite(
            xFrameworkPX_Model_Behavior_AllTests::suite()
        );

        // }}}
        // {{{ xFrameworkPX_Model_RapidDriveTest

        $suite->addTestSuite('xFrameworkPX_Model_RapidDriveTest');

        // }}}

        return $suite;
    }

}

// }}}
// {{{ xFrameworkPX_Model_AllTests::main

if (PHPUnit_MAIN_METHOD == 'xFrameworkPX_Model_AllTests::main') {
    xFrameworkPX_Model_AllTests::main();
}

// }}}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * End:
 */
