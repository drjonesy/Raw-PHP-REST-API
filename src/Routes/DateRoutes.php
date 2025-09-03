<?php
require_once __DIR__ . '/../Controllers/DateController.php';

class DateRoutes
{
    private $controller;

    public function __construct($database)
    {
        $this->controller = new DateController($database);
    }

    // Handle date-related routes
    public function handleRequest($method, $path)
    {
        switch ($method) {
            case 'GET':
                if ($path === '/dates' || $path === '/dates/') {
                    return $this->controller->getAllDates();
                } elseif (preg_match('/^\/dates\/user\/(\d+)\/?$/', $path, $matches)) {
                    $userId = $matches[1];
                    return $this->controller->getDateByUserId($userId);
                }
                break;

            case 'POST':
                if ($path === '/dates' || $path === '/dates/') {
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

                    return $this->controller->createDate($data);
                }
                break;

            case 'PUT':
                if (preg_match('/^\/dates\/id\/(\d+)\/?$/', $path, $matches)) {
                    // Update date by ID
                    $dateId = $matches[1];

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

                    return $this->controller->updateDateById($dateId, $data);
                }
                break;

            case 'DELETE':
                if (preg_match('/^\/dates\/id\/(\d+)\/?$/', $path, $matches)) {
                    // Delete date by ID
                    $dateId = $matches[1];
                    return $this->controller->deleteByDateId($dateId);
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