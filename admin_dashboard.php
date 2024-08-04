<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require 'vendor/autoload.php';

$uri = "mongodb+srv://cinesync:123456789cinesink@cinesync.72zvsfq.mongodb.net/cinesync?retryWrites=true&w=majority";
$client = new MongoDB\Client($uri);
$collection = $client->cinesync->movies;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_movie'])) {
    $category = $_POST['category'];
    $index = (int)$_POST['index'];
    $newTitle = $_POST['new_title'];
    $newDate = $_POST['new_date'];
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["new_picture"]["name"]);

    if (move_uploaded_file($_FILES["new_picture"]["tmp_name"], $targetFile)) {
        $updateField = ($category == 'nowshowing') ? 'nowshowing.' . $index : 'upcoming.' . $index;
        $updateResult = $collection->updateOne(
            [$category => ['$exists' => true]],
            ['$set' => [
                $updateField . '.title' => $newTitle,
                $updateField . '.picture' => $targetFile,
                $updateField . '.date' => $newDate
            ]]
        );
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_time'])) {
    $index = (int)$_POST['index'];
    $newTimes = $_POST['new_times'];

    $formattedTimes = [];
    foreach ($newTimes as $time) {
        if (strtotime($time) === false) {
            echo "<script>alert('Invalid time format: $time');</script>";
        }
        $formattedTime = date('h:i A', strtotime($time));
        $formattedTimes[] = $formattedTime;
    }

    $updateResult = $collection->updateOne(
        ['nowshowing.' . $index => ['$exists' => true]],
        ['$set' => [
            'nowshowing.' . $index . '.times' => $formattedTimes
        ]]
    );
}

$document = $collection->findOne();

$nowShowingMovies = [];
$upcomingMovies = [];

if (isset($document['nowshowing'])) {
    $nowShowingMovies = $document['nowshowing'];
}
if (isset($document['upcoming'])) {
    $upcomingMovies = $document['upcoming'];
}
$collectionP = $client->cinesync->purchased;

// Fetch all purchase records
$purchases = $collectionP->find()->toArray();

$totalAmount = 0;
foreach ($purchases as $purchase) {
    $totalAmount += $purchase['purchaseDetails']['totalAmount'];
}
$totalAmountFormatted = number_format($totalAmount, 2); 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PickMovie</title>
    <link rel="stylesheet" href="admin.css">
    
