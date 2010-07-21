<div<?php echo $this->tag_attributes; ?>><table><form method="<?php echo $this->method; ?>" action="<?php echo $this->action; ?>">
<?php foreach($this->contents as $template): ?>
	<?php $template->display(); ?>
<?php
endforeach ?>
	</table>
		<input type="submit" value="<?php echo $this->submit_button->value; ?>">
</form>
</div>
