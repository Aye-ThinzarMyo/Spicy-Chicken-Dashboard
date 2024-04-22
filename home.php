<?php
include 'components/connect.php';
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['food_category']) ? $_GET['food_category'] : '';
// Number of items per page
$itemsPerPage = 5;

// Base SQL query
$sql = "SELECT COUNT(*) AS total FROM menu WHERE 1";

// Array to store parameters for the prepared statement
$params = [];

// Add conditions for search query and category if they are provided
if (!empty($searchQuery)) {
    $sql .= " AND name LIKE :search";
    $params[':search'] = '%' . $searchQuery . '%';
}
// Add conditions for search query and category if they are provided
if (!empty($category)) {
    $sql .= " AND category_id = :category";
    $params[':category'] = $category ;
}


// Prepare the SQL statement
$stmt = $conn->prepare($sql);

// Bind parameters
foreach ($params as $param => &$value) {
    $stmt->bindParam($param, $value);
}

// Execute the statement
$stmt->execute();

// Fetch the total number of rows
$totalRows = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

// Calculate the total number of pages based on search results
$totalPages = ceil($totalRows / $itemsPerPage);

// Get the current page number from the URL parameter, default to 1 if not set
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $itemsPerPage;

// Base SQL query for fetching menu items
$sql = "SELECT * FROM menu WHERE 1";

// Add conditions for search query and category if they are provided
if (!empty($searchQuery)) {
    $sql .= " AND name LIKE :search";
}
if (!empty($category)) {
    $sql .= " AND category_id = :category";
}

// Add limit and offset for pagination
$sql .= " $whereClause ORDER BY created_at DESC LIMIT :offset, :limit";

// Prepare the SQL statement
$stmt = $conn->prepare($sql);

// Bind parameters
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $itemsPerPage, PDO::PARAM_INT);
foreach ($params as $param => &$value) {
    $stmt->bindParam($param, $value);
}

// Execute the statement
$stmt->execute();

// Fetch the menu items
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                <li><a href="review.php">Review</a></li>
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
    
    <div class='py-24 px-[10%] space-y-6'>
    <div class='font-bold italic text-[48px]'>Menu</div>
    <form action='' method='GET'>
    <div class='text-red-600 text-[13px]'>Search each food name*</div>
    <div class='border rounded-[5px] w-[60%] h-[40px] p-2 mb-6'>
        <input type='text' id='searchInput' class='w-full outline-none' name='search' placeholder='Search Food Name...' value="<?php echo $searchQuery; ?>"/>
    </div>

    <div class='flex flex-row'>
      <div class='flex flex-col w-[25%]'>
        
      <select name="food_category" id="food_category" class="w-full p-2 border rounded">
        <option value="" disabled selected>Select Food Category</option>
        <option value="1" <?php if ($category == 1) echo 'selected'; ?>>Chicken Meals</option>
        <option value="2" <?php if ($category == 2) echo 'selected';?>>Combo Set</option>
        <option value="3" <?php if ($category == 3) echo 'selected';?>>Beverages</option>
        <option value="4" <?php if ($category == 4) echo 'selected';?>>Limited Time Offer</option>
        <option value="5" <?php if ($category == 5)  echo 'selected';?>>Snacks</option>
     </select><br>

      </div>
      <div class='flex flex-col h-[40px] w-[100px] ml-6'>
        <button type='submit' class='border bg-green-300 p-2 rounded-[5px] font-normal object-center hover:bg-green-400'>Search</button>
      </div>
      <div class='flex flex-col h-[40px] w-[100px] ml-6'>
        <button class='border bg-green-300 p-2 rounded-[5px] font-normal text-center justify-center object-center hover:bg-green-400' onclick="cancelSearch()">Cancel</button>
      </div>
      <div class='flex flex-col h-[40px] w-[100px] ml-[50%]'>
      <a href='/menu_create.php' class='border bg-violet-500 p-2 rounded-[5px] font-normal text-center items-center object-center hover:bg-violet-300 justify-end'>Add</a>
      </div>
    </div>
    <script>
    function cancelSearch() {
        // Clear search input
        document.getElementById('searchInput').value = '';
        
        // Reset dropdown list to default
        document.getElementById('food_category').selectedIndex = 0;
    }
</script>
    </form>
   
    
<!-- menu table -->
<?php

if (!empty($searchQuery) || !empty($category)) {
    echo '<div class="text-red-600 font-normal text-[16px]">Search results: ' . $totalRows . '</div>';
}

