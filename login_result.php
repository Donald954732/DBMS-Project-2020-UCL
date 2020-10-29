<?php include 'database.php'; ?>

<?php

// TODO: Extract $_POST variables, check they're OK, and attempt to login.
// Notify user of success/failure and redirect/give navigation options

$LoginUsername = $_POST['Username'];
$LoginPasswordhash = sha1($_POST['password']);
//echo $LoginUsername;
//echo $LoginPasswordhash;
$LoginQuerry = "SELECT UserName, AuthPassWord, UserGroup FROM auction.users WHERE UserName='".$LoginUsername."'";
//AND AuthPassWord = '".$LoginPasswordhash."'"

//echo $LoginQuerry;
$resultLogin = mysqli_query($connectionView, $LoginQuerry);
if (mysqli_num_rows($resultLogin) == 1){
    echo('<div class="text-center">User Exist.</div>');
    $UserInfo = mysqli_fetch_array($resultLogin) ;
    ///echo $UserInfo['AuthPassWord'];
    if ($LoginPasswordhash == $UserInfo['AuthPassWord']){
        echo('<div class="text-center">Password Correct.</div>');
        session_start();
        $_SESSION['logged_in'] = true;
        echo $_SESSION['logged_in'];
        $_SESSION['username'] = $UserInfo['UserName'];
        $_SESSION['account_type'] = $UserInfo['UserGroup'];

        echo('<div class="text-center">You are now logged in! You will be redirected shortly.</div>');
    }
    else {
        echo('<div class="text-center">Invalid Password.</div>');
    }
}
else {
    echo('<div class="text-center">Invalid Username.</div>');
}
// For now, I will just set session variables and redirect.

// Redirect to index after 5 seconds
header("refresh:5;url=index.php");

?>
