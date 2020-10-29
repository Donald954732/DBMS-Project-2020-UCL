<?php include_once("header.php")?>
<?php require("utilities.php")?>

<?php
//use different account for different purpose so that they can't do SQL injection attack
$servername = "localhost";
$username = "AuctionUserView";
$password = "PasswordAuctionViewDBMS2020";
$table = "Auction";

// Create connection
$connectionView = new mysqli($servername, $username, $password, $table);

// Check connection
if ($connectionView->connect_error) {
  die("Connection failed: " . $connectionView->connect_error);
}
//echo "Connected successfully";
?>

<div class="container">

<h2 class="my-3">Browse listings</h2>

<div id="searchSpecs">
<!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->

<form method="get" action="browse.php">
  <div class="row">
    <div class="col-md-5 pr-0">
      <div class="form-group">
        <label for="keyword" class="sr-only">Search keyword:</label>
	    <div class="input-group">
          <div class="input-group-prepend">
            <span class="input-group-text bg-transparent pr-0 text-muted">
              <i class="fa fa-search"></i>
            </span>
          </div>
          <input type="text" class="form-control border-left-0" id="keyword" placeholder="Search for anything" name="keyword">
        </div>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-group">
        <label for="cat" class="sr-only">Search within:</label>
        <select class="form-control" id="cat" name="cat">
          <option selected value="a.Category">All categories</option>
          <?php
          // the code to populate the category list -- Donald
          $querryCategoryList = "SELECT Category FROM auction.categorylist ORDER BY Category ASC";
          $resultCatrgory = mysqli_query($connectionView, $querryCategoryList);
          while ($rowCategory = mysqli_fetch_array($resultCatrgory))
          {
            echo "<option value='".$rowCategory['Category']."'>".$rowCategory['Category']."</option>";
          }
          //mysqli_close($connectionView)
          ?>
        </select>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-inline">
        <label class="mx-2" for="order_by">Sort by:</label>
        <select class="form-control" id="order_by" name="order_by">
          <option selected value="ORDER BY CurrentPrice ASC">Price (low to high)</option>
          <option value="ORDER BY CurrentPrice DESC">Price (high to low)</option>
          <option value="ORDER BY (a.EndingTime - CURRENT_TIMESTAMP) ASC">Soonest expiry</option>
        </select>
      </div>
    </div>
    <div class="col-md-1 px-0">
      <button type="submit" class="btn btn-primary">Search</button>
    </div>
  </div>
</form>
</div> <!-- end search specs bar -->


</div>

<?php
  // Retrieve these from the URL
  if (!isset($_GET['keyword'])) {
    // TODO: Define behavior if a keyword has not been specified.
    $keyword = "%";
  }
  else {
    $keyword = $_GET['keyword'];
  }

  if (!isset($_GET['cat']) or $_GET['cat'] == "a.Category") {
    // TODO: Define behavior if a category has not been specified.
    $category = "a.Category";
  }
  else {
    $category = "'".$_GET['cat']."'";
  }

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

  //echo $ordering;

  /* TODO: Use above values to construct a query. Use this query to
     retrieve data from the database. (If there is no form data entered,
     decide on appropriate default value/default query to make. */
   
  $results_per_page = 10;
  $querryItemList = "SELECT a.AuctionID, a.ItemName, a.ItemDescription, a.StartingPrice, a.EndingTime, ".
  "COUNT(b.BidID) AS 'CountBids', MAX(b.BidPrice) AS 'bidPrice', IF(MAX(bidPrice) IS NULL, a.StartingPrice, MAX(bidPrice))  AS 'CurrentPrice', a.StartingPrice ".
  "FROM auctions a LEFT JOIN bids b ON a.AuctionID = b.AuctionID ".
  "WHERE a.ItemName LIKE '%".$keyword."%' AND (a.EndingTime - CURRENT_TIMESTAMP) > 0 AND a.Category = ".$category." ".
  "GROUP BY a.AuctionID, a.ItemName, a.ItemDescription, a.StartingPrice, a.EndingTime ".$ordering;

  $limiter = " LIMIT ".strval(($curr_page-1)*$results_per_page).", ".strval($results_per_page);
  
  $querryWithLimitItemPerPage = $querryItemList.$limiter;
  //echo $querryWithLimitItemPerPage;
  //echo $querryItemList;
  /*$querryTotalItem = "SELECT Count('RowNum') AS 'total' FROM (".$querryItemList.")";
  $resultSearch = mysqli_query($connectionView, $querryItemList);
  
  $totalItem = mysqli_fetch_array($resultCatrgory);
  */
     /*//echo($keyword);
     //echo($category);

     if (empty($keyword) and $category != "all"){
         // text field empty and category != all
         $search_query = "SELECT AuctionID, ItemName, ItemDescription, StartingPrice, EndingTime FROM Auctions WHERE Category = '$category' ORDER BY StartingPrice $ordering";
     } elseif (!empty($keyword) and $category == "all"){
         // text field not empty and category = all
         $search_query = "SELECT AuctionID, ItemName, ItemDescription, StartingPrice, EndingTime FROM Auctions WHERE INSTR(ItemDescription, '$keyword') > 0 ORDER BY StartingPrice $ordering";
     } elseif (!empty($keyword) and $category != "all"){
         // text field not empty and category != all
         $search_query = "SELECT AuctionID, ItemName, ItemDescription, StartingPrice, EndingTime FROM Auctions WHERE INSTR(ItemDescription, '$keyword') > 0 AND Category = '$category' ORDER BY StartingPrice $ordering";
     }else{
         //text field empty and category = all
         $search_query = "SELECT AuctionID, ItemName, ItemDescription, StartingPrice, EndingTime FROM Auctions ORDER BY StartingPrice $ordering";
     }

     $search_query_result = mysqli_query($connectionView, $search_query) or die("Error with search query". mysql_error());

     // Temporarily listing items here
     while ($row = mysqli_fetch_array($search_query_result)){
           // Need to show: $item_id, $title, $description, $current_price, $num_bids, $end_date
           print_listing_li($row['AuctionID'], $row['ItemName'], $row['ItemDescription'], $row['StartingPrice'], 1, $row['EndingTime']);
     }*/


  /* For the purposes of pagination, it would also be helpful to know the
     total number of results that satisfy the above query */
  //$resultCatrgory = mysqli_query($connectionView, $querryCategoryList);
  
  $resultforCounting = mysqli_query($connectionView, $querryItemList);
  if ($resultforCounting) 
    { 
        // it return number of rows in the table. 
        $num_results = mysqli_num_rows($resultforCounting); 
          
           if ($num_results) 
              { 
                 //printf("Number of row in the table : " . $num_results); 
              }
    } 
  
  //$num_results = mysqli_num_rows($resultforCounting); // TODO: Calculate me for real
  
  //echo $num_results;
  $max_page = ceil($num_results / $results_per_page);

  //echo($max_page);
  //echo($num_results);
  //mysqli_close($connectionView); //Don't close it here
?>

<div class="container mt-5">

<!-- TODO: If result set is empty, print an informative message. Otherwise... -->

<ul class="list-group">

<!-- TODO: Use a while loop to print a list item for each auction listing
     retrieved from the query -->

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

</ul>

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
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
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
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }

  if ($curr_page != $max_page) {
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
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
