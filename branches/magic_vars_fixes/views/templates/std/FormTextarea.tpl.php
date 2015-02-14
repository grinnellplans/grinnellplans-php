<textarea<?php echo $this->tag_attributes; ?> name="<?php echo $this->name; ?>"<?php
if (isset($this->rows)) echo ' rows="' . $this->rows . '"';
if (isset($this->cols)) echo ' cols="' . $this->cols . '"';
?>><?php echo $this->value; ?></textarea>
