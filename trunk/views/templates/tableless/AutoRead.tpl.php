<div class="autoreadname">
	<?php $this->level_link_template->display(); ?>
	<?php $this->markasread_template->display(); ?>
</div>
<?php if (count($this->contents) > 0): ?>
<ul>
<?php foreach ($this->contents as $template): ?>
	<li class="autoreadentry <?php echo $template->list_attributes; ?>"><?php $template->display(); ?></li>
<?php endforeach ?>
</ul>
<?php endif ?>
