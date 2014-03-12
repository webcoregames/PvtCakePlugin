<?php
class MustacheRenderer {
    public function __constructor(View $View) {
        $this->View = $View;
        App::uses('MustacheLoader', 'PvtCake.Lib.Renderer');
        $this->mustache = new Mustache_Engine(array(
            'cache' => TMP . 'cache' . DS . 'mustache',
            'loader' => new PivotMustacheLoader($this->View->template, array('extension' => '.html'))
        ));
    }

    private function name($name) {
        $subDir = null;
        if (!is_null($this->View->subDir)) {
            $subDir = $this->View->subDir . DS;
        }
        if ($name === null) {
            $name = $this->View->view;
        }
        $name = str_replace('/', DS, $name);
        list($plugin, $name) = $this->View->pluginSplit($name);
        if (strpos($name, DS) === false && $name[0] !== '.') {
            $name = $this->View->viewPath . DS . $subDir . Inflector::underscore($name);
        } elseif (strpos($name, DS) !== false) {
            if ($name[0] === DS || $name[1] === ':') {
                if (is_file($name)) {
                    return $name;
                }
                $name = trim($name, DS);
            } elseif ($name[0] === '.') {
                $name = substr($name, 3);
            } elseif (!$plugin || $this->View->viewPath !== $this->name) {
                $name = $this->View->viewPath . DS . $subDir . $name;
            }
        }
        return $name;
    }

    public function compile($views, $vars) {
        $render = $this->mustache->render($this->name($view), $vars);
        return $render;
    }
    public function render($views, $vars) {
        $this->View->Blocks->set('content', '');
        $content = array();
        $this->View->getEventManager()->dispatch(new CakeEvent('View.beforeRender', $this->View, array(join(' ', $view))));
        foreach ($views as $v) {
            $content[] = $this->compile($v, $vars);
        }
        $this->View->Blocks->set('content', join("\n", $content));
        $this->View->getEventManager()->dispatch(new CakeEvent('View.afterRender', $this->View, array(join(' ', $view))));  
        
            
        if ($layout === null) {
            $layout = $this->layout;
        }
        if ($layout && $this->autoLayout) {
            $this->View->getEventManager()->dispatch(new CakeEvent('View.beforeLayout', $this->View, array('')));
            // Renderizando Layout
            $layout = 'layouts' . DS . $layout;
            $content = $this->compile($layout, array_merge($vars, array('content_for_layout' => $this->View->Blocks->get('content'))));

            // Renderizando Skel
            $head_for_skel = join("\n", array_merge(array($this->View->Blocks->get('css'), $this->View->Blocks->get('meta')));
            $vars = array_merge($vars, array(
                'content_for_skel' => $content,
                'head_for_skel' => $head_for_skel,
                'script_for_skel' => $this->Blocks->get('script'),
                'data' => $this->allData($vars)
            ));

            $this->View->Blocks->set('content', $this->compile('layouts/skel', $vars));
        }   $this->View->getEventManager()->dispatch(new CakeEvent('View.afterLayout', $this->View, array('')));
        
        $this->View->hasRendered = true;
        return $this->View->Blocks->get('content');
    }
    public function templates() {
        return $this->mustache->getLoader()->getTemplates();
    }
    private function allData($vars) {
        App::uses('JsonRenderer', 'PvtCake.Lib.Renderer');
        $JsonRenderer = new JsonRenderer($this);
        return $JsonRenderer->serialize($vars);
    }
}