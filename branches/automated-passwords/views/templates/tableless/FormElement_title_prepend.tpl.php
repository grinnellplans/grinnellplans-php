<div <?php echo $this->tag_attributes; unset($this->tag_attributes); ?>>
<?php if ($this->label !== null): ?>
	<label class="prompt_label" for="<?php echo $this->prompt_id; ?>"><?php echo $this->label; ?></label>
<?php endif ?>
<?php
$this->tag_attributes = ' id="'.$this->prompt_id.'"';
if ($this->type == 'textarea') {
	include $this->template('views/templates/std/FormTextarea.tpl.php');
} else {
	include $this->template('views/templates/std/FormInput.tpl.php');
}
?>
<?php if ($this->description !== null): ?>
	<label class="prompt_description"><?php echo $this->description; ?></label>
<?php endif ?>
</div>
