<?php
class ConfigComponent extends Component {
    public $config = array();
    public function initialize(Controller $controller) {
        $this->all('urls', array(
            'base' => Router::url('/'),
            'site' => Router::url('/', true),
            'cdn' => Configure::read('CDN'),
            'assets' => Router::url('/' . ((Configure::read('debug') > 0) ? 'developer/' : 'assets/'))
        ));
    }
    public function skel($path, $value = null) {
        if (is_null($value)) {
            return Hash::get($this->config, 'skel.' . $path);
        } 
        $this->config = Hash::insert($this->config, 'skel.' . $path, $value);
    }
    public function all($path, $value) {
        if (is_null($value)) {
            return Hash::get($this->config, 'all.' . $path);
        }
        $this->config = Hash::insert($this->config, 'all.' .$path, $value);
    }
    public function beforeRender(Controller $controller) {
        $config = (isset($this->config['all'])) ? $this->config['all'] : array();
        if (!$controller->request->is('ajax') && isset($this->config['skel'])) {
            $config = array_merge($config, $this->config['skel']);
        }
        $controller->set('config', $config);
    }
}