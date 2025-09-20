<?php
class UserController
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    // Get all users from the database
    public function getAllUsers()
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            $sql = "SELECT id, first_name, last_name, user_role, email, phone, dob, profile FROM users ORDER BY id";
            $stmt = $connection->prepare($sql);
            $stmt->execute();

            $users = $stmt->fetchAll();

            // Convert data types
            foreach ($users as &$user) {
                $user['id'] = (int) $user['id'];
                $user['user_role'] = (int) $user['user_role'];
            }

            return json_encode([
                'success' => true,
                'data' => $users
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to fetch users'
            ]);
        }
    }

    // Get a single user by ID
    public function getUserById($id)
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
                    'error' => 'User not found'
                ]);
            }

            $sql = "SELECT id, first_name, last_name, user_role, email, phone, dob, profile FROM users WHERE id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$id]);

            $user = $stmt->fetch();

            if (!$user) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            // Convert data types
            $user['id'] = (int) $user['id'];
            $user['user_role'] = (int) $user['user_role'];

            return json_encode([
                'success' => true,
                'data' => $user
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to fetch user'
            ]);
        }
    }

    // Get users by first name
    public function getUserByFirstName($firstName)
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            // Convert to lowercase and validate
            $firstName = strtolower(trim($firstName));
            if (empty($firstName)) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            $sql = "SELECT id, first_name, last_name, user_role, email, phone, dob, profile FROM users WHERE LOWER(first_name) = ? ORDER BY id";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$firstName]);

            $users = $stmt->fetchAll();

            if (empty($users)) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            // Convert data types
            foreach ($users as &$user) {
                $user['id'] = (int) $user['id'];
                $user['user_role'] = (int) $user['user_role'];
            }

            return json_encode([
                'success' => true,
                'data' => $users
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to fetch user'
            ]);
        }
    }

    // Get users by last name
    public function getUserByLastName($lastName)
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            // Convert to lowercase and validate
            $lastName = strtolower(trim($lastName));
            if (empty($lastName)) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            $sql = "SELECT id, first_name, last_name, user_role, email, phone, dob, profile FROM users WHERE LOWER(last_name) = ? ORDER BY id";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$lastName]);

            $users = $stmt->fetchAll();

            if (empty($users)) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            // Convert data types
            foreach ($users as &$user) {
                $user['id'] = (int) $user['id'];
                $user['user_role'] = (int) $user['user_role'];
            }

            return json_encode([
                'success' => true,
                'data' => $users
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to fetch user'
            ]);
        }
    }

    // Get user by email
    public function getUserByEmail($email)
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            // Validate email format
            $email = trim($email);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            $sql = "SELECT id, first_name, last_name, user_role, email, phone, dob, profile FROM users WHERE email = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$email]);

            $user = $stmt->fetch();

            if (!$user) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            // Convert data types
            $user['id'] = (int) $user['id'];
            $user['user_role'] = (int) $user['user_role'];

            return json_encode([
                'success' => true,
                'data' => $user
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to fetch user'
            ]);
        }
    }

    // Get user by phone
    public function getUserByPhone($phone)
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            // Validate phone format (10 digits)
            $phone = trim($phone);
            if (!preg_match('/^[0-9]{10}$/', $phone)) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            $sql = "SELECT id, first_name, last_name, user_role, email, phone, dob, profile FROM users WHERE phone = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$phone]);

            $user = $stmt->fetch();

            if (!$user) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            // Convert data types
            $user['id'] = (int) $user['id'];
            $user['user_role'] = (int) $user['user_role'];

            return json_encode([
                'success' => true,
                'data' => $user
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to fetch user'
            ]);
        }
    }

    // Create a new user
    public function createUser($data)
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            // Check required fields
            $requiredFields = ['first_name', 'last_name', 'user_role', 'email', 'phone', 'dob', 'password'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    http_response_code(400);
                    return json_encode([
                        'success' => false,
                        'error' => "Missing {$field} field"
                    ]);
                }
            }

            // Validate and sanitize data
            $firstName = trim($data['first_name']);
            $lastName = trim($data['last_name']);
            $userRole = $data['user_role'];
            $email = trim($data['email']);
            $phone = trim($data['phone']);
            $dob = trim($data['dob']);
            $password = $data['password'];
            $profile = isset($data['profile']) ? trim($data['profile']) : null;

            // Validate first_name and last_name not empty
            if (empty($firstName) || empty($lastName)) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'First name and last name cannot be empty'
                ]);
            }

            // Validate user_role is positive integer and exists
            if (!is_numeric($userRole) || $userRole <= 0) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Invalid user_role format'
                ]);
            }

            // Check if role exists
            $roleCheckSql = "SELECT COUNT(*) FROM roles WHERE id = ?";
            $roleCheckStmt = $connection->prepare($roleCheckSql);
            $roleCheckStmt->execute([$userRole]);

            if ($roleCheckStmt->fetchColumn() == 0) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Role not found'
                ]);
            }

            // Validate email format
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Invalid email format'
                ]);
            }

            // Check for duplicate email
            $emailCheckSql = "SELECT COUNT(*) FROM users WHERE email = ?";
            $emailCheckStmt = $connection->prepare($emailCheckSql);
            $emailCheckStmt->execute([$email]);

            if ($emailCheckStmt->fetchColumn() > 0) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Email already exists'
                ]);
            }

            // Validate phone format (10 digits)
            if (!preg_match('/^[0-9]{10}$/', $phone)) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Phone must be exactly 10 digits'
                ]);
            }

            // Validate date format
            $dobDateTime = DateTime::createFromFormat('Y-m-d', $dob);
            if (!$dobDateTime || $dobDateTime->format('Y-m-d') !== $dob) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Invalid date format. Use YYYY-MM-DD'
                ]);
            }

            // Validate profile extension if provided
            if ($profile !== null && !empty($profile)) {
                if (!preg_match('/\.(jpg|png|svg)$/i', $profile)) {
                    http_response_code(400);
                    return json_encode([
                        'success' => false,
                        'error' => 'Profile must be a .jpg, .png, or .svg file'
                    ]);
                }
            }

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Create the user
            $sql = "INSERT INTO users (first_name, last_name, user_role, email, phone, dob, profile, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$firstName, $lastName, $userRole, $email, $phone, $dob, $profile, $hashedPassword]);

            // Get the newly created user
            $newUserId = $connection->lastInsertId();
            $newUser = [
                'id' => (int) $newUserId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'user_role' => (int) $userRole,
                'email' => $email,
                'phone' => $phone,
                'dob' => $dob,
                'profile' => $profile
            ];

            http_response_code(201);
            return json_encode([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $newUser
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to create user'
            ]);
        }
    }

    // Update a user by ID
    public function updateUserById($id, $data)
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
                    'error' => 'User not found'
                ]);
            }

            // Check if user exists
            $checkSql = "SELECT * FROM users WHERE id = ?";
            $checkStmt = $connection->prepare($checkSql);
            $checkStmt->execute([$id]);
            $existingUser = $checkStmt->fetch();

            if (!$existingUser) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            $updateFields = [];
            $updateValues = [];

            // Handle first_name update
            if (isset($data['first_name'])) {
                $firstName = trim($data['first_name']);
                if (empty($firstName)) {
                    http_response_code(400);
                    return json_encode([
                        'success' => false,
                        'error' => 'First name cannot be empty'
                    ]);
                }
                $updateFields[] = 'first_name = ?';
                $updateValues[] = $firstName;
            }

            // Handle last_name update
            if (isset($data['last_name'])) {
                $lastName = trim($data['last_name']);
                if (empty($lastName)) {
                    http_response_code(400);
                    return json_encode([
                        'success' => false,
                        'error' => 'Last name cannot be empty'
                    ]);
                }
                $updateFields[] = 'last_name = ?';
                $updateValues[] = $lastName;
            }

            // Handle email update
            if (isset($data['email'])) {
                $email = trim($data['email']);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    http_response_code(400);
                    return json_encode([
                        'success' => false,
                        'error' => 'Invalid email format'
                    ]);
                }

                // Check for duplicate email (excluding current user)
                $emailCheckSql = "SELECT COUNT(*) FROM users WHERE email = ? AND id != ?";
                $emailCheckStmt = $connection->prepare($emailCheckSql);
                $emailCheckStmt->execute([$email, $id]);

                if ($emailCheckStmt->fetchColumn() > 0) {
                    http_response_code(400);
                    return json_encode([
                        'success' => false,
                        'error' => 'Email already exists'
                    ]);
                }

                $updateFields[] = 'email = ?';
                $updateValues[] = $email;
            }

            // Handle phone update
            if (isset($data['phone'])) {
                $phone = trim($data['phone']);
                if (!preg_match('/^[0-9]{10}$/', $phone)) {
                    http_response_code(400);
                    return json_encode([
                        'success' => false,
                        'error' => 'Phone must be exactly 10 digits'
                    ]);
                }
                $updateFields[] = 'phone = ?';
                $updateValues[] = $phone;
            }

            // Handle password update
            if (isset($data['password'])) {
                $password = $data['password'];
                if (empty($password)) {
                    http_response_code(400);
                    return json_encode([
                        'success' => false,
                        'error' => 'Password cannot be empty'
                    ]);
                }
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updateFields[] = 'password = ?';
                $updateValues[] = $hashedPassword;
            }

            // Check if any fields to update
            if (empty($updateFields)) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'No valid fields to update'
                ]);
            }

            // Update the user
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $updateValues[] = $id;
            $stmt = $connection->prepare($sql);
            $stmt->execute($updateValues);

            // Get updated user data
            $getUserSql = "SELECT id, first_name, last_name, user_role, email, phone, dob, profile FROM users WHERE id = ?";
            $getUserStmt = $connection->prepare($getUserSql);
            $getUserStmt->execute([$id]);
            $updatedUser = $getUserStmt->fetch();

            // Convert data types
            $updatedUser['id'] = (int) $updatedUser['id'];
            $updatedUser['user_role'] = (int) $updatedUser['user_role'];

            http_response_code(200);
            return json_encode([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $updatedUser
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to update user'
            ]);
        }
    }

    // Update a user by email
    public function updateUserByEmail($email, $data)
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            // Validate email format
            $email = trim($email);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            // Check if user exists
            $checkSql = "SELECT * FROM users WHERE email = ?";
            $checkStmt = $connection->prepare($checkSql);
            $checkStmt->execute([$email]);
            $existingUser = $checkStmt->fetch();

            if (!$existingUser) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            $updateFields = [];
            $updateValues = [];

            // Handle first_name update
            if (isset($data['first_name'])) {
                $firstName = trim($data['first_name']);
                if (empty($firstName)) {
                    http_response_code(400);
                    return json_encode([
                        'success' => false,
                        'error' => 'First name cannot be empty'
                    ]);
                }
                $updateFields[] = 'first_name = ?';
                $updateValues[] = $firstName;
            }

            // Handle last_name update
            if (isset($data['last_name'])) {
                $lastName = trim($data['last_name']);
                if (empty($lastName)) {
                    http_response_code(400);
                    return json_encode([
                        'success' => false,
                        'error' => 'Last name cannot be empty'
                    ]);
                }
                $updateFields[] = 'last_name = ?';
                $updateValues[] = $lastName;
            }

            // Handle email update
            if (isset($data['email'])) {
                $newEmail = trim($data['email']);
                if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                    http_response_code(400);
                    return json_encode([
                        'success' => false,
                        'error' => 'Invalid email format'
                    ]);
                }

                // Check for duplicate email (excluding current user)
                $emailCheckSql = "SELECT COUNT(*) FROM users WHERE email = ? AND id != ?";
                $emailCheckStmt = $connection->prepare($emailCheckSql);
                $emailCheckStmt->execute([$newEmail, $existingUser['id']]);

                if ($emailCheckStmt->fetchColumn() > 0) {
                    http_response_code(400);
                    return json_encode([
                        'success' => false,
                        'error' => 'Email already exists'
                    ]);
                }

                $updateFields[] = 'email = ?';
                $updateValues[] = $newEmail;
            }

            // Handle phone update
            if (isset($data['phone'])) {
                $phone = trim($data['phone']);
                if (!preg_match('/^[0-9]{10}$/', $phone)) {
                    http_response_code(400);
                    return json_encode([
                        'success' => false,
                        'error' => 'Phone must be exactly 10 digits'
                    ]);
                }
                $updateFields[] = 'phone = ?';
                $updateValues[] = $phone;
            }

            // Handle password update
            if (isset($data['password'])) {
                $password = $data['password'];
                if (empty($password)) {
                    http_response_code(400);
                    return json_encode([
                        'success' => false,
                        'error' => 'Password cannot be empty'
                    ]);
                }
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $updateFields[] = 'password = ?';
                $updateValues[] = $hashedPassword;
            }

            // Check if any fields to update
            if (empty($updateFields)) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'No valid fields to update'
                ]);
            }

            // Update the user
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $updateValues[] = $existingUser['id'];
            $stmt = $connection->prepare($sql);
            $stmt->execute($updateValues);

            // Get updated user data
            $getUserSql = "SELECT id, first_name, last_name, user_role, email, phone, dob, profile FROM users WHERE id = ?";
            $getUserStmt = $connection->prepare($getUserSql);
            $getUserStmt->execute([$existingUser['id']]);
            $updatedUser = $getUserStmt->fetch();

            // Convert data types
            $updatedUser['id'] = (int) $updatedUser['id'];
            $updatedUser['user_role'] = (int) $updatedUser['user_role'];

            http_response_code(200);
            return json_encode([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $updatedUser
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to update user'
            ]);
        }
    }

    // Delete a user by ID
    public function deleteUserById($id)
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
                    'error' => 'User not found'
                ]);
            }

            // Check if user exists
            $checkSql = "SELECT id, first_name, last_name, user_role, email, phone, dob, profile FROM users WHERE id = ?";
            $checkStmt = $connection->prepare($checkSql);
            $checkStmt->execute([$id]);
            $existingUser = $checkStmt->fetch();

            if (!$existingUser) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            // Delete the user
            $sql = "DELETE FROM users WHERE id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$id]);

            // Return deleted user info
            $deletedUser = [
                'id' => (int) $existingUser['id'],
                'first_name' => $existingUser['first_name'],
                'last_name' => $existingUser['last_name'],
                'user_role' => (int) $existingUser['user_role'],
                'email' => $existingUser['email'],
                'phone' => $existingUser['phone'],
                'dob' => $existingUser['dob'],
                'profile' => $existingUser['profile']
            ];

            http_response_code(200);
            return json_encode([
                'success' => true,
                'message' => 'User deleted successfully',
                'data' => $deletedUser
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to delete user'
            ]);
        }
    }

    // Delete a user by email
    public function deleteUserByEmail($email)
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            // Validate email format
            $email = trim($email);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            // Check if user exists
            $checkSql = "SELECT id, first_name, last_name, user_role, email, phone, dob, profile FROM users WHERE email = ?";
            $checkStmt = $connection->prepare($checkSql);
            $checkStmt->execute([$email]);
            $existingUser = $checkStmt->fetch();

            if (!$existingUser) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            // Delete the user
            $sql = "DELETE FROM users WHERE email = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$email]);

            // Return deleted user info
            $deletedUser = [
                'id' => (int) $existingUser['id'],
                'first_name' => $existingUser['first_name'],
                'last_name' => $existingUser['last_name'],
                'user_role' => (int) $existingUser['user_role'],
                'email' => $existingUser['email'],
                'phone' => $existingUser['phone'],
                'dob' => $existingUser['dob'],
                'profile' => $existingUser['profile']
            ];

            http_response_code(200);
            return json_encode([
                'success' => true,
                'message' => 'User deleted successfully',
                'data' => $deletedUser
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to delete user'
            ]);
        }
    }
}
?>