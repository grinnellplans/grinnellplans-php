
<tr><td></td><td><p class="imagelev2">&nbsp;</p></td><td></td>
<td><a href="<?php echo $this->level_link->href; ?>" class="lev2">level <?php echo $this->priority; ?></a>
</td>
<?php if ($this->current): ?>
<?php if ($this->markasread_link): ?>
<td><a onClick ="  return confirm('Are you sure you\'d like to mark all the Plans on level <?php echo $this->priority; ?> as read?')" href="<?php echo $this->markasread_link->href; ?>">X</a></td></tr>
<?php
    endif ?>

<?php foreach($this->names as $name): ?>
<tr><td></td><td></td><td><p class="imagelev3">&nbsp;</p></td>
<td><a href="<?php echo $name->href; ?>" class="lev3">
<?php echo $name->description; ?></a></td></tr>
<?php
    endforeach ?>
<?php
endif ?>
</tr>
