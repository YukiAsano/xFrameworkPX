<?php

class index extends xFrameworkPX_Controller_Action
{
    public function execute()
    {
        if (isset($this->post->test)) {

            // �Z�b�V�����f�[�^�̏�������
            $this->Session->write('test', $this->post->test);
        }

        $this->set(
            'test',

            // �Z�b�V�����f�[�^�̓ǂݍ���
            $this->Session->read('test')
        );
    }
}
