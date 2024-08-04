<?php
session_start();

// Check if the user is logged in and has a user ID
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if (!$user_id) {
    echo "User session not found.";
    exit;
}

// Handle file upload
if (isset($_FILES['paymentImage'])) {
    $file = $_FILES['paymentImage'];

    // Check if there was no error during upload
    if ($file['error'] === UPLOAD_ERR_OK) {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFilename = $user_id . '_' . uniqid() . '.' . $extension;

        $uploadDir = 'payments/';
        $uploadPath = $uploadDir . $newFilename;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            echo "File uploaded successfully.";
            // Optionally, you can store the file path in your database or perform any other actions
        } else {
            echo "Error moving uploaded file.";
        }
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "No file uploaded.";
}
?>