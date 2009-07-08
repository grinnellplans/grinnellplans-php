<tr><td>
<?php include $this->template('views/templates/std/FormInput.tpl.php'); ?>

</td><td>
<?php if ($this->label !== null): ?>
<b><?php echo $this->label; ?></b>
<?php endif ?>
<?php if ($this->description !== null): ?>
<?php echo $this->description; ?>
<?php endif ?>
</td></tr>
