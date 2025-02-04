<?php
session_start();
//header('Content-Type: application/json');
ob_start();
// Database connection
include 'database.php';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("Database connection successful! on Application");
} catch (PDOException $e) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]));
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
error_log("Action received: " . $action);
$response = ['status' => 'error', 'message' => 'Invalid request'];

// Authentication (Login, Register, Logout)
if ($action === 'login') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $query = $db->prepare("SELECT * FROM users WHERE username = ?");
    $query->execute([$username]);
    $user = $query->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
       // $response = ['status' => 'success', 'message' => 'Login successful'];
        header("Location: index.php");
    exit(); // Ensure script stops executing after redirection
    }
}
elseif ($action === 'logout') {
    session_destroy();
    //$response = ['status' => 'success', 'message' => 'Logged out'];
     header("Location: login.html");
    exit(); // Ensure script stops executing after redirection
}
elseif ($action === 'register') {
    // Validate required fields
    $required_fields = ['fullname', 'surname', 'username','password', 'confirm_password','email','company', 'department'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            die(json_encode(['status' => 'error', 'message' => "Missing required field: $field"]));
        }
    }

    $fullname = $_POST['fullname'];
    $surname = $_POST['surname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $company = $_POST['company'];
    $department = $_POST['department'];

    if ($password !== $confirm_password) {
        die(json_encode(['status' => 'error', 'message' => 'Passwords do not match']));
    }

    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Validate department selection
    if (!in_array($department, ['SMME', 'START-UP COMPANY'])) {
        die(json_encode(['status' => 'error', 'message' => 'Invalid department selection']));
    }

    // Check for duplicate username or email
    $checkQuery = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $checkQuery->execute([$username, $email]);
    if ($checkQuery->fetch()) {
        die(json_encode(['status' => 'error', 'message' => 'Username or email already exists']));
    }

    // Insert new user
    $query = $db->prepare("INSERT INTO users (fullname, surname, username, password_hash, email, company, department) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");

    if ($query->execute([$fullname, $surname, $username, $password_hash, $email, $company, $department])) {
        //$response = ['status' => 'success', 'message' => 'Registration successful'];
         header("Location: login.html");
    exit(); // Ensure script stops executing after redirection
    } else {
        error_log("Database Error: " . print_r($query->errorInfo(), true));
        $response = ['status' => 'error', 'message' => 'Registration failed'];
    }
}

// Data Submission and Retrieval
elseif ($action === 'submit_data') {
    $data = $_POST['data'];
    $query = $db->prepare("INSERT INTO records (data) VALUES (?)");
    $query->execute([$data]);
    $response = ['status' => 'success', 'message' => 'Data saved'];
}
elseif ($action === 'fetch_data') {
    $query = $db->query("SELECT * FROM records");
    $response = ['status' => 'success', 'data' => $query->fetchAll()];
}

// File Upload
elseif ($action === 'upload_file') {
    if (!empty($_FILES['file'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
        $response = ['status' => 'success', 'message' => 'File uploaded'];
    }
}

// Lead Tracking & CRM Operations
elseif ($action === 'add_lead') {
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $query = $db->prepare("INSERT INTO leads (name, contact) VALUES (?, ?)");
    $query->execute([$name, $contact]);
    $response = ['status' => 'success', 'message' => 'Lead added'];
}
elseif ($action === 'get_leads') {
    $query = $db->query("SELECT * FROM leads");
    $response = ['status' => 'success', 'data' => $query->fetchAll()];
}

// Inventory Management
elseif ($action === 'add_inventory') {
    $item = $_POST['item'];
    $quantity = $_POST['quantity'];
    $query = $db->prepare("INSERT INTO inventory (item, quantity) VALUES (?, ?)");
    $query->execute([$item, $quantity]);
    $response = ['status' => 'success', 'message' => 'Inventory updated'];
}
elseif ($action === 'get_inventory') {
    $query = $db->query("SELECT * FROM inventory");
    $response = ['status' => 'success', 'data' => $query->fetchAll()];
}

// Calendar Events
elseif ($action === 'add_event') {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $query = $db->prepare("INSERT INTO events (title, date) VALUES (?, ?)");
    $query->execute([$title, $date]);
    $response = ['status' => 'success', 'message' => 'Event created'];
}
elseif ($action === 'get_events') {
    $query = $db->query("SELECT * FROM events");
    $response = ['status' => 'success', 'data' => $query->fetchAll()];
}

// Sales Operations
elseif ($action === 'process_sale') {
    $product = $_POST['product'];
    $amount = $_POST['amount'];
    $query = $db->prepare("INSERT INTO sales (product, amount) VALUES (?, ?)");
    $query->execute([$product, $amount]);
    $response = ['status' => 'success', 'message' => 'Sale recorded'];
}
elseif ($action === 'get_sales') {
    $query = $db->query("SELECT * FROM sales");
    $response = ['status' => 'success', 'data' => $query->fetchAll()];
}

// RFID Scanning
elseif ($action === 'scan_rfid') {
    $rfid_tag = $_POST['rfid_tag'];
    $query = $db->prepare("SELECT * FROM inventory WHERE rfid_tag = ?");
    $query->execute([$rfid_tag]);
    $item = $query->fetch();
    if ($item) {
        $response = ['status' => 'success', 'data' => $item];
    } else {
        $response = ['status' => 'error', 'message' => 'RFID tag not found'];
    }
}

echo json_encode($response);
ob_end_flush();

?>
