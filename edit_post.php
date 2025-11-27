<?php
require_once 'includes/db.php';
// No authentication check - everyone can edit!

$id = $_GET['id'] ?? 0;
$error = "";

$endpoint = "/rest/v1/posts?id=eq.$id&select=*";
$response = callSupabase($endpoint, 'GET');
$post = $response['data'][0] ?? null;

if (!$post) {
    echo "Post not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $body = htmlspecialchars($_POST['body']);
    
    $updateData = ['title' => $title, 'body' => $body];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploaded = uploadImage($_FILES['image']);
        if ($uploaded) $updateData['image_url'] = $uploaded;
    }

    $updateEndpoint = "/rest/v1/posts?id=eq.$id";
    $result = callSupabase($updateEndpoint, 'PATCH', $updateData); // No token

    if ($result['code'] == 200 || $result['code'] == 204) {
        header("Location: index.php");
        exit;
    } else {
        $error = "Error updating post: " . json_encode($result);
    }
}

include 'includes/header.php';
?>

<h2>Edit Post</h2>
<?php if($error) echo "<p class='error'>$error</p>"; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Title</label>
    <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
    
    <label>Content</label>
    <textarea name="body" rows="10" required><?php echo htmlspecialchars($post['body']); ?></textarea>
    
    <label>Update Image (optional)</label>
    <input type="file" name="image" accept="image/*">
    
    <button type="submit" class="btn">Update Post</button>
    <a href="index.php" class="btn" style="background:#6c757d">Cancel</a>
</form>

<?php include 'includes/footer.php'; ?>