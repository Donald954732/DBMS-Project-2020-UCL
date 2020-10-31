<?php
  var_dump($_post); echo "<br />";
  $user = array();


// Create connection
$connectionView = new mysqli($servername, $username, $password, $table);

// Check connection
if ($connectionView->connect_error) {
  die("Connection failed: " . $connectionView->connect_error);
}
echo "Connected successfully";



$querryItemList = "SELECT ROW_NUMBER() OVER(".$ordering.") AS 'RowNum', a.ItemName, a.ItemDescription, a.StartingPrice, a.EndingTime, ".
"COUNT(b.BidID) AS 'CountBids', MAX(b.BidPrice) AS 'CurrentPrice', a.StartingPrice ".
"FROM auctions a LEFT JOIN bids b ON a.AuctionID = b.AuctionID ".
"WHERE a.ItemName LIKE '%".$keyword."%' AND (a.EndingTime - CURRENT_TIMESTAMP) > 0 ".
"GROUP BY a.ItemName, a.ItemDescription, a.StartingPrice, a.EndingTime".
"ORDER BY RowNum ASC";
$querryTotalItem = "SELECT Count('RowNum') FROM (".$querryItemList.")";



?>