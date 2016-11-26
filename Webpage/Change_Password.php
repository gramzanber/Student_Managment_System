<?php
	include_once "Code/utility_functions.php";
	
	if(isset($_POST["submit"]))
	{
		$clientid = $_SESSION["Client_ID"];
		$oldpassword = $_POST["password"];
		$newpassword = $_POST["newpassword"];
		$newpassword2 = $_POST["newpassword2"];
		if($newpassword == "")
		{
			$_SESSION["Error"] = "New passwords cannot be blank!";
			header("Location: Change_Password.php");
		}
		else if($newpassword != $newpassword2)
		{
			$_SESSION["Error"] = "New passwords do not match!";
			header("Location: Change_Password.php");
		}
		else
		{
			$sql = "select * from users where clientid = '" . $_SESSION["Client_ID"] . "' and password = '" . $oldpassword . "'";
			$values = get_row_in_oracle($sql);
			// client found
			if(!empty($values))
			{
				$sql = "update users set password ='$newpassword' where clientid = '$clientid'";
				
				$result_array = execute_sql_in_oracle ($sql);
				
				$result = $result_array["flag"];
				$cursor = $result_array["cursor"];
				if($result == false)
				{
					$_SESSION["Error"] = "Password changed failed, contact ttachibana@uco.edu";
					header("Location: Change_Password.php");
				}
				else
				{
					$_SESSION["Error"] = "Password changed successfully!";
					header("Location: Change_Password.php");
				}
				
				oci_free_statement($cursor);
			}
			else
			{ 
				$_SESSION["Error"] = "Old password incorrect.";
				header("Location: Change_Password.php");
			}
		}
	}
	else
	{
?>
		<html>
			<head>
				<title> Change Password </title>
				<link rel="stylesheet" href="Style/Default.css" type="text/css" />
				<meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
				<meta charset="UTF-8">
			</head>
			<body>
				<h1> Team MTMK - Student Management System </h1>
				<?php Main_Menu(); ?>
				<div id="Main_Content">
					<h2> Change Password </h2>
					<form method="post" action="Change_Password.php">
						<p id="Password_Form">
							<label class="Password_Form_Label"> User ID: </label>
							<INPUT type="text" name="clientid" size="20" value="<?php printf($_SESSION["Client_ID"]); ?>" disabled />
							<br />
							<label class="Password_Form_Label"> Old Password: </label>
							<INPUT type="password" name="password" size="20" />
							<br />
							<label class="Password_Form_Label"> New Password: </label>
							<INPUT type="password" name="newpassword" size="20" />
							<br />
							<label class="Password_Form_Label"> Re-Type New Password: </label>
							<INPUT type="password" name="newpassword2" size="20" />
						</p>
						<br />
						<input type="submit" name="submit" value="Submit">
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