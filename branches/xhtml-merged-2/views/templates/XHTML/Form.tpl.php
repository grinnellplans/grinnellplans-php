<div <?php echo $this->tag_attributes; ?>>
<?php unset($this->tag_attributes); ?>
<form method="<?php echo $this->method; ?>" action="<?php echo $this->action; ?>" <?php echo $this->tag_attributes; ?>>
	<div>
<?php foreach ($this->contents as $template): ?>
	<?php $template->display(); ?>
<?php endforeach ?>
	</div>
</form>
</div>
