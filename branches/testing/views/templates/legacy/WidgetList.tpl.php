<table <?php echo $this->tag_attributes; ?>>
<?php foreach ($this->contents as $template): ?>

<tr><td><?php $template->display(); ?></tr></td>
<?php endforeach ?>
</table>
