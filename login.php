<?php
// Start the session
session_start();

// Database connection details
include 'database.php';

// Establish a connection to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get username and password from the form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($username) || empty($password)) {
        die("Please fill in both username and password fields.");
    }

    // Fetch user from the database
    try {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Password is correct, set session variables
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Redirect to a welcome or dashboard page
                header("Location: welcome.php");
                exit;
            } else {
                // Invalid password
                die("Invalid username or password.");
            }
        } else {
            // User not found
            die("Invalid username or password.");
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>