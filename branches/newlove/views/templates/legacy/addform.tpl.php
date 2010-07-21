<table><tr><td><p class="sub2"><form method="<?php echo $this->method; ?>" action="<?php echo $this->action; ?>">
<?php foreach($this->inputs as $input): ?>
<input type="<?php echo $input->type; ?>" name="<?php echo $input->name; ?>" value="<?php echo $input->value; ?>"<?php if ($input->checked): ?> checked="checked"<?php
    endif ?>><?php if ($input->type == 'radio'):
        echo $input->description; ?></input><?php
    endif ?>
<?php
endforeach ?>
		<input type="submit" value="<?php echo $this->submit_button->value; ?>">
</form></p></td></tr></table>
