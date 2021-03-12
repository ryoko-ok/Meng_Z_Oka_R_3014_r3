<?php

function createUser($user_data)
{
    ## 1. Run the proper SQL query to insert user
    $pdo = Database::getInstance()->getConnection();

    $create_user_query = 'INSERT INTO tbl_user(user_fname, user_name, user_pass, user_email)';
    $create_user_query .= ' VALUES(:fname, :username, :password, :email)';
 
    $create_user_set = $pdo->prepare($create_user_query);
    $create_user_result = $create_user_set->execute(
        array(
            ':fname'=> $user_data['fname'],
            ':username'=> $user_data['username'],
            ':password'=> $user_data['password'],
            ':email'=> $user_data['email'],
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

function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $password = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $password[] = $alphabet[$n];
    }
    return implode($password); //turn the array into a string
}


