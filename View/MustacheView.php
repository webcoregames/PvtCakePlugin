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