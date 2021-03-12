<!-- for any helper functions for login/log out -->
<?php
function login($username, $password, $ip) {

    ## TODO remove the following debug when done
    //return 'You are trying to login with Username:'.$username.'Password:'.$password;

    //database connecters
    $pdo = Database :: getInstance() -> getConnection();
    #TODO: Finish the following query to check if the username and passowrd are matching in DB
    $get_user_query = ' SELECT * FROM tbl_user  WHERE user_name = :username AND user_pass = :password ';
    $user_set = $pdo -> prepare($get_user_query);
    $user_set -> execute(
        array(
            ':username' => $username,
            ':password' => $password
        )
    );

    if($found_user = $user_set -> fetch(PDO::FETCH_ASSOC)){//PDO::FETCH_ASSOC tells PDO to return the result as an associative array.
       //if found user exist in user database, get him in!

        $found_user_id = $found_user['user_id'];//get user id
        //write the username and userID into session
        $_SESSION['user_id'] = $found_user_id;
        $_SESSION['user_name'] = $found_user['user_fname'];
        $_SESSION['success_login_number'] = $found_user['success_login_number']+1;
      

        //update the user IP by the curren logged in one
        $update_user_query = 'UPDATE tbl_user SET user_ip = :user_ip WHERE user_id = :user_id';
        $update_user_set = $pdo -> prepare($update_user_query);
        $update_user_set -> execute(
            array(
                ':user_ip' => $ip,
                ':user_id' => $found_user_id
            )
        );
        $_SESSION['user_ip'] = $found_user['user_ip'];//write user_ip in session


        //update the user last login time by the current loggod in one 
        $update_user_query = 'UPDATE tbl_user SET last_login_time =now() WHERE user_id = :user_id';
        $update_user_set = $pdo -> prepare($update_user_query);
        $update_user_set -> execute(
            array(
                ':user_id' => $found_user_id
                
            )
        );
        $_SESSION['user_lastlogintime'] = $found_user['last_login_time'];//write user_lastlogintime in session

       
        //update the number of successfully login
        $update_user_query = 'UPDATE tbl_user SET success_login_number=:success_login_number WHERE user_id=:user_id';
        $update_user_set = $pdo->prepare($update_user_query);
        $update_user_set->execute(
            array(
                ':user_id'=>$found_user_id,
                ':success_login_number'=>$_SESSION['success_login_number']
                )
        );
        
       ##TODO : debug only, will change here
       //return 'Hello, ' . $username . '!  <br />  Your IP address (using $_SERVER[\'REMOTE_ADDR\']) is ' . $ip . '<br /><br />';
               
       //after login in succes, redirect user back to index.php, redirect_to function
       redirect_to('index.php');

    }else{

       //this is invaild attemp, reject it!
       echo "<br />\n";
       return "Sorry, your username or password isn't correct. ";
    }
}


//only login in user can see the index.php, otherwise, rediect to login page
function confirm_logged_in(){
    if(!isset($_SESSION['user_id'])){
          redirect_to('admin_login.php');
    }
}


//if user log out, redirect user to admin_login.php
function logout(){
    session_destroy();
    redirect_to('admin_login.php');
}