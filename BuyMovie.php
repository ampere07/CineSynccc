<?php
session_start();

require 'vendor/autoload.php';

$selectedTimeIndex = isset($_SESSION['selectedTimeIndex']) ? $_SESSION['selectedTimeIndex'] : null;


$uri = "mongodb+srv://cinesync:123456789cinesink@cinesync.72zvsfq.mongodb.net/cinesync?retryWrites=true&w=majority";
$client = new MongoDB\Client($uri);
$collection = $client->cinesync->movies;

$document = $collection->findOne();
$nowShowingMovies = $document['nowshowing'] ?? [];

if (isset($_GET['title'], $_GET['picture'], $_GET['date'])) {
    $_SESSION['movieTitle'] = $_GET['title'];
    $_SESSION['moviePicture'] = $_GET['picture'];
    $_SESSION['movieDate'] = $_GET['date'];
}
$movieTitle = $_SESSION['movieTitle'] ?? '';
$moviePicture = $_SESSION['moviePicture'] ?? '';
$movieDate = $_SESSION['movieDate'] ?? '';
$selectedSeats = $_POST['selectedSeats'] ?? '';
$selectedQuantity = $_POST['selectedQuantity'] ?? 0;

if (empty($movieTitle) || empty($moviePicture) || empty($movieDate)) {
    header("Location: pickmovie.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BuyMovie</title>
    <link rel="stylesheet" href="BuyMovie.css">
    <style>
        @font-face {
    font-family: 'Anonymous Pro';
        src:url('fonts/Anonymous_Pro_l.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;
}

@font-face {
    font-family: 'Roboto';
        src:url('fonts/Roboto-Regular.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;
}
        
        .nextButton {
            position: absolute;
            color: white;
            font-family: 'Roboto';
            top: 150px;
            left: 1010px;
            width: 170px;
            transform: scale(1); 
            background-color: #AE98AA;
            height: 50px;
            border-radius: 5px;
            font-size: 20px;
            transition: background-color 0.3s ease;
        }
        .nextButton:hover {
            background-color: gray;
        }
        #error-message {
            position: absolute;
            font-weight: bold; 
            display: none;
            top: 200px;
            left: 43%;
            color: red;
            font-style: 'Roboto';
        }
        .PaymentBar1 input[type="checkbox"]{
    position: absolute;
    top: 325px;
    left:1050px;
    width: 7%; 
    transform: scale(1.5); 
    background-color: white;
    height: 7%;
    border-radius: 8px;     
    text-align: center; 
    cursor: pointer;
}
.PaymentBar1 label {
    position: absolute;
    top: 400px;
    left:1100px;
    transform: translate(-50%, -50%);
    font-family: 'Roboto';

}

    .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
            padding-top: 100px;
            font-family: 'Roboto';
        }
        .modal-content {
            align-items: center;
            text-align: center;
            margin: 5% auto; 
            padding: 20px;
            border: 1px solid #888;
            width: 20%; 
            border-radius: 8px;
            background-color: white;
            color: blue;
        }
        .gcashSubmit {
            background-color:green;
            color: white;
            height: 30px;
            width: 90px;
            font-size: 15px;
            border: none;
            transition: 0.3s;
        }
        .gcashSubmit:hover {
            border-radius: 8px;
            background-color: greenyellow;
            transition: 0.3s;
        }
        .radio-time{
            position: absolute;
            top:350px;
            left: 730px;
            color: white;
            font-family: 'Roboto';
            cursor: pointer;
        }
        .TimeOfMovie{
    position: absolute;
    top: 300px;
    left: 645px;
    color: white;
    font-family: 'Roboto';
    text-transform: capitalize;
    text-align: center;
}
        #selectSeat{
            position:absolute;
            top: 580px;
            height:30px;
            font-family: 'Roboto';
            background-color: #AE98AA;
            color: white;
            left: 205px;
            font-size: 20px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        #selectSeat:hover{
            background-color: #888;
            border-radius: 8px;
        }
        .gcashImg {
            width: 200px;
        }
    </style>
</head>
<body>
<div id="error-message" style="display: none;">Please fill in all required fields!</div>

        <img class="logo" src="BuyMovie/8.png" alt="Logo" draggable="false">
        <img class="curtain" src="BuyMovie/GUI.png" alt="Curtain-up" draggable="false">

        <div class="dotted-line"></div>
        <div class="vertical-dotted-line1"></div>
        <div class="vertical-dotted-line2"></div>
        <div class="movie_descriptions">
            <img class="movie" src="<?php echo $moviePicture; ?>" alt="<?php echo $movieTitle; ?>">
            <h2 class="title"><?php echo $movieTitle; ?></h2>
            <div class="description">
            <h3 class="DateOfMovie">(<?php echo $movieDate; ?>)</h3>
            <h4 class="TimeOfMovie">TIME<br>(Reselect the time after selecting seats)</h4>
            </div>
        </div>   

        <div class="heading-container">
            <h2 class="Tickets">T I C K E T S</h2>
            <h2 class="Payment">P A Y M E N T<br>  M E T H O D</h2>
            <h2 class="Order">O R D E R</h2>
            <h2 class="Summarry">S U M M A R R Y</h2>
            <h3 class="Quantity">QUANTITY</h3>
            <h3 class="Price">SEAT PRICE</h3>
            <h3 class="Seat">SEAT</h3>
            <h3 class="SeatPrice">Ticket Price</h3>
            <h3 class="OnlineFee">Online Fee</h3>
            <h3 class="TotalPrice">Total Price</h3>  
        </div>

        <div class="radio-time">
        <?php
        foreach ($nowShowingMovies as $index => $movie) {
            if ($movie['title'] === $movieTitle && $movie['picture'] === $moviePicture && $movie['date'] === $movieDate) {
                echo '<div class="movieGet">';
                echo '<input type="hidden" name="category" value="times">';
                echo '<input type="hidden" name="index" value="' . $index . '">';

                echo '<div class="time-container">';
                foreach ($movie['times'] as $timeIndex => $timeValue) {
                    echo '<div class="time-slots">';
                    $checked = ($selectedTimeIndex !== null && $selectedTimeIndex == $timeIndex) ? ' checked' : '';
                    echo '<input type="radio" name="timeSlot" value="' . htmlspecialchars($timeIndex) . '" id="timeSlot' . $timeIndex . '"' . $checked . ' onclick="updateSelectedTime(this.value)">';
                    echo '<label for="timeSlot' . $timeIndex . '">' . htmlspecialchars($timeValue) . '</label>';
                    echo '</div>';
                }
                echo '</div>';

                echo '</div>';
                break;
            }
        }
        ?>
    </div>

        <div class="QuantityBar">
            <input type="number" name="selectedQuantity" id="quantity" value="<?php echo $selectedQuantity;?>" disabled> 
        </div>

        <div class="PriceBar">
            <input type="text" name="q" value="₱300.00" disabled>
        </div>

        <div class="SeatBar">
            <input type="text" name="selectedSeats" value="<?php echo htmlspecialchars($selectedSeats);?>" disabled>
        </div>
                
        <div class="SeatPriceBar">
            <input type="text" name="q" id="seatPrice" value="₱0.00" disabled>
        </div>

        <div class="OnlineFeeBar">
            <input type="text" name="q" value="₱20.00" disabled>
        </div>

        <div class="TotalPriceBar">
            <input type="text" name="q" id="totalPrice" value="₱0.00" disabled>
        </div> 

        <button class="backButton" onclick="goToPickMovie()">Back</button>
        <form action="receipt.php" method="post" onsubmit="return validateForm()">
            <div class="PaymentBar1">
                <input type="checkbox" id="gcashCheckbox" name="paymentMethod" value="GCash">
                <label for="gcashCheckbox"><span style="color: white; font-family: 'Roboto';">GCash</span></label>
            </div>

            <div class="PaymentBar2">
                <input type="checkbox" id="notAvailableCheckbox" name="paymentMethod" value="Not Available">
                <label for="notAvailableCheckbox"><span style="color: white; font-family: 'Roboto';">Not Available</span></label>
            </div>

            <div class="PaymentBar3">
                <input type="checkbox" id="outOfServiceCheckbox" name="paymentMethod" value="Out of Service">
                <label for="outOfServiceCheckbox"><span style="color: white; font-family: 'Roboto';">Not Available</span></label>
            </div>
       
            <input type="hidden" name="selectedSeats" value="<?php echo htmlspecialchars($selectedSeats); ?>">
            <input type="hidden" name="selectedTime" id="selectedTimeInput" value="">
            <input type="hidden" name="movieTitle" value="<?php echo htmlspecialchars($movieTitle); ?>">
            <input type="hidden" name="moviePicture" value="<?php echo htmlspecialchars($moviePicture); ?>">
            <input type="hidden" name="movieDate" value="<?php echo htmlspecialchars($movieDate); ?>">
            <input type="hidden" name="selectedQuantity" value="<?php echo htmlspecialchars($selectedQuantity); ?>">
            <input type="hidden" name="totalAmount" id="totalAmount" value="">
            <input type="submit" class="nextButton" value="Confirm Payment">
        </form>

        <div id="gcashModal" class="modal">
        <div class="modal-content">
        <img class="gcashImg" src="BuyMovie/gcash.png">

                    <p>Mobile Number: 09123456578</p>
                    <p>Name: CineSync</p>
                    <label for="paymentImage">Upload Payment Screenshot:</label><br><br>
                    <input type="file" id="paymentImage" name="paymentImage"><br><br>
                    <button class="gcashSubmit"onclick="submitGcashPayment()">Submit</button>
                </div>
            </div>
        
            <button id="selectSeat" onclick="selectSeat()">Select Seat</button>

<script>
    function updateSelectedTime(value) {
    const timeValue = document.querySelector('input[name="timeSlot"]:checked + label').textContent.trim();
    document.getElementById('selectedTimeInput').value = timeValue;
}
    var modal = document.getElementById("gcashModal");
    var checkbox = document.getElementById("gcashCheckbox");

    checkbox.onclick = function() {
        if (checkbox.checked) {
            modal.style.display = "block";
        } else {
            modal.style.display = "none";
        }
    };

    const checkboxes = document.querySelectorAll('input[type="checkbox"][name="paymentMethod"]');
    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', function() {
            checkboxes.forEach((otherCheckbox) => {
                if (otherCheckbox !== checkbox) {
                    otherCheckbox.checked = false;
                }
            });
        });
    });

    function toggleTimeSlot(element) {
        var timeSlots = document.querySelectorAll('.time-slots input[type="radio"]');
        timeSlots.forEach(function(slot) {
            slot.parentElement.classList.remove('selected-time');
        });
        element.parentElement.classList.add('selected-time');

        document.getElementById('selectedTimeInput').value = element.value;
    }


    function selectSeat() {
    const selectedTimeIndex = document.querySelector('input[name="timeSlot"]:checked');
    
    if (!selectedTimeIndex) {
        alert('No time selected.');
        return;
    }
    
    const index = selectedTimeIndex.value;
    sessionStorage.setItem('selectedTimeIndex', index); // Store selected time index

    let seatPlanUrl;
    switch (index) {
        case '0':
            seatPlanUrl = 'seatplan1.php';
            break;
        case '1':
            seatPlanUrl = 'seatplan2.php';
            break;
        case '2':
            seatPlanUrl = 'seatplan3.php';
            break;
        case '3':
            seatPlanUrl = 'seatplan4.php';
            break;
        default:
            alert('Invalid time selection.');
            return;
    }

    window.location.href = seatPlanUrl + '?timeIndex=' + index; // Pass time index as URL parameter
}
    function goToPickMovie() {
        window.location.href = 'pickmovie.php';
    }

    function calculateTotalPrice() {
        const quantity = document.getElementById('quantity').value;
        const seatPrice = 300; 
        const onlineFee = 20; 

        const calculatedSeatPrice = quantity * seatPrice;
        const totalPrice = calculatedSeatPrice + onlineFee;

        document.getElementById('seatPrice').value = '₱' + calculatedSeatPrice.toFixed(2);
        document.getElementById('totalPrice').value = '₱' + totalPrice.toFixed(2);

        document.getElementById('totalAmount').value = totalPrice.toFixed(2);
    }

    document.getElementById('quantity').addEventListener('input', calculateTotalPrice);
    window.onload = calculateTotalPrice;

    function validateForm() {
    const selectedSeats = document.querySelector('input[name="selectedSeats"]').value;
    const selectedTime = document.getElementById('selectedTimeInput').value;
    const paymentMethod = Array.from(document.querySelectorAll('input[name="paymentMethod"]')).some(checkbox => checkbox.checked);

    if (!selectedSeats || !selectedTime || !paymentMethod) {
        document.getElementById('error-message').style.display = 'block';
        return false;
    }
    return true;
}

    function submitGcashPayment() {
        var fileInput = document.getElementById('paymentImage');
        var file = fileInput.files[0];
        var formData = new FormData();
        formData.append('paymentImage', file);
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'upload.php');
        xhr.onload = function() {
            if (xhr.status === 200) {
                alert('Payment transaction Successful');
                var modal = document.getElementById('gcashModal');
                modal.style.display = 'none';
            } else {
                alert('Error Payment transaction.');
            }
        };
        xhr.send(formData);
    }
</script>

</body>
</html>