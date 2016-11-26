<?
include_once "Code/utility_functions.php";

if(isset($_POST["submit"]))
{
	$output = "nothing special";
	$connection = get_connection();
	$statment = oci_parse($connection, "begin generateStudent(:fname, :lname, :clientid, :password, :age, :streetnumber, :streetname, :city, :state, :zip, :status, :stype, :id); end;");
	oci_bind_by_name($statment, ":fname", $_POST["fname"]);
	oci_bind_by_name($statment, ":lname", $_POST["lname"]);
	oci_bind_by_name($statment, ":clientid", $_POST["clientid"]);
	oci_bind_by_name($statment, ":password", $_POST["password"]);
	oci_bind_by_name($statment, ":age", $_POST["age"]);
	oci_bind_by_name($statment, ":streetnumber", $_POST["streetnumber"]);
	oci_bind_by_name($statment, ":streetname", $_POST["streetname"]);
	oci_bind_by_name($statment, ":city", $_POST["city"]);
	oci_bind_by_name($statment, ":state", $_POST["state"]);
	oci_bind_by_name($statment, ":zip", $_POST["zip"]);
	oci_bind_by_name($statment, ":status", $_POST["status"]);
	oci_bind_by_name($statment, ":stype", $_POST["stype"]);
	oci_bind_by_name($statment, ":id", $output);
	$result = oci_execute($statment);
	$error = oci_error($statment);
	oci_close($connection);
	
	if($result)
	{
		$_SESSION["Error"] = "Student added successfully!";
		header("Location: student_add.php");
	}
	else
	{
		$_SESSION["Error"] = "Add student failed! <br/>";
		header("Location: student_add.php");
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
			<?php Main_menu(); ?>
			<div id="Main_Content">
				<h2> Add a Student </h2>
				<form method="post" action="student_add.php">
					<div>
						<label> First Name: </label>
						<input type="Text" length=20 name="fname" /> <br/>
						<label> Last Name: </label>
						<input type="Text" length=20 name="lname" /> <br/>
						<label> Client ID: </label>
						<input type="Text" length=20 name="clientid" /> <br/>
						<label> Password: </label>
						<input type="password" length=20 name="password" /> <br/>
						<label> Age: </label>
						<input type="number" length=20 name="age" /> <br/>
						<label> Street Number: </label>
						<input type="Text" length=20 name="streetnumber" /> <br/>
						<label> Street Name: </label>
						<input type="Text" length=20 name="streetname" /> <br/>
						<label> City: </label>
						<input type="Text" length=20 name="city" /> <br/>
						<label> State: </label>
						<input type="Text" length=20 name="state" /> <br/>
						<label> ZIP: </label>
						<input type="number" length=20 name="zip" /> <br/>
						<label> Probation Status: </label>
						<select name="status">
							<option value="NO">Not Probation</option>
							<option value="YES">Probation</option>
						</select> <br />
						<label> Student Type: </label>
						<select name="stype">
							<option value="Under-Graduate">Under-Graduate</option>
							<option value="Graduate">Graduate</option>
						</select> <br />
					</div>
					<br/>
					<input type="submit" name="submit" value="Submit" />
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