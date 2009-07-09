<table><tr><td><p class="main">Username: </p></td><td><b><?php echo $this->username
?></b></td></tr></table><table><tr><td><p class="main2">Last login: </p></td><td><?php echo date(DATE_FORMAT, $this->lastlogin)
?></td></tr></table><table><tr><td><p class="main3">Updated on: </p></td><td><?php echo date(DATE_FORMAT, $this->lastupdate)
?></td></tr></table><table><tr><td><p class="main4">Name:</p></td><td><u><?php echo $this->planname
?></u></td></tr></table>
<?php $this->plan_template->display(); ?>
<?php if ($this->addform_present): ?>
<BR><BR><BR><BR><BR><center>
<?php $this->addform_template->display(); ?>
</center>
<?php endif ?>
