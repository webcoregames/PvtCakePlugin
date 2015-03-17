<?php 
App::uses('View', 'View');
class MustacheView extends View {
    private $_defaults = array(
        'source' => 'source',
        'dist' => 'dist',
        'asset' => 'site'
    );

    public function __construct ( Controller $controller = null ) {
        $this->settings = Hash::merge($this->_defaults, Configure::read('Assets'));
        $this->template = WWW_ROOT . $this->getAssets() . '/' .  $this->settings['asset'] . '-templates';
        parent::__construct($controller);
    }
    private function getAssets() {
        return (Configure::read('debug') > 0) ? $this->settings['source'] : $this->settings['dist'];
    }
   
    public function render($view = null, $layout = null) {
        if ($this->hasRendered) {
            return true;
        }
           
        if (!is_array($view)) {
            $view = array($view);
        }
        if ($layout === null) {
            $layout = $this->layout;
        }
        if ($layout) {
            $layout = 'layouts' . DS . $layout;    
        }
        
        if ($this->request->is('ajax')) {
            App::uses('JsonRenderer', 'PvtCake.Lib/Renderer');
            $JsonRenderer = new JsonRenderer($this);
            return $JsonRenderer->render($view, $this->viewVars, $layout);
        } else {
            App::uses('MustacheRenderer', 'PvtCake.Lib/Renderer');
            $MustacheRenderer = new MustacheRenderer($this);
            return $MustacheRenderer->render($view, $this->viewVars, $layout);
        }
    }

}