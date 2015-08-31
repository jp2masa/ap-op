<?php
	$api_key = 'ee17e8a5-f45e-4bd6-a0cf-17b71862c314';
	$sitename = "AP OP";
	
	if(isset($_GET['q']))
		$q = $_GET['q'];
	else
		$q = 'index';
?>
<!DOCTYPE html>
<html>
	<head>
		<link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css" />
		<link href="/css/styles.css" rel="stylesheet" type="text/css" />
		<link href="/css/tooltipster.css" rel="stylesheet" type="text/css" />
		<link href="/css/view.css" rel="stylesheet" type="text/css" />
		<meta charset="UTF-8"></meta>
		<script src="/js/jquery-2.1.4.min.js"></script>
		<script src="/js/bootstrap.min.js"></script>
		<script src="/js/jquery.tooltipster.min.js"></script>
		<script src="/js/script.js"></script>
	</head>
	<body>
<?php
	include("templates/header.php");
		
	if(!file_exists("templates/$q.php"))
		$q = "404";
		
	include("templates/$q.php");
	
	include("templates/footer.php");
?>
		
		<title><?php echo $pagename . " - " . $sitename; ?></title>
	</body>
</html>