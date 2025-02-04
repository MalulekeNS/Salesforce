<?php
session_start();
ob_start();
include 'database.php';
if (!isset($_SESSION['user_id'])) { // Change 'user_id' to whatever session variable stores user login status
    header("Location: login.html");
    exit();
}else{
    $user_id = $_SESSION['user_id']; 
    
    $query = $db->prepare("SELECT * FROM users WHERE id = ?");
    $query->execute([$user_id]);
    $user = $query->fetch();
   $username =  $user['username'];
    

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    
    <title>TekSA - Business Management Platform</title>
<link rel="stylesheet" href="">
<style>
   body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    height: 100vh; /* Ensures it covers full height */
    background: url('backgroundd..jpg') no-repeat center center;
    background-size: cover;
    background-attachment: fixed;
    display: flex;
}

body {
    display: flex;
    flex-direction: row; /* Ensures sidebar and content are aligned properly */
    align-items: flex-start; /* Moves content to the top */
    justify-content: flex-start;
    height: 100vh;
}

.content {
    flex: 1;
    padding: 0px; /* Ensure no padding adds space */
    margin: 0;
}

.header {
    padding: 15px;
    margin: 0;
    position: relative;
}


*{
    box-sizing: inherit;
}
.sidebar {
    background-color: #2a4c63;
    color: white;
    width: 220px;
    min-height: 100vh;
    padding: 10px;
    display: flex;
    flex-direction: column;
    gap: 30px;
    transition: transform 0.3s ease;
}

.sidebar a {
    color: white;
    text-decoration: none;
    padding: 15px;
    margin: 5px 0;
    display: block;
    border-radius: 10px;
    background-color: #3498DB;
    text-align: center;
}

.sidebar a:hover {
    background-color: #000000;
}

.header {
    background-color: #243f52;
    color: white;
    padding: 20px;
    text-align: center;
    flex: 1;
    position: relative;
}

.header .top-right {
    position: absolute;
    
    top: 20px;
    right: 20px;
    display: flex;

    gap: 10px;
}

.header .top-right a, .header .top-right button {
    color: white;
    text-decoration: none;
    font-size: 14px;
    padding: 10px;
    background-color: #3498DB;
    border-radius: 5px;
    border: none;
    cursor: pointer;
}

.header .top-right a:hover, .header .top-right button:hover {
    background-color: #000000;
}

.navbar {
    display: flex;
    justify-content: space-around;
    background-color: #2a4c63;
    padding: 10px;
}

.card {
    background-color: white;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    max-width: 100%;
    word-wrap: break-word;
    margin-bottom: 20px;
}

.card h2 {
    margin-top: 0;
}

.card h3 {
    color: #2980B9;
}

table {
    width: 100%;
    border-collapse: collapse;
    display: block;
    white-space: nowrap;
    overflow-x: auto;
    margin-top: 10px;
}

th, td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}

th {
    background-color: #3498DB;
    color: white;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

.btn {
    background-color: #2980B9;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 10px;
}

.btn:hover {
    background-color: #1B4F72;
}

input[type=text], input[type=email], select {
    width: 100%;
    padding: 10px;
    margin: 5px 0 10px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
}

input[type=submit], .btn {
    background-color: #2980B9;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input[type=submit]:hover, .btn:hover {
    background-color: #1B4F72;
}

.chat-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #2980B9;
    color: white;
    border: none;
    padding: 15px;
    border-radius: 50px;
    cursor: pointer;
    font-size: 16px;
}

.chat-button:hover {
    background-color: #000000;
}

.chat-window {
    display: none;
    position: fixed;
    bottom: 80px;
    right: 20px;
    width: 300px;
    background-color: white;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    padding: 20px;
}

.chat-window header {
    background-color: #2980B9;
    color: white;
    padding: 10px;
    border-radius: 10px 10px 0 0;
    text-align: center;
}

.search-container {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-top: 10px;
}

.bottom-container {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
    margin-top: 20px;
}

.bottom-container .card {
    flex: 1;
    min-width: 300px;
}

.invite-btn {
    color: white;
    text-decoration: none;
    margin: 0 10px;
    padding: 10px;
    background-color: #3498DB;
    border-radius: 5px;
    border: none;
    cursor: pointer;
}

/* Dropdown Menu */
.dropdown-menu {
    display: none; /* Hidden by default */
    position: absolute;
    top: 50px;
    right: 10px;
    background-color: white;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 250px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 1000;
}

