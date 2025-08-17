<?php
require_once __DIR__ . '/../Controllers/RoleController.php';

class RoleRoutes
{
    private $controller;

    public function __construct($database)
    {
        $this->controller = new RoleController($database);
    }

    // Handle role-related routes
    public function handleRequest($method, $path)
    {
        switch ($method) {
            case 'GET':
                if ($path === '/roles' || $path === '/roles/') {
                    return $this->controller->getAllRoles();
                } elseif (preg_match('/^\/roles\/id\/(\d+)\/?$/', $path, $matches)) {
                    $roleId = $matches[1];
                    return $this->controller->getRoleById($roleId);
                } elseif (preg_match('/^\/roles\/name\/([^\/]+)\/?$/', $path, $matches)) {
                    $roleName = urldecode($matches[1]);
                    return $this->controller->getRoleByName($roleName);
                }
                break;

            case 'POST':
                if ($path === '/roles' || $path === '/roles/') {
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

                    return $this->controller->createRole($data);
                }
                break;

            case 'PUT':
                if (preg_match('/^\/roles\/id\/(\d+)\/?$/', $path, $matches)) {
                    // Update role by ID
                    $roleId = $matches[1];

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

                    return $this->controller->updateRoleById($roleId, $data);
                } elseif (preg_match('/^\/roles\/name\/([^\/]+)\/?$/', $path, $matches)) {
                    // Update role by name
                    $roleName = urldecode($matches[1]);

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

                    return $this->controller->updateRoleByName($roleName, $data);
                }
                break;

            case 'DELETE':
                if (preg_match('/^\/roles\/id\/(\d+)\/?$/', $path, $matches)) {
                    // Delete role by ID
                    $roleId = $matches[1];
                    return $this->controller->deleteRoleById($roleId);
                } elseif (preg_match('/^\/roles\/name\/([^\/]+)\/?$/', $path, $matches)) {
                    // Delete role by name
                    $roleName = urldecode($matches[1]);
                    return $this->controller->deleteRoleByName($roleName);
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