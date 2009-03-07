<div <?php echo $this->tag_attributes; ?>>
	<span class="newest"><?php $this->newest->display(); ?></span>
	<span class="even_newer"><?php $this->even_newer->display(); ?></span>
	<span class="newer"><?php $this->newer->display(); ?></span>
	<span class="current"><?php $this->current->display(); ?></span>
	<span class="older"><?php $this->older->display(); ?></span>
	<span class="even_older"><?php $this->even_older->display(); ?></span>
	<span class="oldest"><?php $this->oldest->display(); ?></span>
</div>
