<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $body = htmlspecialchars($_POST['body']);
    $imageUrl = "";

    // Handle Image Upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploaded = uploadImage($_FILES['image']);
        if ($uploaded) $imageUrl = $uploaded;
    }

    $postData = [
        'title' => $title,
        'body' => $body,
        'image_url' => $imageUrl
    ];

    $result = callSupabase('/rest/v1/posts', 'POST', $postData);

    if ($result['code'] == 201) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Error creating post. Response: " . print_r($result, true);
        error_log($error);
    }
}

include 'includes/header.php';
?>

<h2>Create New Post</h2>

<?php if (isset($error)): ?>
    <div style="color: red; padding: 10px; background: #ffe6e6; margin-bottom: 20px;">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Title</label>
    <input type="text" name="title" required>
    
    <label>Content</label>
    <textarea name="body" rows="10" required></textarea>
    
    <label>Cover Image</label>
    <input type="file" name="image" accept="image/*">
    
    <button type="submit" class="btn">Publish</button>
</form>

<?php include 'includes/footer.php'; ?>