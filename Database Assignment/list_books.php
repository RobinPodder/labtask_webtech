<?php
include 'db.php';

// Handle delete request
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM books WHERE book_id = $id");
}

// Fetch all books
$result = $conn->query("SELECT * FROM books ORDER BY book_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book List</title>
</head>
<body>
<h2>All Books</h2>
<p><a href="add_book.php">Add New Book</a></p>

<table border="1" cellpadding="5" cellspacing="0">
<tr>
    <th>ID</th>
    <th>Title</th>
    <th>Author</th>
    <th>Genre</th>
    <th>ISBN</th>
    <th>Total Copies</th>
    <th>Available</th>
    <th>Price</th>
    <th>Actions</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['book_id'] ?></td>
    <td><?= htmlspecialchars($row['title']) ?></td>
    <td><?= htmlspecialchars($row['author']) ?></td>
    <td><?= htmlspecialchars($row['genre']) ?></td>
    <td><?= $row['isbn'] ?></td>
    <td><?= $row['copies_total'] ?></td>
    <td><?= $row['copies_available'] ?></td>
    <td><?= $row['price'] ?></td>
    <td>
        <a href="update_book.php?id=<?= $row['book_id'] ?>">Edit</a> |
        <a href="list_books.php?delete=<?= $row['book_id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
</body>
</html>
