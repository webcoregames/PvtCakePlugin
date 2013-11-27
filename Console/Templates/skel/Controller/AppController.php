<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {


    public $components = array(
        'DebugKit.Toolbar',
        'Session',
        'Auth' => array(
            'flash' => array(
                'element' => 'alert',
                'key' => 'auth',
                'params' => array(
                    'plugin' => 'BoostCake',
                    'class' => 'alert-error'
                )
            )
        )
    );

    public $helpers = array(
        'Session'
    );

    public function beforeFilter()
    {
        header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
        if (empty($this->request->params['manager'])) {
            // $this->viewClass = 'PivotCakePlugin.Mustache';
            // $this->helpers['PivotCakePlugin.Includer'] = array('includers' => 'site');
            $this->Auth->allow();

        }
        if (isset($this->request->params['manager']) && $this->request->params['manager'] === true) {
            $this->layout = 'manager';
            // $this->helpers['PivotCakePlugin.Includer'] = array('includers' => 'admin');
            $this->helpers['Html'] = array('className' => 'BoostCake.BoostCakeHtml');
            $this->helpers['Form'] = array('className' => 'BoostCake.BoostCakeForm');
            $this->helpers['Paginator'] = array('className' => 'BoostCake.BoostCakePaginator');
        }
        parent::beforeFilter();
    }

    public function isAuthorized($user = null)
    {
        if (empty($this->request->params['manager'])) {
            return true;
        }
        if (isset($this->request->params['manager'])) {
            return isset($user['id']);
        }
        return true;
    }


    public function beforeRender() {
        if( $this->request->is('ajax')) {
            $this->response->type('application/json');
            echo json_encode($this->viewVars);
            exit;
        }
    }
}
