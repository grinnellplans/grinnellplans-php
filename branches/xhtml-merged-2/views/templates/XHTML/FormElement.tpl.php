<div <?php echo $this->tag_attributes; ?>>
<?php unset($this->tag_attributes); $this->input_attributes = ' id="' . $this->prompt_id . '" ' . $this->input_attributes; ?>
<?php include $this->template('views/templates/std/FormInput.tpl.php'); ?>
<?php if ($this->label !== null): ?>
	<label class="prompt_label" for="<?php echo $this->prompt_id; ?>"><?php echo $this->label; ?></label>
<?php endif ?>
<?php if ($this->description !== null): ?>
	<span class="prompt_description"><?php echo $this->description; ?></span>
<?php endif ?>
</div>
