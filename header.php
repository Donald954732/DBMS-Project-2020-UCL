<?php
  // FIXME: At the moment, I've allowed these values to be set manually.
  // But eventually, with a database, these should be set automatically
  // ONLY after the user's login credentials have been verified via a 
  // database query.
  session_start();
  //$_SESSION['logged_in'] = false;
  //$_SESSION['account_type'] = 'seller';
?>
<?php require("database.php")?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  
  <!-- Bootstrap and FontAwesome CSS -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <!-- Custom CSS file -->
  <link rel="stylesheet" href="css/custom.css">

  <title>Group 11 Auction Site</title>
</head>


<body>

<!-- Navbars -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mx-2">
  <a class="navbar-brand" href="#">Group 11 Auction Site</a>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">
    
<?php
  // Displays either login or logout on the right, depending on user's
  // current status (session).
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    echo "<a class='nav-link dropdown-toggle' href='#' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>User</a>".
    "<div class='dropdown-menu dropdown-menu-right' aria-labelledby='navbarDropdown'>".
    "<a class='dropdown-item' href='#'>User Name: ".$_SESSION['username']."</a>".
    "<a class='dropdown-item' href='#'>Email: ".$_SESSION['email']."</a>".
    "<a class='dropdown-item' href='#'>User Group: ".$_SESSION['account_type']."</a>".
    "<div class='dropdown-divider'></div>".
    "<a class='dropdown-item' href='logout.php'>Logout</a>";


    //echo $_SESSION['logged_in'];
  }
  else {
    echo '<button type="button" class="btn nav-link" data-toggle="modal" data-target="#loginModal">Login</button>';
    //echo $_SESSION['logged_in'];
  }
?>
      </div>
    </li>
  </ul>
</nav>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <ul class="navbar-nav align-middle">
	<li class="nav-item mx-1">
      <a class="nav-link" href="browse.php">Browse</a>
    </li>