/* Show Dropdown */
.dropdown-menu.show {
    display: block; /* Display when toggled */
}

/* Dropdown Content */
.dropdown-content {
    padding: 10px;
    font-size: 14px;
    color: #444;
}

.dropdown-content h3 {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 10px;
}

.dropdown-content .invite-action {
    padding: 10px 20px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
}

/* Mobile-specific Adjustments */
@media (max-width: 768px) {
    .dropdown-menu {
        position: fixed; /* Ensure it's accessible */
        top: 60px; /* Adjust for smaller screens */
        right: 10px;
        width: 90%; /* Take more space on small screens */
    }

    .invite-btn {
        width: 100%; /* Larger button for mobile */
        text-align: center;
    }
}

.notification-bubble {
    position: absolute;
    top: -5px;
    right: -20px;
    background-color: #f24d4d;
    color: white;
    font-size: 14px;
    padding: 3px 8px;
    border-radius: 50%;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 30px;
    right: 20px;
    background-color: #00c4cc;
    border-radius: 8px;
    padding: 15px;
    width: 200px;
    color: white;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    z-index: 1;
}

.navbar {
    position: relative;
}

.dropdown-content h3 {
    margin: 0;
    font-size: 18px;
    font-weight: bold;
}

.dropdown-content p {
    margin: 5px 0 15px;
    font-size: 12px;
    color: #e0f7fa;
}

.invite-action {
    background-color: #ffffff;
    color: #00c4cc;
    border: none;
    padding: 8px 16px;
    font-size: 14px;
    border-radius: 15px;
    cursor: pointer;
}
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        z-index: 1000;
    }

    .sidebar.show {
        transform: translateX(0);
    }


    .header {
        padding: 20px; /* Keep the padding consistent */
    }

    .header .top-right {
        top: 0.3rem; /* Move the buttons farther down */
    }

    .header .top-right a, .header .top-right button {
        font-size: 12px; /* Reduce font size for buttons */
        padding: 8px 12px; /* Adjust padding for smaller screens */
    }


    .content {
        margin-top: 60px;
        padding: 10px;
        font-size: 14px;
    }
}

@media (min-width: 769px) {
    .toolbar-mobile {
        display: none;
    }

    .content {
        padding: 20px;
        font-size: 16px;
    }
}

.show {
    display: block;
}

.toolbar-mobile {
    display: none; /* Hidden by default */
}

.toolbar-mobile button {
    background-color: #2980B9;
    color: white;
    border: none;
    font-size: 18px;
    padding: 10px 20px;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 10px;
}

.toolbar-mobile button:hover {
    background-color: #000000;
}

@media (max-width: 768px) {
    .toolbar-mobile {
        display: block; /* Display toolbar on smaller screens */
        margin-bottom: 10px;
    }

    .header {
        padding: 10px; /* Adjust header padding for mobile */
        text-align: left;
    }

    .search-container {
        margin-top: 10px;
    }
}
.header {
    background-color:#2a4c63;
    color: white;
    padding: 20px;
    text-align: center; /* Centers only text */
    position: relative; /* Allows positioning of top-right menu */
}

   


body, html {
    margin: 0;
    padding: 0;
}
.content {
    margin-top: 0 !important;
    padding-top: 0 !important;
}
@media (max-width: 768px) {
    .toolbar-mobile {
        display: flex; /* Ensure it is visible */
        justify-content: flex-start; /* Align to the left */
        position: absolute;
        top: 10px; /* Adjust as needed */
        left: 10px; /* Move to the left */
        z-index: 1001; /* Ensure it stays on top */
    }

    .toolbar-mobile button {
        background-color: #2980B9;
        color: white;
        border: none;
        font-size: 18px;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        margin: 0;
    }

    .header {
        text-align: center;
        padding: 10px 10px 10px 50px; /* Ensure content is not blocked by the button */
        position: relative;
    }

    @media (max-width: 768px) {
    .header {
        text-align: center;
        padding: 60px 10px 20px; /* Increase top padding to push text down */
        position: relative;
    }

    .header h1 {
        margin-top: 30px; /* Move the welcome text further down */
        font-size: 22px; /* Adjust size for better readability on mobile */
    }

    .header p {
        margin-top: 10px;
        font-size: 16px;
    }

    .top-right {
        position: absolute;
        top: 10px; /* Keep login and invite at the top */
        right: 10px;
    }
}

}


    </style>
</head>
<body>
 

