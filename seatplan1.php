<?php
session_start();
require 'vendor/autoload.php';
$uri = "mongodb+srv://cinesync:123456789cinesink@cinesync.72zvsfq.mongodb.net/";
$client = new MongoDB\Client($uri);
$collection = $client->cinesync->SeatsAvailability1;

$availableSeats = [];

// Fetch availability status of seats
$seatRanges = [
    'L' => [1, 20],
    'K' => [21, 40],
    'J' => [41, 60],
    'I' => [61, 80],
    'H' => [81, 100],
    'G' => [101, 120],
    'F' => [121, 140],
    'E' => [141, 156],
    'D' => [157, 170],
    'C' => [171, 182],
    'B' => [183, 192],
    'A' => [193, 200]
];

foreach ($seatRanges as $letter => $range) {
    list($start, $end) = $range;
    for ($i = $start; $i <= $end; $i++) {
        $seatNumber = $letter . '-' . $i;
        $seatInfo = $collection->findOne(['seat_number' => $seatNumber]);

        // Check if seatInfo exists and is available
        if ($seatInfo && $seatInfo['available'] === true) {
            $availableSeats[$seatNumber] = true;
        } else {
            $availableSeats[$seatNumber] = false;
        }
    }
}

// Store available seats in session
$_SESSION['availableSeats'] = $availableSeats;

$movieTitle = $_POST['movieTitle'] ?? $_SESSION['movieTitle'] ?? '';
$moviePicture = $_POST['moviePicture'] ?? $_SESSION['moviePicture'] ?? '';
$movieDate = $_POST['movieDate'] ?? $_SESSION['movieDate'] ?? '';

if (empty($movieTitle) || empty($moviePicture) || empty($movieDate)) {
    header("Location: pickmovie.php");
    exit;
}

