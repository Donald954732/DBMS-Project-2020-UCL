<?php include_once("header.php")?>
<?php include 'database.php'; ?>
<?php require("utilities.php")?>


<?php 
  $username = $_SESSION['username'];
  if(isset($_POST["bid"])){
    $bid = $_POST['bid'];
    $item_id = $_POST["itemid"];
    //check user exists and is buyer 
    if (isset($_SESSION['username']) != true){
      echo "<script language= javascript>alert('Log in to view your bids');history.go(-1);</script>";
    }
    else if ($_SESSION['account_type'] != 'buyer'){
      echo "<script language= javascript>alert('log into buyer sccount to view your bids');history.go(-1);</script>";
    }
    else {
//check auction exists and is running 
      $querryAuction = <<<QUERRYTEXT
      SELECT
        a.AuctionID,
        a.ItemName,
        a.StartingPrice,
        MAX(b.BidPrice) AS 'bidPrice',
        IF(
          MAX(bidPrice) IS NULL,
          a.StartingPrice,
          MAX(bidPrice)
        ) AS 'CurrentPrice'
      FROM
        auctions a
        LEFT JOIN bids b ON a.AuctionID = b.AuctionID
      WHERE
          a.AuctionID = {$item_id}
          AND (a.EndingTime - CURRENT_TIMESTAMP) > 0
      GROUP BY
        a.AuctionID,
        a.ItemName,
        a.ItemDescription,
        a.StartingPrice,
        a.EndingTime
      QUERRYTEXT;
      $resultAuction = mysqli_query($connectionView,$querryAuction);
      if ($resultAuction -> num_rows == 0){
        echo "<script language= javascript>alert('This auction does not exist or ended');history.go(-1);</script>";
      }
      else {
        $rowAuction = mysqli_fetch_array($resultAuction);
        //Insert new bid
        $query = "INSERT INTO bids (UserName, AuctionID, BidPrice, Bidtime) VALUES ('$username', $item_id, $bid, NOW())"; 
        if ($rowAuction['CurrentPrice'] >= $bid){
          echo "<script language= javascript>alert('Price below current bid');history.go(-1);</script>";
        }
        else if($result = mysqli_query($connectionAddBids,$query)){
          echo "<script language= javascript>alert('Bid placed successfully');history.go(-1);</script>";
        }
        else {
          echo "<script language= javascript>alert('Bid could not be placed');history.go(-1);</script>";
        }
      }
    }  
  }
?>
<?php  
  header("refresh:2;url=listing.php?item_id=" . $item_id);
// could add navigation options later

?>