</head>
<body>
    <div class="rectangle-box"></div>
    <img class="logo" src="BuyMovie\8.png" alt="Logo" draggable="false">
    <h2 class="now-showing">NOW SHOWING</h2>
    <h2 class="up-coming">UP COMING</h2>
    


    <div class="selections">
        <h3 class="movies-1">MOVIE 1</h3>
        <h3 class="movies-2">MOVIE 2</h3>
        <h3 class="movies-3">MOVIE 3</h3>
        <h3 class="movies-4">MOVIE 4</h3>
        <h3 class="movies-5">MOVIE 5</h3>
        <h3 class="movies-6">MOVIE 6</h3>
        <h3 class="movie-1">MOVIE 1</h3>
        <h3 class="movie-2">MOVIE 2</h3>
        <h3 class="movie-3">MOVIE 3</h3>
        <h3 class="movie-4">MOVIE 4</h3>
        <h3 class="movie-5">MOVIE 5</h3>
        <h3 class="movie-6">MOVIE 6</h3>
        <h1 class="welcome">Welcome</h1>
        <h2 class="admin">Admin!</h2>
    </div>
    
    <div class="movies-container nowshowing">
        <?php
        foreach ($nowShowingMovies as $index => $movie) {
            echo '<div class="movie">';
            echo '<form method="post" action="" enctype="multipart/form-data">';
            echo '<input type="hidden" name="category" value="nowshowing">';
            echo '<input type="hidden" name="index" value="' . $index . '">';
            echo '<input  id="fileInput" type="file" name="new_picture">';
            echo '<input type="text" name="new_title" value="' . $movie['title'] . '">';
            echo '<input type="date" name="new_date" value="' . $movie['date'] . '">';
            echo '<input type="submit" name="update_movie" value="Update">';
            echo '</form>';
            echo '</div>';
        }
        ?>
    </div>

    <div class="movies-container-time nowshowing" style="display: none;">
        <?php
        foreach ($nowShowingMovies as $index => $movie) {
            echo '<div class="movie">';
            echo '<form method="post" action="" enctype="multipart/form-data">';
            echo '<input type="hidden" name="category" value="nowshowing">';
            echo '<input type="hidden" name="index" value="' . $index . '">';

            echo '<div class="time-container">'; // Added wrapper div for time slots
            for ($i = 0; $i < 4; $i++) { // Only display four time slots
                echo '<div class="time-slots">';
                // Check if the time is set in the database and set the checkbox accordingly
                $checked = !empty($movie['times'][$i]) ? 'checked' : '';
                echo '<label><input type="time" name="new_times[]" value="' . ($movie['times'][$i] ?: '') . '" ' . $checked . '>';
                echo '</label>';
                echo '</div>';
            }
            echo '</div>';
            
            echo '<input type="submit" name="update_time" value="Update">';
            echo '</form>';
            echo '</div>';
        }
        ?>
    </div>


    <div class="movies-container upcoming">
        <?php
        foreach ($upcomingMovies as $index => $movie) {
            echo '<div class="movie">';
            echo '<form method="post" action="" enctype="multipart/form-data">';
            echo '<input type="hidden" name="category" value="upcoming">';
            echo '<input type="hidden" name="index" value="' . $index . '">';
            echo '<input  id="fileInput" type="file" name="new_picture">';
            echo '<input type="text" name="new_title" value="' . $movie['title'] . '">';
            echo '<input type="date" name="new_date" value="' . $movie['date'] . '">';
            echo '<input type="submit" name="update_movie" value="Update">';
            echo '</form>';
            echo '</div>';
        }
        ?>
    </div>
    
    <button class="movieDetails" onclick="showMovies()">MOVIE</button>
    <button class="timeDetails" onclick="showTimes()">TIME</button>
    
    <button class="resetSeats">Reset Seats</button>

    <button class="rawProfit">Raw Profit</button>
    
    <div class="modal1" id="mainTable">
    <div class="modal-content1">
        <span class="close">&times;</span>
        <div class="purchasedTable">
            <table>
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Date of Purchase</th>
                        <th>Time of Purchase</th>
                        <th>Movie Title</th>
                        <th>Quantity</th>
                        <th>Total Amount</th>
                        <th>Payment Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($purchases as $purchase): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($purchase['username']); ?></td>
                            <td><?php echo htmlspecialchars($purchase['purchaseDetails']['dateOfPurchase']); ?></td>
                            <td><?php echo htmlspecialchars($purchase['purchaseDetails']['timeOfPurchase']); ?></td>
                            <td><?php echo htmlspecialchars($purchase['purchaseDetails']['movieTitle']); ?></td>
                            <td><?php echo htmlspecialchars($purchase['purchaseDetails']['selectedQuantity']); ?></td>
                            <td><?php echo htmlspecialchars($purchase['purchaseDetails']['totalAmount']); ?></td>
                            <td><?php echo htmlspecialchars($purchase['purchaseDetails']['paymentMethod']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="totalA" ><strong>Total:</strong></td>
                        <td colspan="10" class="totalP" style="font-weight: bold;"><?php echo $totalAmountFormatted; ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>


    <form action="login.php" method="post">
        <button value="Logout" class="logoutButton">Log out</button>
    </form>
    
    <div id="resetModal" class="modal">
        <div class="modal-content">
            <p>Do you want to reset seats?</p>
            <button id="confirmReset" class="confirmButton">Confirm</button>
            <button id="cancelReset" class="cancelButton">Cancel</button>
        </div>
    </div>
    
    <script>
        var modal = document.getElementById("mainTable");

        var btn = document.querySelector(".rawProfit");

        var span = document.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        document.querySelector('.resetSeats').addEventListener('click', function() {
            document.getElementById('resetModal').style.display = 'block';
        });

        document.getElementById('confirmReset').addEventListener('click', function() {
            fetch('reset_seats.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ reset: true })
            })
            .then(response => {
                document.getElementById('resetModal').style.display = 'none';
                if (response.ok) {
                    alert('Seats reset successfully!');
                } else {
                    alert('Failed to reset seats.');
                }
            })
            .catch(error => {
                document.getElementById('resetModal').style.display = 'none';
                console.error('Error resetting seats:', error);
                alert('An error occurred while resetting seats.');
            });
        });

        document.getElementById('cancelReset').addEventListener('click', function() {
                document.getElementById('resetModal').style.display = 'none';
        });
        function showMovies() {
            document.querySelector('.movies-container').style.display = 'grid';
            document.querySelector('.movies-container-time').style.display = 'none';
        }

        function showTimes() {
            document.querySelector('.movies-container').style.display = 'none';
            document.querySelector('.movies-container-time').style.display = 'grid';
        }


        document.getElementById('fileInput').addEventListener('change', function() {
            var fileInput = document.getElementById('fileInput');
            var chooseFileButton = fileInput.previousElementSibling;
            if (fileInput.files.length > 0) {
                chooseFileButton.style.backgroundColor = 'green';
                chooseFileButton.style.color = 'white';
            }
        });
        
        function validateForm(form) {
                var inputs = form.querySelectorAll('input[type="text"], input[type="date"], input[type="file"]');
                var isValid = true;

                inputs.forEach(function(input) {
                    if (input.type === 'file') {
                        if (input.value === '') {
                            isValid = false;
                            input.classList.add('error');
                        } else {
                            input.classList.remove('error');
                        }
                    } else {
                        if (input.value.trim() === '') {
                            isValid = false;
                            input.classList.add('error');
                        } else {
                            input.classList.remove('error');
                        }
                    }
                });

                return isValid;
            }

            document.querySelectorAll('input[type="file"]').forEach(function(input) {
                input.addEventListener('change', function() {
                    var fileInput = this;
                    var chooseFileButton = fileInput.previousElementSibling;
                    if (fileInput.files.length > 0) {
                        chooseFileButton.style.backgroundColor = 'green'; // Green background
                        chooseFileButton.style.color = 'white'; // White font color
                    }
                });
            });
    </script>
</body>
</html>
