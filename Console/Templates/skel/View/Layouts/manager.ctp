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
        <?php echo $this->Session->flash(); ?>
        <?php echo $this->fetch('content'); ?>
    </div>
    <?php
        echo $this->fetch('script');
    ?>
</body>
</html>
