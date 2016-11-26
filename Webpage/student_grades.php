<?
	include_once "Code/utility_functions.php";

if(isset($_POST["submit"]))
{
	// Suppress PHP auto warning.
	ini_set( "display_errors", 0);  

	// Get input from dept_update.php and update the record.
	$sid = $_POST["sid"];
	$seqid = $_POST["seqid"];
	$grade = $_POST["grade"];

	// the sql string
	if(seqidExits(trim($seqid)))
	{
		if(studentInSection(trim($seqid), trim($sid)))
		{
			$sql = "update enroll set grade = '" . $grade . "' where upper(sid) = upper('" . $sid . "') and seqid = '" . $seqid . "'";
			$result = execute_sql_in_oracle($sql);
			
			
			if($result["flag"])
			{
				$_SESSION["Error"] = "Grade entered successfully";
				header("Location: student_grades.php");
			}
			else
			{
				$_SESSION["Error"] = "Grade enter failed! <br/> ";
				header("Location: student_grades.php");
			}
		}
		else
		{
			$_SESSION["Error"] = "<br/> Student is not in this section! <br/> ";
			header("Location: student_grades.php");
		}
	}
	else
	{
		$_SESSION["Error"] = "<br/> Section does not exist! <br/> ";
		header("Location: student_grades.php");
	}
}

else {
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
			<h2> Enter a Student Grade </h2>
			<form method="POST" action="student_grades.php">
				<div>
					<label> Student ID: </label>
					<input type="Text" length=20 name="sid" /> <br/>
					<label> Section Number: </label>
					<input type="Number" length=20 name="seqid" /> <br/>
					<label> Grade: </label>
					<input type="Number" length=20 name="grade" min="0" max="4" /> <br/>
					<br />
					<input type="submit" name="submit" value="Submit">
					<br />
				</div>
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

<? } ?>