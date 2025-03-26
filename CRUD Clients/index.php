<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2>List of Clients</h2>
        <a class="btn btn-primary" href="/CRUD Clients/create.php" role="button">New Clients</a>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>gender</th>
                    <th>preferences</th>
                    <th>notes</th>
                    <th>file</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php 
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $database = "myshop";

                    // create connection
                    $connection = new mysqli($servername, $username, $password, $database);

                    // Check connection
                    if($connection->connect_error){
                        die("connection failed:" . $connection->connect_error);
                    }

                    // read all row from database table
                    $sql = "SELECT * FROM clients ORDER BY created_at DESC";
                    $result = $connection->query($sql);

                    if(!$result){
                        die("Invalid query:" . $connection->error);
                    }

                    //read data of each row
                    while($row = $result->fetch_assoc()){
                        echo"
                        <tr>
                            <td>$row[id]</td>
                            <td>$row[name]</td>
                            <td>$row[email]</td>
                            <td>$row[phone]</td>
                            <td>$row[address]</td>
                            <td>$row[gender]</td>
                            <td>$row[preferences]</td>
                            <td>$row[notes]</td>
                            <td>$row[file]</td>
                            <td>$row[created_at]</td>
                            <td>
                                <a class='btn btn-primary btn-sm' href='/CRUD Clients/edit.php?id=$row[id]'>Edit</a>
                                <a class='btn btn-danger btn-sm' href='/CRUD Clients/delete.php?id=$row[id]'>Delete</a>
                        </td>
                    </tr>
                    ";
                    }
                ?>
                

            </tbody>
        </table>
    </div>

</body>
</html>