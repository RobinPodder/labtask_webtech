<?php
include 'db.php';

// Handle form submission
if (isset($_POST['mysubmit'])) {
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

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO books 
        (title, author, genre, isbn, publisher, publish_year, copies_total, copies_available, price, added_date) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssiiids", $title, $author, $genre, $isbn, $publisher, $publish_year, $copies_total, $copies_available, $price, $added_date);

    if ($stmt->execute()) {
        $message = "New book added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Book</title>
</head>
<body>
<h2>Add New Book</h2>

<?php if (isset($message)) echo "<p>$message</p>"; ?>

<form method="post">
    Title: <input type="text" name="title" required><br>
    Author: <input type="text" name="author" required><br>
    Genre: <input type="text" name="genre" required><br>
    ISBN: <input type="text" name="isbn"><br>
    Publisher: <input type="text" name="publisher"><br>
    Publish Year: <input type="number" name="publish_year"><br>
    Total Copies: <input type="number" name="copies_total" required><br>
    Available Copies: <input type="number" name="copies_available" required><br>
    Price: <input type="number" step="0.01" name="price" required><br>
    Added Date: <input type="date" name="added_date" required><br>
    <button type="submit" name="mysubmit">Add Book</button>
</form>

<p><a href="list_books.php">View All Books</a></p>
</body>
</html>
