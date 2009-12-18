<div <?php echo $this->tag_attributes; ?>>
<?php foreach ($this->contents as $template): ?>
	<?php $template->display(); ?>
<?php endforeach ?>
</div>
