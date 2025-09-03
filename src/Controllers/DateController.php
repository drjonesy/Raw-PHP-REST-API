<?php
class DateController
{
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    // Get all dates from the database
    public function getAllDates()
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            $sql = "SELECT * FROM dates ORDER BY id";
            $stmt = $connection->prepare($sql);
            $stmt->execute();

            $dates = $stmt->fetchAll();

            // Convert datetime format for response
            foreach ($dates as &$date) {
                $date['dt_start'] = str_replace(' ', 'T', $date['dt_start']);
                $date['dt_end'] = str_replace(' ', 'T', $date['dt_end']);
                $date['id'] = (int) $date['id'];
                $date['user_id'] = (int) $date['user_id'];
            }

            return json_encode([
                'success' => true,
                'data' => $dates
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to fetch dates'
            ]);
        }
    }

    // Get dates by user ID
    public function getDateByUserId($userId)
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            // Validate user ID is a positive integer
            if (!is_numeric($userId) || $userId <= 0) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            // Check if user exists
            $userCheckSql = "SELECT COUNT(*) FROM users WHERE id = ?";
            $userCheckStmt = $connection->prepare($userCheckSql);
            $userCheckStmt->execute([$userId]);

            if ($userCheckStmt->fetchColumn() == 0) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            $sql = "SELECT * FROM dates WHERE user_id = ? ORDER BY dt_start";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$userId]);

            $dates = $stmt->fetchAll();

            // Convert datetime format for response
            foreach ($dates as &$date) {
                $date['dt_start'] = str_replace(' ', 'T', $date['dt_start']);
                $date['dt_end'] = str_replace(' ', 'T', $date['dt_end']);
                $date['id'] = (int) $date['id'];
                $date['user_id'] = (int) $date['user_id'];
            }

            return json_encode([
                'success' => true,
                'data' => $dates
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to fetch dates'
            ]);
        }
    }

    // Create a new date
    public function createDate($data)
    {
        try {
            $connection = $this->db->getConnection();

            if (!$connection) {
                http_response_code(500);
                return json_encode(['error' => 'Database connection failed']);
            }

            // Check required fields
            $requiredFields = ['user_id', 'dt_start', 'dt_end'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    http_response_code(400);
                    return json_encode([
                        'success' => false,
                        'error' => "Missing {$field} field"
                    ]);
                }
            }

            // Validate user_id
            if (!is_numeric($data['user_id']) || $data['user_id'] <= 0) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Invalid user_id format'
                ]);
            }

            // Check if user exists
            $userCheckSql = "SELECT COUNT(*) FROM users WHERE id = ?";
            $userCheckStmt = $connection->prepare($userCheckSql);
            $userCheckStmt->execute([$data['user_id']]);

            if ($userCheckStmt->fetchColumn() == 0) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'User not found'
                ]);
            }

            // Convert datetime format (T to space for MySQL)
            $dtStart = str_replace('T', ' ', trim($data['dt_start']));
            $dtEnd = str_replace('T', ' ', trim($data['dt_end']));

            // Validate datetime formats
            $startTime = DateTime::createFromFormat('Y-m-d H:i:s', $dtStart);
            $endTime = DateTime::createFromFormat('Y-m-d H:i:s', $dtEnd);

            if (!$startTime || !$endTime) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Invalid datetime format. Use YYYY-MM-DDTHH:MM:SS'
                ]);
            }

            // Validate that dt_end is after dt_start
            if ($endTime <= $startTime) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'End time must be after start time'
                ]);
            }

            // Create the date record
            $sql = "INSERT INTO dates (user_id, dt_start, dt_end) VALUES (?, ?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$data['user_id'], $dtStart, $dtEnd]);

            // Get the newly created date
            $newDateId = $connection->lastInsertId();
            $newDate = [
                'id' => (int) $newDateId,
                'user_id' => (int) $data['user_id'],
                'dt_start' => str_replace(' ', 'T', $dtStart),
                'dt_end' => str_replace(' ', 'T', $dtEnd)
            ];

            http_response_code(201);
            return json_encode([
                'success' => true,
                'message' => 'Date created successfully',
                'data' => $newDate
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to create date'
            ]);
        }
    }

    // Update a date by ID
    public function updateDateById($id, $data)
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
                    'error' => 'Date not found'
                ]);
            }

            // Check if date exists
            $checkSql = "SELECT * FROM dates WHERE id = ?";
            $checkStmt = $connection->prepare($checkSql);
            $checkStmt->execute([$id]);
            $existingDate = $checkStmt->fetch();

            if (!$existingDate) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'Date not found'
                ]);
            }

            // Check required fields
            $requiredFields = ['dt_start', 'dt_end'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    http_response_code(400);
                    return json_encode([
                        'success' => false,
                        'error' => "Missing {$field} field"
                    ]);
                }
            }

            // Convert datetime format (T to space for MySQL)
            $dtStart = str_replace('T', ' ', trim($data['dt_start']));
            $dtEnd = str_replace('T', ' ', trim($data['dt_end']));

            // Validate datetime formats
            $startTime = DateTime::createFromFormat('Y-m-d H:i:s', $dtStart);
            $endTime = DateTime::createFromFormat('Y-m-d H:i:s', $dtEnd);

            if (!$startTime || !$endTime) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'Invalid datetime format. Use YYYY-MM-DDTHH:MM:SS'
                ]);
            }

            // Validate that dt_end is after dt_start
            if ($endTime <= $startTime) {
                http_response_code(400);
                return json_encode([
                    'success' => false,
                    'error' => 'End time must be after start time'
                ]);
            }

            // Update the date
            $sql = "UPDATE dates SET dt_start = ?, dt_end = ? WHERE id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$dtStart, $dtEnd, $id]);

            // Return updated date
            $updatedDate = [
                'id' => (int) $id,
                'user_id' => (int) $existingDate['user_id'],
                'dt_start' => str_replace(' ', 'T', $dtStart),
                'dt_end' => str_replace(' ', 'T', $dtEnd)
            ];

            http_response_code(200);
            return json_encode([
                'success' => true,
                'message' => 'Date updated successfully',
                'data' => $updatedDate
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to update date'
            ]);
        }
    }

    // Delete a date by ID
    public function deleteByDateId($id)
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
                    'error' => 'Date not found'
                ]);
            }

            // Check if date exists
            $checkSql = "SELECT * FROM dates WHERE id = ?";
            $checkStmt = $connection->prepare($checkSql);
            $checkStmt->execute([$id]);
            $existingDate = $checkStmt->fetch();

            if (!$existingDate) {
                http_response_code(404);
                return json_encode([
                    'success' => false,
                    'error' => 'Date not found'
                ]);
            }

            // Delete the date
            $sql = "DELETE FROM dates WHERE id = ?";
            $stmt = $connection->prepare($sql);
            $stmt->execute([$id]);

            // Return deleted date info
            $deletedDate = [
                'id' => (int) $existingDate['id'],
                'user_id' => (int) $existingDate['user_id'],
                'dt_start' => str_replace(' ', 'T', $existingDate['dt_start']),
                'dt_end' => str_replace(' ', 'T', $existingDate['dt_end'])
            ];

            http_response_code(200);
            return json_encode([
                'success' => true,
                'message' => 'Date deleted successfully',
                'data' => $deletedDate
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            return json_encode([
                'success' => false,
                'error' => 'Failed to delete date'
            ]);
        }
    }
}
?>