<!-- Desktop Sidebar -->
<div class="sidebar" id="desktopSidebar">
    <h2>Local Village Africa</h2>
    <a href="dashboard.html">Dashboard</a>
    <a href="crm.html">CRM</a>
    <a href="erp.html">ERP</a>
    <a href="inventory.html">Inventory</a>
    <a href="file.html">File Storage</a>
    <a href="https://localvillage.africa/shop/">Ecommerce</a>
    <a href="hr.html">HR</a>
    <a href="developer.html">Developer</a>
    <a href="file.html">Automation</a>
    <a href="logout.html">Logout</a>
</div>

<script>
    // Function to toggle the mobile sidebar visibility
function toggleToolbar() {
    document.getElementById('mobileSidebar').classList.toggle('show');
}

// Close the sidebar when clicking outside
document.addEventListener('click', function (event) {
    const sidebar = document.getElementById('mobileSidebar');
    const toolbarButton = document.getElementById('toolbarMobile');
    if (!sidebar.contains(event.target) && !toolbarButton.contains(event.target)) {
        sidebar.classList.remove('show'); // Close sidebar
    }
});

</script>

    <div class="content">
        <div class="header">
 <!-- Mobile Toolbar with Sidebar -->
<div class="toolbar-mobile" id="toolbarMobile">
    <button onclick="toggleToolbar()" onclick="toggleSidebar()">☰ Menu</button>
    
    <div class="sidebar" id="mobileSidebar">
        <h2>Local Village Africa</h2>
        <a href="dashboard.html">Dashboard</a>
        <a href="crm.html">CRM</a>
        <a href="erp.html">ERP</a>
        <a href="inventory.html">Inventory</a>
        <a href="file.html">File Storage</a>
        <a href="https://localvillage.africa/shop/" target="_blank" rel="noopener noreferrer">Ecommerce</a>
        <a href="hr.html">HR</a>
        <a href="file.html">Developer</a>
        <a href="file.html">Automation</a>
        <a href="logout.html">Logout</a>
    </div>
</div>

           
            <h1>Welcome to Local Village Africa</h1>
            <p>All in one Management Business platform.</p>
          
      
            <!-- Search Bar -->
            <div class="search-container">
                <input type="text" id="searchTerm" placeholder="Search..." onkeypress="search(event)">
                <button onclick="search()">Search</button>
            </div>
            <!-- Top right section -->
            <div class="top-right">
                <a href="login.html">Login</a>
                <span><?php echo htmlspecialchars($username); ?></span>
               <!-- Invite Button -->
<button class="invite-btn" onclick="toggleDropdown()">Invite</button>
<span class="notification-bubble" id="notificationBubble">0</span>

<!-- Dropdown Menu -->
<div class="dropdown-menu" id="inviteDropdown">
    <div class="dropdown-content">
        <h3>Invite Users</h3>
        <p>Why invite?</p>
        <a href="invite.html">
            <button class="invite-action">+ Invite</button>
        </a>
    </div>
</div>

