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