<div class="plan">
	<div id="header">
		<ul>
		<li class="username"><span class="title">Username:</span> <span class="value"><?php echo $this->username ?></span></li>
		<li class="lastupdated"><span class="title">Last Updated:</span> 
			<span class="value">
				<span class="long"><?php echo date(LONG_DATE_FORMAT, $this->lastupdate) ?></span>
				<span class="short"><?php echo date(SHORT_DATE_FORMAT, $this->lastupdate) ?></span>
			</span>
		</li>
		<li class="lastlogin"><span class="title">Last Login:</span> 
			<span class="value">
				<span class="long"><?php echo date(LONG_DATE_FORMAT, $this->lastlogin) ?></span>
				<span class="short"><?php echo date(SHORT_DATE_FORMAT, $this->lastlogin) ?></span>
			</span>
		</li>

		<li class="planname"><span class="title">Name:</span> <span class="value"><?php echo $this->planname ?></span></li>
		</ul>
	</div>
<?php $this->plan_template->display(); ?>
<?php $this->addform_template->display(); ?>
</div>
