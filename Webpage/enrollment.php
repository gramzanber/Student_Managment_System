<?
	include_once "Code/utility_functions.php";

if(isset($_POST["submit"]))
{
	$courses = Array($_POST["course1"], $_POST["course2"], $_POST["course3"], $_POST["course4"], $_POST["course5"], $_POST["course6"]);
	
	$sql = "select sid from student where clientid = '" . $_SESSION["Client_ID"] . "'";
	$result = get_row_in_oracle($sql);
	$sid = $result["SID"];

	for($i = 0; $i < 6; $i++)
	{
		if(trim($courses[$i]) != '')
		{
			if(seqidExits(trim($courses[$i])))
			{
				$sql = "select avail_seat from section where seqid = '" . $courses[$i] . "'";
				$result = get_row_in_oracle($sql);
				$seats = $result["AVAIL_SEAT"];
				
				if($seats > 0)
				{
					$sql = "insert into enroll(sid, seqid) values('" . $sid . "', '" . $courses[$i] . "')";
					$result = execute_sql_in_oracle($sql);
					
					if($result["flag"])
					{
						$_SESSION["Error"] = $_SESSION["Error"] . "<br/> Enrolled successfully into section " . $courses[$i] ."<br/>";
						$sql = "update section set avail_seat = '" . ($seats - 1) . "' where seqid = '" . $courses[$i] . "'";
						$result = execute_sql_in_oracle($sql);
					}
					else
					{
						$_SESSION["Error"] = $_SESSION["Error"] . "<br/> Enrollment into section " . $courses[$i] ."<br/>";
					}
				}
				else
				{
					$_SESSION["Error"] = $_SESSION["Error"] . "<br/> Section is full. " . $courses[$i] ."<br/>";
				}
			}
			else
			{
				$_SESSION["Error"] = $_SESSION["Error"] . "<br/> Section does not exist " . $courses[$i] ."<br/>";
			}
		}
	}
	
	header("Location: enrollment.php");
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
			<form method="POST" action="enrollment.php">
				<div>
					<label> Enroll in Sections </label> <br/>
					<input type="course1" length=10 name="course1" />
					<input type="course2" length=10 name="course2" /> <br/>
					<input type="course3" length=10 name="course3" />
					<input type="course4" length=10 name="course4" /> <br/>
					<input type="course5" length=10 name="course5" />
					<input type="course6" length=10 name="course6" />
					<br/> <br/>
					<input type="submit" name="submit" value="Submit">
					<br /> <br/>
					<input type="submit" name="search" value="List Courses">
					<br />
				</div>
			</form>
			<?php
				if(isset($_SESSION["Error"]))
				{ ?>
					<p>
						<?php printf($_SESSION["Error"]); ?>
					</p>
				<?php 
					unset($_SESSION["Error"]);
				}
			?>
			<?php if(isset($_POST["search"]))
			{
				
				$query = "select * from section s, course c where s.cnumber = c.cnumber";
				$values = get_all_rows_in_oracle($query); 
				$count = count($values['SEQID']); ?>
				
				<?php if($count > 0) { ?>
				<h2> Available Courses </h2>
				<table>
				<tr>
					<th> Section Number </th>
					<th> Course Number </th>
					<th> Course Name </th>
					<th> Credits </th>
					<th> Semester </th>
					<th> Year </th>
					<th> Max Seats </th>
					<th> Available Seats </th>
					<th> Course Time </th>
					<th> Course Description </th>
				</tr>
				<?php
					for($index = 0; $index < $count; $index = $index + 1)
					{
						echo"<tr>
							<td>".$values['SEQID'][$index]."</td>
							<td>".$values['CNUMBER'][$index]."</td>
							<td>".$values['CNAME'][$index]."</td>
							<td>".$values['CREDITS'][$index]."</td>
							<td>".$values['SEMESTER'][$index]."</td>
							<td>".$values['YEAR'][$index]."</td>
							<td>".$values['MAX_SEAT'][$index]."</td>
							<td>".$values['AVAIL_SEAT'][$index]."</td>
							<td>".$values['TIME'][$index]."</td>
							<td>".$values['CDESC'][$index]."</td>
						</tr>";
					}
					unset($values);
				?>
				</table>
				<?php } else { printf("<h4>No course records found.</h4>"); }
			} ?>
		</div>
	</body>
</html>

<? } ?>