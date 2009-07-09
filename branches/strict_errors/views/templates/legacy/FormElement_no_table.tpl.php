<?php if ($this->label !== null): ?>
<b><?php echo $this->label; ?></b>
<?php endif ?>
<?php include $this->template('views/templates/std/FormInput.tpl.php'); ?>

<?php if ($this->description !== null): ?>
<?php echo $this->description; ?>
<?php endif ?>
