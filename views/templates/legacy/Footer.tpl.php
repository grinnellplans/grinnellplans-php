<?php if ($this->doyouread_link_template): ?>
Do you read <?php $this->doyouread_link_template->display(); ?>, who just updated?<hr>	<hr>
<?php endif ?>

<?php if ($this->legal_template): ?>
<p style="font-size: 80%">
		<?php $this->legal_template->display(); ?>
<?php endif ?>
