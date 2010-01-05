<table <?php echo $this->tag_attributes; ?>>
<tr class="heading"><th>Title</th><th>Newest Message</th><th># Posts</th><th>First</th><th>Last</th></tr>

<?php foreach($this->contents as $template): ?>
	<?php $template->display(); ?>
<?php
endforeach ?>

</table>
