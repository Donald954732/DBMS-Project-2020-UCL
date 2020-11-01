<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php require("database.php")?>

<div class="container">

<h2 class="my-3">Recommendations for you</h2>

<?php
  // This page is for showing a buyer recommended items based on their bid 
  // history. It will be pretty similar to browse.php, except there is no 
  // search bar. This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  
  // Done - Donald: Check user's credentials (cookie/session).
  if (!isset($_SESSION['logged_in'])) {
    echo('<div class="text-center">You are not logged in!</div>');
  }
  else {
      if ($_SESSION['account_type'] == 'buyer'){
      // Done - Donad: Perform a query to pull up auctions they might be interested in.
      //echo $_SESSION['username'];
      $recommendQuerry = "SELECT a.AuctionID, COUNT(b.UserName) AS 'RecommandationChance', a.ItemName, a.ItemDescription, a.StartingPrice, a.EndingTime, ".
      "COUNT(b.BidID) AS 'CountBids', MAX(b.BidPrice) AS 'bidPrice', IF(MAX(bidPrice) IS NULL, a.StartingPrice, MAX(bidPrice))  AS 'CurrentPrice', a.StartingPrice ".
      "FROM auctions a LEFT JOIN bids b ON a.AuctionID = b.AuctionID ".
      "WHERE b.UserName IN (SELECT Username FROM bids WHERE auctionID IN (SELECT AuctionID FROM bids WHERE UserName = '".
      $_SESSION['username']."' ) AND NOT UserName = '".$_SESSION['username']."' ) AND (a.EndingTime - CURRENT_TIMESTAMP) > 0 GROUP BY a.AuctionID, a.ItemName, a.ItemDescription, a.StartingPrice, a.EndingTime ".
      "ORDER BY COUNT(b.UserName) DESC";
      //echo $recommendQuerry;
      //echo $_SESSION['account_type'];
      $resultforRecommend = mysqli_query($connectionView, $recommendQuerry);
      // Demonstration of what listings will look like using dummy data.
      while ($row = mysqli_fetch_array($resultforRecommend)){
        $item_id = $row['AuctionID'];
        $title = $row['ItemName'];
        $description = $row['ItemDescription'];
        $current_price = $row['CurrentPrice'];
        $num_bids = $row['CountBids'];
        $end_date = new DateTime($row['EndingTime']);
        // This uses a function defined in utilities.php
        print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
      }
    }
    else {
      echo('<div class="text-center">You are a seller!</div>');
    }
  }
?>