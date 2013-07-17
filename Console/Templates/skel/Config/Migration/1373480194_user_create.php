<?php
class UserCreate extends CakeMigration {

/**
 * Migration description
 *
 * @var string
 * @access public
 */
	public $description = '';

/**
 * Actions to be performed
 *
 * @var array $migration
 * @access public
 */
	public $migration = array(
        'up' => array(
            'create_table' => array(
                'users' => array(
                    'id' => array('type' => 'integer', 'null'=> false, 'key' => 'primary'),
                    'username' => array('type' => 'string'),
                    'password' => array('type' => 'string'),
                    'role' => array('type' => 'string', 'length' => 20),
                    'created' => array('type' => 'datetime'),
                    'modified' => array('type' => 'datetime'),
                    'indexes' => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => true)
                    )
                ),
            )
        ),
        'down' => array(
            'drop_table' => array(
                'users'
            )
        ),
	);

/**
 * Before migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function before($direction) {
		return true;
	}

/**
 * After migration callback
 *
 * @param string $direction, up or down direction of migration process
 * @return boolean Should process continue
 * @access public
 */
	public function after($direction) {
        if ($direction == 'up') {
            $User = $this->generateModel('User');
            App::uses('AuthComponent', 'Controller/Component');
            $User->saveAll(array(
                'username' => 'manager',
                'password' => AuthComponent::password('iddppw2013$')
            ));
        }
		return true;
	}
}
