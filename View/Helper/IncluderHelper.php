<?php
class IncluderHelper extends Helper {
    public $helpers = array('Html');

    public $settings = array(
        'includers' => array(
            'application'
        )
    );
    public function beforeLayout($layout) {
        $this->includers();
    
    }
    private function includers() {
    
        $version = Configure::read('Version') ?: '0';
        foreach ($this->settings['includers'] as $includer) {
            if (Configure::read('debug') > 0) {
                $this->addAssetsFiles(array(
                    'style' => array(
                        '/developer/styles/fonts/'. $includer .'-fonts.css',
                        '/developer/styles/'. $includer .'-styles.css'
                    ),
                    'header' => array(
                        '/developer/scripts/vendors/modernizr/modernizr.js'
                    ),
                    'script' => array(
                        '/developer/scripts/vendors/requirejs/require.js' => array(
                            'data-main' => Router::url('/developer/scripts/'. $includer .'-config.js')
                        )
                    )
                ));
            } else {
                $this->addAssetsFiles(array(
                    'style' => array(
                        '/assets/styles/fonts/'. $includer .'-fonts.min.css?v='.$version,
                        '/assets/styles/'. $includer .'-styles.min.css?v='.$version
                    ),
                    'header' => array(
                        '/assets/scripts/'. $includer .'-header.min.js?v='.$version, 
                    ),
                    'script' => array(
                        '/assets/scripts/'. $includer .'-body.min.js?v='.$version,
                    )
                ));
            }
        }
    }
    private function addAssetsFiles($settings){
        $this->Html->css(
            $settings['style'], 
            null,
            array('block' => 'css')
        );
        $this->Html->script(
            $settings['header'], 
            array(
                'block' => 'meta'
            )
        );
        foreach ($settings['script'] as $key => $script) {
            $attributes = array();
            if (!is_numeric($key)) {
                $attributes = $script;
                $script = $key;
            }
            $this->Html->script(
                $script,    
                array_merge(
                    array(
                        'block' => 'script'
                    ),
                    $attributes
                )
            );
        }
    }
}