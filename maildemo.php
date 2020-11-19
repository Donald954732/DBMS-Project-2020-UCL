<?php require("database.php")?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Demo of mail function</title>
</head>
<body>

<?php
/*email for Watchlist and Outbid daily update*/
echo "Notification Email Demo:";
echo "<br>";

$username = "brainyFalcon4";
$user_email = "bvelazquez@yahoo.com";
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
          UserName = '{$username}'
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
      UserName = '{$username}'
    GROUP BY
      AuctionID
  ) MaxPriceUser ON MaxPriceGlobal.AuctionID = MaxPriceUser.AuctionID
WHERE
  MaxPriceGlobal.CurrentPrice != MaxPriceUser.UserMax 
ORDER BY
  MaxPriceGlobal.AuctionID
QUERRYTEXT;
$resultOutbid = mysqli_query($connectionView, $querryOutbid);

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
      UserName = '{$username}'
  )
GROUP BY
  a.AuctionID,
  a.ItemName,
  a.ItemDescription,
  a.StartingPrice,
  a.EndingTime
ORDER BY
  a.AuctionID
QUERRYTEXT;
$resultWatchList = mysqli_query($connectionView, $querryWatchList);
if (($resultWatchList->num_rows > 0) and ($resultOutbid->num_rows > 0)){
    $subject = "Your WatchList and Outbid Update";
    echo  'To: ';
    echo  $user_email;
    echo "<br>";
    echo  'Subject: ';
    echo  $subject;
    echo "<br>";
    $body = "Hello $username, \n";
    $body .= "<br>";
    if ($resultOutbid->num_rows > 0) {
        $body .= "Some of the items you bidded is being outbidded by other user! \n";
        $body .= "<br>";
        while ($rowOutbid = mysqli_fetch_array($resultOutbid)){
            $body .= "The Auction ID: {$rowOutbid['AuctionID']}, Item Name: {$rowOutbid['ItemName']}, Current Price: {$rowOutbid['CurrentPrice']}, Your Max Bid Price: {$rowOutbid['UserMax']} . \n";
            $body .= "<br>";
        }
    }
    if ($resultWatchList->num_rows > 0) {
        $body .= "Update for Auctions in your watchlist in the last 24 hours \n";
        $body .= "<br>";
        while ($rowWatchList = mysqli_fetch_array($resultWatchList)){
            $body .= "The Auction ID: {$rowWatchList['AuctionID']}, Item Name: {$rowWatchList['ItemName']}, Current Price: {$rowWatchList['CurrentPrice']} .\n";
            $body .= "<br>";
        }
    }
    $body .= "Regards, \n";
    $body .= "<br>";
    $body .= "Auction Team \n";
    $body .= "<br>";
    echo "body : \n";
    echo "<br>";
    echo $body;
}
echo "<br>";
echo "Auction Email Demo:";
echo "<br>";

