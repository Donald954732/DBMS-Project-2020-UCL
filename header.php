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

  <title>[Group 11 Auction Site] <!--CHANGEME!--></title>
</head>


<body>

<!-- Navbars -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mx-2">
  <a class="navbar-brand" href="#">Group 11 Auction Site<!--CHANGEME!--></a>
  <ul class="navbar-nav ml-auto">
    <li class="nav-item dropdown">
    
<?php
  // Displays either login or logout on the right, depending on user's
  // current status (session).
  if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
    $querryUser = "SELECT Email, UserGroup FROM auction.users WHERE Username = '".$_SESSION['username']."'";
    //echo $querryUser;
    $resultUser = mysqli_query($connectionView, $querryUser);
    $arrayUser = mysqli_fetch_array($resultUser);
    echo "<a class='nav-link dropdown-toggle' href='#' id='navbarDropdown' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>User</a>".
    "<div class='dropdown-menu dropdown-menu-right' aria-labelledby='navbarDropdown'>".
    "<a class='dropdown-item' href='#'>User Name: ".$_SESSION['username']."</a>".
    "<a class='dropdown-item' href='#'>Email: ".$arrayUser['Email']."</a>".
    "<a class='dropdown-item' href='#'>User Group: ".$arrayUser['UserGroup']."</a>".
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