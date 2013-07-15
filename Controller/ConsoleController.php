<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joaojose
 * Date: 15/07/13
 * Time: 18:08
 * To change this template use File | Settings | File Templates.
 */

class ConsoleController extends Controller
{
    public $uses = false;

    public function run()
    {
        $this->request->addDetector('internal', array(
            'env' => 'REMOTE_ADDR',
            'options' => Configure::read('Internal.ip')
        ));

        try {
            if ($this->request->is('internal')) {
                App::uses('WebShellDispatcher', 'Lib');
                $Dispatcher = new WebShellDispatcher($this->request->params['pass'], false);
                $Dispatcher->dispatch();
                echo '<pre>' . file_get_contents($Dispatcher->out)  . '</pre>';
                unlink($Dispatcher->out);
            } else {
                echo 'Error de IP';
                // debug($_SERVER);
            }
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
        exit;

    }

}