/*email for auction finish daily update*/
/*retrieving all the auction finished yesterday*/
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
    /*NoBids*/
    if ($Final_Price == "") {
        /*email to seller if NoBids Occured*/
        $subject = "The outcome of Item: {$Item_Name} ID: {$Auction_ID}";
        $user_email = $Seller_Email;
        echo "<br>";
        echo "To Seller:";
        echo "<br>";
        echo  'To: ';
        echo  $user_email;
        echo "<br>";
        echo  'Subject: ';
        echo  $subject;
        echo "<br>";
        $body = "The Item: {$Item_Name} ID: {$Auction_ID} ended the auction with no Bids.\n";
        $body .= "<br>";
        $body .= "Regards, \n";
        $body .= "<br>";
        $body .= "Auction Team \n";
        $body .= "<br>";
        echo $body;
        $Outcome = "NoBids";
        
    }
    /*if buying price is more than the reserve price*/
    else if ($Final_Price >= $Reserve_Price) {
        /*email to seller*/
        $subject = "The outcome of Item: {$Item_Name} ID: {$Auction_ID}";
        $user_email = $Seller_Email;
        echo "<br>";
        echo "To Seller:";
        echo "<br>";
        echo  'To: ';
        echo  $user_email;
        echo "<br>";
        echo  'Subject: ';
        echo  $subject;
        echo "<br>";
        $body = "The Item: {$Item_Name} ID: {$Auction_ID} is bidded by {$Buyer} at £{$Final_Price}. Please arrange payment and shipping as soon as possible with buyer's email: {$Buyer_Email}\n";
        $body .= "<br>";
        $body .= "Regards, \n";
        $body .= "<br>";
        $body .= "Auction Team \n";
        $body .= "<br>";
        echo $body;
        /*email to buyer*/
        $subject = "The outcome of Item: {$Item_Name} ID: {$Auction_ID}";
        $user_email = $Buyer_Email;
        echo "<br>";
        echo "To buyer:";
        echo "<br>";
        echo  'To: ';
        echo  $user_email;
        echo "<br>";
        echo  'Subject: ';
        echo  $subject;
        echo "<br>";
        $body = "You win the Bid of The Item: {$Item_Name} ID: {$Auction_ID} from {$Seller} at £{$Final_Price}. Please arrange payment and shipping as soon as possible with seller's email: {$Seller_Email}\n";
        $body .= "<br>";
        $body .= "Regards, \n";
        $body .= "<br>";
        $body .= "Auction Team \n";
        $body .= "<br>";
        echo $body;
        $Outcome = "Success";
    }
    else {
        /*email to seller if it don't meet the reserve price*/
        $subject = "The outcome of Item: {$Item_Name} ID: {$Auction_ID}";
        $user_email = $Seller_Email;
        echo "<br>";
        echo "To Seller:";
        echo "<br>";
        echo  'To: ';
        echo  $user_email;
        echo "<br>";
        echo  'Subject: ';
        echo  $subject;
        echo "<br>";
        $body = "The Item: {$Item_Name} ID: {$Auction_ID} ended the auction at £{$Final_Price}. The bidders failed to meet the reserve price.\n";
        $body .= "<br>";
        $body .= "Regards, \n";
        $body .= "<br>";
        $body .= "Auction Team \n";
        $body .= "<br>";
        echo $body;
        /*email to highest bidder beow reserve price*/
        $subject = "The outcome of Item: {$Item_Name} ID: {$Auction_ID}";
        $user_email = $Buyer_Email;
        echo "<br>";
        echo "To Lost Buyer:";
        echo "<br>";
        echo  'To: ';
        echo  $user_email;
        echo "<br>";
        echo  'Subject: ';
        echo  $subject;
        echo "<br>";
        $body = "You Failed to bid The Item: {$Item_Name} ID: {$Auction_ID}.\n";
        $body .= "<br>";
        $body .= "Regards, \n";
        $body .= "<br>";
        $body .= "Auction Team \n";
        $body .= "<br>";
        echo $body;
        $Outcome = "BLWReser";
    }
    $updateOucomeQuerry = <<<QUERRYTEXT
    UPDATE auctions
    SET 
      Outcome = "{$Outcome}"
    WHERE AuctionID = {$Auction_ID};
    QUERRYTEXT;
    $resultUpdateOutcome = mysqli_query($connectionUpdateOutcome, $updateOucomeQuerry);
    //echo $updateOucomeQuerry;
    if ($resultUpdateOutcome){
      //echo "Updated to Nobids";
    }
    /* Email To Loser*/
    $querryloser = <<<QUERRYTEXT
    SELECT
      DISTINCT u.Email 
    FROM
      bids b 
      JOIN users u
      ON b.UserName = u.Username
    WHERE
      b.AuctionID = {$Auction_ID}
      AND b.UserName != '{$Buyer}'
    ORDER BY
      b.BidPrice DESC,
      b.BidTime ASC;
    QUERRYTEXT;
    $resultloser = mysqli_query($connectionView, $querryloser);
    while ($rowloser = mysqli_fetch_array($resultloser)){
      /*email to loser*/
      $subject = "The outcome of Item: {$Item_Name} ID: {$Auction_ID}";
      $user_email = $rowloser['Email'];
      echo "<br>";
      echo "To Lost Buyer:";
      echo "<br>";
      echo  'To: ';
      echo  $user_email;
      echo "<br>";
      echo  'Subject: ';
      echo  $subject;
      echo "<br>";
      $body = "You Failed to bid The Item: {$Item_Name} ID: {$Auction_ID}.\n";
      $body .= "<br>";
      $body .= "Regards, \n";
      $body .= "<br>";
      $body .= "Auction Team \n";
      $body .= "<br>";
      echo $body;
    }

   }
}



?>
</body>
</html>