if (count($menuItems) > 0) {


    echo '<table class="min-w-full border bg-white">';
            echo '<thead>';
                echo '<tr>';
                    echo '<th class="border p-2 w-[20%]">Food Name</th>';
                    echo '<th class="border p-2 w-[20%]">Category</th>';
                    echo '<th class="border p-2 w-[20%]">Image</th>';
                    echo '<th class="border p-2 w-[10%]">Publish</th>';
                    echo '<th class="border p-2 w-[30%]">Action</th>';
                echo '</tr>';
            echo '</thead>';
           echo '<tbody>';
                
                
                foreach ($menuItems as $item) {
                    echo '<tr>';
                    echo '<td class="border p-2 w-[20%] text-center">' . $item['name'] . '</td>';
                    
                  
                    $stmt = $conn->prepare("SELECT name FROM categories WHERE id = :category_id");
                    $stmt->bindParam(':category_id', $item['category_id']);
                    $stmt->execute();
                    $category = $stmt->fetch(PDO::FETCH_ASSOC);
                
                    echo '<td class="border p-2 w-[20%] text-center">' . ($category ? $category['name'] : '') . '</td>';
                    
                
                    
                    echo '<td class="border p-2 w-[20%] text-center"><img src="img/' . $item['image'] . '" alt="Food Image" class="w-20 h-16 object-cover border text-center mx-auto"></td>';

                    
                    $isActive = ($item['is_active'] == 1) ? 'true' : 'false';
                    echo '<td class="border p-2 w-[10%] text-center">' . $isActive . '</td>';
                    
                    echo '<td class="border p-2 w-[30%] text-center space-x-6">
                    <a href="/menu_edit.php?id=' . $item['id'] . '" class="bg-blue-500 hover:bg-blue-400 text-white px-4 py-2 rounded">Edit</a>
                    <a href="/menu_delete.php?id=' . $item['id'] . '" class="bg-red-500 hover:bg-red-400 text-white px-4 py-2 rounded">Delete</a>
                  </td>';
                     

                    echo '</tr>';
                }
                
            echo '</tbody>';
        echo '</table>';

        // Pagination

        echo '<div class="flex justify-end mt-4">';
        echo '<nav class="flex" aria-label="Pagination">';
        echo '<ul class="flex">';
        
        $category = isset($_GET['food_category']) ? $_GET['food_category'] : '';
        
        // Previous page link
        if ($page > 1) {
            // Generate URL for the previous page
            $prevUrl = "http://localhost:8000/home.php?search=" . urlencode($searchQuery) . "&food_category=" . (is_array($category) ? implode(',', $category) : $category) . "&page=" . ($page - 1);
            echo '<li><a href="' . $prevUrl . '" class="border bg-gray-200 text-gray-600 hover:bg-gray-300 px-3 py-1 mr-2 rounded">Prev</a></li>';
        } else {
            echo '<li><span class="border bg-gray-200 text-gray-600 px-3 py-1 mr-2 rounded cursor-not-allowed">Prev</span></li>';
        }
        
        // Page links
        for ($i = 1; $i <= $totalPages; $i++) {
            // Generate URL for each page
            $pageUrl = "http://localhost:8000/home.php?search=" . urlencode($searchQuery) . "&food_category=" . (is_array($category) ? implode(',', $category) : $category) . "&page=" . $i;
        
            // Check if the current page is equal to $i, and apply a different CSS class if it is
            $class = ($i == $page) ? 'border border-red-500 bg-red-200 text-red-800 font-semibold' : 'border bg-gray-200 text-gray-600 hover:bg-gray-300';
        
            // Display page numbers based on current page
            if ($i == 1 || $i == $totalPages || abs($page - $i) <= 1 || ($page == 1 && $i == 2) || ($page == $totalPages && $i == $totalPages - 1)) {
                echo '<li><a href="' . $pageUrl . '" class="border px-3 py-1 mr-2 rounded ' . $class . '">' . $i . '</a></li>';
            } elseif (abs($page - $i) == 2) {
                echo '<li><span class="border bg-gray-200 text-gray-600 px-3 py-1 mr-2 rounded">...</span></li>';
            }
        }
        
        // Next page link
        if ($page < $totalPages) {
            // Generate URL for the next page
            $nextUrl = "http://localhost:8000/home.php?search=" . urlencode($searchQuery) . "&food_category=" . (is_array($category) ? implode(',', $category) : $category) . "&page=" . ($page + 1);
            echo '<li><a href="' . $nextUrl . '" class="border bg-gray-200 text-gray-600 hover:bg-gray-300 px-3 py-1 mr-2 rounded">Next</a></li>';
        } else {
            echo '<li><span class="border bg-gray-200 text-gray-600 px-3 py-1 mr-2 rounded cursor-not-allowed">Next</span></li>';
        }
        
        echo '</ul>';
        echo '</nav>';
        echo '</div>';
//         echo '<div class="flex justify-end mt-4">';
//         echo '<nav class="flex" aria-label="Pagination">';
//         echo '<ul class="flex">';

//         $category = isset($_GET['food_category']) ? $_GET['food_category'] : '';
        
//         // Previous page link
//         if ($page > 1) {
//             // Generate URL for the previous page
//             $prevUrl = "http://localhost:8000/home.php?search=" . urlencode($searchQuery) . "&food_category=" . (is_array($category) ? implode(',', $category) : $category) . "&page=" . ($page - 1);
//             echo '<li><a href="' . $prevUrl . '" class="border bg-gray-200 text-gray-600 hover:bg-gray-300 px-3 py-1 mr-2 rounded">Prev</a></li>';
//         } else {
//             echo '<li><span class="border bg-gray-200 text-gray-600 px-3 py-1 mr-2 rounded cursor-not-allowed">Prev</span></li>';
//         }
        
//       // Page links
// for ($i = 1; $i <= $totalPages; $i++) {
//     // Generate URL for each page
//     $pageUrl = "http://localhost:8000/home.php?search=" . urlencode($searchQuery) . "&food_category=" . (is_array($category) ? implode(',', $category) : $category) . "&page=" . $i;
    
//     // Check if the current page is equal to $i, and apply a different CSS class if it is
//     $class = ($i == $page) ? 'border border-red-500 bg-red-200 text-red-800 font-semibold' : 'border bg-gray-200 text-gray-600 hover:bg-gray-300';
    
//     echo '<li><a href="' . $pageUrl . '" class="border px-3 py-1 mr-2 rounded ' . $class . '">' . $i . '</a></li>';
// }

        
//         // Next page link
//         if ($page < $totalPages) {
//             // Generate URL for the next page
//             $nextUrl = "http://localhost:8000/home.php?search=" . urlencode($searchQuery) . "&food_category=" . (is_array($category) ? implode(',', $category) : $category) . "&page=" . ($page + 1);
//             echo '<li><a href="' . $nextUrl . '" class="border bg-gray-200 text-gray-600 hover:bg-gray-300 px-3 py-1 mr-2 rounded">Next</a></li>';
//         } else {
//             echo '<li><span class="border bg-gray-200 text-gray-600 px-3 py-1 mr-2 rounded cursor-not-allowed">Next</span></li>';
//         }
        
//         echo '</ul>';
//         echo '</nav>';
//         echo '</div>';
        
        


    // echo '<div class="flex justify-end mt-4">';
    // echo '<nav class="flex" aria-label="Pagination">';
    // echo '<ul class="flex">';
    
    // // Previous page link
    // if ($page > 1) {
    //     echo '<li><a href="http://localhost:8000/home.php?search=&food_category=5&page=1" class="border bg-gray-200 text-gray-600 hover:bg-gray-300 px-3 py-1 mr-2 rounded">Prev</a></li>';
    // } else {
    //     echo '<li><span class="border bg-gray-200 text-gray-600 px-3 py-1 mr-2 rounded cursor-not-allowed">Prev</span></li>';
    // }
    
    // // Page links
    // for ($i = 1; $i <= $totalPages; $i++) {
    //     echo '<li><a href="?page=' . $i . '" class="border bg-gray-200 text-gray-600 hover:bg-gray-300 px-3 py-1 mr-2 rounded">' . $i . '</a></li>';
    // }
    
    // // Next page link
    // if ($page < $totalPages) {
    //     echo '<li><a href="http://localhost:8000/home.php?search=&food_category=5&page=2" class="border bg-gray-200 text-gray-600 hover:bg-gray-300 px-3 py-1 mr-2 rounded">Next</a></li>';
    // } else {
    //     echo '<li><span class="border bg-gray-200 text-gray-600 px-3 py-1 mr-2 rounded cursor-not-allowed">Next</span></li>';
    // }
    
    // echo '</ul>';
    // echo '</nav>';
    // echo '</div>';
}else {
    echo '<div>No menu items Found.</div>';
}
?>
</body>
</html>