<?php
require_once 'includes/db.php';

$endpoint = "/rest/v1/posts?select=*&order=created_at.desc";
$response = callSupabase($endpoint, 'GET');
$posts = $response['data'] ?? [];

include 'includes/header.php';
?>

<div class="hero" style="text-align:center; padding: 40px 0;">
    <h1>Welcome to SupaBlog</h1>
    <p>A simple blog powered by PHP and Supabase</p>
    <br>
    <a href="create_post.php" class="btn" style="font-size: 1.2rem;">+ Post Something!</a>
</div>

<div class="blog-grid">
    <?php foreach($posts as $post): ?>
        <div class="card">
            <?php if(!empty($post['image_url'])): ?>
                <img src="<?php echo $post['image_url']; ?>" alt="Cover">
            <?php endif; ?>
            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
            <small>By <?php echo htmlspecialchars($post['author_email'] ?? 'Guest'); ?> on <?php echo date('M d, Y', strtotime($post['created_at'])); ?></small>
            <p><?php echo substr(htmlspecialchars($post['body']), 0, 100); ?>...</p>
            <div style="display: flex; gap: 10px; margin-top: 10px;">
                <a href="post.php?id=<?php echo $post['id']; ?>" class="btn">Read More</a>
                <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn" style="background:#28a745">Edit</a>
                <a href="delete_post.php?id=<?php echo $post['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this post?')">Delete</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php include 'includes/footer.php'; ?>