<?php
  if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'buyer') {
  echo('
	<li class="nav-item mx-1">
      <a class="nav-link" href="mybids.php">My Bids</a>
    </li>
	<li class="nav-item mx-1">
      <a class="nav-link" href="recommendations.php">Recommended</a>
    </li>');
  }
  if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'seller') {
  echo('
	<li class="nav-item mx-1">
      <a class="nav-link" href="mylistings.php">My Listings</a>
    </li>
	<li class="nav-item ml-3">
      <a class="nav-link btn border-light" href="create_auction.php">+ Create auction</a>
    </li>');
  }
?>
  </ul>
</nav>
<?php
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
  if (isset($_SESSION['account_type']) && $_SESSION['account_type'] == 'buyer') {

/*Notification for outbid */
$querryOutbid = <<<QUERRYTEXT
SELECT *
FROM
  (
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
      (a.EndingTime - CURRENT_TIMESTAMP) > 0
      AND a.AuctionID IN (
        SELECT
          AuctionID
        FROM
          bids
        WHERE
          UserName = '{$_SESSION['username']}'
        GROUP BY
          AuctionID
      )
    GROUP BY
      a.AuctionID,
      a.ItemName,
      a.ItemDescription,
      a.StartingPrice,
      a.EndingTime
  ) MaxPriceGlobal
  INNER JOIN (
    SELECT
      AuctionID,
      MAX(BidPrice) AS UserMax
    FROM
      bids
    WHERE
      UserName = '{$_SESSION['username']}'
    GROUP BY
      AuctionID
  ) MaxPriceUser ON MaxPriceGlobal.AuctionID = MaxPriceUser.AuctionID
WHERE
  MaxPriceGlobal.CurrentPrice != MaxPriceUser.UserMax 
QUERRYTEXT;
//echo $querryOutbid;
$resultOutbid = mysqli_query($connectionView, $querryOutbid);
//echo $querryOutbid;
if ($resultOutbid->num_rows > 0) {

echo <<<TABLEPARTS
<div class="alert alert-primary alert-dismissible fade show" role="alert">
  <strong>Some of the items you bidded is being outbidded by other user!</strong>
  <table class="table">
  <thead>
    <tr>
      <th scope="col">Auction ID</th>
      <th scope="col">Name Of Item</th>
      <th scope="col">Current Bid Price (£)</th>
      <th scope="col">Your Bid (£)</th>
    </tr>
  </thead>
  <tbody>
TABLEPARTS;
while ($rowOutbid = mysqli_fetch_array($resultOutbid)){
echo <<<ROWPARTS
    <tr>
      <th scope="row">{$rowOutbid['AuctionID']}</th>
      <td>{$rowOutbid['ItemName']}</td>
      <td>{$rowOutbid['CurrentPrice']}</td>
      <td>{$rowOutbid['UserMax']}</td>
    </tr>
ROWPARTS;
}
echo <<<TABLEPARTS
  </tbody>
</table>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
TABLEPARTS;
}

/*Extracting variable for the WatchList*/
   $querryWatchList = <<<QUERRYTEXT
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
  b.BidTime > CURDATE() - INTERVAL 1 DAY
  AND b.Bidtime < CURDATE()
  AND a.AuctionID IN (
    SELECT
      AuctionID
    FROM
      watchlist
    WHERE
      UserName = '{$_SESSION['username']}'
  )
GROUP BY
  a.AuctionID,
  a.ItemName,
  a.ItemDescription,
  a.StartingPrice,
  a.EndingTime
QUERRYTEXT;
//echo $querryWatchList;
   //echo $querryWatchList;
   $resultWatchList = mysqli_query($connectionView, $querryWatchList);
   //echo $querryOutbid;
   if ($resultWatchList->num_rows > 0) {
   echo <<<TABLEPARTS
   <div class="alert alert-secondary alert-dismissible fade show" role="alert">
     <strong>Update for your watchlist</strong>
     <table class="table">
     <thead>
       <tr>
         <th scope="col">Auction ID</th>
         <th scope="col">Name Of Item</th>
         <th scope="col">Current Bid Price (£)</th>
       </tr>
     </thead>
     <tbody>
   TABLEPARTS;
   while ($rowWatchList = mysqli_fetch_array($resultWatchList)){
    echo <<<ROWPARTS
        <tr>
          <th scope="row">{$rowWatchList['AuctionID']}</th>
          <td>{$rowWatchList['ItemName']}</td>
          <td>{$rowWatchList['CurrentPrice']}</td>
        </tr>
    ROWPARTS;
    }
   echo <<<TABLEPARTS
     </tbody>
   </table>
     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
       <span aria-hidden="true">&times;</span>
     </button>
   </div>
   TABLEPARTS;
      }   
 }
 /* Finished Auction */
  /* Fetching Auction ending today  */
 $querryTodayAuctionFinish = <<<QUERRYTEXT
 SELECT
  a.AuctionID, 
  a.UserName, 
  u.Email,
  a.ItemName, 
  a.ReservePrice
 FROM
  auctions a
  JOIN users u
  ON u.UserName = a. UserName
 WHERE
  EndingTime > CURDATE() - INTERVAL 1 DAY
  AND EndingTime < CURDATE()
 QUERRYTEXT;
 $resultTodayAuctionFinish = mysqli_query($connectionView, $querryTodayAuctionFinish);
 if ($resultTodayAuctionFinish->num_rows > 0) {
  echo <<<TABLEPARTS
  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>Auction Just Ended</strong>
    <table class="table">
    <thead>
      <tr>
        <th scope="col">Auction ID</th>
        <th scope="col">Name Of Item</th>
        <th scope="col">Final Price (£)</th>
        <th scope="col">Buyer</th>
        <th scope="col">Buyer Email</th>
        <th scope="col">Seller</th>
        <th scope="col">Seller Email</th>
      </tr>
    </thead>
    <tbody>
  TABLEPARTS;
  while ($rowFinAuction = mysqli_fetch_array($resultTodayAuctionFinish)){
    $Auction_ID = $rowFinAuction['AuctionID'];
    $Item_Name = $rowFinAuction['ItemName'];
    $Seller = $rowFinAuction['UserName'];
    $Seller_Email = $rowFinAuction['Email'];
    $Reserve_Price = $rowFinAuction['ReservePrice'];
    $querryWinner = <<<QUERRYTEXT
    SELECT
      b.UserName,
      b.bidPrice,
      u.Email 
    FROM
      bids b 
      JOIN users u
      ON b.UserName = u.Username
    WHERE
      b.AuctionID = {$Auction_ID}
    ORDER BY
      b.BidPrice DESC,
      b.BidTime ASC;
    QUERRYTEXT;
    $resultWinner = mysqli_query($connectionView, $querryWinner);
    $rowWinner = mysqli_fetch_array($resultWinner);
    $Final_Price = $rowWinner['bidPrice'];
    $Buyer = $rowWinner['UserName'];
    $Buyer_Email = $rowWinner['Email'];
    if ($Final_Price >= $Reserve_Price) {
      echo <<<ROWPARTS
      <tr>
        <th scope="row">{$Auction_ID}</th>
        <td>{$Item_Name}</td>
        <td>{$Final_Price}</td>
        <td>{$Buyer}</td>
        <td>{$Buyer_Email}</td>
        <td>{$Seller}</td>
        <td>{$Seller_Email}</td>
      </tr>
  ROWPARTS;
    }
   }
  echo <<<TABLEPARTS
    </tbody>
  </table>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  TABLEPARTS;
}

}

?>

<!-- Login modal -->
<div class="modal fade" id="loginModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title">Login</h4>
      </div>

      <!-- Modal body -->
      <div class="modal-body">
        <form method="POST" action="login_result.php">
          <div class="form-group">
            <label for="Username">Username</label>
            <input type="text" class="form-control" name="Username" placeholder="Username">
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password" placeholder="Password">
          </div>
          <button type="submit" class="btn btn-primary form-control">Sign in</button>
        </form>
        <div class="text-center">or <a href="register.php">create an account</a></div>
      </div>

    </div>
  </div>
</div> <!-- End modal -->