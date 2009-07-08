<form method="<?php echo $this->method; ?>" action="<?php echo $this->action; ?>"><table style="text-align:center"><tr><th>Options</th><th>Yes! I want to pick this one</th><th>Votes so far</th></tr>
<?php foreach ($this->contents as $template): ?>
<?php if ($template->contents): ?>
	<?php $input = $template->contents[0]; $sum = $template->contents[1]; ?>
	<tr><td><?php echo $input->label; ?></td><td><input type="<?php echo $input->type; ?>" name="<?php echo $input->name; ?>" value="<?php echo $input->value; ?>"<?php if (isset($input->checked) && $input->checked) echo ' checked="checked"'; ?>></td><td><?php echo $sum->text; ?></td></tr>
<?php else: ?>
<?php     $template->display(); ?>
<?php endif ?>
<?php endforeach ?>
	</table>
		<input type="submit" value="<?php echo $this->submit_button->value; ?>">
</form>
