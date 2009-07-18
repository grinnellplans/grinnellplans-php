<table class="boardshow">
<tr class="boardrow1"><td><center><b>Title</b></center></td><td><center><b>Newest Message</b></center></td><td><center><b># Posts</b></center></td><td><center><b>First</b></center></td><td><center><b>Last</b></center></td></tr>

<?php foreach ($this->contents as $template): ?>
	<?php $template->display(); ?>
<?php endforeach ?>

</table>
