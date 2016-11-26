<?php
	include "utility_functions.php";

	$sessionid = $_SESSION["Session_ID"];
	verify_session($sessionid);

	$sql = "delete from sessions where sessionid = '$sessionid'";

	$result_array = execute_sql_in_oracle($sql);
	$result = $result_array["flag"];
	$cursor = $result_array["cursor"];
	if ($result == false)
	{
		display_oracle_error_message($cursor);
		$_SESSION["Error"] = "Session removal failed";
		header("Location: ../index.php");
	}
	else
	{
		$_SESSION["Error"] = "Successfully Logged Out.";
		header("Location: ../index.php");
	}
?>
