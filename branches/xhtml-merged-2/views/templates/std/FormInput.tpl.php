<input<?php echo $this->tag_attributes; ?> type="<?php echo $this->type; ?>" name="<?php echo $this->name; ?>" value="<?php echo $this->value; ?>"<?php
if (isset($this->checked) && $this->checked) echo ' checked="checked"';
if (isset($this->cols)) echo ' size="'.$this->cols.'"';
?> >
