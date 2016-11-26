<?php
	include_once "Code/utility_functions.php";

	if(isset($_POST["submit"]))
	{
		// Get the client id and password and verify them
		$clientid = $_POST["clientid"];
		$password = $_POST["password"];

		$sql = "select * from users where clientid = '" . $clientid . "' and password = '" . $password . "'";

		$values = get_row_in_oracle($sql);

		// client found
		if(!empty($values))
		{
			$clientid = $values[0];

			$sessionid = md5(uniqid(rand()));
			$_SESSION["Session_ID"] = $sessionid;
			$_SESSION["Client"] = $values[2] . " " . $values[3];
			$_SESSION["Client_ID"] = $clientid;
			$_SESSION["User_Type"] = $values[4];
			$_SESSION["Logged_In"] = true;
			
			$sql = "insert into sessions(sessionid, clientid) values('$sessionid', '$clientid')";
			$result_array = execute_sql_in_oracle ($sql);

			if ($result_array["flag"] == false)
			{
				display_oracle_error_message($result_array["cursor"]);
				die("Failed to create a new session");
			} 
			else { header("Location: Home.php"); }
			oci_free_statement($result_array["cursor"]);
		}
		else
		{ 
			$_SESSION["Error"] = "Login Failed.";
			header("Location: index.php");
		}
	}
	else
	{
?>
		<html>
			<head>
				<title> Team MTMK </title>
				<link rel="stylesheet" href="Style/Default.css" type="text/css" />
				<meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
				<meta charset="UTF-8">
			</head>
			<body>
				<h1> Team MTMK - Student Management System </h1>
				<div id="Main_Content">
					<form method="post" action="index.php">
						<p id="Login_Form">
							<br />
							<label> User ID: </label>
							<input type="text" name="clientid" size="20" />
							<br />
							<label> Password: </label>
							<input type="password" name="password" size="20" />
							<br />
						</p>
						<br />
						<input type="submit" name="submit" value="Login">
						<br />
					</form>
					<?php
						if(isset($_SESSION["Error"]))
						{?>
							<p>
								<?php print($_SESSION["Error"]); ?>
							</p>
						<?php 
							unset($_SESSION["Error"]);
						}
					?>
				</div>
			</body>
		</html>
<?php
	} 
?>