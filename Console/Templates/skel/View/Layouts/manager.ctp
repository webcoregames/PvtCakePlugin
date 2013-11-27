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
		echo $this->fetch('css');
	?>
</head>
<body>
    <div class="container">
        <div class="masthead">
            <div class="muted logo"></div>
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
        
        echo $this->fetch('script');
    ?>
</body>
</html>
