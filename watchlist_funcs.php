<?php require("database.php")?> 

<?php
session_start();
if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
  return;
}

// Extract arguments from the POST variables:
$item_id = $_POST['arguments'];

if ($_POST['functionname'] == "add_to_watchlist") {
  // TODO: Update database and return success/failure.
  $querryAddWatchList = <<<QUERRYTEXT
    INSERT INTO 
      watchlist 
    VALUES 
      ('{$_SESSION['username']}', {$item_id[0]})
  QUERRYTEXT;
  $resultAddWatchList = mysqli_query($connectionWatchlist, $querryAddWatchList);
  if ($resultAddWatchList){
    $res = "success";  
  }
}
else if ($_POST['functionname'] == "remove_from_watchlist") {
  // TODO: Update database and return success/failure.
  $querryDeleteWatchList = <<<QUERRYTEXT
    DELETE FROM 
      watchlist 
    WHERE 
      UserName = '{$_SESSION['username']}'
      AND AuctionID = {$item_id[0]}
  QUERRYTEXT;
  $resultDeleteWatchList = mysqli_query($connectionWatchlist, $querryDeleteWatchList);
  if ($resultDeleteWatchList){
    $res = "success";  
  }
}

// Note: Echoing from this PHP function will return the value as a string.
// If multiple echo's in this file exist, they will concatenate together,
// so be careful. You can also return JSON objects (in string form) using
// echo json_encode($res).
echo $res;

?>