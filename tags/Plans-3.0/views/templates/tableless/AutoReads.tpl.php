<div <?php echo $this->tag_attributes; ?>><h2>Autoread List</h2>
<ul>
<?php foreach ($this->contents as $template): ?>
	<li class="autoreadlevel <?php echo $template->list_attributes; ?>"><?php $template->display(); ?></li>
<?php endforeach ?>
</ul>
</div>
