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

// === Handle Upload ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $tmpFilePath = $_FILES['image']['tmp_name'];

    try {
        // Upload to Cloudinary
        $uploadResult = $cloudinary->uploadApi()->upload($tmpFilePath);
        $imageUrl = $uploadResult['secure_url'];
        $publicId = $uploadResult['public_id']; // ✅ assign public ID

        // Connect to MySQL and save URL + public ID
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("INSERT INTO images (image_url, public_id) VALUES (:url, :pid)");
        $stmt->execute([
            'url' => $imageUrl,
            'pid' => $publicId
        ]);

        // Return JSON response
        echo json_encode([
            'success' => true,
            'url' => $imageUrl
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'No image uploaded.'
    ]);
}
?>
