<!DOCTYPE html>
<html>
	<head>
		<title><?php echo ucwords($module) . " : " . ucwords($action); ?></title>
		<link rel="shortcut icon" href="/favicon.ico">
		<meta http-equiv="content-type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width,minimum-scale=1.0">
		<link href="https://fonts.googleapis.com/css?family=Lato:400,400italic,700,700italic" rel="stylesheet" type="text/css" />
		<link href="/css/main.css" rel="stylesheet" type="text/css" />
	<head>
<body<?php the_body_class();?>>
<?php echo $view; ?>

</body>
</html>