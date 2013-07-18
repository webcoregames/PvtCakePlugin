<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joaojose
 * Date: 17/07/13
 * Time: 12:52
 * To change this template use File | Settings | File Templates.
 */
class IncluderHelper extends AppHelper {
    public $helpers = array('Html');
    public function beforeRender() {
        $this->Html->css('/styles/index.css', null, array('block' => 'css'));
        $this->Html->scriptBlock($this->_View->element('config'), array('block' => 'meta'));
        $this->Html->scriptBlock('var DATA = ' . json_encode($this->_View->viewVars), array('block' => 'script'));
        $this->Html->script('/scripts/libs/requirejs/require.js', array('block' => 'script', 'data-main'=> $this->Html->url('/scripts/config')));
    }
}