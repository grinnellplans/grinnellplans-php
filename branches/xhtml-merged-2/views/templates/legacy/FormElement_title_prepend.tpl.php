<tr><td>
<?php if ($this->label !== null): ?>
<b><?php echo $this->label; ?></b>
<?php endif ?>

</td><td>
<?php include $this->template('views/templates/std/FormInput.tpl.php'); ?>
<?php if ($this->description !== null): ?>
</td><td>
<?php echo $this->description; ?>
<?php endif ?>
</td></tr>
