<?php include 'database.php'; ?>
<?php include 'register.php'; ?>

<?php
if(isset($_POST["Submit"]) && $_POST["Submit"] == "Register") 
{
    $Email = $_POST["email"];
    $Password = $_POST["password"];
    $Passwordconfirm = $_POST["passwordConfirmation"];
    //username?
    if($Email == "" || $_Password == "" || $_Passwordconfirm == "")//空的
    {
        echo"<div class="text-center">Fields can not be left blank!</div>";
    }
    else//不空
    { 
        if //两次密码相同
        else 
    }
}

// TODO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation
// options.


?>
