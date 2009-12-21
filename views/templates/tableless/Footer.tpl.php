<div id="footer">
<?php if ($this->doyouread_link_template): ?>
	<div id="justupdated">
		<div>Do you read <?php $this->doyouread_link_template->display(); ?>, who just updated?</div>
	</div>
<?php
endif ?>


	<div id="poweredby"><div>
		<img src="images/logo-small.png" class="logo" width="120px" height="45px">
		<?php $this->powered_by->display(); ?>
	</div></div>

<?php if ($this->legal_template): ?>
	<div id="legal"><div>
		<?php $this->legal_template->display(); ?>
	</div></div>
<?php
endif ?>
</div>
