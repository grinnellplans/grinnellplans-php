<table class="<?php echo $this->list_attributes; ?>" width="100%"><tr><td><tr><td><b><p id="<?php echo $this->post_id; ?>"></p></b></td></tr><tr><td><?php?>
<table border="1" width="100%"><tr><td><?php echo $this->post_id; ?></td><?php?>
<td><span id="<?php echo $this->post_id; ?>y" onclick="vote(<?php echo $this->post_id; ?>,'y');" class="clickable"<?php echo $this->yes_style; ?>>↑</span>
<span id="<?php echo $this->post_id; ?>n" onclick="vote(<?php echo $this->post_id; ?>,'n');" class="clickable"<?php echo $this->no_style; ?>>↓</span>
(
<span id="<?php echo $this->post_id; ?>c"><?php echo $this->score; ?></span>
) (
<span id="<?php echo $this->post_id; ?>i"><?php echo $this->votes; ?></span>
 Votes)</td>
<td><?php echo date(DATE_FORMAT, $this->date) ?></td><?php?>
<td><center>[<?php echo $this->post_author_template->display(); ?>]</center></td><?php?>
</tr></table></td></tr><tr><td><?php echo $this->text; ?></td></tr></table>
