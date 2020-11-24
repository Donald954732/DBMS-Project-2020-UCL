<?php require("database.php")?>

<?php
ini_set("sendmail_path", "C:\wamp64\sendmail\sendmail.exe");
ini_set("smtp_port","2525");
$headers = 'From: webmaster@G11Auction.com' . "\r\n" .
    'Reply-To: webmaster@G11Auction.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

$name = "Group 11 Auction Site Admin"; //sender’s name
$email = "Admin@Auction.com"; //sender’s e-mail address
/*email for Watchlist and Outbid daily update*/
/*retrieving a list of all user*/
$querryAllUser = <<<QUERRYTEXT
SELECT 
    UserName, Email
FROM
    users
QUERRYTEXT;
$resultAllUser = mysqli_query($connectionView, $querryAllUser);
while ($rowAllUser = mysqli_fetch_array($resultAllUser)){
    /*Notification for outbid */
    $username = $rowAllUser['UserName'];
    $user_Email = $rowAllUser['Email'];
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
        $body = "Hello $username, \n";
        if ($resultOutbid->num_rows > 0) {
            $body .= "Some of the items you bidded is being outbidded by other user! \n";
            while ($rowOutbid = mysqli_fetch_array($resultOutbid)){
                $body .= "The Auction ID: {$rowOutbid['AuctionID']}, Item Name: {$rowOutbid['ItemName']}, Current Price: {$rowOutbid['CurrentPrice']}, Your Max Bid Price: {$rowOutbid['UserMax']} . \n";
            }
        }
        if ($resultWatchList->num_rows > 0) {
            $body .= "Update for Auctions in your watchlist in the last 24 hours \n";
            while ($rowWatchList = mysqli_fetch_array($resultWatchList)){
                $body .= "The Auction ID: {$rowWatchList['AuctionID']}, Item Name: {$rowWatchList['ItemName']}, Current Price: {$rowWatchList['CurrentPrice']} .\n";
            }
        }
        $body .= "Regards, \n";
        $body .= "Auction Team \n";
        /*sending mail*/
        $result = mail($user_Email, $subject, $body, $headers);
        if( $result ) {
          echo 'Success';
       }else{
          echo 'Fail';
       }
    }
}
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
    if ($Final_Price == "") {
        /*email to seller if it has no bid*/
        $subject = "The outcome of Item: {$Item_Name} ID: {$Auction_ID}";
        $body = "Hello $Seller, \n";
        $body .= "The Item: {$Item_Name} ID: {$Auction_ID} ended the auction with no Bids.\n";
        $body .= "Regards, \n";
        $body .= "Auction Team \n";
        mail($Seller_Email, $subject, $body, $headers);
        $Outcome = "NoBid";
    }
    /*if buying price is more than the reserve price*/
    else if ($Final_Price >= $Reserve_Price) {
        /*email to seller*/
        $subject = "The outcome of Item: {$Item_Name} ID: {$Auction_ID}";
        $body = "Hello $Seller, \n";
        $body .= "The Item: {$Item_Name} ID: {$Auction_ID} is bidded by {$Buyer} at £{$Final_Price}. Please arrange payment and shipping as soon as possible with buyer's email: {$Buyer_Email}\n";
        $body .= "Regards, \n";
        $body .= "Auction Team \n";
        $result = mail($Seller_Email, $subject, $body, $headers);
        if( $result ) {
          echo 'Success';
        }else{
          echo 'Fail';
        }
        /*email to buyer*/
        $subject = "The outcome of Item: {$Item_Name} ID: {$Auction_ID}";
        $body = "Hello $Buyer, \n";
        $body .= "You win the Bid of The Item: {$Item_Name} ID: {$Auction_ID} from {$Seller} at £{$Final_Price}. Please arrange payment and shipping as soon as possible with seller's email: {$Seller_Email}\n";
        $body .= "Regards, \n";
        $body .= "Auction Team \n";
        $result = mail($Buyer_Email, $subject, $body, $headers);
        if( $result ) {
          echo 'Success';
        }else{
          echo 'Fail';
        }
        $Outcome = "Success";
        /* Email To Loser See SQL manual for Offset!*/
        $querryloser = <<<QUERRYTEXT
        SELECT
          DISTINCT u.Email,
          u.UserName 
        FROM
          bids b 
          JOIN users u
          ON b.UserName = u.Username
        WHERE
          b.AuctionID = {$Auction_ID}
        ORDER BY
          b.BidPrice DESC,
          b.BidTime ASC
        LIMIT
          1, 18446744073709551615
        QUERRYTEXT;
        $resultloser = mysqli_query($connectionView, $querryloser);
        while ($rowloser = mysqli_fetch_array($resultloser)){
          /*email to loser*/
          $subject = "The outcome of Item: {$Item_Name} ID: {$Auction_ID}";
          $loser_email = $rowloser['Email'];
          $username = $rowloser['UserName'];
          $body = "Hello $username, \n";
          $body .= "You Failed to bid The Item: {$Item_Name} ID: {$Auction_ID}.\n";
          $body .= "Regards, \n";
          $body .= "Auction Team \n";
          $result = mail($loser_email, $subject, $body, $headers);
          if( $result ) {
            echo 'Success';
          }else{
            echo 'Fail';
          }
        }
    }
    else {
        /*email to seller if it don't meet the reserve price*/
        $subject = "The outcome of Item: {$Item_Name} ID: {$Auction_ID}";
        $body = "Hello $Seller, \n";
        $body .= "The Item: {$Item_Name} ID: {$Auction_ID} ended the auction at £{$Final_Price}. The bidders failed to meet the reserve price.\n";
        $body .= "Regards, \n";
        $body .= "Auction Team \n";
        $result = mail($Seller_Email, $subject, $body, $headers);
        if( $result ) {
          echo 'Success';
        }else{
          echo 'Fail';
        }
        $Outcome = "BelowReserve";
        /*email to highest bidder beow reserve price*/
        $subject = "The outcome of Item: {$Item_Name} ID: {$Auction_ID}";
        $body = "Hello $Buyer, \n";
        $body .= "You Failed to bid The Item: {$Item_Name} ID: {$Auction_ID}.\n";
        $body .= "Regards, \n";
        $body .= "Auction Team \n";
        $result = mail($Buyer_Email, $subject, $body, $headers);
        if( $result ) {
          echo 'Success';
        }else{
          echo 'Fail';
        }
        /* Email To Loser*/
        $querryloser = <<<QUERRYTEXT
        SELECT
          DISTINCT u.Email,
          u.UserName 
        FROM
          bids b 
          JOIN users u
          ON b.UserName = u.Username
        WHERE
          b.AuctionID = {$Auction_ID}
        ORDER BY
          b.BidPrice DESC,
          b.BidTime ASC
        LIMIT
          1, 18446744073709551615
        QUERRYTEXT;
        $resultloser = mysqli_query($connectionView, $querryloser);
        while ($rowloser = mysqli_fetch_array($resultloser)){
          /*email to loser*/
          $subject = "The outcome of Item: {$Item_Name} ID: {$Auction_ID}";
          $loser_email = $rowloser['Email'];
          $username = $rowloser['UserName'];
          $body = "Hello {$username}, \n";
          $body .= "You Failed to bid The Item: {$Item_Name} ID: {$Auction_ID}.\n";
          $body .= "Regards, \n";
          $body .= "Auction Team \n";
          $result = mail($loser_email, $subject, $body, $headers);
          if( $result ) {
            echo 'Success';
          }else{
            echo 'Fail';
          }
        }
    }
    /* Updating Outcome*/
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

   }
}

?>