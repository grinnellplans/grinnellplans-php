<div class="notes_post <?php echo $this->list_attributes; ?>">
	<div class="notes_post_header">
		<div class="post_id"><?php echo $this->post_id; ?></div>
		<div class="post_author">[<?php echo $this->post_author_template->display(); ?>]</div>
		<div class="post_date date"><span class="long"><?php echo date(LONG_DATE_FORMAT, $this->date) ?></span><span class="short"><?php echo date(SHORT_DATE_FORMAT, $this->date) ?></span></div>
		<div class="post_votes">(<?php echo $this->score; ?>) (<?php echo $this->votes; ?> Votes)</div>
	</div>
	<div class="notes_post_content">
		<?php echo $this->text; ?>
	</div>
</div>
