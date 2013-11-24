<?php
class IncluderHelper extends Helper {
    public $settings = array(
        'includers' => array(
            'application' => array(
                'developer' => array(
                    'style' => array(
                        '/developer/styles/fonts/fonts.css',
                        '/developer/styles/main.css'
                    ),
                    'header' => array(
                        '/developer/scripts/vendors/modernirz/modernirz.js'
                    ),
                    'script' => array(
                        '/developer/scripts/vendors/requirejs/require.js' => array(
                            'data-main' => Router::url('/developer/scripts/application-config.js')
                        )
                    )
                ),
                'production' => array(
                    'style' => array(
                        '/assets/styles/fonts/fonts.css?v='.Configure::read('Version')
                        '/assets/styles/dist.css?v='.Configure::read('Version')
                    ),
                    'header' => array(
                        '/assets/scripts/header.min.js?v='.Configure::read('Version'), 
                    ),
                    'script' => array(
                        '/assets/scripts/application.min.js?v='.Configure::read('Version'),
                    ),
                )    
            )
        )
        
    );
    public function beforeLayout($layout) {
        foreach ($this->settings['includers'] as $what => $settings) {
            if (Configure::read('debug') > 0) {
                $this->addAssetsFiles($settings['developer']);
            } else {
                $this->addAssetsFiles($settings['production']);
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
        foreach ($settings['scripts'] as $key => $script) {
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