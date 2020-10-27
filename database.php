<?php
  // Load database
  // Need to change this, needs to be more secure
  $connection = mysqli_connect("localhost", "AuctionUser", "PasswordAuctionDBMS2020", "Auction");

  if (mysqli_connect_errno())
    echo 'Failed to connect to the MySQL server: '. mysqli_connect_error();
?>
