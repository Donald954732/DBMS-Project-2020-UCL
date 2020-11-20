<?php include 'database.php'; ?>
<?php include 'register.php'; ?>

<?php

$UserGroup = $_POST["accountType"];
$Username = $_POST["username"];
$Email = $_POST["email"];
$Password = sha1($_POST["password"]);
$Passwordconfirm = sha1($_POST["passwordConfirmation"]);
if($Username==""||$Email == "" || $Password == "" || $Passwordconfirm == "")
{
    echo "<script language= javascript>alert('Fields can not be left blank!');history.go(-1);</script>";
}
else
{ 
    if ( $Password == $Passwordconfirm)
    {
        $sqlQuerry = "SELECT Username, AuthPassWord, UserGroup , Email From Auction.users WHERE Email = '".$_POST['email']."'";
        $resultEmail = mysqli_query($connectionView, $sqlQuerry);
        if($resultEmail -> num_rows > 0 || !filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            echo "<script language= javascript>alert('Error: Invalid Email or duplicate');history.go(-1);</script>";
        }
        else
        {
            $sql_insert = "INSERT INTO Auction.users  (Username, AuthPassWord, UserGroup, Email) VALUES ('$Username', '$Password', '$UserGroup', '$Email')";
            $result_insert = mysqli_query($connectionAddUser, $sql_insert);
            if($result_insert)
            {
                echo "<script language= javascript>alert('Registration Complete!');window.location.href='browse.php';</script>";
            }
            else
            {
                echo "<script language= javascript>alert('Error: Duplicate User Name');history.go(-1);</script>";
            }
        }
    }
    else 
    { 
        echo "<script language= javascript>alert('Inconsistent passwords! Try again.');history.go(-1);</script>";
    }
}


 // TODO: Extract $_POST variables, check they're OK, and attempt to create
 // an account. Notify user of success/failure and redirect/give navigation
 // options.


?>
