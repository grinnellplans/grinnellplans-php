<td valign="top" align="left" class="left" width="12%">
<table class="mainpanel"><tr><td>
<p class="logo">&nbsp;</p>
<Form action="<?php echo $this->panel->fingerbox->action
?>" method="<?php echo $this->panel->fingerbox->method
?>">
<?php foreach ($this->panel->fingerbox->contents as $input): ?>
	<input type="<?php echo $input->type; ?>" name="<?php echo $input->name; ?>" value="<?php echo $input->value; ?>" />
	<?php if ($input->name == "searchname") echo "<br>\n"; ?>
<?php endforeach ?>
</form>

<table class="lowerpanel">
<?php
$this->links_template->display();
$this->autoread_template->display();
?>

</table>
</td></tr></table>
</td>
