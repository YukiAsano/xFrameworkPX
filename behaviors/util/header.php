<?php
/**
 *
 * headerクラス
 *
 * @category   Behavior
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */

// {{{ header

/**
 * header Class
 *
 * @category   Behavior
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */

class util_header extends xFrameworkPX_Model_Behavior {

    // {{{ props

    private $_key = 'testhoge';

    // }}}

    // {{{ __construct

    /**
     * コンストラクタ
     *
     * @access public
     */
    public function __construct($module) {

        parent::__construct($module);

    }

    // }}}
    // {{{ outputBrowser

    /**
     * ファイル出力メソッド
     *
     * @param $data ファイルデータ
     * @param $fileName ファイル名
     * @return void
     */
    public function bindOutputBrowser($data, $fileName, $download = false)
    {

        // 拡張子の取得
        $ext = pathinfo($fileName, PATHINFO_EXTENSION );

        $methodName = '';
        switch(strtolower($ext)) {

            case 'jpg':
                $methodName = 'headerJpg';
                break;

            case 'png':
                $methodName = 'headerPng';
                break;

            case 'gif':
                $methodName = 'headerGif';
                break;

            case 'pdf':
                $methodName = 'headerPdf';
                break;

            case 'csv':
                $methodName = 'headerCsv';
                break;

            case 'xml':
                $methodName = 'headerXml';
                break;

            case 'xls':
                $methodName = 'headerXls';
                break;

            case 'txt':
                $methodName = 'headerText';
                break;

            case 'doc':
                $methodName = 'headerDoc';
                break;

            case 'zip':
                $methodName = 'headerZip';
                break;

            case 'css':
                $methodName = 'headerCss';
                break;

            case 'js':
                $methodName = 'headerJs';
                break;

            case 'html':
                $methodName = 'headerHtml';
                break;

            default:
                $methodName = '_headerNone';
        }

        // ヘッダ送信
        $this->$methodName($fileName, $download);

        // ファイル出力
        print $data;
        exit( 0 );
    }

    // }}}
    // {{{ setHeader

    /**
     * ファイル出力時のヘッダーを生成、出力します
     *
     * @param $contentType 出力タイプ
     * @param $fileName ファイル名
     * @param $download ダウンロードフラグ
     * @return string
     */

    private function _setHeader($contentType, $fileName, $download = false)
    {

        header(
            'Expires: '.gmdate('D, d M Y H:i:s',time() - 3600 * 24 * 365).' GMT'
        );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );
        header( 'Cache-Control: public' );
        header( 'Pragma: public' );
        header( 'Content-type: ' . $contentType );

        if( $download ) {
            header( 'Content-Disposition: attachment; filename=' . $fileName );
        } else {
            header( 'Content-Disposition: inline; filename=' . $fileName );
        }
    }

    // }}}
    // {{{ headerCsv

    /**
     * CSVヘッダメソッド
     *
     * @param $fileName ファイル名 [default: tmp.csv]
     */
    public function headerCsv($fileName = 'tmp.csv', $download)
    {
        $this->_setHeader('application/x-msexcel-csv', $fileName, $download);
    }

    // }}}
    // {{{ headerPdf

    /**
     * PDFヘッダメソッド
     *
     * @param $fileName ファイル名 [default: tmp.pdf]
     */
    public function headerPdf($fileName = 'tmp.pdf', $download)
    {
        $this->_setHeader( 'application/pdf', $fileName, $download );
    }

    // }}}
    // {{{ headerPng

    /**
     * PNGヘッダメソッド
     *
     * @param $fileName ファイル名 [default: tmp.png]
     */
    public function headerPng($fileName = 'tmp.png', $download)
    {
        $this->_setHeader( 'image/png', $fileName, $download );
    }

    // }}}
    // {{{ headerJpg

    /**
     * JPGヘッダメソッド
     *
     * @param $fileName ファイル名 [default: tmp.jpg]
     */
    public function headerJpg($fileName = 'tmp.jpg', $download)
    {
        $this->_setHeader( 'image/jpeg', $fileName, $download );
    }

    // }}}
    // {{{ headerGif

    /**
     * GIFヘッダメソッド
     *
     * @param $fileName ファイル名 [default: tmp.gif]
     */
    public function headerGif($fileName = 'tmp.gif', $download)
    {
        $this->_setHeader('image/gif', $fileName, $download);
    }

    // }}}
    // {{{ headerXml

    /**
     * XMLヘッダメソッド
     *
     * @param $fileName ファイル名 [default: tmp.gif]
     */
    public function headerXml($fileName = 'tmp.xml', $download)
    {
        $this->_setHeader('application/xml', $fileName, $download);
    }

    // }}}
    // {{{ headerXls

    /**
     * XMLヘッダメソッド
     *
     * @param $fileName ファイル名 [default: tmp.gif]
     */
    public function headerXls($fileName = 'tmp.xls', $download)
    {
        $this->_setHeader('application/vnd.ms-excel', $fileName, $download);
    }

    // }}}
    // {{{ headerText

    /**
     * XMLヘッダメソッド
     *
     * @param $fileName ファイル名 [default: tmp.gif]
     */
    public function headerText($fileName = 'tmp.txt', $download)
    {
        $this->_setHeader('text/text', $fileName, $download);
    }

    // }}}
    // {{{ headerDoc

    /**
     * XMLヘッダメソッド
     *
     * @param $fileName ファイル名 [default: tmp.gif]
     */
    public function headerDoc($fileName = 'tmp.doc', $download)
    {
        $this->_setHeader('application/msword', $fileName, $download);
    }

    // }}}
    // {{{ headerZip

    /**
     * XMLヘッダメソッド
     *
     * @param $fileName ファイル名 [default: tmp.gif]
     */
    public function headerZip($fileName = 'tmp.zip', $download)
    {
        $this->_setHeader('application/zip', $fileName, $download);
    }

    // }}}
    // {{{ headerCss

    /**
     * XMLヘッダメソッド
     *
     * @param $fileName ファイル名 [default: tmp.gif]
     */
    public function headerCss($fileName = 'tmp.css', $download)
    {
        $this->_setHeader('text/css', $fileName, $download);
    }

    // }}}
    // {{{ headerJs

    /**
     * XMLヘッダメソッド
     *
     * @param $fileName ファイル名 [default: tmp.gif]
     */
    public function headerJs($fileName = 'tmp.js', $download)
    {
        $this->_setHeader('text/javascript', $fileName, $download);
    }

    // }}}
    // {{{ headerHtml

    /**
     * XMLヘッダメソッド
     *
     * @param $fileName ファイル名 [default: tmp.gif]
     */
    public function headerHtml($fileName = 'tmp.html', $download)
    {
        $this->_setHeader('text/html', $fileName, $download);
    }

    // }}}
    // {{{ _headerNone

    /**
     * 例外ヘッダメソッド
     *
     */
    private function _headerNone()
    {
        throw new Exception('ファイルを出力できません。');
    }

    // }}}

}

// }}}
