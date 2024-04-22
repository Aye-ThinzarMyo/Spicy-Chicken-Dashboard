<?php
include 'components/connect.php';

if(isset($_POST["submit"])){
    $address = $_POST["address"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];

    // Prepare the SQL statement
    $query = "UPDATE contactus SET address = :address, email = :email, phone = :phone, updated_at = NOW()";

    $statement = $conn->prepare($query);

    // Bind parameters
    $statement->bindParam(':address', $address);
    $statement->bindParam(':email', $email);
    $statement->bindParam(':phone', $phone);

    // Execute the statement
    if($statement->execute()) {
        echo "<script>alert('Successfully Updated');</script>";
        echo "<script>window.location.href='home.php';</script>";
    } else {
        echo "<script>alert('Error while updating contact');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Edit Contact</title>
</head>
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
                <li><a class="active" href="contactus_edit.php">Contact</a></li>
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
        <div class='font-bold italic text-[48px]'>Edit Contact</div>
        <?php
        $sql = "SELECT * FROM contactus";
        $statement = $conn->prepare($sql);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        ?>
       <form class='space-y-6' action='' method='post' autocomplete='off' enctype='multipart/form-data' onsubmit="return validateForm()">
            <a href="javascript:history.back()" class="bg-blue-500 hover:bg-blue-400 text-white px-4 py-2 rounded mb-6 w-[100px] h-[40px] text-[16px]">Back</a>
            <div class='flex flex-col w-[100%] space-y-4'>
                <label for='address' class='text-[18px] font-semibold'>Address<span class='ml-3 text-red-600'>*</span></label>
                <input type='text' id='address' name='address' required value="<?php echo $row['address']?>" class='border w-[60%] rounded h-[40px]'/>
                <div id="addressError" class="text-red-600 hidden">Please enter your address.</div>
            </div>
            <div class='flex flex-col w-[100%] space-y-4'>
                <label for='phone' class='text-[18px] font-semibold'>Phone<span class='ml-3 text-red-600'>*</span></label>
                <input type='text' id='phone' name='phone' required pattern="09\d{8,9}" title="Please enter a valid phone number starting with 09 and having 9 to 11 digits." value="<?php echo $row['phone']?>" class='border w-[60%] rounded h-[40px]'/>
                <div id="phoneError" class="text-red-600 hidden">Please enter a valid phone number starting with 09 and having 9 to 11 digits.</div>
            </div>
            <div class='flex flex-col w-[100%] space-y-4'>
                <label for='email' class='text-[18px] font-semibold'>Email<span class='ml-3 text-red-600'>*</span></label>
                <input type='email' id='email' name='email' required value="<?php echo $row['email']?>" class='border w-[60%] rounded h-[40px]'/>
                <div id="emailError" class="text-red-600 hidden">Please enter a valid email address.</div>
            </div>
            <button class='border bg-blue-500 p-2 w-[10%] h-[40px] font-bold text-[16px] text-white rounded-[5px] items-center object-center' name='submit' type='submit'>Save</button>
            <a class='border bg-blue-500 p-2 w-[10%] h-[40px] font-bold text-[16px] text-white rounded-[5px] items-center object-center' href='/home.php'>Cancel</a>
        </form>  
    </div>    
    <script>
        function validateForm() {
            var address = document.getElementById('address').value;
            var phone = document.getElementById('phone').value;
            var email = document.getElementById('email').value;
            var phonePattern = /^09\d{8,9}$/;
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            // Reset error messages
            document.getElementById('addressError').classList.add('hidden');
            document.getElementById('phoneError').classList.add('hidden');
            document.getElementById('emailError').classList.add('hidden');

            // Validate address
            if (address.trim() === '') {
                document.getElementById('addressError').classList.remove('hidden');
                return false;
            }

            // Validate phone
            if (!phonePattern.test(phone)) {
                document.getElementById('phoneError').classList.remove('hidden');
                return false;
            }

            // Validate email
            if (!emailPattern.test(email)) {
                document.getElementById('emailError').classList.remove('hidden');
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
