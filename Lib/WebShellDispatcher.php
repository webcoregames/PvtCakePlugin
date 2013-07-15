<?php
/**
 * Created by JetBrains PhpStorm.
 * User: joaojose
 * Date: 15/07/13
 * Time: 18:07
 * To change this template use File | Settings | File Templates.
 */

App::uses('ShellDispatcher', 'Console');
class WebShellDispatcher extends ShellDispatcher {
    protected function _getShell($shell) {
        list($plugin, $shell) = pluginSplit($shell, true);

        $plugin = Inflector::camelize($plugin);
        $class = Inflector::camelize($shell) . 'Shell';

        App::uses('Shell', 'Console');
        App::uses('AppShell', 'Console/Command');
        App::uses($class, $plugin . 'Console/Command');

        if (!class_exists($class)) {
            throw new MissingShellException(array(
                'class' => $class
            ));
        }
        $this->out = TMP . uniqid();
        $Shell = new $class(new ConsoleOutput($this->out));
        $Shell->plugin = trim($plugin, '.');
        return $Shell;
    }
}