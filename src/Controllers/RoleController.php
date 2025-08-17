<?php
class RoleController
{
    private $db;
    private $protectedRoleIds = [1, 2]; // Role IDs that cannot be deleted

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

    // Create a new role
    public function createRole($data)
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            // Check if name field exists
            if (!isset($data['name'])) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Missing name field'
                ]);
            }

            // Convert to lowercase and trim
            $name = strtolower(trim($data['name']));

            // Check if name is empty
            if (empty($name)) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Name cannot be empty'
                ]);
            }

            // Check for special characters (only letters allowed)
            if (!preg_match('/^[a-z]+$/', $name)) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Failed to create role because special characters were used'
                ]);
            }

            // Check for duplicate name
            $checkSql = "SELECT COUNT(*) FROM roles WHERE LOWER(name) = ?";
            $checkStmt = $connection->prepare($checkSql);
            $checkStmt->execute([$name]);

            if ($checkStmt->fetchColumn() > 0) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Role name already exists'
                ]);
            }

            // Create the role
            $sql = "INSERT INTO roles (name) VALUES (?)";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$name]);

            // Get the newly created role
            $newRoleId = $connection->lastInsertId();
            $newRole = [
                'id' => (int) $newRoleId,
                'name' => $name
            ];

            http_response_code(201);
            return json_encode([
                'success' => true,
                'message' => 'Role created successfully',
                'data' => $newRole
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to create role'
            ]);
        }
    }

    // Update a role by ID
    public function updateRoleById($id, $data)
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

            // Check if name field exists
            if (!isset($data['name'])) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Missing name field'
                ]);
            }

            // Convert to lowercase and trim
            $name = strtolower(trim($data['name']));

            // Check if name is empty
            if (empty($name)) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Name cannot be empty'
                ]);
            }

            // Check for special characters (only letters allowed)
            if (!preg_match('/^[a-z]+$/', $name)) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Failed to update role because special characters were used'
                ]);
            }

            // Check if role exists
            $checkSql = "SELECT * FROM roles WHERE id = ?";
            $checkStmt = $connection->prepare($checkSql);
            $checkStmt->execute([$id]);
            $existingRole = $checkStmt->fetch();

            if (!$existingRole) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'Role not found'
                ]);
            }

            // Check for duplicate name (excluding current role)
            $dupSql = "SELECT COUNT(*) FROM roles WHERE LOWER(name) = ? AND id != ?";
            $dupStmt = $connection->prepare($dupSql);
            $dupStmt->execute([$name, $id]);

            if ($dupStmt->fetchColumn() > 0) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Role name already exists'
                ]);
            }

            // Update the role
            $sql = "UPDATE roles SET name = ? WHERE id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$name, $id]);

            // Return updated role
            $updatedRole = [
                'id' => (int) $id,
                'name' => $name
            ];

            http_response_code(200);
            return json_encode([
                'success' => true,
                'message' => 'Role updated successfully',
                'data' => $updatedRole
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to update role'
            ]);
        }
    }

    // Delete a role by ID
    public function deleteRoleById($id)
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

            // Check if role exists
            $checkSql = "SELECT * FROM roles WHERE id = ?";
            $checkStmt = $connection->prepare($checkSql);
            $checkStmt->execute([$id]);
            $existingRole = $checkStmt->fetch();

            if (!$existingRole) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'Role not found'
                ]);
            }

            // Check if role is protected from deletion
            if (in_array((int) $id, $this->protectedRoleIds)) {
                http_response_code(403);
                return json_encode([
                    'success' => false,
                    'error' => 'Role cannot be deleted'
                ]);
            }

            // Delete the role
            $sql = "DELETE FROM roles WHERE id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$id]);

            // Return deleted role info
            $deletedRole = [
                'id' => (int) $existingRole['id'],
                'name' => $existingRole['name']
            ];

            http_response_code(200);
            return json_encode([
                'success' => true,
                'message' => 'Role deleted successfully',
                'data' => $deletedRole
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to delete role'
            ]);
        }
    }

    // Delete a role by name
    public function deleteRoleByName($name)
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            // Convert name to lowercase
            $name = strtolower(trim($name));
            if (empty($name)) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'Role not found'
                ]);
            }

            // Check if role exists
            $checkSql = "SELECT * FROM roles WHERE LOWER(name) = ?";
            $checkStmt = $connection->prepare($checkSql);
            $checkStmt->execute([$name]);
            $existingRole = $checkStmt->fetch();

            if (!$existingRole) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'Role not found'
                ]);
            }

            // Check if role is protected from deletion
            if (in_array((int) $existingRole['id'], $this->protectedRoleIds)) {
                http_response_code(403);
                return json_encode([
                    'success' => false,
                    'error' => 'Role cannot be deleted'
                ]);
            }

            // Delete the role
            $sql = "DELETE FROM roles WHERE id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$existingRole['id']]);

            // Return deleted role info
            $deletedRole = [
                'id' => (int) $existingRole['id'],
                'name' => $existingRole['name']
            ];

            http_response_code(200);
            return json_encode([
                'success' => true,
                'message' => 'Role deleted successfully',
                'data' => $deletedRole
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to delete role'
            ]);
        }
    }

    // Update a role by name
    public function updateRoleByName($currentName, $data)
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            // Convert current name to lowercase
            $currentName = strtolower(trim($currentName));
            if (empty($currentName)) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'Role not found'
                ]);
            }

            // Check if name field exists
            if (!isset($data['name'])) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Missing name field'
                ]);
            }

            // Convert new name to lowercase and trim
            $newName = strtolower(trim($data['name']));

            // Check if new name is empty
            if (empty($newName)) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Name cannot be empty'
                ]);
            }

            // Check for special characters (only letters allowed)
            if (!preg_match('/^[a-z]+$/', $newName)) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Failed to update role because special characters were used'
                ]);
            }

            // Check if role exists
            $checkSql = "SELECT * FROM roles WHERE LOWER(name) = ?";
            $checkStmt = $connection->prepare($checkSql);
            $checkStmt->execute([$currentName]);
            $existingRole = $checkStmt->fetch();

            if (!$existingRole) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'Role not found'
                ]);
            }

            // Check for duplicate new name (excluding current role)
            $dupSql = "SELECT COUNT(*) FROM roles WHERE LOWER(name) = ? AND id != ?";
            $dupStmt = $connection->prepare($dupSql);
            $dupStmt->execute([$newName, $existingRole['id']]);

            if ($dupStmt->fetchColumn() > 0) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Role name already exists'
                ]);
            }

            // Update the role
            $sql = "UPDATE roles SET name = ? WHERE id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$newName, $existingRole['id']]);

            // Return updated role
            $updatedRole = [
                'id' => (int) $existingRole['id'],
                'name' => $newName
            ];

            http_response_code(200);
            return json_encode([
                'success' => true,
                'message' => 'Role updated successfully',
                'data' => $updatedRole
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to update role'
            ]);
        }
    }
}
?>