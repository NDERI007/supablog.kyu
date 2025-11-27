<?php
require_once 'includes/db.php';
// No authentication check - everyone can delete!

$id = $_GET['id'] ?? 0;

if ($id) {
    $endpoint = "/rest/v1/posts?id=eq.$id";
    callSupabase($endpoint, 'DELETE'); // No token
}

header("Location: index.php");
exit;
?>