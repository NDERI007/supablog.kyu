<?php
require_once 'includes/db.php';
$id = $_GET['id'] ?? 0;
// Supabase filter: id=eq.X
$endpoint = "/rest/v1/posts?id=eq.$id&select=*";
$response = callSupabase($endpoint, 'GET');
$post = $response['data'][0] ?? null;

include 'includes/header.php';
?>

<?php if($post): ?>
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <?php if(!empty($post['image_url'])): ?>
        <img src="<?php echo $post['image_url']; ?>" style="max-width:100%; border-radius:8px;">
    <?php endif; ?>
    <p><strong>Author:</strong> <?php echo $post['author_email']; ?></p>
    <hr>
    <div class="content">
        <?php echo nl2br(htmlspecialchars($post['body'])); ?>
    </div>
    <br>
    <a href="index.php" class="btn">Back to Home</a>
<?php else: ?>
    <h2>Post not found</h2>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>