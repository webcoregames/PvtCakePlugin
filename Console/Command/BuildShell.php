<?php
class BuildShell extends AppShell {
    public function main() {
        $this->out('Rodando os comandos:');
        $this->out('webdispatcher@' . (env('HTTP_HOST') ?: 'localhost') . ' $~/ cd '. APP);
        passthru('cd '. APP);
        $this->out('webdispatcher@' . (env('HTTP_HOST') ?: 'localhost') . ' $'.APP.' ~/libs/composer.phar install');
        passthru('~/libs/composer.phar install');
        $this->out('webdispatcher@' . (env('HTTP_HOST') ?: 'localhost') . ' $'.APP.' npm install');
        passthru('npm install');
        $this->out('webdispatcher@' . (env('HTTP_HOST') ?: 'localhost') . ' $'.APP.' bower install');
        passthru('bower install');
        $this->out('webdispatcher@' . (env('HTTP_HOST') ?: 'localhost') . ' $'.APP.' grunt');
        passthru('grunt');
    }
}