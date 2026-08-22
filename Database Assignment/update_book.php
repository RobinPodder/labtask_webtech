<?php
include 'db.php';

if (!isset($_GET['id'])) {
    die("No book ID provided.");
}

$id = intval($_GET['id']);
$msg = "";

// Fetch existing data
$res = $conn->query("SELECT * FROM books WHERE book_id = $id");
if ($res->num_rows == 0) die("Book not found.");
$book = $res->fetch_assoc();

// Handle update form
if (isset($_POST['update'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $isbn = $_POST['isbn'];
    $publisher = $_POST['publisher'];
    $publish_year = $_POST['publish_year'];
    $copies_total = $_POST['copies_total'];
    $copies_available = $_POST['copies_available'];
    $price = $_POST['price'];
    $added_date = $_POST['added_date'];

    $stmt = $conn->prepare("UPDATE books SET title=?, author=?, genre=?, isbn=?, publisher=?, publish_year=?, copies_total=?, copies_available=?, price=?, added_date=? WHERE book_id=?");
    $stmt->bind_param("sssssiiidsi", $title, $author, $genre, $isbn, $publisher, $publish_year, $copies_total, $copies_available, $price, $added_date, $id);
    if ($stmt->execute()) $msg = "Updated successfully!";
    else $msg = "Error: " . $stmt->error;
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Book</title>
</head>
<body>
<h2>Update Book</h2>
<?php if($msg) echo "<p>$msg</p>"; ?>

<form method="post">
    Title: <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required><br>
    Author: <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" required><br>
    Genre: <input type="text" name="genre" value="<?= htmlspecialchars($book['genre']) ?>" required><br>
    ISBN: <input type="text" name="isbn" value="<?= $book['isbn'] ?>"><br>
    Publisher: <input type="text" name="publisher" value="<?= htmlspecialchars($book['publisher']) ?>"><br>
    Publish Year: <input type="number" name="publish_year" value="<?= $book['publish_year'] ?>"><br>
    Total Copies: <input type="number" name="copies_total" value="<?= $book['copies_total'] ?>" required><br>
    Available Copies: <input type="number" name="copies_available" value="<?= $book['copies_available'] ?>" required><br>
    Price: <input type="number" step="0.01" name="price" value="<?= $book['price'] ?>" required><br>
    Added Date: <input type="date" name="added_date" value="<?= $book['added_date'] ?>" required><br>
    <button type="submit" name="update">Update</button>
</form>

<p><a href="list_books.php">Back to Book List</a></p>
</body>
</html>
