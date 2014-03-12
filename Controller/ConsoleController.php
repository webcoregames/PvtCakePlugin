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
        $this->request->addDetector('internal-ip', array(
            'env' => 'REMOTE_ADDR',
            'options' => Configure::read('Internal.ip')
        ));
        $this->request->addDetector('internal-host', array(
            'env' => 'HTTP_HOST',
            'options' => Configure::read('Internal.host')
        ));

        try {
            if ($this->request->is('internal-ip') || $this->request->is('internal-host')) {
                App::uses('WebShellDispatcher', 'PvtCake.Lib');
                $Dispatcher = new WebShellDispatcher($this->request->params['pass'], false);
                $Dispatcher->dispatch();
                echo '<pre>' . file_get_contents($Dispatcher->out)  . '</pre>';
                unlink($Dispatcher->out);
            } else {
                echo 'Erro de autorização: ' . env('REMOTE_ADDR') . '@' . env('HTTP_HOST');
                
            }
        } catch (Exception $e) {
            if (isset($Dispatcher->out) && file_exists($Dispatcher->out) {
                unlink($Dispatcher->out);
            }
            echo 'Erro: ' . $e->getMessage();
        }
        exit;
    }

}