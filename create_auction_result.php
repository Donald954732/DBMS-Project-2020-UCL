<?php include_once("header.php")?>
<?php include 'database.php'; ?>

<div class="container my-5">

<?php
$username = $_SESSION["username"];
$auctitle = $_POST["auctionTitle"];
$aucdetial = $_POST["auctionDetails"];
$aucategory = $_POST["auctionCategory"];
$aucStartprice = $_POST["auctionStartPrice"];
$aucRsvprice = $_POST["auctionReservePrice"];
$aucdate = $_POST["auctionEndDate"];

if($auctitle ==""||$aucStartprice == "" || $aucRsvprice == "" || $aucdate == "")
{
    echo "<script language= javascript>alert('Fields can not be left blank!');history.go(-1);</script>";
}
else
{
    $sqlQuerry = "SELECT UserName, ItemName, ItemDescription, Category, StartingPrice, ReservePrice, EndingTime From Auction.auctions WHERE ItemName = '".$_POST['auctiontitle']."'";
    $resultTitle = mysqli_query($connectionAddAuction, $sqlQuerry);
    if(empty(mysqli_fetch_array($resultTitle)) != TRUE)
    {
        echo "<script language= javascript>alert('Depulicate auction title!');history.go(-1);</script>";
    }
    else
    {
        $sql_insert = "INSERT INTO Auction.auctions (UserName, ItemName, ItemDescription, Category, StartingPrice, ReservePrice, EndingTime) VALUES ('$username', '$auctitle', '$aucdetial', '$aucategory', '$aucStartprice', '$aucRsvprice', '$aucdate')";
        $result_insert = mysqli_query($connectionAddAuction, $sql_insert);
        if($result_insert)
        {
            echo "<script language= javascript>alert('Auction successfully created!');window.location.herf='mylistings.php';</script>";
        }
        else
        {
            echo "<script language= javascript>alert('Depulicate auction title!');history.go(-1);</script>";
        }

    }

}


// This function takes the form data and adds the new auction to the database.



/* TODO #2: Extract form data into variables. Because the form was a 'post'
            form, its data can be accessed via $POST['auctionTitle'], 
            $POST['auctionDetails'], etc. Perform checking on the data to
            make sure it can be inserted into the database. If there is an
            issue, give some semi-helpful feedback to user. */


/* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */
            

// If all is successful, let user know.



?>

</div>


<?php include_once("footer.php")?>