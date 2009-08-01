<?php if ($this->doyouread_link_template): ?>
Do you read <?php $this->doyouread_link_template->display(); ?>, who just updated?<hr>	<hr>
<?php endif ?>

<div id="poweredby">
<img src="images/logo-small.png">
Powered by <a href="<?php echo ProjectInformation::projectUrl(); ?>">GrinnellPlans</a> <?php echo ProjectInformation::version(); ?>, an opensource project. File a <a href="<?php echo ProjectInformation::bugReportUrl(); ?>">bug report</a>.
</div>

<?php if ($this->legal_template): ?>
<p style="font-size: 80%">
		<?php $this->legal_template->display(); ?>
<?php endif ?>
