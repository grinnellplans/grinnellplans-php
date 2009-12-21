<div <?php echo $this->tag_attributes; ?>>
<?php foreach(array('newest', 'even_newer', 'newer', 'current', 'older', 'even_older', 'oldest') as $linkname): ?>
	<span class="<?php echo $linkname . ($this->navigable[$linkname] ? ' enabled' : ' disabled'); ?>"><?php $this->$linkname->display(); ?></span>
<?php
endforeach ?>
</div>
