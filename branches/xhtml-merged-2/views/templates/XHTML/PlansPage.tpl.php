<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title><?php echo $this->page_title ?></title>

<?php foreach ($this->stylesheets as $css): ?>
	<link rel="stylesheet" type="text/css" href="<?php echo $css; ?>" />
<?php endforeach; ?>
<?php foreach ($this->scripts as $scriptfile): ?>
	<script type="text/javascript" src="<?php echo $scriptfile; ?>"></script>
<?php endforeach; ?>

</head>
<body id="<?php echo $this->body_id; ?>" class="<?php echo $this->body_class; ?>">

<div id="wrapper">

<?php $this->mainpanel_template->display(); ?>
<?php
			//if ($page->mainpanel) $this->disp_mainpanel($page);
?>

<div id="main"><div>

<?php
	foreach ($this->contents as $template):
		$template->display();
	endforeach
?>
</div></div>

<?php $this->footer_template->display(); ?>
</div>

</body></html>
