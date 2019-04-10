<?php

    session_start();

    $error = "";    

    if (array_key_exists("logout", $_GET)) {
        
        unset($_SESSION);
        setcookie("id", "", time() - 60*60);
        $_COOKIE["id"] = "";  
        
    } else if ((array_key_exists("id", $_SESSION) AND $_SESSION['id']) OR (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'])) {
        
        header("Location: loggedinpage.php");
        
    }

    if (array_key_exists("submit", $_POST)) {
        
        include("connection.php");
        
        if (!$_POST['email']) {
            
            $error .= "An email address is required<br>";
            
        } 
        
        if (!$_POST['password']) {
            
            $error .= "A password is required<br>";
            
        } 
        
        if ($error != "") {
            
            $error = "<p>There were error(s) in your form:</p>".$error;
            
        } else {
            
            if ($_POST['signUp'] == '1') {
            
                $query = "SELECT id FROM `diaryusers` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";

                $result = mysqli_query($link, $query);

                if (mysqli_num_rows($result) > 0) {

                    $error = "That email address is taken.";

                } else {

                    $query = "INSERT INTO `diaryusers` (`email`, `password`) VALUES ('".mysqli_real_escape_string($link, $_POST['email'])."', '".mysqli_real_escape_string($link, $_POST['password'])."')";

                    if (!mysqli_query($link, $query)) {

                        $error = "<p>Could not sign you up - please try again later.</p>";

                    } else {

                        $query = "UPDATE `diaryusers` SET password = '".md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id = ".mysqli_insert_id($link)." LIMIT 1";

                        mysqli_query($link, $query);

                        $_SESSION['id'] = mysqli_insert_id($link);

                        if ($_POST['stayLoggedIn'] == '1') {

                            setcookie("id", mysqli_insert_id($link), time() + 60*60*24*365);

                        } 

                        header("Location: loggedinpage.php");

                    }

                } 
                
            } else {
                    
                    $query = "SELECT * FROM `diaryusers` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."'";
                
                    $result = mysqli_query($link, $query);
                
                    $row = mysqli_fetch_array($result);
                
                    if (isset($row)) {
                        
                        $hashedPassword = md5(md5($row['id']).$_POST['password']);
                        
                        if ($hashedPassword == $row['password']) {
                            
                            $_SESSION['id'] = $row['id'];
                            
                            if ($_POST['stayLoggedIn'] == '1') {

                                setcookie("id", $row['id'], time() + 60*60*24*365);

                            } 

                            header("Location: loggedinpage.php");
                                
                        } else {
                            
                            $error = "That email/password combination could not be found.";
                            
                        }
                        
                    } else {
                        
                        $error = "That email/password combination could not be found.";
                        
                    }
                    
                }
            
        }
        
        
    }


?>

<?php include("header.php"); ?>


      <div class ="container" id="firstPage">

      <h1>Secret Diary</h1>     

       <div id="error"><?php echo $error; ?></div>

        <form method="post" id="signUpForm">
 
        <div class="form-group">
             <label for="email">Email address</label>
            <input class="form-control" type="email" name="email" placeholder="Your Email">
        </div>


        <div class="form-group">    
             <label for="password">Password</label>
            <input class="form-control" type="password" name="password" placeholder="Password">
        </div>
            
        <div class="form-group form-check">
            <input class="form-check-input" type="checkbox" name="stayLoggedIn" value=1>
            <label class="form-check-label" for="exampleCheck1">Remember me</label>
        </div>
            
            <input type="hidden" name="signUp" value="1">
                
            <button type="submit" name="submit" value="Sign up" class="btn btn-success">Sign up</button>
             
             <p><a class="toggleForm">Log in</a></p>


        </form>


        <form method="post" id="logInForm">
 
        <div class="form-group">
             <label for="email">Email address</label>
            <input class="form-control" type="email" name="email" placeholder="Your Email">
        </div>


        <div class="form-group">    
             <label for="password">Password</label>
            <input class="form-control" type="password" name="password" placeholder="Password">
        </div>
            
        <div class="form-group form-check">
            <input class="form-check-input" type="checkbox" name="stayLoggedIn" value=1>
            <label class="form-check-label" for="exampleCheck1">Stay Logged In</label>
        </div>
            
            <input type="hidden" name="signUp" value="0">
                
            <button type="submit" name="submit" value="Log in" class="btn btn-success">Log in</button>

            <a class="toggleForm">Sign up</a>

        </form>

</div>

</html>

