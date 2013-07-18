<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->fetch('meta');
        echo $this->Html->css(array('/bootstrap/css/bootstrap', 'main'));
		echo $this->fetch('css');


	?>
</head>
<body>
    <div class="container">
        <div class="masthead">
            <div class="muted logo"><?php echo $this->Html->image('logo.png')?> <span></span></span></div>
            <div class="navbar">
                <div class="navbar-inner">
                    <div class="container">
                        <ul class="nav">
                            <?php if ($this->Session->check('Auth.User')):  ?>
                            <li><?php echo $this->Html->link('Logout', array('controller' => 'users', 'action' => 'logout', 'manager' => true))?></li>
                            <?php else: ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div><!-- /.navbar -->
        </div>

        <?php echo $this->Session->flash(); ?>
        <?php echo $this->fetch('content'); ?>
    </div>
    <?php
        echo $this->Html->script(array(
            'jquery-1.10.2.min',
            'jquery-migrate-1.2.1.min',
            '/bootstrap/js/bootstrap',
            'main'
        ));
        echo $this->fetch('script');
    ?>
</body>
</html>
