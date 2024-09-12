<?php
	$inData = getRequestInfo();
	
	$firstname = $inData["firstname"];
	$lastname = $inData["lastname"];
	$username = $inData["username"];
	$password = $inData["password"];

	$conn = new mysqli("localhost", "Chris", "ChrisB", "contactManager");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		// Check if username already exists
		$stmt = $conn->prepare("SELECT ID FROM Users WHERE Username=?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) 
		{
			$stmt->close();
			$conn->close();
			returnWithError("Username already exists");
		}
		else
		{
			// Insert the new user
			$stmt = $conn->prepare("INSERT into Users (FirstName, LastName, Username, Password) VALUES(?,?,?,?)");
			$stmt->bind_param("ssss", $firstname, $lastname, $username, $password);
			$stmt->execute();
			$stmt->close();
			$conn->close();
			returnWithError("");
		}
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
