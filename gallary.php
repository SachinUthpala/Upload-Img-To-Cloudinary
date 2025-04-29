<?php
// Connect to DB
$db_host = 'localhost';
$db_name = 'Test2';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM images ");
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("DB error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Uploaded Images</title>
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
    }
    td, th {
      padding: 8px;
      border: 1px solid #ccc;
      text-align: center;
    }
    img {
      max-width: 150px;
      height: auto;
    }
    button {
      padding: 6px 12px;
      background: red;
      color: white;
      border: none;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <h2>Uploaded Images</h2>

  <table>
    <thead>
      <tr>
        <th>Image</th>
        <th>URL</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody id="imageTable">
      <?php foreach ($images as $image): ?>
        <tr id="row-<?= $image['id'] ?>">
          <td><img src="<?= $image['image_url'] ?>" alt="Uploaded"></td>
          <td><a href="<?= $image['image_url'] ?>" target="_blank">View</a></td>
          <td>
            <button onclick="deleteImage(<?= $image['id'] ?>)">Delete</button>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <script>
    function deleteImage(id) {
      if (!confirm("Are you sure you want to delete this image?")) return;

      fetch('delete.php?id=' + id)
        .then(res => res.text())
        .then(data => {
          alert(data);
          // Remove row from table
          const row = document.getElementById('row-' + id);
          if (row) row.remove();
        })
        .catch(err => {
          alert("Error deleting image: " + err);
        });
    }
  </script>

</body>
</html>
