<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php require("database.php")?>

<div class="container">

<h2 class="my-3">My listings</h2>

<div id="searchSpecs">
<!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->

<form method="get" action="mylistings.php">
    <div class="col-md-3 pr-0">
      <div class="form-inline">
        <label class="mx-2" for="order_by">Sort by:</label>
        <select type = "submit"  id="order_by" name="order_by" onchange="this.form.submit();">
          <option selected value="ORDER BY CurrentPrice ASC">Price (low to high)</option>
          <option value="ORDER BY CurrentPrice DESC">Price (high to low)</option>
          <option value="ORDER BY (a.EndingTime - CURRENT_TIMESTAMP) ASC">Soonest expiry</option>
        </select>
    </div>
  </div>
</form>
</div> <!-- end search specs bar -->


</div>

<?php

  if (!isset($_GET['order_by'])) {
    // TODO: Define behavior if an order_by value has not been specified.
    $ordering = "";
  }
  else {
    $ordering = $_GET['order_by'];
  }

  if (!isset($_GET['page'])) {
    $curr_page = 1;
  }
  else {
    $curr_page = $_GET['page'];
  }


  if (isset($_SESSION['username']) != true){
    echo "Log in to view your bids";
  }
  else if ($_SESSION['account_type'] != 'seller'){
    echo "log into seller sccount to view your listing";
  }
  $results_per_page = 10;
  $querryItemList = <<<QUERRYTEXT
  SELECT
    a.AuctionID,
    a.ItemName,
    a.ItemDescription,
    a.StartingPrice,
    a.EndingTime,
    COUNT(b.BidID) AS 'CountBids',
    MAX(b.BidPrice) AS 'bidPrice',
    IF(
      MAX(bidPrice) IS NULL,
      a.StartingPrice,
      MAX(bidPrice)
    ) AS 'CurrentPrice',
    a.StartingPrice
  FROM
    auctions a
    LEFT JOIN bids b ON a.AuctionID = b.AuctionID
  WHERE
    a.UserName = '{$_SESSION['username']}'
  GROUP BY
    a.AuctionID,
    a.ItemName,
    a.ItemDescription,
    a.StartingPrice,
    a.EndingTime {$ordering} 
  QUERRYTEXT;
  //echo $querryItemList;

  $limiter = " LIMIT ".strval(($curr_page-1)*$results_per_page).", ".strval($results_per_page);
  
  $querryWithLimitItemPerPage = $querryItemList.$limiter;

  
  $resultforCounting = mysqli_query($connectionView, $querryItemList);
  //echo mysqli_error($connectionview)
  if ($resultforCounting) 
    { 
        // it return number of rows in the table. 
        $num_results = mysqli_num_rows($resultforCounting); 
    } 
  $max_page = ceil($num_results / $results_per_page);


?>

<div class="container mt-5">

<!-- TODO: If result set is empty, print an informative message. Otherwise... -->

<ul class="list-group">


<?php
  // Demonstration of what listings will look like using dummy data.
  $search_query_result = mysqli_query($connectionView, $querryWithLimitItemPerPage);
  while ($row = mysqli_fetch_array($search_query_result)){
  $item_id = $row['AuctionID'];
  $title = $row['ItemName'];
  $description = $row['ItemDescription'];
  $current_price = $row['CurrentPrice'];
  $num_bids = $row['CountBids'];
  $end_date = new DateTime($row['EndingTime']);
  // This uses a function defined in utilities.php
  print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
  }
?>

<!-- Pagination for results listings -->
<nav aria-label="Search results pages" class="mt-5">
  <ul class="pagination justify-content-center">

<?php

  // Copy any currently-set GET variables to the URL.
  $querystring = "";
  foreach ($_GET as $key => $value) {
    if ($key != "page") {
      $querystring .= "$key=$value&amp;";
    }
  }

  $high_page_boost = max(3 - $curr_page, 0);
  $low_page_boost = max(2 - ($max_page - $curr_page), 0);
  $low_page = max(1, $curr_page - 2 - $low_page_boost);
  $high_page = min($max_page, $curr_page + 2 + $high_page_boost);

  if ($curr_page != 1) {
    echo('
    <li class="page-item">
      <a class="page-link" href="mybids.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
        <span class="sr-only">Previous</span>
      </a>
    </li>');
  }

  for ($i = $low_page; $i <= $high_page; $i++) {
    if ($i == $curr_page) {
      // Highlight the link
      echo('
    <li class="page-item active">');
    }
    else {
      // Non-highlighted link
      echo('
    <li class="page-item">');
    }

    // Do this in any case
    echo('
      <a class="page-link" href="mybids.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }

  if ($curr_page != $max_page) {
    echo('
    <li class="page-item">
      <a class="page-link" href="mybids.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
        <span class="sr-only">Next</span>
      </a>
    </li>');
  }
?>

  </ul>
</nav>


</div>

<?php include_once("footer.php")?>

