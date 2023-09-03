<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">

<html>

<head>
<title><?php echo $this->page_title ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php foreach($this->stylesheets as $css): ?>
	<link rel="stylesheet" type="text/css" href="<?php echo $css; ?>">
<?php
endforeach; ?>
<?php foreach($this->scripts as $scriptfile): ?>
	<script type="text/javascript" src="<?php echo $scriptfile; ?>"></script>
<?php
endforeach; ?>

</head>
<body id="<?php echo $this->body_id; ?>" class="<?php echo $this->body_class; ?>">

<div id="wrapper">

<?php $this->mainpanel_template->display(); ?>
<?php
//if ($page->mainpanel) $this->disp_mainpanel($page);

?>

<div id="main"><div>

<?php
foreach($this->contents as $template):
    $template->display();
endforeach
?>
</div></div>

<?php $this->footer_template->display(); ?>
</div>

</body></html>