<script>
    // Toggle dropdown visibility
    function toggleDropdown() {
        const dropdown = document.getElementById('inviteDropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    // Close dropdown when clicking outside (for mobile and desktop usability)
    document.addEventListener('click', function (event) {
        const dropdown = document.getElementById('inviteDropdown');
        const inviteButton = document.querySelector('.invite-btn');
        if (!dropdown.contains(event.target) && event.target !== inviteButton) {
            dropdown.style.display = 'none';
        }
    });
</script>

                
                
            </div>
        </div>

        <div class="card">
            <h2>Project Management</h2>
            <h3>Current Projects</h3>
            <table>
                <tr>
                    <th>Project</th>
                    <th>Status</th>
                    <th>Assignee</th>
                    <th>Due Date</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <td>Customer Portal</td>
                    <td>In Progress</td>
                    <td>John Mahlangu</td>
                    <td>2024-12-15</td>
                    <td><button class="btn">Update</button></td>
                </tr>
            </table>
            <button class="btn">Add New Project</button>
        </div>

        <div class="card">
            <h2>Task Tracking</h2>
            <h3>Project Tasks</h3>
            <table>
                <tr>
                    <th>Task</th>
                    <th>Assigned To</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <td>Update API endpoints</td>
                    <td>Jane Simelane</td>
                    <td>Pending</td>
                    <td>High</td>
                    <td><button class="btn">Edit</button></td>
                </tr>
            </table>
            <button class="btn">Add Task</button>
        </div>

        <div class="card">
            <h2>Team Collaboration</h2>
            <p>Access real-time chat, comments, and notifications.</p>
            <button class="btn">Start Chat</button>
            <button class="btn">View Notifications</button>
        </div>

        <!-- Customer Management and Lead Tracking cards -->
        <div class="bottom-container">
            <div class="card">
                <h2>Customer Management</h2>
                <form action="/submit-customer" method="POST">
                    <label for="name">Customer Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter customer name" required>
                    <label for="email">Customer Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter customer email" required>
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="new">New</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <input type="submit" value="Send Email">
                </form>
            </div>

            <div class="card">
                <h2>Lead Tracking</h2>
                <table>
                    <tr>
                        <th>Lead Name</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                    <tr>
                        <td>John Mahlangu</td>
                        <td>Mahlanguj@gmail.com</td>
                        <td>New</td>
                    </tr>
                    <tr>
                        <td>Jane Simelane</td>
                        <td>janeS@outlook.com</td>
                        <td>Contacted</td>
                    </tr>
                    <tr>
                        <td>Mark Davis</td>
                        <td>mark@eteksa.co.za</td>
                        <td>Closed</td>
                    </tr>
                </table>
                <button class="btn">Export Leads</button>
            </div>
        </div>
    </div>
    <button class="chat-button" onclick="toggleChat()">Hi, how can I help you?</button>

<!-- Chat Window -->
<div class="chat-window" id="chatWindow">
    <header>Chat Support</header>
    <div class="chat-content">
        <p>Hello! How can I assist you today?</p>
    </div>
    <div class="input-container">
        <input type="text" id="chatInput" placeholder="Type your message...">
        <button class="send-button" onclick="sendMessage()">Send</button>
    </div>
</div>

<script>
    function toggleChat() {
    const chatWindow = document.getElementById('chatWindow');
    chatWindow.style.display = chatWindow.style.display === 'none' || chatWindow.style.display === '' ? 'block' : 'none';
}

// Close the chat window when clicking outside of it
document.addEventListener('click', function(event) {
    const chatWindow = document.getElementById('chatWindow');
    const chatButton = document.querySelector('.chat-button');

    // Check if the click was outside of the chat button and the chat window
    if (!chatButton.contains(event.target) && !chatWindow.contains(event.target)) {
        chatWindow.style.display = 'none'; // Close the chat window
    }
});

// Prevent closing the chat window if clicking inside the chat window or button
document.getElementById('chatWindow').addEventListener('click', function(event) {
    event.stopPropagation(); // Prevent the event from bubbling up to the document
});

</script>
<style>
    body {
        font-family: Arial, sans-serif;
    }

    .chat-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: linear-gradient(135deg, #1B4F72, #2980B9); /* Gradient background */
    color: white;
    border: 2px solid  #1B4F72;; /* Thin border with matching color */
    padding: 12px 18px;
    border-radius: 25px; /* Fully rounded corners */
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    z-index: 1000;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow for depth */
    transition: all 0.3s ease; /* Smooth hover transition */
}

.chat-button:hover {
    background: linear-gradient(135deg, #2980B9, #1B4F72); /* Reverse gradient on hover */
    border-color: #1B4F72; /* Slightly darker border on hover */
    transform: translateY(-2px); /* Slight lift effect */
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3); /* More pronounced shadow on hover */
}

    .chat-window {
        display: none;
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 300px;
        max-width: 90%;
        border: 1px solid #ccc;
        border-radius: 5px;
        background: #f9f9f9;
        z-index: 1000;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .chat-window header {
        background-color:  #1B4F72;
        color: white;
        padding: 10px;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        font-size: 16px;
    }

    .chat-content {
        max-height: 300px;
        overflow-y: auto;
        padding: 10px;
    }

    .input-container {
        display: flex;
        padding: 10px;
        background: #f1f1f1;
        border-top: 1px solid #ccc;
    }

    .input-container input {
        flex: 1;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    .send-button {
        padding: 8px 15px;
        margin-left: 5px;
        background:  #1B4F72;
        color: white;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }

    .send-button:hover {
        background: #0056b3;
    }

    /* Responsive Design */
    @media (max-width: 600px) {
        .chat-window {
            width: 95%;
            bottom: 20px;
            right: 10px;
        }

        .chat-button {
            bottom: 15px;
            right: 10px;
        }
    }

</style>

</html>