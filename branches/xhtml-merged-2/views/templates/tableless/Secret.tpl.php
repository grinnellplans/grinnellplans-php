<div class="secret">
	<span class="secret_id"><?php echo $this->secret_id; ?></span>
	<span class="date_posted"><?php echo date(LONG_DATE_FORMAT, $this->date); ?></span>
	<?php echo $this->message; ?>
</div>
