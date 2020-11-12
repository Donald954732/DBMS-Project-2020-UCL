<?php include_once("header.php")?>
<?php include 'database.php'; ?>
<?php require("utilities.php")?>


<?php ////
  $username = $_SESSION['username'];
  if(isset($_POST["bid"])){
    $bid = $_POST['bid'];
    $item_id = $_POST["itemid"];
    }
?>
<?php  

  //check user exists and is buyer 
  $queryusertype = "SELECT UserGroup, username FROM Users where username = '$username'";
  $resultusertype = mysqli_query($connectionView, $queryusertype)
    or die("MySQL query error." . mysql_error($connectionview));
  $UserInfo = mysqli_fetch_array($resultusertype) ;
  $UserType = $UserInfo['UserGroup'];

  if (mysqli_num_rows($resultusertype)<1){
    die("Log in to place bid");
  }
  if ($UserType != 'Buyer'){
    die("Log into buyer account to place bid");
  }

  //check auction exists and is running 
  $query = "SELECT AuctionID FROM auctions WHERE AuctionID = $item_id AND EndingTime > CURDATE()";
  $result = mysqli_query($connectionView,$query)
    or die("MySQL query error." . mysql_error($connectionview));
  if (mysqli_num_rows($result)<1)
    die("Auction finished");
  
  //Insert new bid
  $query = "INSERT INTO bids (UserName, AuctionID, BidPrice, Bidtime, Outcome) VALUES ('$username', $item_id, $bid, NOW(), 'Pending')"; 
  $result = mysqli_query($connectionView,$query)
    or die("MySQL query error." . mysql_error($connectionview));

  mysqli_close($connection);
  header("refresh:2;url=listing.php?item_id=" . $item_id);

?>
