<?php

class hasOne5 extends xFrameworkPX_Controller_Action
{

    public $modules = array(
        'Docs_Tutorial_Database_hasOne5_uriage',
    );

    public function execute() {

        // テストした結果（配列）を格納
        $this->set('test', $this->Docs_Tutorial_Database_hasOne5_uriage->test());

    }

}
