<?php
//use different account for different purpose so that they can't do SQL injection attack
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
//echo "Connected successfully";
?>

<?php
//$connectionAddAuction is for creating Auction
$servername = "localhost";
$username = "AuctionUserCreateAuction";
$password = "PasswordAuctionCreateAuctionDBMS2020";
$table = "Auction";

// Create connection
$connectionAddAuction = new mysqli($servername, $username, $password, $table);

// Check connection
if ($connectionAddAuction->connect_error) {
  die("Connection failed: " . $connectionAddAuction->connect_error);
}
//echo "Connected successfully auctionCreate";
?>

<?php
//$connectionAddUser is for creating Auction
$servername = "localhost";
$username = "AuctionUserCreateUser";
$password = "PasswordAuctionCreateUserDBMS2020";
$table = "Auction";

// Create connection
$connectionAddUser = new mysqli($servername, $username, $password, $table);

// Check connection
if ($connectionAddUser->connect_error) {
  die("Connection failed: " . $connectionAddUser->connect_error);
}
//echo "Connected successfully userCreate";
?>

<?php
//$connectionAddWatchlist is for creating Auction
$servername = "localhost";
$username = "AuctionUserWatchList";
$password = "PasswordAuctionWatchListDBMS2020";
$table = "Auction";

// Create connection
$connectionWatchlist = new mysqli($servername, $username, $password, $table);

// Check connection
if ($connectionWatchlist->connect_error) {
  die("Connection failed: " . $connectionWatchlist->connect_error);
}
//echo "Connected successfully Watchlist";
?>

<?php
//$connectionAddWatchlist is for creating Auction
$servername = "localhost";
$username = "AuctionUserCreateBids";
$password = "PasswordAuctionCreateBidsDBMS2020";
$table = "Auction";

// Create connection
$connectionAddBids = new mysqli($servername, $username, $password, $table);

// Check connection
if ($connectionAddBids->connect_error) {
  die("Connection failed: " . $connectionAddBids->connect_error);
}
//echo "Connected successfully WatchlistCreate";
?>

<?php
//$connectionAddWatchlist is for creating Auction
$servername = "localhost";
$username = "AuctionUserUpdateOutcome";
$password = "PasswordAuctionUpdateOutcomeDBMS2020";
$table = "Auction";

// Create connection
$connectionUpdateOutcome = new mysqli($servername, $username, $password, $table);

// Check connection
if ($connectionUpdateOutcome->connect_error) {
  die("Connection failed: " . $connectionUpdateOutcome->connect_error);
}
//echo "Connected successfully Outcome Update";
?>