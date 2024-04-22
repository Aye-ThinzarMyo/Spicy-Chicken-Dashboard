<?php
// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Database connection details
    $db_host = 'localhost';
    $db_name = 'spicy_chicken';
    $db_user = 'root';
    $db_password = '';

    try {
        // Establish a PDO database connection
        $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_password);
        
        // Set PDO to throw exceptions on errors
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Prepare the SQL query to delete data from the 'menu' table
        $sql = 'DELETE FROM menu WHERE id = :id';
        
        // Prepare the statement
        $stmt = $conn->prepare($sql);
        
        // Bind the 'id' parameter
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        // Execute the statement
        $stmt->execute();
        
        // Redirect to the homepage after successful deletion
        header("Location: /home.php");
        exit;
    } catch(PDOException $e) {
        // Handle connection errors or query errors gracefully
        echo "Error: " . $e->getMessage();
    }
} else {
    // Handle case where 'id' parameter is not set in the URL
    echo "ID parameter is missing";
}
?>
