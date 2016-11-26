<?
include_once "Code/utility_functions.php";
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
			<h2> Find Student </h2>
			<form method="POST" action="student_search.php">
				<div>
					<label> Student First Name: </label>
					<input type="Text" lenght=20 name="sfname" /> <br/>
					<label> Student Last Name: </label>
					<input type="Text" lenght=20 name="slname" /> <br/>
					<label> Student ID: </label>
					<input type="Text" lenght=20 name="studentid" /> <br/>
					<label> Course Number: </label>
					<input type="Text" length=20 name ="coursenum" /> <br/>
					<br />
					<input type="submit" name="submit" value="Submit">
					<input type="submit" name="allStudents" value="View All">
					<br />
				</div>
			</form>
			<?php
				if(isset($_POST["allStudents"]))
				{
					$query = "select * from student s natural join users u";
					$values = get_all_rows_in_oracle($query); 
					$count = count($values['SID']); ?>
					
					
					<br>
					<h2> All Students </h2>
					<?php if($count > 0) { ?>
					<table>
						<tr>
							<th> Student ID </th>
							<th> Student Name </th>
							<th> Age </th>
							<th> Address </th>
							<th> Probation </th>
							<th> Type </th>
						</tr>
					<?php
					for($index = 0; $index < $count; $index = $index + 1)
					{
						echo"<tr>
							<td>".$values['SID'][$index]."</td>
							<td>".$values['FIRSTNAME'][$index] . " " . $values['LASTNAME'][$index] ."</td>
							<td>".$values['AGE'][$index]."</td>
							<td>".$values['STREETNUMBER'][$index] . " " . $values['STREETNAME'][$index] . " "  . $values['CITY'][$index] . ", " . $values['STATE'][$index] . " " . $values['ZIP'][$index] ."</td>
							<td>".$values['STATUS'][$index]."</td>
							<td>".$values['STYPE'][$index]."</td>
						</tr>";
					}
					?>
					</table>
					<?php } else { printf("<h4>No student records found.</h4>"); } ?>
			<?php }
			?>
			<?php
				if(isset($_POST["submit"]))
				{
					$query = "select distinct sid, firstname, lastname, age, streetnumber, streetname, city, state, zip, status, stype from users natural join student where ";
					if(trim($_POST["studentid"]) != "") { $query = $query . " upper(sid) like upper('" . $_POST["studentid"] . "%')"; }
					if(trim($_POST["sfname"]) != "") { $query = $query . " upper(firstname) like upper('" . $_POST["sfname"] . "%')"; }
					if(trim($_POST["slname"]) != "") { $query = $query . " upper(lastname) like upper('" . $_POST["slname"] . "%')"; }
					if(trim($_POST["coursenum"]) != "")
					{
						$query = "select distinct sid, firstname, lastname, age, streetnumber, streetname, city, state, zip, status, stype from users u natural join student s natural join enroll e natural join section where upper(cnumber) like upper('" . $_POST["coursenum"] . "')";
					}
					$values = get_all_rows_in_oracle($query); 
					$count = count($values['SID']); ?>
					
					<br>
					<h2> Search Results </h2>
					<?php if($count > 0) { ?>
					<table>
						<tr>
							<th> Student ID </th>
							<th> Student Name </th>
							<th> Age </th>
							<th> Address </th>
							<th> Probation </th>
							<th> Type </th>
						</tr>
					<?php
					for($index = 0; $index < $count; $index = $index + 1)
					{
						echo"<tr>
							<td>".$values['SID'][$index]."</td>
							<td>".$values['FIRSTNAME'][$index] . " " . $values['LASTNAME'][$index] ."</td>
							<td>".$values['AGE'][$index]."</td>
							<td>".$values['STREETNUMBER'][$index] . " " . $values['STREETNAME'][$index] . " "  . $values['CITY'][$index] . ", " . $values['STATE'][$index] . " " . $values['ZIP'][$index] ."</td>
							<td>".$values['STATUS'][$index]."</td>
							<td>".$values['STYPE'][$index]."</td>
						</tr>";
					}
					?>
					</table>
					<?php } else { printf("<h4>No student records found.</h4>"); } ?>
			<?php }
			?>
		</div>
	</body>
</html>