<?
include_once "Code/utility_functions.php";
$query = "select * from section s, course c where s.cnumber = c.cnumber";
$values = get_all_rows_in_oracle($query);
$count = count($values['SEQID']);
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
			<h2> General Section Information </h2>
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
			<?php } else { printf("<br/><br/> There was no information in the database, contact ttachibana@uco.edu."); } ?>
		</div>
	</body>
</html>