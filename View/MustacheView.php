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
            $JsonRenderer = new JsonRenderer($this);
            return $JsonRenderer->render($view, $this->viewVars);
        } else {
            $this->Blocks->set('content', '');
            
            $content = array();
            $this->getEventManager()->dispatch(new CakeEvent('View.beforeRender', $this, array(join(' ', $view))));
            foreach ($view as $v) {
                $viewFileName = $this->getMustacheTemplateName($v);
                $content[] = $this->doMustache($viewFileName, $this->viewVars);
            }
            $this->getEventManager()->dispatch(new CakeEvent('View.afterRender', $this, array(join(' ', $view))));  
            $this->Blocks->set('content', join("\n", $content));
                
            if ($layout === null) {
                $layout = $this->layout;
            }
            if ($layout && $this->autoLayout) {

                
                $alldata = $this->_serialize(array_keys($this->viewVars));
                $layout = 'layouts' . DS . $layout;
                $content = $this->doMustache($layout, array_merge($this->viewVars, array('content_for_layout' => $this->Blocks->get('content'))));
                $head = join("\n\t", $this->_scripts);
                
                $this->getEventManager()->dispatch(new CakeEvent('View.beforeLayout', $this, array('')));
                $head .= $this->Blocks->get('css') . $this->Blocks->get('meta');
                $this->viewVars = array_merge($this->viewVars, array(
                    'content_for_skel' => $content,
                    'head_for_skel' => $head,
                    'script_for_skel' => $this->Blocks->get('script'),
                    'alldata' => $alldata
                ));

                $this->Blocks->set('content', $this->mustache->render('layouts/skel', $this->viewVars));
            }   $this->getEventManager()->dispatch(new CakeEvent('View.afterLayout', $this, array('')));
            
            $this->hasRendered = true;
            return $this->Blocks->get('content');
        }
    }

}