<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * xFrameworkPX_Log_LogFile Class File
 *
 * PHP versions 5
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX_Log
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    SVN $Id: LogFile.php 1174 2010-01-05 14:28:45Z tamari $
 */

// {{{ xFrameworkPX_Log_LogFile

/**
 * xFrameworkPX_Log_LogFile Class
 *
 * @category   xFrameworkPX
 * @package    xFrameworkPX_Log
 * @author     Kazuhiro Kotsutsumi <kotsutsumi@xenophy.com>
 * @copyright  Copyright (c) 2006-2010 Xenophy.CO.,LTD All rights Reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @version    Release: 3.5.0
 * @link       http://www.xframeworkpx.com/api/?class=xFrameworkPX_Log_LogFile
 */
class xFrameworkPX_Log_LogFile extends xFrameworkPX_Log_LogBase
{

    const _apacheUser = 'apache';

    // {{{ execute

    /**
     * ロギング実行メソッド
     *
     * @param int $level ログレベル
     * @param array $location ロケーション情報
     */
    public function execute($level, $location)
    {
        $fileName = (string)$this->_param->filename;
        $logDirPath = (string)$this->_param->dirpath;
        $quota = $this->_param->quota;

        // 日付別用ファイル名生成
        $filename = $this->_makeFileName($fileName, $quota);

        // 出力先の設定
        $outputFileName = $this->_makeOutputDir($fileName, $this->_logDir, $logDirPath);

        // ローテート処理
        $ret = $this->_logRotate($outputFileName, $quota);

        // ファイル出力
        $ret = $this->_outPutLog($level, $location, $this->_message, $outputFileName);

        // ファイル所有者変更
        $ret = $this->_fileChangeOwner($outputFileName, self::_apacheUser);
    }

    // }}}
    // {{{ _makeFileName

    /**
     * ログファイル名作成メソッド
     *
     * @param string $fileName ファイル名
     * @param object $quota クオータ情報
     */
    private function _makeFileName($fileName, $quota) {
        // クオータ設定がなかったら処理せず戻す
        if ($quota->date == 'true' || $quota->date == 'yes') {
            $pathInfo = array();

            $pathInfo = pathinfo($fileName);

            // 現在の日付を取得
            $date = getdate();
            $formatDate = sprintf(
                '%04d%02d%02d', $date['year'], $date['mon'], $date['mday']
            );

            // ファイル名＋日付のファイル名作成
            if (isset($pathInfo['extension'])) {
                $fileName = $pathInfo['filename'] .
                            $formatDate . '.' . $pathInfo['extension'];
            } else {
                $fileName = $pathInfo['filename'] . $formatDate;
            }
        }

        return $fileName;
    }

    // }}}
    // {{{ _makeOutputDir

    /**
     * ログディレクトリ設定メソッド
     *
     * @param string $fileName ファイル名
     * @param string $defaultLogDirPath デフォルトファイルパス
     * @param string $logDirPath 指定ファイルパス
     */
    private function _makeOutputDir($fileName, $defaultLogDirPath, $logDirPath) {
        if ($logDirPath !== '') {
            // 出力パスが指定されている場合
            if (file_exists($logDirPath) && !is_dir($logDirPath)) {
                // 指定されたパスがファイルだったらデフォルトを使用
                $outputFileName = normalize_path($defaultLogDirPath . DS . $fileName);
            } else {
                // ディレクトリ作成
                $ret = makeDirectory($logDirPath);
                if ($ret === true) {
                    // ディレクトリに問題がなければ指定されたパスを使用
                    $outputFileName = normalize_path($logDirPath . DS . $fileName);
                } else {
                    // 作成失敗しているならデフォルトを使用
                    $outputFileName = normalize_path($defaultLogDirPath . DS . $fileName);
                }
            }
        } else {
            // デフォルト
            $outputFileName = normalize_path($defaultLogDirPath . DS . $fileName);
        }

        return $outputFileName;
    }

    // }}}
    // {{{ _logRotate

    /**
     * ログローテーションメソッド
     *
     * @param string $outputFileName 出力ファイル
     * @param object $quota クオータ情報
     */
    private function _logRotate($outputFileName, $quota) {
        // クオータ設定がなかったら処理せず戻す
        if (
            (int)$quota->size > 0 &&
            @filesize($outputFileName) >= (int)$quota->size
        ) {

            // 枝番号最高値取得
            $count = 1;

            while (file_exists($outputFileName . '.' . $count)) {
                $count++;
            }

            // リネーム処理
            for ($i = $count; $i > 0; --$i) {

                if (($i- 1) > 0) {
                    $suffix = ($i - 1);
                } else {
                    $suffix = '';
                }

                if ((int)$quota->limit > 0 && (int)$quota->limit <= $i) {
                    @unlink($outputFileName . '.' . $suffix);
                } else {
                    if ($suffix != '') {
                        @rename(
                            $outputFileName . '.' . $suffix,
                            $outputFileName . '.' . $i
                        );
                    } else {
                        @rename(
                            $outputFileName,
                            $outputFileName . '.' . $i
                        );
                    }
                }
            }
        }

        return true;
    }

    // }}}
    // {{{ _outPutLog

    /**
     * ログファイルへの出力メソッド
     *
     * @param int $level ログレベル
     * @param array $location ロケーション情報
     * @param string $message 出力文字列
     * @param string $outputFileName 出力ファイル
     */
    private function _outPutLog($level, $location, $message, $outputFileName) {
        $date = getdate();
        $buffer = '';
        $buffer = $buffer . sprintf(
            '%04d-%02d-%02d %02d:%02d:%02d',
            $date['year'],
            $date['mon'],
            $date['mday'],
            $date['hours'],
            $date['minutes'],
            $date['seconds']
        );
        $buffer = $buffer . ',' . sprintf(
            '% -6d',
            $location['line']
        ) . ' ';
        $buffer = $buffer . '[' . sprintf(
            '%05d',
            (function_exists('posix_getpid')) ? posix_getpid() : getmypid()
        ) . ']';
        $buffer = $buffer . ' ' . sprintf(
            '% -5s',
            $this->convertLevelString($level)
        );
        $buffer = $buffer . ' ' . $location['file'];
        $buffer = $buffer . ' - ' . $message;
        $buffer = $buffer . "\n";

        return file_forceput_contents($outputFileName, $buffer, FILE_APPEND);
    }

    // }}}
    // {{{ _fileChangeOwner

    /**
     * ファイル所有者変更メソッド
     *
     * @param string $outputFileName ファイル名
     * @param string $user 変更したい所有者名
     */
    private function _fileChangeOwner($outputFileName, $user = self::_apacheUser) {
        return file_change_owner($outputFileName, $user);
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
