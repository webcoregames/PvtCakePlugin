<?php
App::uses('View', 'View');
class MustacheView extends View {
    public function __construct(Controller $controller = null) {
        $path = ROOT . DS .APP_DIR . DS . 'webroot' . DS . 'templates';
        $this->mustache = new Mustache_Engine(array(
            'cache' => TMP . 'cache',
            'loader' => new Mustache_Loader_FilesystemLoader($path, array('extension' => '.html')),
            'helpers' => array(
                'url' => function($text) {
                    return Router::url('/') . $text;
                }
            ),
        ));
        parent::__construct($controller);
    }
    public function render($view = null, $layout = null) {

        $templates = array();
        if ($this->hasRendered) {
            return true;
        }

        $this->loadHelpers();

        $this->Blocks->set('content', '');
        try {
            if ($view !== false) {
                $this->_currentType = self::TYPE_VIEW;
                $viewFileName = $this->getMustacheTemplateName($view);
                $this->getEventManager()->dispatch(new CakeEvent('View.beforeRender', $this, array($viewFileName)));
                $this->Blocks->set('content', $this->mustache->render($viewFileName, $this->viewVars));
                $templates['templates/'.$viewFileName] = $this->mustache->getLoader()->load($viewFileName);

                $partials = (array_reduce($this->mustache->getTokenizer()->scan($templates['templates/'.$viewFileName]), function ($v, $a) {
                    if (is_null($v)) { $v = array(); }
                    if ($a['type'] == '>') {
                        $v[] = $a['name'];
                    }
                    return $v;
                }));
                foreach($partials as $partial) {
                    $templates['templates/'.$partial] = $this->mustache->getLoader()->load($partial);
                }
                $this->getEventManager()->dispatch(new CakeEvent('View.afterRender', $this, array($viewFileName)));
            }
        } catch (Exception $e) {
            debug($e->getMessage());
        }

//
        if ($layout === null) {
            $layout = $this->layout;
        }

        if ($layout && $this->autoLayout) {

            $layout = 'layouts' . DS . $layout;
            try {


                if (!isset($this->viewVars['title_for_layout'])) {
                    $this->viewVars['title_for_layout'] = Inflector::humanize($this->viewPath);
                }
                $content = $this->mustache->render(
                    $layout,
                    array_merge($this->viewVars, array( 'content_for_layout' => $this->Blocks->get('content')))
                );
                $templates["templates/".$layout] = $this->mustache->getLoader()->load($layout);

                $this->Blocks->set('content', $content);
            } catch(Exception $e) {
                debug($e->getMessage());
            }
        }
        try {
            $head = implode("\n\t", $this->_scripts);
            $head .= $this->Blocks->get('meta') . $this->Blocks->get('css') ;
            $tpl = 'var JST = window.JST || {};';
            foreach($templates as $key => $template) {
                $tpl .= "JST['{$key}'] = ".json_encode($template) . ';';
            }
            $this->Blocks->concat('script', $this->Html->scriptBlock($tpl));
            $this->viewVars = array_merge($this->viewVars, array(
                'content_for_skel' => $this->Blocks->get('content'),
                'head_for_skel' => $head,
                'script_for_skel' => $this->Blocks->get('script')
            ));


            if (!isset($this->viewVars['title_for_layout'])) {
                $this->viewVars['title_for_layout'] = Inflector::humanize($this->viewPath);
            }
            $content = $this->mustache->render(
                'layouts/skel', $this->viewVars
            );
            $this->Blocks->set('content', $content);
        } catch(Exception $e) {
            debug($e->getMessage());
        }
        if ($this->mustache->getLoader())
            $this->hasRendered = true;
        return $this->Blocks->get('content');
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