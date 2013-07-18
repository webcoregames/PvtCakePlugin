<?php
/**
 * Created by JetBrains PhpStorm.
 * User: lucas
 * Date: 7/17/13
 * Time: 10:01 AM
 * To change this template use File | Settings | File Templates.
 */
class UsersController extends AppController {

    public $layout = 'manager';

    public function manager_login() {
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                $this->redirect($this->Auth->redirectUrl());
            } else {
                $this->Session->setFlash('Usuário ou senha inválido', 'default', array('class' => 'alert alert-error'));
            }
        }
    }

    public function manager_logout() {
        $this->redirect($this->Auth->logout());
    }
}