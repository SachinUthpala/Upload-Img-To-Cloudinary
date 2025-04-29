<?php
require 'vendor/autoload.php';

use Cloudinary\Cloudinary;

// === Cloudinary Configuration ===
$cloudinary = new Cloudinary([
    'cloud' => [
        'cloud_name' => 'dbc14iumo',
        'api_key'    => '195745487779913',
        'api_secret' => 'N4LHdwLXmrVdEMTFmDaqVL1YhhA',
    ],
    'url' => ['secure' => true]
]);

// === Database Configuration ===
$db_host = 'localhost';
$db_name = 'Test2';
$db_user = 'root';
$db_pass = '';

// === Validate Input ===
if (!isset($_GET['id'])) {
    die('Missing image ID.');
}

$imageId = (int) $_GET['id'];

try {
    // Connect to DB
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch image public_id from DB
    $stmt = $pdo->prepare("SELECT public_id FROM images WHERE id = :id");
    $stmt->execute(['id' => $imageId]);
    $image = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$image) {
        die('Image not found.');
    }

    $publicId = $image['public_id'];

    // Delete from Cloudinary
    $cloudinary->uploadApi()->destroy($publicId);

    // Delete from DB
    $stmt = $pdo->prepare("DELETE FROM images WHERE id = :id");
    $stmt->execute(['id' => $imageId]);

    echo "Image deleted successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
