
<?php 

include 'configs/db_connection.php';

$host= gethostname();
$ip = mysqli_real_escape_string($conn ,gethostbyname($host));

//check if allowed
$query = "SELECT * FROM failed_attempts WHERE ip = '$ip'";
$result = mysqli_query($conn, $query);
if(mysqli_num_rows($result) > 0){
    $line = mysqli_fetch_assoc($result);
    if($line['counter'] == 3){
        //show red page
        header('Location: utilisateur_blocké.php');
    }
}

$username = $password = $error_message = "";

if(isset($_POST['submit'])){
    if(empty($_POST['username'])){ //form validation, no empty fields.
        $error_message = "<i class=\"fas fa-exclamation-triangle\"></i> Le Nom d'utilisateur est nécessaire.";
        $password = $_POST['password'];
    }elseif(empty($_POST['password'])){
        $error_message = "<i class=\"fas fa-exclamation-triangle\"></i> Le Mot de pass est nécessaire.";
        $username = $_POST['username'];
    }else{
        $username = mysqli_real_escape_string($conn ,$_POST['username']);
        $password = mysqli_real_escape_string($conn ,$_POST['password']);
        $failed = false;
        $query = "SELECT username, motDePass FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        if(mysqli_num_rows($result) > 0){
            $checked = false;
            while($line = mysqli_fetch_assoc($result)){
                if(password_verify($password, $line['motDePass'])){
                    $checked = true;
                    break;
                }
            }
            if($checked){
                //handle successful login
                //delete privious failed attempts
                $query = "DELETE FROM failed_attempts WHERE ip = '$ip'";  //prblm with this: as long as someone can login successfuly to one account, he can try to login to other accounts infinitely.
                $result = mysqli_query($conn, $query);
                //set session
                session_start();
                $_SESSION['username'] = $username;
                //redirect to menu principal
                header('Location: menu_principal.php');
            }else{
                $failed = true;
            }
        }else{
            $failed = true;
        }

        if($failed){
            //handle failed login
            //show wrong username or password message.
            $error_message = "<i class=\"fas fa-exclamation-triangle\"></i> Nom d'utilisateur ou mot de pass erroné.";
            //+1 to failed attempts counter if exists
            $query = "SELECT * FROM failed_attempts WHERE ip = '$ip'";
            $result = mysqli_query($conn, $query);
            if(mysqli_num_rows($result) > 0){
                //record exist then counter +1
                $query = "UPDATE failed_attempts SET counter = counter + 1 WHERE ip = '$ip'";
                $result = mysqli_query($conn, $query);
                //check if counter >= 3
                $query = "SELECT * FROM failed_attempts WHERE ip = '$ip'";
                $result = mysqli_query($conn, $query);
                if(mysqli_num_rows($result) > 0){
                    $line = mysqli_fetch_assoc($result);
                    if($line['counter'] == 3){
                        //redirect to red page
                        header('Location: utilisateur_blocké.php');
                    }
                }
            }else{
                //record doen't exist then insert new record
                $query = "INSERT INTO failed_attempts(ip) VALUES ('$ip')";
                $result = mysqli_query($conn, $query);
                if(!$result){
                    //show error page
                    header('Location: page_erreur.php');
                }
            }
        }
        
    }
}
// mysqli_free_result($result); //(ida ma mchatch plustard siyi na7i hada)
mysqli_close($conn);

?>
  <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APP_NAME</title>
    <link rel="stylesheet" href="public/stylesheets/lib/bootstrap.css">
    <link rel="stylesheet" href="public/stylesheets/lib/semantic.min.css">
    <link rel="stylesheet" href="public/stylesheets/confirm_notif_window.css">
    <link rel="stylesheet" href="public/stylesheets/style.css">
</head>
<body>
  <section class="showcase">
    <header>
      <a href="menu_principal.php">
          <h2 class="logo">APP_NAME</h2>
      </a>
      <div class="toggle"></div>
    </header>
    <!-- <video src="public\stylesheets\background_video\WKU_Flyover.mp4" muted loop autoplay></video> -->
    <div class="overlay"></div>
      
    <div class="whiteContainer column col-sm-11 col-md-10 mb-5 px-5 pb-3">
        <h1 id="loginHeader" class="py-4">Authentification</h1>
        <form action="login.php" method="POST" class="ui form m-3">
          <div class="field">
            <label><span class="label">Nom Utilisateur</span></label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>">
          </div>
          <div class="field">
            <label><span class="label">Mot De Pass</span></label>
            <input type="password" name="password" value="<?php echo htmlspecialchars($password); ?>">
          </div>
          <p class="wrong_username_or_password"><?php echo $error_message; ?></p>
          <button class="ui blue basic button mt-4" type="submit" name="submit" value="submit">Connecter</button>
        </form>
    </div>
        
    </section>

    <script>
        document.getElementsByClassName("toggle")[0].style.display = "none";
    </script>
</body>
</html>