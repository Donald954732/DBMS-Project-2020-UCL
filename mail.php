<?php require("database.php")?>

<?php
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
        mail($user_Email, $subject, $body);
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
    /*if buying price is more than the reserve price*/
    if ($Final_Price >= $Reserve_Price) {
        /*email to seller*/
        $subject = "The outcome of Item: {$Item_Name} ID: {$Auction_ID}";
        $user_email = $Seller_Email;
        $body = "The Item: {$Item_Name} ID: {$Auction_ID} is bidded by {$Buyer} at £{$Final_Price}. Please arrange payment and shipping as soon as possible with buyer's email: {$Buyer_Email}\n";
        $body .= "Regards, \n";
        $body .= "Auction Team \n";
        mail($user_Email, $subject, $body);
        /*email to buyer*/
        $subject = "The outcome of Item: {$Item_Name} ID: {$Auction_ID}";
        $user_email = $Buyer_Email;
        $body = "You win the Bid of The Item: {$Item_Name} ID: {$Auction_ID} from {$Seller} at £{$Final_Price}. Please arrange payment and shipping as soon as possible with seller's email: {$Seller_Email}\n";
        $body .= "Regards, \n";
        $body .= "Auction Team \n";
        mail($user_Email, $subject, $body);
    }
   }
}

?>