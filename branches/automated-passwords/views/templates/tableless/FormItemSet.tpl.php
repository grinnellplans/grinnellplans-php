<div <?php echo $this->tag_attributes; ?>>
<?php if (isset($this->title)): ?>
	<span class="formitemset_label"><?php echo $this->title; ?></span>
<?php endif ?>
<?php
foreach ($this->contents as $template):
	$template->display();
endforeach
?>
</div>
