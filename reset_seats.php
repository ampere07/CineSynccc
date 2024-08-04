<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("HTTP/1.1 401 Unauthorized");
    exit("Unauthorized access");
}

require 'vendor/autoload.php';

$uri = "mongodb+srv://cinesync:123456789cinesink@cinesync.72zvsfq.mongodb.net/cinesync?retryWrites=true&w=majority";
$client = new MongoDB\Client($uri);

$collectionSeats = ['SeatsAvailability1', 'SeatsAvailability2', 'SeatsAvailability3', 'SeatsAvailability4'];
$totalModifiedCount = 0;

foreach ($collectionSeats as $collectionName) {
    $collection = $client->cinesync->$collectionName;
    $updateResult = $collection->updateMany([], ['$set' => ['available' => true]]);
    $totalModifiedCount += $updateResult->getModifiedCount();
}

if ($totalModifiedCount > 0) {
    http_response_code(200);
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to update seats', 'details' => 'No documents modified']);
}
?>
