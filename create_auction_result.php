<?php include_once("header.php")?>
<?php include 'database.php'; ?>

<div class="container my-5">

<?php
/* TODO #2: Extract form data into variables. Because the form was a 'post'
            form, its data can be accessed via $POST['auctionTitle'], 
            $POST['auctionDetails'], etc. Perform checking on the data to
            make sure it can be inserted into the database. If there is an
            issue, give some semi-helpful feedback to user. */
$username = $_SESSION["username"];
$auctitle = $_POST["auctionTitle"];
$aucdetial = $_POST["auctionDetails"];
$aucategory = $_POST["auctionCategory"];
$aucStartprice = $_POST["auctionStartPrice"];
if ($_POST["auctionReservePrice"] == ""){
    $aucRsvprice = 0;
}
else{
    $aucRsvprice = $_POST["auctionReservePrice"];
}
$aucPHPtimestamp = strtotime($_POST["auctionEndDate"]);
$aucdate = date('Y-m-d H:i:s', $aucPHPtimestamp);


if($auctitle ==""||$aucStartprice == "" || $aucdate == "" || ($aucRsvprice != "" && intval($aucStartprice) > intval($aucRsvprice)) || $aucPHPtimestamp < date('Y-m-d H:i:s'))
{
    if($auctitle ==""||$aucStartprice == "" || $aucRsvprice == "" || $aucdate == ""){
        echo "<script language= javascript>alert('Fields can not be left blank!');history.go(-1);</script>";
    }
    elseif(intval($aucStartprice) > intval($aucRsvprice)){
        echo "<script language= javascript>alert('Reserve Price must be equal or above auction start Price');history.go(-1);</script>";
    }
    elseif($aucPHPtimestamp < date('Y-m-d H:i:s'))
    {
        echo "<script language= javascript>alert('Auctio can'tend before current time');history.go(-1);</script>";
    }
    
}
else
{
    /* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */
    $sql_insert = "INSERT INTO auctions (UserName, ItemName, ItemDescription, Category, StartingPrice, ReservePrice, EndingTime) VALUES ('{$username}', '{$auctitle}', '{$aucdetial}', '{$aucategory}', {$aucStartprice}, {$aucRsvprice}, '{$aucdate}')";
    $result_insert = mysqli_query($connectionAddAuction, $sql_insert);
    //echo $sql_insert;
    if($result_insert)
    {
        // If all is successful, let user know.
        echo "<script language= javascript>alert('Auction successfully created!');window.location.herf='mylistings.php';</script>";
        
    }
    else
    {
        echo "<script language= javascript>alert('Depulicate auction title! Add');history.go(-1);</script>";
    }

}


// This function takes the form data and adds the new auction to the database.







            





?>

</div>


<?php include_once("footer.php")?>