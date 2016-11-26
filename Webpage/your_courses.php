<?
include_once "Code/utility_functions.php";
$query = "select seqid, cnumber, cname, semester, credits, grade, year from student s natural join enroll e natural join section se natural join course c where s.clientid = '" . $_SESSION["Client_ID"] . "' order by year";
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
			<h2> My Courses </h2>
			<table>
				<tr>
					<th> Section ID </th>
					<th> Course No. </th>
					<th> Course Name </th>
					<th> Semester </th>
					<th> Year </th>
					<th> Credits </th>
					<th> Grade </th>
				</tr>
				<?php
					for($index = 0; $index < $count; $index = $index + 1)
					{
						if($values['GRADE'][$index] == '') { $values['GRADE'][$index] = "N/A"; }
						echo"<tr>
							<td>".$values['SEQID'][$index]."</td>
							<td>".$values['CNUMBER'][$index]."</td>
							<td>".$values['CNAME'][$index]."</td>
							<td>".$values['SEMESTER'][$index]."</td>
							<td>".$values['YEAR'][$index]."</td>
							<td>".$values['CREDITS'][$index]."</td>
							<td>".$values['GRADE'][$index]."</td>
						</tr>";
					}
				?>
			</table>
			<p>
				<?php
				$query = "select * from student where clientid = '" . $_SESSION["Client_ID"] . "'";
				$values = get_row_in_oracle($query); 
				if($values['GPA'] != '') { ?>
				GPA: <? printf($values['GPA']); ?> <br/>
				Total Credit Hours Earned: <? printf($values['TOTALCREDITS']); ?> <br/>
				Total Courses Completed: <? printf($values['COURSESCOMPLETED']); ?> <br/> <?php } ?>
			</p>
			<?php } else { printf("<br/><br/> You do not have any past or current classes, please enroll."); } ?>
		</div>
	</body>
</html>