if (isset($_POST['selectedSeats'])) {
    $_SESSION['selectedSeats'] = $_POST['selectedSeats'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seat Plan</title>
    <link rel="stylesheet" href="seatplan1.css">

    <style>
        .seatplan button[disabled] {
        background-color: #B8A9C9;
        color: #ffffff; 
        cursor: not-allowed;
    }
    .seatplan1 button[disabled],
        .seatplan2 button[disabled],
        .seatplan3 button[disabled],
        .seatplan4 button[disabled],
        .seatplan5 button[disabled] {
            background-color: #B8A9C9;
            color: #ffffff;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="screen-label">SCREEN</div>

    <div class="container">
        <hr class="horizontal-line">
        <div class="vertical-line"></div>
        <div class="vertical-line1"></div>
        <div class="vertical-text">
        LKJIHGF
        </div>
        <div class="vertical-text1">
        LKJIHGF
        </div>
        <div class="vertical-text2">
        EDCBA
        </div>
        <div class="vertical-text3">
        EDCBA
        </div>
    </div>

    <div class="seats">
        <!-- First Row -->
        <?php for ($i = 1; $i <= 20; $i++) { 
            $seatNumber = 'L-' . $i;
            $disabled = isset($availableSeats[$seatNumber]) && !$availableSeats[$seatNumber] ? 'disabled' : '';
        ?>
            <div class="seatplan <?= $disabled ?>" id="seat<?= $i ?>" onclick="selectSeat('<?= $seatNumber ?>')">
                <button <?= $disabled ?>><?= $i ?></button>
            </div>
        <?php } ?>
    </div>

    <div class="seats1">
        <!-- Second Row -->
        <?php for ($i = 21; $i <= 40; $i++) {  
            $seatNumber = 'K-' . $i;
            $disabled = isset($availableSeats[$seatNumber]) && !$availableSeats[$seatNumber] ? 'disabled' : '';
        ?>
            <div class="seatplan <?= $disabled ?>" id="seat<?= $i ?>" onclick="selectSeat('<?= $seatNumber ?>')">
                <button <?= $disabled ?>><?= $i ?></button>
            </div>
        <?php } ?>
    </div>

    <div class="seats2">
        <!-- Third Row -->
        <?php for ($i = 41; $i <= 60; $i++) {  
            $seatNumber = 'J-' . $i;
            $disabled = isset($availableSeats[$seatNumber]) && !$availableSeats[$seatNumber] ? 'disabled' : '';
        ?>
            <div class="seatplan <?= $disabled ?>" id="seat<?= $i ?>" onclick="selectSeat('<?= $seatNumber ?>')">
                <button <?= $disabled ?>><?= $i ?></button>
            </div>
        <?php } ?>
    </div>

    <div class="seats3">
        <!-- Fourth Row -->
        <?php for ($i = 61; $i <= 80; $i++) {  
            $seatNumber = 'I-' . $i;
            $disabled = isset($availableSeats[$seatNumber]) && !$availableSeats[$seatNumber] ? 'disabled' : '';
        ?>
            <div class="seatplan <?= $disabled ?>" id="seat<?= $i ?>" onclick="selectSeat('<?= $seatNumber ?>')">
                <button <?= $disabled ?>><?= $i ?></button>
            </div>
        <?php } ?>
    </div>

    <div class="seats4">
        <!-- Fifth Row -->
        <?php for ($i = 81; $i <= 100; $i++) {  
            $seatNumber = 'H-' . $i;
            $disabled = isset($availableSeats[$seatNumber]) && !$availableSeats[$seatNumber] ? 'disabled' : '';
        ?>
            <div class="seatplan <?= $disabled ?>" id="seat<?= $i ?>" onclick="selectSeat('<?= $seatNumber ?>')">
                <button <?= $disabled ?>><?= $i ?></button>
            </div>
        <?php } ?>
    </div>

    <div class="seats5">
        <!-- Sixth Row -->
        <?php for ($i = 101; $i <= 120; $i++) {  
            $seatNumber = 'G-' . $i;
            $disabled = isset($availableSeats[$seatNumber]) && !$availableSeats[$seatNumber] ? 'disabled' : '';
        ?>
            <div class="seatplan <?= $disabled ?>" id="seat<?= $i ?>" onclick="selectSeat('<?= $seatNumber ?>')">
                <button <?= $disabled ?>><?= $i ?></button>
            </div>
        <?php } ?>
    </div>
    <div class="seats6">
        <!-- Sixth Row -->
        <?php for ($i = 121; $i <= 140; $i++) {  
            $seatNumber = 'F-' . $i;
            $disabled = isset($availableSeats[$seatNumber]) && !$availableSeats[$seatNumber] ? 'disabled' : '';
        ?>
            <div class="seatplan <?= $disabled ?>" id="seat<?= $i ?>" onclick="selectSeat('<?= $seatNumber ?>')">
                <button <?= $disabled ?>><?= $i ?></button>
            </div>
        <?php } ?>
    </div>
    <div class="seats7">
        <!-- Sixth Row -->
        <?php for ($i = 141; $i <= 156; $i++) {  
            $seatNumber = 'E-' . $i;
            $disabled = isset($availableSeats[$seatNumber]) && !$availableSeats[$seatNumber] ? 'disabled' : '';
        ?>
            <div class="seatplan1 <?= $disabled ?>" id="seat<?= $i ?>" onclick="selectSeat('<?= $seatNumber ?>')">
                <button <?= $disabled ?>><?= $i ?></button>
            </div>
        <?php } ?>
    </div>
    <div class="seats8">
        <!-- Sixth Row -->
        <?php for ($i = 157; $i <= 170; $i++) {  
            $seatNumber = 'D-' . $i;
            $disabled = isset($availableSeats[$seatNumber]) && !$availableSeats[$seatNumber] ? 'disabled' : '';
        ?>
            <div class="seatplan2 <?= $disabled ?>" id="seat<?= $i ?>" onclick="selectSeat('<?= $seatNumber ?>')">
                <button <?= $disabled ?>><?= $i ?></button>
            </div>
        <?php } ?>
    </div>
    <div class="seats9">
        <!-- Sixth Row -->
        <?php for ($i = 171; $i <= 182; $i++) {  
            $seatNumber = 'C-' . $i;
            $disabled = isset($availableSeats[$seatNumber]) && !$availableSeats[$seatNumber] ? 'disabled' : '';
        ?>
            <div class="seatplan3 <?= $disabled ?>" id="seat<?= $i ?>" onclick="selectSeat('<?= $seatNumber ?>')">
                <button <?= $disabled ?>><?= $i ?></button>
            </div>
        <?php } ?>
    </div>
    <div class="seats10">
        <!-- Sixth Row -->
        <?php for ($i = 183; $i <= 192; $i++) {  
            $seatNumber = 'B-' . $i;
            $disabled = isset($availableSeats[$seatNumber]) && !$availableSeats[$seatNumber] ? 'disabled' : '';
        ?>
            <div class="seatplan4 <?= $disabled ?>" id="seat<?= $i ?>" onclick="selectSeat('<?= $seatNumber ?>')">
                <button <?= $disabled ?>><?= $i ?></button>
            </div>
        <?php } ?>
    </div>
    <div class="seats11">
        <!-- Sixth Row -->
        <?php for ($i = 193; $i <= 200; $i++) {  
            $seatNumber = 'A-' . $i;
            $disabled = isset($availableSeats[$seatNumber]) && !$availableSeats[$seatNumber] ? 'disabled' : '';
        ?>
            <div class="seatplan5 <?= $disabled ?>" id="seat<?= $i ?>" onclick="selectSeat('<?= $seatNumber ?>')">
                <button <?= $disabled ?>><?= $i ?></button>
            </div>
        <?php } ?>
    </div>

    <div class="legend">
        <div class="legend-item">
            <span class="seat taken"></span> Seats taken
        </div>
        <div class="legend-item">
            <span class="seat selected"></span> Seats selected
        </div>
        <div class="legend-item">
            <span class="seat available"></span> Seats available
        </div>
    </div>
    
    <form id="seatForm" method="post" action="buymovie.php">
        <input type="hidden" name="movieTitle" value="<?php echo htmlspecialchars($movieTitle);?>">
        <input type="hidden" name="moviePicture" value="<?php echo htmlspecialchars($moviePicture);?>">
        <input type="hidden" name="movieDate" value="<?php echo htmlspecialchars($movieDate);?>">
        <input type="hidden" id="selectedQuantity" name="selectedQuantity" value="0">
        <input type="hidden" id="selectedSeats" name="selectedSeats" value="">
        <input type="submit" class="next" value="Submit">
    </form>

    <script>
         let selectedSeats = [];

const selectSeat = (seat) => {
    if (selectedSeats.includes(seat)) {
        const index = selectedSeats.indexOf(seat);
        selectedSeats.splice(index, 1);
    } else {
        selectedSeats.push(seat);
    }
    
    const selectedSeatsInput = document.getElementById('selectedSeats');
    selectedSeatsInput.value = selectedSeats.join(',');
    
    const selectedQuantityInput = document.getElementById('selectedQuantity');
    selectedQuantityInput.value = selectedSeats.length;
};

    // Add event listeners and disable unavailable seats
    document.addEventListener('DOMContentLoaded', () => {
        const seatplans = document.querySelectorAll('.seatplan button');
        seatplans.forEach(button => {
            const seatId = button.parentElement.id; // Assuming id is like 'seatL-1', 'seatK-21', ...

            if (!availableSeats[seatId]) {
                button.disabled = true;
                button.parentElement.classList.add('unavailable');
            }

            button.addEventListener('click', () => {
                if (button.disabled) return;

                button.classList.toggle('clicked');
                selectSeat(seatId);
            });
        });
    });

        const seatplans = document.querySelectorAll('.seats .seatplan button, .seats1 .seatplan1 button, .seats2 .seatplan2 button, .seats3 .seatplan3 button, .seats4 .seatplan4 button, .seats5 .seatplan5 button');
            seatplans.forEach((button, index) => {
                button.innerText = index + 1;
            });

            const seatPositions = [
                245, 280, 405, 445, 485, 525, 565, 605, 645, 685, 
                725, 765, 805, 845, 885, 925, 965, 1005, 1125, 1165
            ];

            const seats = document.querySelectorAll('.seatplan');
            seats.forEach((seat, index) => {
                const row = Math.floor(index / 20);
                const leftIndex = index % 20;
                const top = row * 35.8 + 42.6;
                const left = seatPositions[leftIndex];
                
                seat.style.left = `${left}px`;
                seat.style.top = `${top}px`;
            });

            const seatPositions1 = [
                405, 445, 485, 525, 565, 605, 645, 685, 
                725, 765, 805, 845, 885, 925, 965, 1005
            ];

            const seats1 = document.querySelectorAll('.seatplan1');
            seats1.forEach((seat, index) => {
                const row = Math.floor(index / 16);
                const leftIndex = index % 16;
                const top = row * 345.8 + 352.6;
                const left = seatPositions1[leftIndex];
                
                seat.style.left = `${left}px`;
                seat.style.top = `${top}px`;
            });

            const seatPositions2 = [
                445, 485, 525, 565, 605, 645, 685, 
                725, 765, 805, 845, 885, 925, 965, 
            ];

            const seats2 = document.querySelectorAll('.seatplan2');
            seats2.forEach((seat, index) => {
                const row = Math.floor(index / 14);
                const leftIndex = index % 14;
                const top = row * 380.8 + 387.6; 
                const left = seatPositions2[leftIndex];
                
                seat.style.left = `${left}px`;
                seat.style.top = `${top}px`;
            });

            const seatPositions3 = [
                485, 525, 565, 605, 645, 685, 
                725, 765, 805, 845, 885, 925, 
            ];

            const seats3 = document.querySelectorAll('.seatplan3');
            seats3.forEach((seat, index) => {
                const row = Math.floor(index / 12);
                const leftIndex = index % 12;
                const top = row * 415.8 + 422.6;
                const left = seatPositions3[leftIndex];
                
                seat.style.left = `${left}px`;
                seat.style.top = `${top}px`;
            });

            const seatPositions4 = [
                525, 565, 605, 645, 685, 
                725, 765, 805, 845, 885,
            ];

            const seats4 = document.querySelectorAll('.seatplan4');
            seats4.forEach((seat, index) => {
                const row = Math.floor(index / 10);
                const leftIndex = index % 10;
                const top = row * 446 + 458;
                const left = seatPositions4[leftIndex];
                
                seat.style.left = `${left}px`;
                seat.style.top = `${top}px`;
            });

            const seatPositions5 = [
                565, 605, 645, 685, 
                725, 765, 805, 845
            ];

            const seats5 = document.querySelectorAll('.seatplan5');
            seats5.forEach((seat, index) => {
                const row = Math.floor(index / 8);
                const leftIndex = index % 8;
                const top = row * 485.8 + 492.6;
                const left = seatPositions5[leftIndex];
                
                seat.style.left = `${left}px`;
                seat.style.top = `${top}px`;
            });


            seats.forEach(seat => {
                const button = seat.querySelector('button');
                
                button.addEventListener('click', () => {
                    button.classList.toggle('clicked');
                });
            });

            seats1.forEach(seat => {
                const button = seat.querySelector('button');
                
                button.addEventListener('click', () => {
                    button.classList.toggle('clicked');
                });
            });

            seats2.forEach(seat => {
                const button= seat.querySelector('button');
                
                button.addEventListener('click', () => {
                    button.classList.toggle('clicked');
                });
            });

            seats3.forEach(seat => {
                const button = seat.querySelector('button');
                
                button.addEventListener('click', () => {
                    button.classList.toggle('clicked');
                });
            });
            seats4.forEach(seat => {
                const button = seat.querySelector('button');
                
                button.addEventListener('click', () => {
                    button.classList.toggle('clicked');
                });
            });
            seats5.forEach(seat => {
                const button = seat.querySelector('button');
                
                button.addEventListener('click', () => {
                    button.classList.toggle('clicked');
                });
            });
    </script>
</body>
</html>