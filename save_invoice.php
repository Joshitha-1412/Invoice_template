<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "invoice_system_sample";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode($_POST['invoice_data'], true);

// Save to invoices table
$stmt = $conn->prepare("INSERT INTO invoices (invoice_id, invoice_date, due_date, client_name, client_address, subtotal, discount, tax, transport, total, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssssddds",
    $data['invoice_id'],
    $data['invoice_date'],
    $data['due_date'],
    $data['client_name'],
    $data['client_address'],
    $data['subtotal'],
    $data['discount'],
    $data['tax'],
    $data['transport'],
    $data['total'],
    $data['message']
);
$stmt->execute();
$stmt->close();

// Save each item
foreach ($data['items'] as $item) {
    $stmt = $conn->prepare("INSERT INTO invoice_items (invoice_id, description, qty, rate, amount) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssidd", $data['invoice_id'], $item['description'], $item['qty'], $item['rate'], $item['amount']);
    $stmt->execute();
    $stmt->close();
}

echo "Invoice Saved Successfully!";
$conn->close();

header("Location: homepage.html"); 
exit();
?>
