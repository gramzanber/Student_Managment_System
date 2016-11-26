<?
include_once "Code/utility_functions.php";
$query = "select * from student natural join users where clientid ='" . $_SESSION["Client_ID"] . "'";
$values = get_row_in_oracle($query);
$count = count($values);
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
			<?php if($count > 0) { ?>
			<h2> Student Information </h2>
			<table>
				<tr>
					<th> Student ID </th>
					<th> Student Name </th>
					<th> Age </th>
					<th> Address </th>
					<th> Probation </th>
					<th> Student Type </th>
				</tr>
				<?php
					echo"<tr>
						<td>".$values['SID']."</td>
						<td>".$values['FIRSTNAME']. " " . $values['LASTNAME']."</td>
						<td>".$values['AGE']."</td>
						<td>".$values['STREETNUMBER']. " " . $values['STREETNAME']. " "  . $values['CITY']. ", " . $values['STATE']. " " . $values['ZIP']."</td>
						<td>".$values['STATUS']."</td>
						<td>".$values['STYPE']."</td>
					</tr>";
				?>
			</table>
			<?php } else { printf("<br/><br/> There was no information in the database, contact ttachibana@uco.edu."); } ?>
		</div>
	</body>
</html>