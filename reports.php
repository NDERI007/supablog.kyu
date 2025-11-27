<?php
require_once 'includes/db.php';
// No authentication - everyone can view reports

$endpoint = "/rest/v1/posts?select=id,created_at,author_email";
$response = callSupabase($endpoint, 'GET');
$allPosts = $response['data'] ?? [];

// Posts Per Month
$postsPerMonth = [];
foreach ($allPosts as $post) {
    $month = date('Y-F', strtotime($post['created_at']));
    if (!isset($postsPerMonth[$month])) $postsPerMonth[$month] = 0;
    $postsPerMonth[$month]++;
}

// Active Authors
$authorCounts = [];
foreach ($allPosts as $post) {
    $auth = $post['author_email'] ?? 'Unknown';
    if (!isset($authorCounts[$auth])) $authorCounts[$auth] = 0;
    $authorCounts[$auth]++;
}
arsort($authorCounts);

include 'includes/header.php';
?>

<h1>System Reports</h1>

<div class="card">
    <h3>1. Posts Per Month</h3>
    <table>
        <tr><th>Month</th><th>Count</th></tr>
        <?php foreach($postsPerMonth as $month => $count): ?>
            <tr><td><?php echo $month; ?></td><td><?php echo $count; ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="card">
    <h3>2. Most Active Authors</h3>
    <table>
        <tr><th>Author Name</th><th>Posts Created</th></tr>
        <?php foreach($authorCounts as $author => $count): ?>
            <tr><td><?php echo htmlspecialchars($author); ?></td><td><?php echo $count; ?></td></tr>
        <?php endforeach; ?>
    </table>
</div>

<a href="index.php" class="btn">Back to Home</a>

<?php include 'includes/footer.php'; ?>