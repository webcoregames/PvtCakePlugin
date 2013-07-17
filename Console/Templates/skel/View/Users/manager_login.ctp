<div class="users form">

    <?php echo $this->Session->flash('auth'); ?>
    <?php echo $this->Form->create('User', array(
        'inputDefaults' => array(
            'div' => 'control-group',
            'label' => array(
                'class' => 'control-label'
            ),
            'wrapInput' => 'controls'
        ),
        'class' => 'well form-horizontal'
    )); ?>
    <fieldset>
        <legend>Login</legend>
        <?php echo $this->Form->input('User.username', array(
            'placeholder' => 'Username'
        )); ?>
        <?php echo $this->Form->input('User.password', array(
            'placeholder' => 'Password'
        )); ?>
        <div class="form-actions">
            <?php echo $this->Form->submit('Login', array(
                'div' => false,
                'class' => 'btn btn-primary'
            )); ?>
        </div>
        </fieldset>
    <?php echo $this->Form->end(); ?>
</div>