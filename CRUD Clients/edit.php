<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "myshop";

// Create connection
$connection = new mysqli($servername, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$id = "";
$name = "";
$email = "";
$phone = "";
$address = "";
$gender = "";
$preferences = "";
$notes = "";

$errorMessage = "";
$successMessage = "";

// ✅ Handling GET Request (Fetching Client Data)
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!isset($_GET["id"]) || empty($_GET["id"])) {
        header("location: /CRUD Clients/index.php");
        exit;
    }

    $id = $_GET["id"];

    // ✅ Using Prepared Statement to Fetch Data
    $stmt = $connection->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        die("No client found with ID: $id");
    }

    // Assign values safely
    $name = $row["name"] ?? "";
    $email = $row["email"] ?? "";
    $phone = $row["phone"] ?? "";
    $address = $row["address"] ?? "";
    $gender = $row["gender"] ?? "";
    $preferences = $row["preferences"] ?? "";  // Fix spelling
    $notes = $row["notes"] ?? "";
} 
// ✅ Handling POST Request (Updating Client Data)
else {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $gender = $_POST["gender"];
    $preferencesArray = isset($_POST["preferences"]) ? $_POST["preferences"] : []; 
    $preferences = implode(",", $preferencesArray); // Convert array to string
    $notes = $_POST["notes"];

    do {
        if (empty($name) || empty($email) || empty($phone) || empty($address) || empty($gender) || empty($notes)) {
            $errorMessage = "All fields are required";
            break;
        }

        // ✅ Using Prepared Statement for Update Query
        $stmt = $connection->prepare("UPDATE clients SET name = ?, email = ?, phone = ?, address = ?, gender = ?, preferences = ?, notes = ? WHERE id = ?");
        $stmt->bind_param("sssssssi", $name, $email, $phone, $address, $gender, $preferences, $notes, $id);
        $result = $stmt->execute();

        if (!$result) {
            $errorMessage = "Error updating record: " . $stmt->error;
            break;
        }

        $successMessage = "Client updated successfully";
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
    <title>My Shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container my-5">
        <h2>Edit Client</h2>

        <?php if (!empty($errorMessage)): ?>
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <strong><?php echo $errorMessage; ?></strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Name</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($name); ?>">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Email</label>
                <div class="col-sm-6">
                    <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($email); ?>">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Phone</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Address</label>
                <div class="col-sm-6">
                    <input type="text" class="form-control" name="address" value="<?php echo htmlspecialchars($address); ?>">
                </div>
            </div>

            <!-- Gender Selection -->
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Gender</label>
                <div class="col-sm-6">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" value="Male" <?php if ($gender == 'Male') echo "checked"; ?>>
                        <label class="form-check-label">Male</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" value="Female" <?php if ($gender == 'Female') echo "checked"; ?>>
                        <label class="form-check-label">Female</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" value="Other" <?php if ($gender == 'Other') echo "checked"; ?>>
                        <label class="form-check-label">Other</label>
                    </div>
                </div>
            </div>

            <!-- Preferences Checkboxes -->
            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Preferences</label>
                <div class="col-sm-6">
                    <?php $selectedPreferences = explode(",", $preferences); ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="preferences[]" value="Email" <?php if (in_array('Email', $selectedPreferences)) echo "checked"; ?>>
                        <label class="form-check-label">Email</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="preferences[]" value="SMS" <?php if (in_array('SMS', $selectedPreferences)) echo "checked"; ?>>
                        <label class="form-check-label">SMS</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="preferences[]" value="Phone Call" <?php if (in_array('Phone Call', $selectedPreferences)) echo "checked"; ?>>
                        <label class="form-check-label">Phone Call</label>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <label class="col-sm-3 col-form-label">Notes</label>
                <div class="col-sm-6">
                    <textarea class="form-control" name="notes"><?php echo htmlspecialchars($notes); ?></textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
            <a class="btn btn-outline-primary" href="/CRUD Clients/index.php">Cancel</a>
        </form>
    </div>
</body>
</html>
