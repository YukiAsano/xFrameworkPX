<?php

class index extends xFrameworkPX_Controller_Action
{
    public function execute()
    {

        if (isset($this->post->test)) {

            // �Z�b�V�����f�[�^�̏�������
            $this->Session->write('test', $this->post->test);
        }

        if (isset($this->post->exec)) {

            // �Z�b�V�����f�[�^�̍폜
            $this->Session->remove('test');
        }

        $this->set(
            'test',

            // �Z�b�V�����f�[�^�̓ǂݍ���
            $this->Session->read('test')
        );
    }
}
