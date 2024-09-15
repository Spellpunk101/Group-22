<?php
	$inData = getRequestInfo();
	
	$name = $inData["name"];
	$phone = $inData["phone"];
	$email = $inData["email"];
	$userID = $inData["userID"];

	$conn = new mysqli("localhost", "Chris", "ChrisB", "contactManager");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("INSERT into Contacts (Name, Phone, Email, UserID) VALUES(?,?,?)");
		$stmt->bind_param("ssss", $name, $phone, $email, $userID);
		$stmt->execute();
		$stmt->close();
		$conn->close();
		returnWithError("");
	}

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
