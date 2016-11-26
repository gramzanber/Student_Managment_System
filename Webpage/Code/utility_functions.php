<?php

session_start();

function get_connection()
{
	//return oci_connect("gq030", "atmdvj", "gqian:1521/orcl");
	return oci_connect("gq030", "atmdvj", "gqiannew2:1521/pdborcl");
	//return oci_connect("gq037", "jcdlbw", "gqiannew2:1521/pdborcl");
	//return oci_connect("gq024", "akpraw", "gqiannew2:1521/pdborcl");
}

function execute_sql_in_oracle($sql)
{
	$connection = get_connection();
	if($connection == false){
	display_oracle_error_message(null);
	$_SESSION["Error"] = "Failed to connect";
	}

	$cursor = oci_parse($connection, $sql);

	if ($cursor == false) {
	display_oracle_error_message($connection);
	oci_close ($connection);
	$_SESSION["Error"] = "SQL Parsing Failed";
	}

	$result = oci_execute($cursor);

	if ($result == false) {
	display_oracle_error_message($cursor);
	oci_close ($connection);
	$_SESSION["Error"] = "SQL execution Failed";
	}

	oci_free_statement($cursor);
	oci_close ($connection);  

	$return_array["flag"] = $result;
	$return_array["cursor"] = $cursor;

	return $return_array;
}

function get_all_rows_in_oracle($sql)
{
	$connection = get_connection();
	$cursor = oci_parse($connection, $sql);

	if ($cursor == false)
	{
		oci_close ($connection);
		return array();
	}

	$result = oci_execute($cursor);

	if ($result == false)
	{
		oci_close ($connection);
		return array();
	}

	oci_execute($cursor);
	$values = array();
	oci_fetch_all($cursor, $values);
	oci_free_statement($cursor);
	oci_close ($connection);

	return $values;
}

function get_row_in_oracle($sql)
{
	$connection = get_connection();
	$cursor = oci_parse($connection, $sql);

	if ($cursor == false)
	{
		oci_close ($connection);
		return array();
	}

	$result = oci_execute($cursor);

	if ($result == false)
	{
		oci_close ($connection);
		return array();
	}

	oci_execute($cursor);
	$values = oci_fetch_array($cursor);
	oci_free_statement($cursor);
	oci_close($connection);

	return $values;
}

function verify_session()
{
	$sessionid = $_SESSION["Session_ID"];
	$sql = "select clientid from sessions where sessionid='$sessionid'";

	$result = get_row_in_oracle($sql);

	if(empty($result))
	{
		header("Location: index.php");
		$_SESSION["Error"] = "Not logged in.";
		exit();
	}

	oci_free_statement($cursor);
} 

function display_oracle_error_message($resource)
{
	if (is_null($resource))
	$err = oci_error();
	else
	$err = oci_error($resource);

	echo "<BR />";
	echo "Oracle Error Code: " . $err['code'] . "<BR />";
	echo "Oracle Error Message: " . $err['message'] . "<BR />" . "<BR />";

	if ($err['code'] == 1)
	echo("Duplicate Values.  <BR /><BR />");
	else if ($err['code'] == 984 or $err['code'] == 1861 
	or $err['code'] == 1830 or $err['code'] == 1839 or $err['code'] == 1847
	or $err['code'] == 1858 or $err['code'] == 1841)
	echo("Wrong type of value entered.  <BR /><BR />");
	else if ($err['code'] == 1400 or $err['code'] == 1407)
	echo("Required field not correctly filled.  <BR /><BR />");
	else if ($err['code'] == 2292)
	echo("Child records exist.  Need to delete or update them first.  <BR /><BR />");
}

function Main_Menu()
{
	($_SESSION["User_Type"] == 1) ? $User_Level = "Administrator" : $User_Level = "Student"; ?>
	<div id="Main_Menu">
			<p>
				<?php printf($User_Level); ?>'s Menu <br/>
				<hr>
				<a href="Home.php"> Home </a> <br/>
				<hr>
				<?php
					if($User_Level == "Administrator")
					{?>
            			<a href="student_add.php"> Add a Student </a> <br/>
                        <a href="student_search.php"> Student Search </a><br/>
                        <a href="student_grades.php"> Student Grades </a><br/>
					<?php } 
					else { ?>
					    <a href="view_self.php"> My Information </a><br/> 
                        <a href="your_courses.php"> My Courses </a><br/>
                        <a href="all_courses.php"> General Course Info </a><br/>
                        <a href="enrollment.php"> Enrollment</a> <br/>
                    <?php } ?>
				<hr>
				<a href="Change_Password.php"> Change Password </a> <br/>
				<a href="Code/Logout.php"> Logout </a> <br/>
				<hr>
				<a href="Information.php"> About Us </a> <br/>
			</p>
		</div>
<?php }

// Functions to be run before loading page
if(basename($_SERVER['PHP_SELF']) != "index.php") { verify_session(); }

function seqidExits($seqid)
{
	$sql = "select seqid from section where seqid = '" . $seqid . "'";
	$result = get_row_in_oracle($sql);
	if(!empty($result)) { return true; } else { return false; }
}

function studentInSection($seqid, $student)
{
	$sql = "select * from enroll where seqid = '" . $seqid . "' and upper(sid) = upper('" . $student . "')";
	$result = get_row_in_oracle($sql);
	if(!empty($result)) { return true; } else { return false; }
}

?>