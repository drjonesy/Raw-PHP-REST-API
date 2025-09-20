<?php
// Include the database class and routes
require_once 'Database.php';
require_once 'src/Routes/RoleRoutes.php';
require_once 'src/Routes/DateRoutes.php';
require_once 'src/Routes/UserRoutes.php';

// Tell the browser we're sending JSON data
header('Content-Type: application/json');

// Allow requests from any website (CORS)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests (browsers send these first)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get the request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove the /api part from the path
$path = str_replace('/api', '', $path);

// Create database connection
try {
    $database = new Database();
    $connected = $database->connect();

    if (!$connected) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Handle different routes
if (strpos($path, '/roles') === 0) {
    $roleRoutes = new RoleRoutes($database);
    echo $roleRoutes->handleRequest($method, $path);
} elseif (strpos($path, '/dates') === 0) {
    $dateRoutes = new DateRoutes($database);
    echo $dateRoutes->handleRequest($method, $path);
} elseif (strpos($path, '/users') === 0) {
    $userRoutes = new UserRoutes($database);
    echo $userRoutes->handleRequest($method, $path);
} else {
    // Handle root requests (for testing connection)
    switch ($method) {
        case 'GET':
            handleGet($database);
            break;

        case 'POST':
            handlePost();
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
}

// Function to handle GET requests (for connection testing)
function handleGet($database)
{
    $response = [
        'msg' => 'Connected'
    ];

    echo json_encode($response);
}

// Function to handle POST requests
function handlePost()
{
    // Get the raw POST data
    $input = file_get_contents('php://input');

    // Convert JSON string to PHP array
    $data = json_decode($input, true);

    // Check if JSON was valid
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400); // Bad request
        echo json_encode(['error' => 'Invalid JSON']);
        return;
    }

    // Create response with received data
    $response = [
        'msg' => 'Data received',
        'data' => $data
    ];

    echo json_encode($response);
}
?>