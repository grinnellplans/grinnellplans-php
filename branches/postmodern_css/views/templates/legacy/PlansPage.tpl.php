<html>
<head>
<META NAME="ROBOTS" CONTENT="NOARCHIVE">

<title><?php echo $this->page_title ?></title>

<?php foreach($this->stylesheets as $css): ?>
	<link rel="stylesheet" type="text/css" href="<?php echo $css; ?>" />
<?php
endforeach; ?>
<?php foreach($this->scripts as $scriptfile): ?>
	<script type="text/javascript" src="<?php echo $scriptfile; ?>"></script>
<?php
endforeach; ?>

</head>
<body>
<!-- body: <?php echo $this->body_id; ?> class: <?php echo $this->body_class; ?> -->

<table width="100%" cellspacing="0" cellpadding="0" class="main">
<tr>
<?php $this->mainpanel_template->display(); ?>

<td valign="top">

<br />

<table>

<tr><td>

<?php if ($this->center): ?>
<center>
<?php
endif ?>

<?php
foreach($this->contents as $template):
    $template->display();
endforeach
?>

<?php if ($this->center): ?>
</center>
<?php
endif ?>

</td></tr></table></td></tr></table>

<?php $this->footer_template->display(); ?>

</body></html>
