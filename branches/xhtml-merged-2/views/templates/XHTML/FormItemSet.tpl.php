<div <?php echo $this->tag_attributes; ?>>
<?php if (isset($this->title)): ?>
	<span class="promptset_label"><?php echo $this->title; ?></span>
<?php endif ?>
<?php
foreach ($this->contents as $template):
	$template->display();
endforeach
?>
</div>
