<?php
$servername = "localhost";
$username = "Chris";  // Database username
$password = "ChrisB";  // Database password
$dbname = "contactManager";  // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}
header('Content-Type: application/json');

// Check database connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Check request type
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    // User registration
    if ($action === 'register') {
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $login = $_POST['login'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);  // Hash password

        // Prepare SQL statement to insert a new user
        $stmt = $conn->prepare("INSERT INTO Users (FirstName, LastName, Login, Password) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            echo json_encode(["error" => "Prepare statement failed: " . $conn->error]);
            exit();
        }
        $stmt->bind_param("ssss", $firstName, $lastName, $login, $password);

        // Execute statement
        if ($stmt->execute()) {
            echo json_encode(["message" => "User registered successfully"]);
        } else {
            echo json_encode(["error" => "Registration failed: " . $stmt->error]);
        }

    // User login
    } elseif ($action === 'login') {
        $login = $_POST['login'];
        $password = $_POST['password'];

        // Prepare SQL statement to fetch user information
        $stmt = $conn->prepare("SELECT ID, Password FROM Users WHERE Login = ?");
        if (!$stmt) {
            echo json_encode(["error" => "Prepare statement failed: " . $conn->error]);
            exit();
        }
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $stmt->bind_result($userID, $hashedPassword);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            echo json_encode(["message" => "Login successful", "userID" => $userID]);
        } else {
            echo json_encode(["error" => "Invalid login or password"]);
        }
    }
}
$conn->close();
?>
