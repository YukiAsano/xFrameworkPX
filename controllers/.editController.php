<?php
/**
 *
 * 編集画面用アクションクラス
 *
 * @category   common
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */

// {{{ editController

/**
 * editController Class
 *
 * @category   common
 * @author     Kazuhiro Yasunaga <yasunaga@xenophy.com>
 * @version    Release: 1.0.0
 */

class editController extends xFrameworkPX_Controller_Action
{

    // {{{ props

    /**
     * 暗号キー
     *
     * var string
     */
    public $common_key = 'Px_Common_Key';

    /**
     * 戻り時ハッシュ生成Suffix
     *
     * var string
     */
    public $prevSuffix = '_prev';

    /**
     * 登録時ハッシュ生成Suffix
     *
     * var string
     */
    public $saveSuffix = '_save';

    /**
     * 確認時ハッシュ
     *
     * var string
     */
    public $verifyHash = null;

    /**
     * 戻り時ハッシュ
     *
     * var string
     */
    public $prevHash = null;

    /**
     * 登録時ハッシュ
     *
     * var string
     */
    public $saveHash = null;

    /**
     * 実行モード
     *
     * var string
     */
    public $mode = null;

    /**
     * ベースURI
     *
     * var string
     */
    public $uri = null;

    /**
     * 初期化フラグ
     *
     * var boolean
     */
    private $_bInit = false;

    /**
     * チェック対象トークン
     *
     * var string
     */
    private $_token = null;

    /**
     * ハッシュ設定セッション名
     *
     * var string
     */
    private $_sesname = null;

    /**
     * 強制URI設定
     *
     * var string
     */
    private $_forceBaseUri = '';

    // }}}
    // {{{ __construct

    /**
     * コンストラクタ
     *
     * @access public
     * @return void
     */
    public function __construct($conf) {

        // スーパークラスメソッドコール
        parent::__construct($conf);

        // 設定初期化
        $this->_init();

        // {{{ 各種ハッシュ生成

        $this->_createHash();

        // }}}
        // {{{ トークン生成

        $this->_createToken();

        // }}}
        // {{{ アクション変数セット

        // 入力チェック・確認画面時
        $this->set('verifyAction', $this->verifyHash);

        // 戻り時
        $this->set('prevAction', $this->prevHash);

        // 登録時
        $this->set('saveAction', $this->saveHash);

        // }}}

    }

    // }}}
    // {{{ _init

    /**
     * 初期化メソッド
     *
     * @access private
     * @return void
     */
    private function _init() {

        // 設定の確認
        if (!isset($this->editSetting) || !isset($this->editSetting['name'])) {
            throw new xFrameworkPX_Exception('editControllerの設定が正しくありません。');
        }

        $this->_sesname = $this->editSetting['name'];
        // 強制URI設定
        if (isset($this->editSetting['uri'])) {
            $this->_forceBaseUri = $this->editSetting['uri'];
        }

    }

    // }}}
    // {{{ _createHash

    /**
     * ハッシュ生成メソッド
     *
     * @access public
     * @params $text 暗号対象テキスト
     * @return void
     */
    private function _createHash() {

        $params = $this->Session->read($this->_sesname);

        // 初期化フラグ
        $this->_bInit = false;

        // IPアドレス
        $this->ip = $this->env('REMOTE_ADDR');

        if (!isset($params['startUri']) || !isset($params['mode'])) {
            // セッションの値がなければ、初期化対象
            $this->_bInit = true;
        } else {

            $this->uri = $params['startUri'];
            $this->mode = $params['mode'];

            $this->verifyHash = $this->getVerifyHash();
            $this->prevHash = $this->getPrevHash();
            $this->saveHash = $this->getSaveHash();

        }

        if (!isset($this->post['action']) || empty($this->post['action'])) {
            // actionが送信されない確認画面では、modeで判定する
            if (
                $this->mode != 'verify' &&
                $this->mode != 'none'
            ) {
                $this->_bInit = true;
            }

            // 2画面以上の入力画面でverifyモードは便宜上違うと思うので
            if ($this->mode == 'verify') {
                $this->mode = 'none';
            }

            // 最初の画面であれば初期化
            if (isset($this->editSetting['init']) && $this->editSetting['init'] === true) {
                $this->_bInit = true;
            }
        }

        if ($this->_bInit) {
            // 実行しているURI
            $this->uri = $this->getUri();

            // actionが送信されていたら、モードを判定する
            $this->mode = $this->checkMode();

            // セッション初期化
            $this->Session->remove($this->_sesname);

            $this->verifyHash = $this->getVerifyHash();
            $this->prevHash = $this->getPrevHash();
            $this->saveHash = $this->getSaveHash();

        } else {

            if (isset($this->post['action']) && !empty($this->post['action'])) {
                // actionが送信されていたら、モードを判定する
                $this->mode = $this->checkMode();
            }
        }

        if ($this->mode !== 'save') {
            $this->Session->write($this->_sesname, array(
                'startUri' => $this->uri,
                'mode' => $this->mode,
            ));
        } else {
            $this->Session->remove($this->_sesname);
        }

    }

