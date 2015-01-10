<tr <?php echo $this->tag_attributes; ?>>
<?php if (isset($this->title)): ?>
	<td><?php echo $this->title; ?></td>
<?php
endif ?>
<?php
foreach($this->contents as $template):
    $template->display();
endforeach
?>
</tr>
