<?php
class RoleController
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    // Get all roles from the database
    public function getAllRoles()
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            $sql = "SELECT * FROM roles ORDER BY id";
            $stmt = $connection->prepare($sql);
            $stmt->execute();

            $roles = $stmt->fetchAll();

            return json_encode([
                'success' => true,
                'data' => $roles
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to fetch roles'
            ]);
        }
    }

    // Get a single role by ID
    public function getRoleById($id)
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            // Validate ID is a positive integer
            if (!is_numeric($id) || $id <= 0) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'Role not found'
                ]);
            }

            $sql = "SELECT * FROM roles WHERE id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$id]);

            $role = $stmt->fetch();

            if (!$role) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'Role not found'
                ]);
            }

            return json_encode([
                'success' => true,
                'data' => $role
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to fetch role'
            ]);
        }
    }

    // Get a single role by name
    public function getRoleByName($name)
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            // Convert name to lowercase and validate it's not empty
            $name = strtolower(trim($name));
            if (empty($name)) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'Role not found'
                ]);
            }

            $sql = "SELECT * FROM roles WHERE LOWER(name) = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$name]);

            $role = $stmt->fetch();

            if (!$role) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'Role not found'
                ]);
            }

            return json_encode([
                'success' => true,
                'data' => $role
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to fetch role'
            ]);
        }
    }
}
?>