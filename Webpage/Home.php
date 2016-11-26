<?php
	include "Code/utility_functions.php";

	$sessionid = $_SESSION["Session_ID"];
	verify_session($sessionid);
	
	$display = $_SESSION["Is_Admin"];
	$user = $_SESSION["Client"];
	($display == 1) ? $User_Level = "Administrator" : $User_Level = "Student";
?>
<html>
	<head>
		<title> Team MTMK - Student Management System </title>
		<link rel="stylesheet" href="Style/Default.css" type="text/css" />
		<meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
		<meta charset="UTF-8">
	</head>
	<body>
		<h1> Welcome Home - <?php printf($user); ?> </h1>
		<?php Main_menu(); ?>
		<div id="Main_Content">
			<p>
				Navigate this site with the menu on the left.
			</p>
		</div>
	</body>
</html>