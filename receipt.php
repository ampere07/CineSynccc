<?php
session_start();

require 'vendor/autoload.php';

$movieTitle = $_POST['movieTitle'] ?? '';
$moviePicture = $_POST['moviePicture'] ?? '';
$movieDate = $_POST['movieDate'] ?? '';
$selectedQuantity = $_POST['selectedQuantity'] ?? 0;
$selectedTimeInput = $_POST['selectedTime'] ?? ''; // Ensure this is correctly retrieved
$paymentMethod = $_POST['paymentMethod'] ?? '';
$selectedSeats = $_POST['selectedSeats'] ?? '';

$seatPrice = 300;
$onlineFee = 20; 
$totalAmount = ($seatPrice * $selectedQuantity) + $onlineFee;

$dateOfPurchase = date('Y-m-d');
$timeOfPurchase = date('H:i:s');

$transactionId = uniqid();

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

$rating = isset($_POST['rating']) ? $_POST['rating'] : null;
// Check if rating is submitted
if (isset($_POST['rating'])) {
    $rating = $_POST['rating'];
}
$ticketData = [
    "username" => $user_id,
    "purchaseDetails" => [
        "dateOfPurchase" => $dateOfPurchase,
        "timeOfPurchase" => $timeOfPurchase,
        "movieTitle" => $movieTitle,
        "movieDate" => $movieDate,
        "selectedTime" => $selectedTimeInput,
        "selectedQuantity" => $selectedQuantity,
        "selectedSeats" => $selectedSeats,
        "totalAmount" => number_format($totalAmount, 2),
        "paymentMethod" => $paymentMethod,
        "rating" => $rating     
        ]
];

try {
    $client = new MongoDB\Client("mongodb+srv://cinesync:123456789cinesink@cinesync.72zvsfq.mongodb.net/cinesync?retryWrites=true&w=majority");

    $collection = $client->cinesync->purchased;
    $result = $collection->insertOne($ticketData);

    $collectionName = '';

    switch ($selectedTimeInput) {
        case '09:00 AM':
        case '10:00 AM':
            $collectionName = 'SeatsAvailability1';
            break;
        case '11:00 AM':
        case '01:00 PM':
            $collectionName = 'SeatsAvailability2';
            break;
        case '02:00 PM':
        case '04:00 PM':
            $collectionName = 'SeatsAvailability3';
            break;
        case '05:00 PM':
        case '06:00 PM':
            $collectionName = 'SeatsAvailability4';
            break;
        default:
            // Handle default case if necessary
            break;
    }

    if (!empty($collectionName)) {
        $SeatsAvail = $client->cinesync->$collectionName;

        $selectedSeatArray = explode(',', $selectedSeats);

        foreach ($selectedSeatArray as $seat) {
            $seatNumber = trim($seat);

            $updateResult = $SeatsAvail->updateOne(
                ['seat_number' => $seatNumber],
                ['$set' => ['available' => false]]
            );
        }
    } else {
        throw new Exception("No collection found for the selected time: $selectedTimeInput");
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Ticket Purchase Receipt</title>
    <link rel="stylesheet" href="receipt.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        .rating-form {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            display: none; 
            text-align: center;
        }

        .rating-form h2 {
            margin-top: 0;
        }

        .star-rating {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

        .star-rating label {
            font-size: 30px;
            color: #ccc;
            cursor: pointer;
            order: 1;
            transition: color 0.3s ease-in-out;
        }

        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input[type="radio"]:checked ~ label {
            color: #ffc107; 
            order: 1;
        }

        .rating-form button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .rating-form button:hover {
            background-color: #45a049;
        }
        .receipt1 {
            position: absolute;
            align-items: center;
            top: 18px;
            left: 406px;
            width: 570px;
        }
    </style>
</head>
<body>
    <img class="receipt1" src="images/receipt.png" draggable="false">
    <div class="receipt">
        <h2>Ticket Purchase Receipt</h2>
        <div class="details">
            <p>Date: <?php echo $dateOfPurchase; ?></p>
            <p>Time: <?php echo $timeOfPurchase; ?></p>
            <p>Transaction ID: <?php echo $transactionId; ?></p>
        </div>
        <hr>
        <div class="details">
            <h3>Ticket Details:</h3>
            <p>Movie: <?php echo $movieTitle; ?></p>
            <p>Date: <?php echo $movieDate; ?></p>
            <p>Time: <?php echo $selectedTimeInput; ?></p>
            <p>Quantity: <?php echo $selectedQuantity; ?></p>  
            <p>Selected Seats: <?php echo $selectedSeats; ?></p>
        </div>
        <hr>
        <div class="details">
            <h3>Payment Details:</h3>
            <p>Total Amount: ₱<?php echo number_format($totalAmount, 2); ?></p>
            <p>Payment Method: <?php echo $paymentMethod; ?></p>
        </div>
        <hr>
        <div class="footer">
        <p>SM Molino Branch</p>
            <p>Thank you for your purchase!</p>
            <p>Take a printscreen or picture of the receipt!</p>
            <p>For any inquiries or assistance, please contact:</p>
            <p>[cinesync2.5@gmail.com]</p>
        </div>
        <a href="pickmovie.php" class="home-button">
            <div class="roof"></div>
            <i class="fas fa-home"></i>
        </a>
        
        <div id="ratingForm" class="rating-form">
        <h2>Rate Your Experience</h2>
        <div class="star-rating">
            <input type="radio" id="star5" name="rating" value="5"><label for="star5"><i class="fas fa-star"></i></label>
            <input type="radio" id="star4" name="rating" value="4"><label for="star4"><i class="fas fa-star"></i></label>
            <input type="radio" id="star3" name="rating" value="3"><label for="star3"><i class="fas fa-star"></i></label>
            <input type="radio" id="star2" name="rating" value="2"><label for="star2"><i class="fas fa-star"></i></label>
            <input type="radio" id="star1" name="rating" value="1"><label for="star1"><i class="fas fa-star"></i></label>
        </div>
        <button onclick="submitRating()">Submit Rating</button>
    </div>
        <form method="post" action>
            <input type="hidden" id="rating" name="rating" value="">

        </form>
        </form>
    </div>
</body>
</html>

<script>
      document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('ratingForm').style.display = 'block';
    });

    function submitRating() {
        var rating = document.querySelector('.star-rating input:checked').value;
        document.getElementById('rating').value = rating; // Set rating value in hidden input

        // Hide rating form after submission
        document.getElementById('ratingForm').style.display = 'none';
        alert('Thank you for rating ' + rating + ' stars!');
        // Optionally, you can submit the form using JavaScript if necessary
        var form = document.getElementById('receiptForm');
        form.submit();

        // You may also add a confirmation message or action here
        
    }
    </script>
