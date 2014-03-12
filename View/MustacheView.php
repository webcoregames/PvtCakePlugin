<?php 
App::uses('View', 'View');
class MustacheView extends View {

    public function __construct ( Controller $controller = null ) {
        $this->asset = (!empty($controller->asset)) ? $controller->asset : 'application';
        $this->template = WWW_ROOT . $this->getAssets() . '/' . $this->asset. '-templates';
        parent::__construct($controller);
    }
    private function getAssets() {
        return (Configure::read('debug') > 0) ? 'developer' : 'assets';
    }
    // public function doMustache($view, $vars) {
    //     $render = $this->mustache->render($view, $vars);
    //     return $render;
    // }
    
    public function loadHelpers() {
        if ($this->request->is('ajax')) {
            return;
        }
        parent::loadHelpers();
    }
   
    public function render($view = null, $layout = null) {
        if ($this->hasRendered) {
            return true;
        }
        
        if (!is_array($view)) {
            $view = array($view);
        }
        $this->initMustache();
        if ($this->request->is('ajax')) {
            App::uses('JsonRenderer', 'PvtCake.Lib.Renderer');
            $JsonRenderer = new JsonRenderer($this);
            return $JsonRenderer->render($view, $this->viewVars);
        } else {
            App::uses('MustacheRenderer', 'PvtCake.Lib.Renderer');
            $MustacheRenderer = new MustacheRenderer($this);
            return $MustacheRenderer->render($view, $this->viewVars);
        }
    }

}