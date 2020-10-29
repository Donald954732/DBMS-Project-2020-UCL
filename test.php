<?php
$servername = "localhost";
$username = "AuctionUserView";
$password = "PasswordAuctionViewDBMS2020";
$table = "Auction";

// Create connection
$connectionView = new mysqli($servername, $username, $password, $table);

// Check connection
if ($connectionView->connect_error) {
  die("Connection failed: " . $connectionView->connect_error);
}
echo "Connected successfully";
?>