    // }}}
    // {{{ checkMode

    /**
     * モード取得メソッド
     *
     * @access public
     * @return string
     */
    public function checkMode() {

        if (!isset($this->post['action']) || empty($this->post['action'])) {
            return 'none';
        }

        if ($this->checkVerify($this->post['action'])) {
            return 'verify';
        } else if ($this->checkPrev($this->post['action'])) {
            return 'prev';
        } else if ($this->checkSave($this->post['action'])) {
            return 'save';
        }

        return 'none';

    }

    // }}}
    // {{{ getUri

    /**
     * uri取得メソッド
     *
     * @access public
     * @params $text 暗号対象テキスト
     * @return void
     */
    public function getUri() {

        // 強制URI設定があれば、それを返す
        if ($this->_forceBaseUri !== '') {
            return $this->_forceBaseUri;
        }

        $userDir = '';
        $ret = '';
        $ret = $this->env('REQUEST_URI');

        if (substr($ret,0, 2) == '/~') {
            $userDir = substr($ret, 0, strpos($ret, '/', strlen('/~')));
        }
        if ($userDir) {
            $ret = preg_replace('/'.preg_quote($userDir, '/').'/', '', $ret);
            if (strlen($ret) > 1 && startsWith($ret, '/')) {
                $ret = substr($ret, 1);
            }
        }

        return $ret;

    }

    // }}}
    // {{{ encrypt

    /**
     * 暗号化メソッド
     *
     * @access public
     * @params $text 暗号対象テキスト
     * @return void
     */
    public function encrypt($text)
    {
        return encrypt($this->common_key, $text);
    }

    // }}}
    // {{{ decrypt

    /**
     * 復号化メソッド
     *
     * @access public
     * @params $text 暗号対象テキスト
     * @return string
     */
    public function decrypt($text)
    {
        return decrypt($this->common_key, $text);
    }

    // }}}
    // {{{ checkVerify

    /**
     * 実行モード確認メソッド
     *
     * @access public
     * @params $text 暗号テキスト
     * @return boolean
     */
    public function checkVerify($hash) {

        $text = $this->decrypt($hash);
        $verify = $this->uri.$this->ip;

        return $text === $verify;

    }

    // }}}
    // {{{ checkPrev

    /**
     * 戻りモード確認メソッド
     *
     * @access public
     * @params $text 暗号テキスト
     * @return boolean
     */
    public function checkPrev($hash) {

        $text = $this->decrypt($hash);
        $prev = $this->uri.$this->ip.$this->prevSuffix;

        return $text === $prev;

    }

    // }}}
    // {{{ checkSave

    /**
     * 登録モード確認メソッド
     *
     * @access public
     * @params $text 暗号テキスト
     * @return boolean
     */
    public function checkSave($hash) {

        $text = $this->decrypt($hash);
        $save = $this->uri.$this->ip.$this->saveSuffix;

        if ($text === $save) {
            return true;
        }

        // saveモードで設定セッションがなければ復活
        if ($this->mode == 'save' && !$this->Session->read($this->_sesname)) {
            $this->Session->write($this->_sesname, array(
                'startUri' => $this->uri,
                'mode' => $this->mode,
            ));
        }

        return false;

    }

    // }}}
    // {{{ getVerifyHash

    /**
     * 実行モードハッシュ取得メソッド
     *
     * @access public
     * @params $text 暗号テキスト
     * @return boolean
     */
    public function getVerifyHash() {

        return $this->encrypt($this->uri.$this->ip);

    }

    // }}}
    // {{{ getPrevHash

    /**
     * 戻りモードハッシュ取得メソッド
     *
     * @access public
     * @params $text 暗号テキスト
     * @return boolean
     */
    public function getPrevHash() {

        return $this->encrypt($this->uri.$this->ip.$this->prevSuffix);

    }

