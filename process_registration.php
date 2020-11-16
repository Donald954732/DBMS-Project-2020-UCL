<?php include 'database.php'; ?>
<?php include 'register.php'; ?>

<?php
if(isset($_POST["Submit"]) && $_POST["Submit"] == "Register") 
{
    $UserGroup = $_POST["accountType"];
    $Username = $_POST["username"];
    $Email = $_POST["email"];
    $Password = sha1($_POST["password"]);
    $Passwordconfirm = sha1($_POST["passwordConfirmation"]);
    if($Username==""||$Email == "" || $_Password == "" || $_Passwordconfirm == "")
    {
        echo"<div class="text-center">Fields can not be left blank!</div>";
    }
    else
    { 
        if ( $Password == $Passwordconfirm)
        {
            $sqlQuerry = "SELECT Username, AuthPassWord, UserGroup , Email From Auction.users WHERE Email = '$Email'";
            $resultEmail = mysqli_query($connectionAddUser, $sqlQuerry);
            $num = mysql_num_rows($resultEmail);
            if($num)
            {
                echo "<div class="text-center">This email address has been registered</div>";
            }
            else
            {
                $sql_insert = "INSERT INTO Auction.users  (Username, AuthPassWord, UserGroup, Email)
                VALUES ('$Username', '$Password', '$UserGroup', '$Email')";
                $result_insert = mysqli_query($sql_insert);
                if($result_insert)
                {
                    echo "<div class="text-center">Registration complete!</div>";
                }
                else
                {
                    echo "<div class="text-center">The system is busy. Please try again later.</div>";
                }

            }
        }
        else 
        { 
            echo "<div class="text-center">Inconsistent passwords!</div>" ;
        }
    }
}

// TODO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation
// options.


?>
