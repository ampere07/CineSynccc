<?php
session_start();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$uri = "mongodb+srv://cinesync:123456789cinesink@cinesync.72zvsfq.mongodb.net/cinesync?retryWrites=true&w=majority";

require 'vendor/autoload.php';
$client = new MongoDB\Client("mongodb+srv://cinesync:123456789cinesink@cinesync.72zvsfq.mongodb.net/cinesync?retryWrites=true&w=majority");
$db = $client->selectDatabase('cinesync');
$usersCollection = $db->accounts;

// Handle form submission for updating account details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Update document in MongoDB
    $updateResult = $usersCollection->updateOne(
        ['username' => $user_id],
        ['$set' => ['username' => $username, 'email' => $email, 'password' => $hashed_password]]
    );

    if ($updateResult->getModifiedCount() > 0) {
        // Update successful
        $_SESSION['user_id'] = $username;
        header("Location: userProfile.php");
        exit;
    } else {
        // Update failed
        echo "Failed to update account details.";
    }
}

// Query to find the user by username
$userInfo = $usersCollection->findOne(['username' => $user_id]);

$email = '';
$password = '';

if ($userInfo) {
    $email = isset($userInfo['email']) ? $userInfo['email'] : '';
    $password = isset($userInfo['password']) ? $userInfo['password'] : '';
}

$purchaseCollection = $db->purchased;
$purchases = $purchaseCollection->find(['username' => $user_id]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="userProfile.css">
    <title>User Profile</title>
</head>
<body>
    <div class="rectangle-box">
        <img class="logo" src="BuyMovie\8.png" alt="Logo" draggable="false">
        <h2 class="user">Hi! <?php echo $user_id; ?></h2>
            <div class="Infos">   
                <h4 class="purchased">Purchased History</h4>
                <h4 class="account active">Account Details</h4>
            </div>
        <button class="backButton">Back</button>
        <button class="logoutButton">Log out</button>
        </div>

    <div class="mainTable">
        <div class="purchasedTable">
            <table>
                <thead>
                    <tr>
                        <th>Date of Purchase</th>
                        <th>Time of Purchase</th>
                        <th>Movie Title</th>
                        <th>Movie Date</th>
                        <th>Selected Time</th>
                        <th>Quantity</th>
                        <th>Selected Seats</th>
                        <th>Total Amount</th>
                        <th>Payment Method</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($purchases as $purchase): ?>
                        <tr>
                            <td><?php echo $purchase['purchaseDetails']['dateOfPurchase']; ?></td>
                            <td><?php echo $purchase['purchaseDetails']['timeOfPurchase']; ?></td>
                            <td><?php echo $purchase['purchaseDetails']['movieTitle']; ?></td>
                            <td><?php echo $purchase['purchaseDetails']['movieDate']; ?></td>
                            <td><?php echo $purchase['purchaseDetails']['selectedTime']; ?></td>
                            <td><?php echo $purchase['purchaseDetails']['selectedQuantity']; ?></td>
                            <td><?php echo $purchase['purchaseDetails']['selectedSeats']; ?></td>
                            <td><?php echo $purchase['purchaseDetails']['totalAmount']; ?></td>
                            <td><?php echo $purchase['purchaseDetails']['paymentMethod']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="accountDetails"> 
            <div class="mainDetails">
                <form id="editForm" method="post" action="userProfile.php">
                    <div class="usernameBox">
                        <label class="username" for="username">Username:</label>
                        <p class="usernameText"><?php echo $user_id; ?></p>
                    </div>

                    <div class="emailBox">
                        <label class="email" for="email">Email:</label>
                        <p class="emailText"><?php echo $email; ?></p>
                    </div>

                    <div class="passwordBox">
                        <label class="password" for="password">Password:</label>
                        <p class="passwordText"><?php echo $password; ?></p>
                    </div>

                    <button type="button" id="editAccountButton" class="updateAccount">Edit Account</button>
                </form>
            </div>

            <div class="editDetails" style="display: none;">  
                <form id="saveChangesForm" method="post" action="userProfile.php">
                    <div class="usernameBox">
                        <label class="username" for="username">Username:</label>
                        <input type="text" class="usernameEdit" name="username" value="<?php echo $user_id; ?>">
                    </div>

                    <div class="emailBox">
                        <label class="email" for="email">Email:</label>
                        <input type="email" class="emailEdit" name="email" value="<?php echo $email; ?>">
                    </div>

                    <div class="passwordBox">
                        <label class="password" for="password">Password:</label>
                        <input type="password" class="passwordEdit" name="password" value="<?php echo $password; ?>">
                    </div>

                    <button type="submit" class="updateAccount">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event listener to the logout button
            document.querySelector('.logoutButton').addEventListener('click', function() {
                // Redirect to login.php when the button is clicked
                window.location.href = 'login.php';
            });
        });


        document.addEventListener('DOMContentLoaded', function() {
            // Add click event listener to the logout button
            document.querySelector('.backButton').addEventListener('click', function() {
                // Redirect to login.php when the button is clicked
                window.location.href = 'PickMovie.php';
            });
        });

    document.addEventListener('DOMContentLoaded', function() {
        var purchasedTable = document.querySelector('.purchasedTable');
        var accountDetails = document.querySelector('.accountDetails');

        purchasedTable.style.display = 'none'; // Initially hide purchasedTable (optional)
        
        var togglePurchased = document.querySelector('.purchased');
        var toggleAccount = document.querySelector('.account');

        togglePurchased.addEventListener('click', function() {
            purchasedTable.style.display = 'block';
            accountDetails.style.display = 'none';

            togglePurchased.classList.add('active');
            toggleAccount.classList.remove('active');
        });

        toggleAccount.addEventListener('click', function() {
            purchasedTable.style.display = 'none';
            accountDetails.style.display = 'block';

            toggleAccount.classList.add('active');
            togglePurchased.classList.remove('active');
        });

        var editAccountButton = document.getElementById('editAccountButton');
        var editDetails = document.querySelector('.editDetails');
        var mainDetails = document.querySelector('.mainDetails');

        editAccountButton.addEventListener('click', function() {
            mainDetails.style.display = 'none';
            editDetails.style.display = 'block';
        });
    });
</script>
