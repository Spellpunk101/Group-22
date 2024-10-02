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
		if($name == "")
		{
			if($phone == "")
			{
				$stmt = $conn->prepare("INSERT into Contacts (ID, Name, Phone, Email, UserID) VALUES(?,?,?,?,?) On DUPLICATE KEY UPDATE Name=VALUES(?) Phone=VALUES(?) UserID=VALUES(?)");
				$stmt->bind_param("ssssssss", $contactId, $name, $phone, $email, $userId, $name, $phone, $userId);
			}
			else if($email == "")
			{
				$stmt = $conn->prepare("INSERT into Contacts (ID, Name, Phone, Email, UserID) VALUES(?,?,?,?,?) On DUPLICATE KEY UPDATE Name=VALUES(?) Email=VALUES(?) UserID=VALUES(?)");
				$stmt->bind_param("ssssssss", $contactId, $name, $phone, $email, $userId, $name, $email, $userId);
			}
			else
			{
				$stmt = $conn->prepare("INSERT into Contacts (ID, Name, Phone, Email, UserID) VALUES(?,?,?,?,?) On DUPLICATE KEY UPDATE Name=VALUES(?) UserID=VALUES(?)");
				$stmt->bind_param("sssssss", $contactId, $name, $phone, $email, $userId, $name, $userId);
			}
		}
		else if($phone == "")
		{
			if($email == "")
			{
				$stmt = $conn->prepare("INSERT into Contacts (ID, Name, Phone, Email, UserID) VALUES(?,?,?,?,?) On DUPLICATE KEY UPDATE Phone=VALUES(?) Email=VALUES(?) UserID=VALUES(?)");
				$stmt->bind_param("ssssssss", $contactId, $name, $phone, $email, $userId, $phone, $email, $userId);
			}
			else
			{
				$stmt = $conn->prepare("INSERT into Contacts (ID, Name, Phone, Email, UserID) VALUES(?,?,?,?,?) On DUPLICATE KEY UPDATE Phone=VALUES(?) UserID=VALUES(?)");
				$stmt->bind_param("sssssss", $contactId, $name, $phone, $email, $userId, $phone, $userId);
			}
		}
		else if($email == "")
		{
			$stmt = $conn->prepare("INSERT into Contacts (ID, Name, Phone, Email, UserID) VALUES(?,?,?,?,?) On DUPLICATE KEY UPDATE Email=VALUES(?) UserID=VALUES(?)");
			$stmt->bind_param("sssssss", $contactId, $name, $phone, $email, $userId, $email, $userId);
		}
		else
		{
			$stmt = $conn->prepare("INSERT into Contacts (ID, Name, Phone, Email, UserID) VALUES(?,?,?,?,?) On DUPLICATE KEY UPDATE Name=VALUES(?) Phone=VALUES(?) Email=VALUES(?) UserID=VALUES(?)");
			$stmt->bind_param("sssssssss", $contactId, $name, $phone, $email, $userId, $name, $phone, $email, $userId);
		}
		
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
