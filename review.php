<?php
// Step 1: PDO MySQL connection
$dsn = 'mysql:host=localhost;dbname=spicy_chicken';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}

// Step 2: Retrieve data from the submissions table
$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$searchDate = isset($_GET['searchDate']) ? $_GET['searchDate'] : '';

// Add condition for searching by date if a date is selected
$whereClause = "";
$params = []; // Array to store parameters for prepared statement
if (!empty($searchDate)) {
    $whereClause = "WHERE DATE(created_at) = :searchDate";
    $params[':searchDate'] = $searchDate; // Add search date to parameters array
}

$sql = "SELECT * FROM submissions $whereClause ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
foreach ($params as $param => $value) {
    $stmt->bindValue($param, $value);
}
$stmt->execute();
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Step 3: Pagination
$sql = "SELECT COUNT(*) AS count FROM submissions $whereClause";
$stmt = $pdo->prepare($sql);
foreach ($params as $param => $value) {
    $stmt->bindValue($param, $value);
}
$stmt->execute();
$total_results = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
$total_pages = ceil($total_results / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Spicy Chicken Myanmar</title>
    <style>
          .nav-bar {
  width: 100%;
  padding: 20px 90px 20px 90px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: fixed;
  z-index: 10;
  top: 0;
  background-color:black;
  height: 80px; 
}

.logo-container img {
  width: 70px;
  height: 70px;
  border-radius: 50%;
  /* margin-top:40px; */
  margin-left:-70px;
}

.nav-bar-item ul li a::after {
  content: "";
  width: 0;
  height: 2px;
  background-color: red;
  position: absolute;
  left: 0;
  bottom: -4px;
  transition: 0.3s;
}

.nav-bar-item ul li a:hover::after,
.nav-bar-item ul li .active::after {
  width: 50%;
}

.nav-bar-item ul li a:hover,
.nav-bar-item ul li .active {
  color: red;
}

.nav-bar-item ul li a {
  text-decoration: none;
  color: white;
  padding-right: 50px;  
  position: relative;
  transition: 0.3s;
  font-size: 18px;
}

.nav-bar-item ul {
  display: flex;
  margin-bottom: 0;
}
    </style>
</head>
<body>
<header class="nav-bar">
        <a href="home.php">
        <div class="logo-container flex flex-row items-center pl-3 gap-x-2">
            <img src="img/download.png" alt="Logo" class="h-16 w-auto rounded-full">
            <span class='text-red-600 font-semibold text-[24px] italic'>Spicy Chicken Myanmar Dashboard</span>
        </div>
        </a>
        <div class="nav-bar-item">
            <ul>
                <li><a href="menu_create.php">Menu</a></li>
                <li><a href="contactus_edit.php">Contact</a></li>
                <li><a class="active" href="review.php">Review</a></li>
            </ul>
        </div>
        <button class='border text-[13px] rounded-full h-[38px] border border-black bg-red-600 hover:bg-red-500 text-black font-semibold p-2 items-center text-center object-center justify-center' onclick='confirmLogout()'>            
                 LogOut            
        </button>
    </header>

<script>
    function confirmLogout() {
        if (confirm("Are you sure you want to logout?")) {
            window.location.href = "logout.php";
        }
    }
</script>
<div class='py-24 px-[6%]'>
    <form method="GET" action="">

    <div class='font-bold italic text-[48px] mb-6'>Submissions</div>
    <a href="javascript:history.back()" class="bg-blue-500 hover:bg-blue-400 text-white px-4 py-2 rounded mb-6 w-[100px] h-[40px] text-[16px]">Back</a>
    <div class='flex flex-row my-6'>
        <div class='flex flex-col border rounded-[5px] w-[20%] h-[40px] p-2'>
            <input type='date' id='searchDate' class='w-full outline-none' name='searchDate' value="<?php echo $searchDate; ?>"/>
        </div>

        <div class='flex flex-col h-[40px] w-[100px] ml-6'>
            <button type='submit' class='border bg-green-300 p-2 rounded-[5px] font-normal object-center hover:bg-green-400'>Search</button>
        </div>

        <div class='flex flex-col h-[40px] w-[100px] ml-6'>
            <button type='button' onclick='clearSearch()' class='border bg-green-300 p-2 rounded-[5px] font-normal object-center hover:bg-green-400 ml-2'>Cancel</button>
        </div>
    </div>
    <?php if (!empty($searchDate)): ?> 
        <div class="text-red-600 font-normal text-[16px]">Total search results: <?php echo $total_results; ?></div>
    <?php endif; ?>

</form>

<script>
    function clearSearch() {
        document.getElementById('searchDate').value = '';
        window.location.href = "review.php";
    }
</script>

<div class=''>
<?php if ($total_results > 0): ?>
    <table class="min-w-full border">
        <thead>
            <tr>
                <th class="border p-2">Name</th>
                <th class="border p-2">Email</th>
                <th class="border p-2">Phone</th>
                <th class="border p-2">Subject</th>
                <th class="border p-2">Message</th>
                <th class="border p-2">Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($submissions as $submission): ?>
                <tr>
                    <td class="border p-2 text-center w-[15%]"><?php echo $submission['customer_name']; ?></td>
                    <td class="border p-2 text-center w-[15%]"><?php echo $submission['email']; ?></td>
                    <td class="border p-2 text-center"><?php echo $submission['phone']; ?></td>
                    <td class="border p-2 text-center w-[15%] justify-center"><?php echo $submission['subject']; ?></td>
                    <td class="border p-2 text-center text-justify"><?php echo $submission['message']; ?></td>
                    <td class="border p-2 text-center w-[10%]">
                        <?php 
                            $createdAt = new DateTime($submission['created_at']);
                            echo $createdAt->format('Y-m-d'); 
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


  <!-- Pagination -->
  <?php
  // Pagination
echo '<div class="flex justify-end mt-4">';
echo '<nav class="flex" aria-label="Pagination">';
echo '<ul class="flex">';

// Previous page link
if ($page > 1) {
    // Generate URL for the previous page
    $prevUrl = "http://localhost:8000/review.php?searchDate=" . urlencode($searchDate) . "&page=" . ($page - 1);
    echo '<li><a href="' . $prevUrl . '" class="border bg-gray-200 text-gray-600 hover:bg-gray-300 px-3 py-1 mr-2 rounded">Prev</a></li>';
} else {
    echo '<li><span class="border bg-gray-200 text-gray-600 px-3 py-1 mr-2 rounded cursor-not-allowed">Prev</span></li>';
}

// Page links
for ($i = 1; $i <= $total_pages; $i++) {
    // Generate URL for each page
    $pageUrl = "http://localhost:8000/review.php?searchDate=" . urlencode($searchDate) . "&page=" . $i;

    // Check if the current page is equal to $i, and apply a different CSS class if it is
    $class = ($i == $page) ? 'border border-red-500 bg-red-200 text-red-800 font-semibold' : 'border bg-gray-200 text-gray-600 hover:bg-gray-300';

    echo '<li><a href="' . $pageUrl . '" class="border px-3 py-1 mr-2 rounded ' . $class . '">' . $i . '</a></li>';
}

// Next page link
if ($page < $total_pages) {
    // Generate URL for the next page
    $nextUrl = "http://localhost:8000/review.php?searchDate=" . urlencode($searchDate) . "&page=" . ($page + 1);
    echo '<li><a href="' . $nextUrl . '" class="border bg-gray-200 text-gray-600 hover:bg-gray-300 px-3 py-1 mr-2 rounded">Next</a></li>';
} else {
    echo '<li><span class="border bg-gray-200 text-gray-600 px-3 py-1 mr-2 rounded cursor-not-allowed">Next</span></li>';
}

echo '</ul>';
echo '</nav>';
echo '</div>';

  ?>
<?php else: ?>
    <div class="text-red-600 font-normal text-[16px]">No search results found.</div>
<?php endif; ?>
</div>

</body>
</html>