    // }}}
    // {{{ getSaveHash

    /**
     * 登録モードハッシュ取得メソッド
     *
     * @access public
     * @params $text 暗号テキスト
     * @return boolean
     */
    public function getSaveHash() {

        return $this->encrypt($this->uri.$this->ip.$this->saveSuffix);

    }

    // }}}
    // {{{ _createToken

    /**
     * トークン生成メソッド
     *
     * @access private
     * @return void
     */
    private function _createToken() {

        $key = $this->_getTokenKey();

        // トークン生成するかどうか
        if ($this->_bInit) {

            // トークンセッションを取得
            $tokens = $this->Session->read('TOKEN_VALUES');
            if (!is_array($tokens)) {
                $tokens = array();
            }

            // トークン生成
            $token = md5(uniqid('', 1));

            $tokens[$key] = $token;

            // トークンセッションに保存
            $this->Session->write('TOKEN_VALUES', $tokens);

        }

        // tokenが送信されてきたら、セッションに保持（リダイレクトする遷移に対応）
        $sendToken = null;
        if (isset($this->post['token'])) {
            $sendToken = $this->post['token'];
        } else if (isset($this->get['token'])) {
            $sendToken = $this->get['token'];
        }

        // 送信トークン配列
        $sesTokens = $this->Session->read('SEND_TOKENS');

        if (!is_null($sendToken)) {

            if (!is_array($sesTokens)) {
                $sesTokens = array();
            }
            $sesTokens[$key] = $sendToken;

            // チェック対象のトークンをセット
            $this->_token = $sendToken;
            // 送信トークンを保存
            $this->Session->write('SEND_TOKENS', $sesTokens);

        } else {

            $this->_token = isset($sesTokens[$key])? $sesTokens[$key] : null;

        }

        // トークンセット
        $this->set('token', $this->_getToken());

    }

    // }}}
    // {{{ checkToken

    /**
     * トークン照合メソッド
     *
     * @access public
     * @return boolean
     */
    public function checkToken() {

        // トークンセッション
        $tokens = $this->Session->read('TOKEN_VALUES');

        $key = $this->_getTokenKey();

        // トークンが存在しない場合はfalse
        if (!isset($tokens[$key])) {
            return false;
        }

        if ($tokens[$key] === $this->_token) {
            return true;
        }

        // saveモードで設定セッションがなければ復活
        if ($this->mode == 'save' && !$this->Session->read($this->_sesname)) {
            $this->Session->write($this->_sesname, array(
                'startUri' => $this->uri,
                'mode' => $this->mode,
            ));
        }

        return false;

    }

    // }}}
    // {{{ deleteToken

    /**
     * トークン削除メソッド
     *
     * @access public
     * @return string $token
     */
    public function deleteToken() {
        $token = $this->Session->remove('TOKEN_VALUES');
        return $token;
    }

    // }}}
    // {{{ _getTokenKey

    /**
     * トークン配列キー生成メソッド
     *
     * @access private
     * @return string
     */
    private function _getTokenKey() {

        // 画面名を取得
        $uri = $this->uri;

        // "/"を"__"
        $key = preg_replace('/\//', '__', $uri);
        // "."を"___"
        $key = preg_replace('/\./', '___', $key);

        return $key;

    }

    // }}}
    // {{{ _getToken

    /**
     * トークン取得メソッド
     *
     * @access private
     * @return mixed
     */
    private function _getToken() {

        $key = $this->_getTokenKey();

        $tokens = $this->Session->read('TOKEN_VALUES');

        return isset($tokens[$key])? $tokens[$key] : null;

    }

    // }}}
    // {{{ getForceUri

    /**
     * 強制URI取得メソッド
     *
     * @access public
     * @return string
     */
    public function getForceUri() {

        return $this->_forceBaseUri;

    }

    // }}}
    // {{{ getUserData

    /**
     * ログインユーザデータ取得メソッド
     *
     * @access public
     * @return array
     */
    public function getUserData() {

        // ログインデータ取得
        return $this->Session->read('USER_INFO');

    }

    // }}}
    // {{{ getUserId

    /**
     * ログインユーザID取得メソッド
     *
     * @access public
     * @return array
     */
    public function getUserId() {

        // ログインデータ取得
        $user = $this->Session->read('USER_INFO');

        return $user['id'];

    }

    // }}}

}

// }}}
