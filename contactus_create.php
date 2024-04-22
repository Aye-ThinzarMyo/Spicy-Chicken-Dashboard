<?php
include 'components/connect.php';

if(isset($_POST["submit"])){
    $name = $_POST["name"];
    $published = isset($_POST["published"]) ? 1 : 0;
    $category_id = $_POST["food_category"];

    if($_FILES["image"]["error"] === 4){
        echo "<script> alert('Image does not exist');</script>";
    }else{
        $fileName = $_FILES["image"]["name"];
        $fileSize = $_FILES["image"]["size"];
        $tmpName = $_FILES["image"]["tmp_name"];
        $validImageExtension = ['jpg', 'jpeg', 'png'];
        $imageExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $imageExtension = strtolower($imageExtension);

        if(!in_array($imageExtension, $validImageExtension)){
            echo "<script> alert('Invalid image extension');</script>";
        }else if($fileSize > 1000000){
            echo "<script> alert('Image size is too large');</script>";
        }else{
            $newImageName = uniqid() . '.' . $imageExtension;
            move_uploaded_file($tmpName, 'img/' . $newImageName);

            // Prepare the SQL statement
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
</head>
<body>
<div class='py-20 px-[10%] space-y-6'>
    <div class='font-bold italic text-[48px]'>Create New Menu</div>

    <form class='space-y-6' action='' method='post' autocomplete='off' enctype='multipart/form-data'>
    <div class='flex flex-row w-[100%] gap-11'>
        <label for='name' class='text-[32px] font-semibold'>Food Name:</label>
        <input type='text' id='name' name='name' required value='' class='border w-[50%] rounded h-[40px]'/>
    </div>
    <label for='image' >File image</label><br>
    <input type='file' id='image' name='image' accept='.jpg,.jpeg,.png' value='' onchange="previewImage(event)"/><br>
    <div class="image-preview-container">
        <div class="image-preview" id="imagePreview"></div>
        <button class="cancel-button" id="cancelButton" onclick="removeImage()">X</button>
    </div><br>
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

</body>
</html>

