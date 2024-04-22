<?php
session_start(); // Start the session

include 'components/connect.php';

// Check if the form is submitted
if(isset($_POST["submit"])){
    $name = $_POST["name"];
    $published = isset($_POST["published"]) ? 1 : 0;
    $category_id = $_POST["food_category"];

    // Set the session variables for the form inputs
    $_SESSION['name'] = $name;
    $_SESSION['published'] = $published;
    $_SESSION['food_category'] = $category_id;

    // Check if the food name already exists in the database
    $query_check = "SELECT COUNT(*) as count FROM menu WHERE name = :name";
    $statement_check = $conn->prepare($query_check);
    $statement_check->bindParam(':name', $name);
    $statement_check->execute();
    $result_check = $statement_check->fetch(PDO::FETCH_ASSOC);

    if($result_check['count'] > 0) {
        } else {
            $fileName = $_FILES["image"]["name"];
            $fileSize = $_FILES["image"]["size"];
            $tmpName = $_FILES["image"]["tmp_name"];
            $validImageExtension = ['jpg', 'jpeg', 'png'];
            $imageExtension = pathinfo($fileName, PATHINFO_EXTENSION);
            $imageExtension = strtolower($imageExtension);

            if(!in_array($imageExtension, $validImageExtension)){
            } else if($fileSize > 1000000){
                echo "<script> alert('Image size is too large');</script>";
            } else {
                $newImageName = uniqid() . '.' . $imageExtension;
                move_uploaded_file($tmpName, 'img/' . $newImageName);

                // Prepare the SQL statement for inserting the new menu item
                $query = "INSERT INTO menu (name, image, is_active, category_id, created_at, updated_at) VALUES (:name, :image, :published, :category_id, NOW(), NOW())";
                $statement = $conn->prepare($query);

                // Bind parameters
                $statement->bindParam(':name', $name);
                $statement->bindParam(':image', $newImageName);
                $statement->bindParam(':published', $published);
                $statement->bindParam(':category_id', $category_id);

                // Execute the statement
                if($statement->execute()) {
                    echo "<script>alert('Successfully Added');</script>";
                    echo "<script>window.location.href='home.php';</script>";
                } else {
                    echo "<script>alert('Error while adding menu item');</script>";
                }
            }

    unset($_SESSION['name']);
    unset($_SESSION['published']);
    unset($_SESSION['food_category']);
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Document</title>
    <style>
        .error-message {
    color: red;
    font-size: 14px;
    margin-top: 5px;
}

        /* Add your CSS styles here */
        .image-preview-container {
            position: relative;
            display: inline-block;
             /* Ensure container only takes up necessary space */
        }

        .image-preview {
            max-width: 300px;
            margin-top: 10px;
        }

        .image-preview img {
            max-width: 100%;
            height: 200px;
            display: block;
            border: 2px; 
            border-style: solid;
  border-color: gray;
        }

        .cancel-button {
            display: none; /* Initially hide the cancel button */
            position: absolute;
            top: 0;
            right: 0;
            /* background-color: white; */
            border: none;
            border-radius: 50%;
            padding: 5px;
            cursor: pointer;
            color: red;
        }
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
                <li><a class="active" href="menu_create.php">Menu</a></li>
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
    <div class='font-bold italic text-[48px]'>Create New Menu</div>

    <form class='space-y-6' action='' method='post' autocomplete='off' enctype='multipart/form-data'>
    <a href="javascript:history.back()" class="bg-blue-500 hover:bg-blue-400 text-white px-4 py-2 rounded mb-6 w-[100px] h-[40px] text-[16px]">Back</a>
    <div class='flex flex-row w-[100%] gap-11'>
        <label for='name' class='text-[16px]'>Food Name:</label>
        <input type='text' id='name' name='name' required value='<?php echo isset($_POST["name"]) ? htmlspecialchars($_POST["name"]) : "" ?>' class='border w-[50%] rounded h-[40px]'/>

    </div>
    <div class="error-message">
    <?php
    if(isset($_POST["submit"]) && $result_check['count'] > 0) {
        echo "Your food name already exists.";
    }
    ?>
</div>
<script>
    // Check if error message is displayed and clear the food name input field
    window.onload = function() {
        var errorMessage = document.querySelector('.error-message');
        var foodNameInput = document.getElementById('name');
        
        if(errorMessage) {
            foodNameInput.value = '';
        }
    };
</script>


    <!-- <label for='image' >File image</label><br>
    <input type='file' id='image' name='image' accept='.jpg,.jpeg,.png' value='' onchange="previewImage(event)"/><br>
    <div class="image-preview-container">
        <div class="image-preview" id="imagePreview"></div>
        <button class="cancel-button" id="cancelButton" onclick="removeImage()">X</button>
    </div><br> -->
    <label for='image' >File image</label><br>
    
    <!-- File input field -->
   <input type='file' id='imageInput' name='image' accept='.jpg,.jpeg,.png' style="display: none;" onchange="previewImage(event)" />
   
   <!-- Button to trigger file input field -->
   <button type="button" class="border p-1 justify-center bg-green-300 font-semibold rounded-[5px] h-[40px] w-[140px] mr-4" onclick="document.getElementById('imageInput').click()">Choose Image</button>
   
   <!-- Text input field to display file name -->
   <input type='text' id='fileName' class="border h-[40px] w-[380px] p-1" value="" readonly /><br>
   
      
   <div class="image-preview-container" id="imagePreviewContainer">
       <!-- Image preview -->
       <?php if (!empty($row['image'])): ?>
           <div class="image-preview">
               <img src="img/<?php echo $row['image']; ?>" class="image-preview-img">
           </div>
           <button class="cancel-button" id="cancelButton" onclick="removeImage()">X</button>    
        <?php endif; ?>
   
   </div>
   <br>
    <select name="food_category" id="food_category" class="w-1/2 p-2 border rounded">
        <option value="" disabled selected>Select Food Category</option>
        <option value="1">Chicken Meals</option>
        <option value="2">Combo Set</option>
        <option value="3">Beverages</option>
        <option value="4">Limited Time Offer</option>
        <option value="5">Snacks</option>
    </select><br>
    
    <label class="relative inline-flex items-center cursor-pointer">
    <input type="checkbox" name="published" class="sr-only peer" checked>
    <div class="w-11 h-6 bg-gray-200 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-green-600"></div>
    <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300">Published</span>
</label><br>


    <button class='border bg-blue-500 p-2 w-[10%] h-[40px] font-bold text-[16px] text-white rounded-[5px] items-center object-center' name='submit' type='submit'>Create</button>
</div> 
    </form>
    
</div>    

<script>
      // Function to show the cancel button when the page is loaded
      document.addEventListener("DOMContentLoaded", function() {
        var cancelButton = document.getElementById('cancelButton');
        var imagePreviewContainer = document.getElementById('imagePreviewContainer');
        
        // If there's an image preview, show the cancel button
        if (imagePreviewContainer.children.length > 0) {
            cancelButton.style.display = 'block';
        }
    });
      function removeImage() {
    // Show the cancel button
    var cancelButton = document.getElementById('cancelButton');
    cancelButton.style.display = 'block';

    // Clear the image preview by setting its inner HTML to an empty string
    var imagePreviewContainer = document.getElementById('imagePreviewContainer');
    imagePreviewContainer.innerHTML = '';

    // Reset the file input value to clear the selected file
    var fileInput = document.getElementById('imageInput');
    fileInput.value = '';

    // Clear the file name input field
    var fileNameInput = document.getElementById('fileName');
    fileNameInput.value = '';
}

function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.querySelector('.image-preview-container');
                output.innerHTML = '<div class="image-preview"><img src="' + reader.result + '"></div>';
                
                // Show cancel button
                var cancelButton = document.getElementById('cancelButton');
                cancelButton.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
            
            // Update the file name input field with the chosen file name
            var fileNameInput = document.getElementById('fileName');
            fileNameInput.value = event.target.files[0].name;
        }

        // Event listener for the cancel button
        document.getElementById('cancelButton').addEventListener('click', function() {
            // Clear the image preview
            document.querySelector('.image-preview-container').innerHTML = '';
            // Hide the cancel button again
            this.style.display = 'none';
        });
</script>

</body>
</html>

