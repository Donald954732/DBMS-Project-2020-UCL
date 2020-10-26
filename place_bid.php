<?php

session_start();
$user= $SESSION['UserName'];

if(isset($_POST['bid']))
{$bid= $_POST['bid'];
} else {
die("Bid amount not specified");
}

if(isset($_POST['AuctionID']))
{$auction_id = $_POST['AuctionID']}}



$servername = "localhost";
$username = "root"
$password = "root"
$dbname = "auctions"
 
 
// connecting to db

$connection = mysqli_connect($servername,$username, $password,$dbname)
    or die('Error connecting to MySQL server.' . mysql_error());

//check auction exists and is still open

$query = "SELECT AuctionID FROM auctions WHERE AuctionID = $auction_id AND EndingTime > CURDATE()";
$result = mysqli_query($connection,$query)
    or die("MYSQL query error." . mysql_error());
    
if (mysqli_num_rows($result)<1)
    die("No Valid auction found")
    
    
//Insert new bid
$query = "INSERT INTO bids (UserName, AuctionID, BidPrice, Bidtime, Outcome) "; 
$result = mysqli_query($connection,$query)
    or die("MySQL query error." . mysql_error();
    
    
header("Location: /view_listing.php?id=" . $auction_id);

?>



// Done: Extract $_POST variables, check they're OK, and attempt to make a bid.
// TODO: Notify user of success/failure and redirect/give navigation options.
