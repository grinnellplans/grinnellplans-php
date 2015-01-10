<?php if ($this->doyouread_link_template): ?>
Do you read <?php $this->doyouread_link_template->display(); ?>, who just updated?<hr>	<hr>
<?php
endif ?>

<div id="poweredby">
<img src="images/logo-small.png" class="logo" width="120px" height="45px">
<?php $this->powered_by->display(); ?>
</div>

<?php if ($this->legal_template): ?>
<p style="font-size: 80%">
		<?php $this->legal_template->display(); ?>
<?php
endif ?>
