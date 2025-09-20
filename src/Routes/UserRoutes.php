<?php
require_once __DIR__ . '/../Controllers/UserController.php';

class UserRoutes
{
    private $controller;

    public function __construct($database)
    {
        $this->controller = new UserController($database);
    }

    // Handle user-related routes
    public function handleRequest($method, $path)
    {
        switch ($method) {
            case 'GET':
                if ($path === '/users' || $path === '/users/') {
                    return $this->controller->getAllUsers();
                } elseif (preg_match('/^\/users\/id\/(\d+)\/?$/', $path, $matches)) {
                    $userId = $matches[1];
                    return $this->controller->getUserById($userId);
                } elseif (preg_match('/^\/users\/firstname\/([^\/]+)\/?$/', $path, $matches)) {
                    $firstName = urldecode($matches[1]);
                    return $this->controller->getUserByFirstName($firstName);
                } elseif (preg_match('/^\/users\/lastname\/([^\/]+)\/?$/', $path, $matches)) {
                    $lastName = urldecode($matches[1]);
                    return $this->controller->getUserByLastName($lastName);
                } elseif (preg_match('/^\/users\/email\/([^\/]+)\/?$/', $path, $matches)) {
                    $email = urldecode($matches[1]);
                    return $this->controller->getUserByEmail($email);
                } elseif (preg_match('/^\/users\/phone\/([^\/]+)\/?$/', $path, $matches)) {
                    $phone = urldecode($matches[1]);
                    return $this->controller->getUserByPhone($phone);
                }
                break;

            case 'POST':
                if ($path === '/users' || $path === '/users/') {
                    // Get JSON data from request body
                    $input = file_get_contents('php://input');
                    $data = json_decode($input, true);

                    // Check if JSON was valid
                    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                        http_response_code(400);
                        return json_encode([
                            'success' => false,
                            'error' => 'Invalid JSON'
                        ]);
                    }

                    return $this->controller->createUser($data);
                }
                break;

            case 'PUT':
                if (preg_match('/^\/users\/id\/(\d+)\/?$/', $path, $matches)) {
                    // Update user by ID
                    $userId = $matches[1];

                    // Get JSON data from request body
                    $input = file_get_contents('php://input');
                    $data = json_decode($input, true);

                    // Check if JSON was valid
                    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                        http_response_code(400);
                        return json_encode([
                            'success' => false,
                            'error' => 'Invalid JSON'
                        ]);
                    }

                    return $this->controller->updateUserById($userId, $data);
                } elseif (preg_match('/^\/users\/email\/([^\/]+)\/?$/', $path, $matches)) {
                    // Update user by email
                    $email = urldecode($matches[1]);

                    // Get JSON data from request body
                    $input = file_get_contents('php://input');
                    $data = json_decode($input, true);

                    // Check if JSON was valid
                    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                        http_response_code(400);
                        return json_encode([
                            'success' => false,
                            'error' => 'Invalid JSON'
                        ]);
                    }

                    return $this->controller->updateUserByEmail($email, $data);
                }
                break;

            case 'DELETE':
                if (preg_match('/^\/users\/id\/(\d+)\/?$/', $path, $matches)) {
                    // Delete user by ID
                    $userId = $matches[1];
                    return $this->controller->deleteUserById($userId);
                } elseif (preg_match('/^\/users\/email\/([^\/]+)\/?$/', $path, $matches)) {
                    // Delete user by email
                    $email = urldecode($matches[1]);
                    return $this->controller->deleteUserByEmail($email);
                }
                break;

            default:
                http_response_code(405);
                return json_encode(['error' => 'Method not allowed']);
        }

        // If no route matches
        http_response_code(404);
        return json_encode(['error' => 'Route not found']);
    }
}
?>