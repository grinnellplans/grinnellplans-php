<div><form method="<?php echo $this->method; ?>" action="<?php echo $this->action; ?>"<?php echo $this->tag_attributes; ?>><table>
<?php foreach($this->contents as $template): ?>
	<?php $template->display(); ?>
<?php
endforeach ?>
	</table>
		<input type="submit" value="<?php echo $this->submit_button->value; ?>"<?php if (isset($this->submit_button->identifier)) { echo ' id="'.$this->submit_button->identifier.'"'; } ?>>
</form>
</div>
