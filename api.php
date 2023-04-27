<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Replace with your own database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "safekornrdb";

try {
  // Create a new PDO connection
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // Set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Your SQL query
  $sql = "SELECT * FROM `uuserids`";

  // Prepare the statement and execute
  $stmt = $conn->prepare($sql);
  $stmt->execute();

  // Fetch the results as an associative array
  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Display the results
  foreach ($result as $row) {
    echo "UUID: " . $row['uuid'] . " - Name: " . $row['name'] . "<br>";
  }
} catch (PDOException $e) {
  echo "Error: " . $e->getMessage();
}


$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true); 
switch ($method) {
    case 'GET':
        if (isset($_GET['uuid'])) {
            $stmt = $conn->prepare("SELECT * FROM uuserids WHERE uuid = :uuid");
            $stmt->bindParam(':uuid', $_GET['uuid']);
        } else {
            $stmt = $conn->prepare("SELECT * FROM uuserids");
        }
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($result); //[] result here <-
        break;

    case 'POST':
        if (isset($input['uuid']) && isset($input['urid']) && isset($input['uiid'])) {
            $stmt = $conn->prepare("INSERT INTO uuserids (uuid, urid, uiid) VALUES (:uuid, :urid, :uiid)");
            $stmt->bindParam(':uuid', $input['uuid']);
            $stmt->bindParam(':urid', $input['urid']);
            $stmt->bindParam(':uiid', $input['uiid']);
            $stmt->execute();
            http_response_code(201);
            echo json_encode(["message" => "User created successfully"]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Invalid input data"]);
        }
        break;
 
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
}

// Close the connection
$conn = null;
?>