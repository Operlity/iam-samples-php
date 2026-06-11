<?php
/**
 * Database Helper for Contact Management
 */

function get_db_connection() {
    static $pdo = null;

    if ($pdo === null) {
        $db_dir = __DIR__ . '/database';
        
        // Ensure database directory exists
        if (!file_exists($db_dir)) {
            mkdir($db_dir, 0777, true);
        }

        $db_file = $db_dir . '/contacts.sqlite';

        try {
            // Establish PDO connection
            $pdo = new PDO("sqlite:" . $db_file);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Create contacts table if it doesn't exist
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS contacts (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_sub TEXT NOT NULL,
                    name TEXT NOT NULL,
                    email TEXT,
                    phone TEXT,
                    company TEXT,
                    job_title TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ");

            // Create index for performance on user_sub queries
            $pdo->exec("
                CREATE INDEX IF NOT EXISTS idx_contacts_user_sub ON contacts (user_sub)
            ");

        } catch (PDOException $e) {
            // Log error or display message
            http_response_code(500);
            echo json_encode([
                'error' => 'Database connection failed: ' . $e->getMessage()
            ]);
            exit();
        }
    }

    return $pdo;
}
