<?php
session_start();

require 'vendor/autoload.php';

$uri = "mongodb+srv://cinesync:123456789cinesink@cinesync.72zvsfq.mongodb.net/cinesync?retryWrites=true&w=majority";
$client = new MongoDB\Client($uri);
$collection = $client->cinesync->movies;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Fetch movie data
$document = $collection->findOne();

$nowShowingMovies = [];
$upcomingMovies = [];

if (isset($document['nowshowing'])) {
    $nowShowingMovies = $document['nowshowing'];
}
if (isset($document['upcoming'])) {
    $upcomingMovies = $document['upcoming'];
}

if (isset($_GET['title'], $_GET['picture'], $_GET['date'])) {
    $_SESSION['movieTitle'] = $_GET['title'];
    $_SESSION['moviePicture'] = $_GET['picture'];
    $_SESSION['movieDate'] = $_GET['date'];
    header("Location: buymovie.php"); 
    exit;
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PickMovie</title>
    <link rel="stylesheet" href="pickmovie.css">
</head>
<body>
        <div class="dotted-line1"></div>
        <div class="dotted-line2"></div>
        <div class="dotted-line3"></div>
        <div class="dotted-line4"></div>
        
        <div class="bgBorder"> 
            </div> 
            <img class="logo" src="BuyMovie\8.png" alt="Logo" draggable="false">
            <h3 class="welcome">Welcome!  <?php echo $user_id; ?><br>to SM Molino Branch</h3>
            <button class="user-icon-button">
                <div class="user-icon">
                    <div class="head"></div>
                    <div class="bodyicon"></div>
                </div>
            </button>
              
        <h2 class="now-showing">NOW SHOWING</h2>
        <h2 class="up-coming">UP COMING</h2>
        <div class="nowshowing-container">
        <div class="container">   
            <div class="nowshowing">
                <?php foreach ($nowShowingMovies as $movie): ?>
                    <div class="movie">
                    <a href="BuyMovie.php?title=<?php echo urlencode($movie['title']); ?>&picture=<?php echo urlencode($movie['picture']); ?>&date=<?php echo urlencode($movie['date']); ?>" class="buyMovie">
                        <img class="movie-image" src="<?php echo $movie['picture']; ?>" alt="movie">
                    </a>
                        <h3 class="title"><?php echo $movie['title']; ?></h3>
                        <h4 class="date"><?php echo $movie['date']; ?></h4>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="upcoming-container">
            <div class="upcoming">
                <?php foreach ($upcomingMovies as $movie): ?>
                    <div class="movie1">
                          <img class="movie-image1" src="<?php echo $movie['picture']; ?>" alt="movie">
                        <h3 class="title1"><?php echo $movie['title']; ?></h3>
                        <h4 class="date1"><?php echo $movie['date']; ?></h4>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var userProfileButton = document.querySelector('.user-icon-button');
            userProfileButton.addEventListener('click', function() {
                var userId = "<?php echo $user_id; ?>"; // PHP variable in JavaScript
                if (userId) {
                    window.location.href = 'userProfile.php?userId=' + userId;
                } else {
                    alert('User not logged in');
                }
            });
        });
    </script>
</body>
</html>
