<?php
	$inData = getRequestInfo();

	$contactId = $inData["contactId"];
	$name = $inData["name"];
	$phone = $inData["phone"];
	$email = $inData["email"];
	$userId = $inData["userId"];

	$conn = new mysqli("localhost", "Chris", "ChrisB", "contactManager");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$stmt = $conn->prepare("select Name, Phone, Email from Contacts where ID like ? and UserID=?");
		$stmt->bind_param("ss", $contactId, $userId);
		$stmt->execute();
		
		$result = $stmt->get_result();
		$contact = $result->fetch_assoc();
		if($name == "")
		{
			$name = $contact["Name"];
		}
		if($phone == "")
		{
			$phone = $contact["Phone"];
		}
		if($email == "")
		{
			$email = $contact["Email"];
		}
		
		$stmt = $conn->prepare("DELETE FROM Contacts WHERE ID=? AND UserID=?");
		$stmt->bind_param("ss", $contactId, $userId);
		$stmt->execute();
		
		$stmt = $conn->prepare("INSERT into Contacts (ID, Name, Phone, Email, UserID) VALUES(?,?,?,?,?)");
		$stmt->bind_param("sssss", $contactId, $name, $phone, $email, $userId);
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
