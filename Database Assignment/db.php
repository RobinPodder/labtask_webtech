<?php
$host = "localhost";
$user = "root";
$password = "";
$dbName = "library_book_management_db";

// Connect to MySQL
$conn = mysqli_connect($host, $user, $password);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbName";
if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
mysqli_select_db($conn, $dbName);


//CREATE books TABLE

$bookTable = "CREATE TABLE IF NOT EXISTS books (
    book_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    author VARCHAR(100) NOT NULL,
    genre VARCHAR(50) NOT NULL,
    isbn VARCHAR(20) UNIQUE,
    publisher VARCHAR(100),
    publish_year YEAR,
    copies_total INT NOT NULL,
    copies_available INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    added_date DATE NOT NULL,
    status ENUM('Available','Unavailable') DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($bookTable) !== TRUE) {
    die("Error creating books table: " . $conn->error);
}


//INSERT DATA INTO books

$insertBooks = "INSERT INTO books 
(title, author, genre, isbn, publisher, publish_year, copies_total, copies_available, price, added_date)
VALUES
('The Silent Patient', 'Alex Michaelides', 'Thriller', '9781250301697', 'Celadon Books', 2019, 5, 5, 650, '2023-02-10'),
('Sapiens', 'Yuval Noah Harari', 'Non-Fiction', '9780062316097', 'Harper', 2015, 4, 3, 850, '2022-11-05'),
('The Alchemist', 'Paulo Coelho', 'Fiction', '9780061122415', 'HarperOne', 1993, 6, 6, 500, '2023-05-18'),
('Clean Code', 'Robert C. Martin', 'Technology', '9780132350884', 'Prentice Hall', 2008, 3, 2, 1200, '2024-01-22')
";

$conn->query($insertBooks);

echo "Database, table, and sample data created successfully!";
?>
