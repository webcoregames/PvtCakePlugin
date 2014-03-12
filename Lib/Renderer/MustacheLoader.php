<?php
class MustacheLoader extends Mustache_Loader_FilesystemLoader {
    public function getTemplates() {
        return $this->templates;
    }
    public function load($name) {
        if (!isset($this->templates[$name])) {
            $this->templates[$name] = parent::load($name);
        }
        return $this->templates[$name];
    }
}