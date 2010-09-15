<?php if (count($this->contents) > 0): ?>
<ul <?php echo $this->tag_attributes; ?>>
<?php foreach($this->contents as $template): ?>
	<li class="<?php echo $template->list_attributes; ?>"><?php $template->display(); ?></li>
<?php
    endforeach ?>
</ul>
<?php
endif ?>
