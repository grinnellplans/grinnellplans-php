<a href="<?php echo $this->href; ?>" onclick="<?php echo $this->onclick; ?>"<?php echo $this->tag_attributes; ?>><?php echo $this->description; ?></a>
<?php if ($this->js): ?>
<?php /* Using window.onload is pretty terrible - we need a framework */ ?>
<script>window.onload=function() { <?php echo $this->js; ?> };</script>
<?php endif ?>
