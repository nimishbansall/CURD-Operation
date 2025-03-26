<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "myshop";

// Create database connection
$connection = new mysqli($servername, $username, $password, $database);

$name = $_POST["name"] ?? "";
$email = $_POST["email"] ?? "";
$phone = $_POST["phone"] ?? "";
$address = $_POST["address"] ?? "";
$gender = $_POST["gender"] ?? "";
$preferences = $_POST["preferences"] ?? [];
$notes = $_POST["notes"] ?? "";
$file = $_FILES["file"] ?? null;

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    do {
        // Validate required fields
        if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($gender) || empty($notes) || empty($preferences)) {
            $errorMessage = "All fields are required!";
            break;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = "Invalid email format!";
            break;
        }

        // Validate phone number
        if (!ctype_digit($phone) || strlen($phone) !== 10) {
            $errorMessage = "Phone number must be exactly 10 digits!";
            break;
        }

        // Validate file upload
        if (!isset($_FILES["file"]) || $_FILES["file"]["error"] !== 0) {
            $errorMessage = "Image upload is required!";
            break;
        } else {
            $allowed_types = ["jpg", "jpeg", "png"];
            $file_name = $_FILES["file"]["name"];
            $file_tmp = $_FILES["file"]["tmp_name"];
            $file_size = $_FILES["file"]["size"];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed_types)) {
                $errorMessage = "Only JPG, JPEG, and PNG files are allowed!";
                break;
            } elseif ($file_size > 2 * 1024 * 1024) {
                $errorMessage = "File size must be 2MB or less!";
                break;
            }

            // Move uploaded file to "uploads" directory
            $upload_dir = "uploads/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $new_file_name = uniqid("img_", true) . "." . $file_ext;
            $file_path = $upload_dir . $new_file_name;

            if (!move_uploaded_file($file_tmp, $file_path)) {
                $errorMessage = "Failed to upload image.";
                break;
            }
        }

        // Convert preferences array to a comma-separated string
        $preferencesString = implode(',', $preferences);

        // Insert data into database
        $sql = "INSERT INTO clients (name, email, phone, address, gender, preferences, notes, file) 
                VALUES ('$name', '$email', '$phone', '$address', '$gender', '$preferencesString', '$notes', '$file_path')";
        $result = $connection->query($sql);

        if (!$result) {
            $errorMessage = "Invalid query: " . $connection->error;
            break;
        }

        // Reset form fields after successful submission
        $name = $email = $phone = $address = $gender = $notes = "";
        $preferences = [];
        $file_path = "";

        $successMessage = "Client added successfully!";
        header("location: /CRUD Clients/index.php");
        exit;
    } while (false);
}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container my-5">
        <h2>New Client</h2>

        <?php
        if(!empty($errorMessage)){
            echo "
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
            <strong>$errorMessage</strong>
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
            
            ";
        }



        ?>
        <form method="post" enctype="multipart/form-data">
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="name" value="<?php echo $name; ?>">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Email</label>
                <div class="col-sm-6">
                    <input type="email" class="form-control" name="email" value="<?php echo $email; ?>">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">phone</label>
                <div class="col-sm-6">
                    <input type="number"  class="form-control" name="phone" value="<?php echo $phone; ?>">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Address</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="address" value="<?php echo $address; ?>">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Gender</label>
                <div class="col-sm-6">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" value="Male" <?php if($gender == 'Male') echo "checked"; ?>>
                <label class="form-check-label">Male</label>
            </div>
            <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gender" value="Female" <?php if($gender == 'Female') echo "checked"; ?>>
            <label class="form-check-label">Female</label>
            </div>
            <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="gender" value="Other" <?php if($gender == 'Other') echo "checked"; ?>>
            <label class="form-check-label">Other</label>
            </div>
        </div>
    </div>


    <div class="row mb-3">
    <label class="col-sm-3 col-form-label">Preferences</label>
    <div class="col-sm-6">
        <?php
        if (is_string($preferences)) {
            $preferences = explode(',', $preferences);
        }
        
        ?>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="preferences[]" value="Email" <?php if(in_array('Email', $preferences)) echo "checked"; ?>>
            <label class="form-check-label">Email</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="preferences[]" value="SMS" <?php if(in_array('SMS', $preferences)) echo "checked"; ?>>
            <label class="form-check-label">SMS</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="preferences[]" value="Phone Call" <?php if(in_array('Phone Call', $preferences)) echo "checked"; ?>>
            <label class="form-check-label">Phone Call</label>
            </div>
            </div>
        </div>


        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Notes</label>
        <div class="col-sm-6">
        <textarea class="form-control" name="notes" rows="4"><?php echo htmlspecialchars($notes); ?></textarea>
        </div>
    </div>


    <div class="container mt-4">
    <div class="row mb-3">
        <label class="col-sm-3 col-form-label">Upload Image</label>
        <div class="col-sm-6">
            <input type="file" class="form-control" accept="image/*" name="file">
        </div>
    </div>

            <?php
            if(!empty($successMessage)){
                echo "
                <div class='row mb-3'>
                    <div class='offset-sm-3 col-sm-6'>                
                        <div class='alert alert-success alert-dismissible fade show' role='alert'>
                         <strong>$successMessage</strong>
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                        </div>
                    </div>
                </div>
            
            ";
            }
            ?>
            <div class="row mb-3">
                <div class="offset-sm-3 col-sm-3 d-grid">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                <div class="col-sm-3 d-grid">
                    <a class="btn btn-outline-primary" href="/myshop/index.php" role="button">Cancel</a>
                </div>
            </div>
        </form>         
    </div>
</body>
</html>