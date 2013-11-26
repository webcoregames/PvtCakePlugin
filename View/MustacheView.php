<?php 
App::uses('View', 'View');
class MustacheView extends View {

    public $templates = array();
    
    public function __construct ( Controller $controller = null ) {
        $this->asset = 'application';
        if (isset($controller->asset)) {
            $this->asset = $controller->asset;
        }
        $this->templateDir = WWW_ROOT . $this->getAssets() . '/' . $this->asset. '-templates';
        parent::__construct($controller);
    }
    private function getAssets() {
        return (Configure::read('debug') > 0) ? 'developer' : 'assets';
    }
    public function doMustache($view, $vars) {
        $render = $this->mustache->render($view, $vars);
        return $render;
    }
    protected function initMustache() {
        App::uses('PivotMustacheLoader', 'PivotCakePlugin.Lib');
        
        $this->mustache = new Mustache_Engine(array(
            'cache' => TMP . 'cache' . DS . 'mustache',
            'loader' => new PivotMustacheLoader( $this->templateDir, array('extension' => '.html')),
            'helpers' => array(
                'urls' => array(
                    'base' => Router::url('/'),
                    'site' => Router::url('/', true),
                    'cdn' => Configure::read('CDN'),
                    'assets' => $this->getAssets()
                )
            )
        ));
    }
    public function loadHelpers() {
        if ($this->request->is('ajax')) {
            return;
        }
        parent::loadHelpers();
    }
    protected function _serialize($serialize) {
        if (is_array($serialize)) {
            $data = array();
            foreach ($serialize as $alias => $key) {
                if (is_numeric($alias)) {
                    $alias = $key;
                }
                if (array_key_exists($key, $this->viewVars)) {
                    $data[$alias] = $this->viewVars[$key];
                }
            }
            $data = !empty($data) ? $data : null;
        } else {
            $data = isset($this->viewVars[$serialize]) ? $this->viewVars[$serialize] : null;
        }

        if (version_compare(PHP_VERSION, '5.4.0', '>=') && Configure::read('debug')) {
            return json_encode($data, JSON_PRETTY_PRINT);
        }

        return json_encode($data);
    }
    public function render($view = null, $layout = null) {
        if ($this->hasRendered) {
            return true;
        }
        
        if ($this->request->is('ajax')) {
            $this->response->type('json');
            $return = $this->_serialize(array_keys($this->viewVars));
            if (!empty($this->viewVars['_jsonp'])) {
                $jsonpParam = $this->viewVars['_jsonp'];
                if ($this->viewVars['_jsonp'] === true) {
                    $jsonpParam = 'callback';
                }
                if (isset($this->request->query[$jsonpParam])) {
                    $return = sprintf('%s(%s)', h($this->request->query[$jsonpParam]), $return);
                    $this->response->type('js');
                }
            }
            return $return;
        } else {
            $this->initMustache();
            $this->Blocks->set('content', '');

            if ($view !== false) {
                if (!is_array($view)) {
                    $view = array($view);
                }
                $content = array();
                $this->getEventManager()->dispatch(new CakeEvent('View.beforeRender', $this, array(join(' ', $view))));
                foreach ($view as $v) {
                    $viewFileName = $this->getMustacheTemplateName($v);
                    $content[] = $this->doMustache($viewFileName, $this->viewVars);
                }
                $this->getEventManager()->dispatch(new CakeEvent('View.afterRender', $this, array(join(' ', $view))));  
                $this->Blocks->set('content', join("\n", $content));
            }
                
            if ($layout === null) {
                $layout = $this->layout;
            }
            if ($layout && $this->autoLayout) {

                $layout = 'layouts' . DS . $layout;
                
                $content = $this->doMustache($layout, array_merge($this->viewVars, array('content_for_layout' => $this->Blocks->get('content'))));
                $head = join("\n\t", $this->_scripts);
                $this->viewVars['templates'] = $this->mustache->getLoader()->getTemplates();
                $this->getEventManager()->dispatch(new CakeEvent('View.beforeLayout', $this, array('')));
                $head .= $this->Blocks->get('css') . $this->Blocks->get('meta');
                $this->viewVars = array_merge($this->viewVars, array(
                    'content_for_skel' => $content,
                    'head_for_skel' => $head,
                    'script_for_skel' => $this->Blocks->get('script')
                ));
                $this->Blocks->set('content', $this->mustache->render('layouts/skel', $this->viewVars));
            }   $this->getEventManager()->dispatch(new CakeEvent('View.afterLayout', $this, array('')));
            
            $this->hasRendered = true;
            return $this->Blocks->get('content');
        }
    }

    private function getMustacheTemplateName($name) {
        $subDir = null;
        if (!is_null($this->subDir)) {
            $subDir = $this->subDir . DS;
        }
        if ($name === null) {
            $name = $this->view;
        }
        $name = str_replace('/', DS, $name);
        list($plugin, $name) = $this->pluginSplit($name);
        if (strpos($name, DS) === false && $name[0] !== '.') {
            $name = $this->viewPath . DS . $subDir . Inflector::underscore($name);
        } elseif (strpos($name, DS) !== false) {
            if ($name[0] === DS || $name[1] === ':') {
                if (is_file($name)) {
                    return $name;
                }
                $name = trim($name, DS);
            } elseif ($name[0] === '.') {
                $name = substr($name, 3);
            } elseif (!$plugin || $this->viewPath !== $this->name) {
                $name = $this->viewPath . DS . $subDir . $name;
            }
        }
        return $name;
    }
}