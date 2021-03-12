<?php

function getUserLevelMap() // the name start from verb
{
    return array(
        '0'=>'Web Editor',
        '1'=>'Web Admin',
    );
}

function getCurrentUserLevel()
{
    $user_level_map = getUserLevelMap();

    if (isset($_SESSION['user_level']) && array_key_exists($_SESSION['user_level'], $user_level_map)) {
        return $user_level_map[$_SESSION['user_level']];
    } else {
        return "Unrecognized";
    }
}

function createUser($user_data)
{
    if(empty($user_data['username'])||isUsernameExists($user_data['username'])){
        return 'Username is invalid!';
    }

    ## 1. Run the proper SQL query to insert user
    $pdo = Database::getInstance()->getConnection();

    $create_user_query = 'INSERT INTO tbl_user(user_fname, user_name, user_pass, user_email, user_level)';
    $create_user_query .= ' VALUES(:fname, :username, :password, :email, :user_level)';
 
    $create_user_set = $pdo->prepare($create_user_query);
    $create_user_result = $create_user_set->execute(
        array(
            ':fname'=> $user_data['fname'],
            ':username'=> $user_data['username'],
            ':password'=> $user_data['password'],
            ':email'=> $user_data['email'],
            ':user_level'=> $user_data['user_level'],
        )
    );

    ## 2. Redirect to index.php if create user successfully (maybe with some message??)
    # otherwise, showing the error message
    
    if ($create_user_result) {
        $email_headers = array(
            'From'=>'info@admin.com'
        );

        // $email_result = mail($user_data['email'], 'subject', 'test', $email_headers);
        $results['fname'] = $user_data['fname'];
        $results['username'] = $user_data['username'];
        $results['password'] = $user_data['password'];
        $results['email'] = $user_data['email'];
        
        // 2. Prepare the email
        $email_subject = 'Welcome to Admin Team';
        $email_recipient = $user_data['email'];
        $email_message = sprintf('fname: %s, username: %s, password: %s, email: %s', $user_data['fname'], $user_data['username'], $user_data['password'], $user_data['email']);
        
        // 3. Send out the email
        $email_result = mail($email_recipient, $email_subject, $email_message, $email_headers);
        if ($email_result) {
            $results['message'] = sprintf('We are sorry but the email did not go through');
        } else {
            $results['message'] = sprintf('Welcome to admin team! You will be able to login from url: ');
        }        
        
        echo json_encode($results);

        redirect_to('index.php');
    } else {
        return 'The user did not go through!';
    }
}

// function randomPassword() {
//     $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
//     $password = array(); //remember to declare $pass as an array
//     $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
//     for ($i = 0; $i < 8; $i++) {
//         $n = rand(0, $alphaLength);
//         $password[] = $alphabet[$n];
//     }
//     return implode($password); //turn the array into a string
// }

function getSingleUser($user_id){
    // ## Testing line
    // echo 'You are trying to fetch user:' .$user_id; 

    $pdo = Database::getInstance()->getConnection();

    $get_user_query = 'SELECT * FROM tbl_user WHERE user_id = :id';
    $get_user_set = $pdo->prepare($get_user_query);
    $result = $get_user_set->execute(
        array(
            ':id'=>$user_id
        )
    );

    if($result && $get_user_set->rowCount()){
        return $get_user_set;
    } else {
        return false;
    }
}

function editUser($user_data){
    if(empty($user_data['username'])||isUsernameExists($user_data['username'])){
        return 'Username is invalid!';
    }
    $pdo = Database::getInstance()->getConnection();

    ## Finish the SQL query in here
    $update_user_query = 'UPDATE tbl_user SET user_fname=:fname, user_name=:username, user_pass=:password, user_email=:email, user_level=:user_level WHERE user_id = :id';
    $update_user_set = $pdo->prepare($update_user_query);
    $update_user_result = $update_user_set->execute(
        array(
            ':fname'        => $user_data['fname'],
            ':username'     => $user_data['username'],
            ':password'     => $user_data['password'],
            ':email'        => $user_data['email'],
            ':user_level'   => $user_data['user_level'],
            ':id' => $user_data['id'],
        )
    );

    // $update_user_set->debugDumpParams();
    // exit;

    if($update_user_result){
        $_SESSION['user_name'] = $user_data['fname'];
        $_SESSION['user_level'] = $user_data['user_level'];
        redirect_to('index.php');
    } else {
        return 'Guess you got canned...';
    }
}

function isCurrentUserAdminAbove(){
    return !empty($_SESSION['user_level']);
}

function isUsernameExists($username){
    $pdo = Database::getInstance()->getConnection();

    $user_exists_query = 'SELECT COUNT(*) FROM tbl_user WHERE user_name = :username';
    $user_exists_set = $pdo->prepare($user_exists_query);
    $user_exists_result = $user_exists_set->execute(
        array(
            ':username'=>$username
        )
    );

    return !$user_exists_result || $user_exists_set->fetchColumn()>0;
}
