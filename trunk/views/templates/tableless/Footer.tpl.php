<div id="footer">
<?php if ($this->doyouread_link_template): ?>
	<div id="justupdated">
		<div>Do you read <?php $this->doyouread_link_template->display(); ?>, who just updated?</div>
	</div>
<?php endif ?>


<div id="poweredby">
<div>
<img src="images/logo-small.png" style="vertical-align: middle;" width="120px">
Powered by <a href="<?php echo ProjectInformation::projectUrl(); ?>">GrinnellPlans</a> <?php echo ProjectInformation::version(); ?>, an opensource project. File a <a href="<?php echo ProjectInformation::bugReportUrl(); ?>">bug report</a>.
</div>
</div>

<?php if ($this->legal_template): ?>
	<div id="legal"><div>
		<?php $this->legal_template->display(); ?>
	</div></div>
<?php endif ?>
</div>
