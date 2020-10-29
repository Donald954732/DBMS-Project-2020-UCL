<?php include_once("header.php")?>
<?php require("utilities.php")?>

<?php include 'database.php'; ?>

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
          <?php

          ?>
        </div>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-group">
        <label for="cat" class="sr-only">Search within:</label>
        <select class="form-control" id="cat" name="cat">
        <option selected value="All">All</option>

         <?php
          $category_query = "SELECT c.Category FROM CategoryList c ORDER BY c.Category ASC";
          $category_query_result = mysqli_query($connection, $category_query) or die("Error with category query". mysql_error());

          while ($row = mysqli_fetch_array($category_query_result)){
                echo "<option>". $row['Category']."</option>";
          }
          ?>

        </select>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-inline">
        <label class="mx-2" for="order_by">Sort by:</label>
        <select class="form-control" id="order_by" name="order_by">
          <option selected value="pricelow">Price (low to high)</option>
          <option value="pricehigh">Price (high to low)</option>
          <option value="date">Soonest expiry</option>
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
    // Show all bids if no keyword is entered
    $keyword = "";
  }
  else {
    $keyword = $_GET['keyword'];
  }

  if (!isset($_GET['cat'])) {
    // TODO: Define behavior if a category has not been specified.
    // Show all categpries if no keyword is entered
    $category = "All";
  }
  else {
    $category = $_GET['cat'];
  }

  if (!isset($_GET['order_by'])) {
    // TODO: Define behavior if an order_by value has not been specified.
    $ordering = "ASC";
  }
  else {
      if ($_GET['order_by'] == "pricelow"){
          $ordering = "ASC";
      }
      elseif ($_GET['order_by'] == "pricehigh"){
          $ordering = "DESC";
      }
      else{
          // Show all bids in order of closest date
          $ordering = "";
      }
  }

  if (!isset($_GET['page'])) {
    $curr_page = 1;
  }
  else {
    $curr_page = $_GET['page'];
  }

  /* TODO: Use above values to construct a query. Use this query to
     retrieve data from the database. (If there is no form data entered,
     decide on appropriate default value/default query to make. */

     if ($category == "All" && $keyword == ""){
         //$search_query = "SELECT a.AuctionID, a.ItemName, a.ItemDescription, a.StartingPrice, a.EndingTime FROM Auctions a WHERE a.Category='$category' AND a.ItemDescription LIKE '%$keyword%' ORDER BY a.StartingPrice $ordering";
         echo("inside if");
         $search_query = "SELECT a.AuctionID, a.ItemName, a.ItemDescription, a.StartingPrice, a.EndingTime FROM Auctions a ORDER BY a.StartingPrice $ordering";
     } else {
         echo ("Inside Else");
         $search_query = "SELECT a.AuctionID, a.ItemName, a.ItemDescription, a.StartingPrice, a.EndingTime FROM Auctions a WHERE a.Category = '$category' AND INSTR(a.ItemDescription, '$keyword') > 0 ORDER BY a.StartingPrice $ordering";
     }

     $search_query_result = mysqli_query($connection, $search_query) or die("Error with search query". mysql_error());

     // Temporarily listing items here
     while ($row = mysqli_fetch_array($search_query_result)){
           // Need to show: $item_id, $title, $description, $current_price, $num_bids, $end_date
           print_listing_li($row['AuctionID'], $row['ItemName'], $row['ItemDescription'], $row['StartingPrice'], 1, $row['EndingTime']);
     }


  /* For the purposes of pagination, it would also be helpful to know the
     total number of results that satisfy the above query */
  $num_results = mysqli_num_rows($search_query_result); // TODO: Calculate me for real
  $results_per_page = 10;
  $max_page = ceil($num_results / $results_per_page);

  //echo($max_page);
  //echo($num_results);
  mysqli_close($connection);
?>

<div class="container mt-5">

<!-- TODO: If result set is empty, print an informative message. Otherwise... -->

<ul class="list-group">

<!-- TODO: Use a while loop to print a list item for each auction listing
     retrieved from the query -->

<?php
  // Demonstration of what listings will look like using dummy data.
  $item_id = "87021";
  $title = "Dummy title";
  $description = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum eget rutrum ipsum. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Phasellus feugiat, ipsum vel egestas elementum, sem mi vestibulum eros, et facilisis dui nisi eget metus. In non elit felis. Ut lacus sem, pulvinar ultricies pretium sed, viverra ac sapien. Vivamus condimentum aliquam rutrum. Phasellus iaculis faucibus pellentesque. Sed sem urna, maximus vitae cursus id, malesuada nec lectus. Vestibulum scelerisque vulputate elit ut laoreet. Praesent vitae orci sed metus varius posuere sagittis non mi.";
  $current_price = 30;
  $num_bids = 1;
  $end_date = new DateTime('2020-09-16T11:00:00');

  // This uses a function defined in utilities.php
  print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);

  $item_id = "516";
  $title = "Different title";
  $description = "Very short description.";
  $current_price = 13.50;
  $num_bids = 3;
  $end_date = new DateTime('2020-11-02T00:00:00');

  print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
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
