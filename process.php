<?php
session_start();
require("connection.php");


//Register Process
if(isset($_POST['register_submit']) && ($_POST['register_submit'] == 'register')){
	//Set session variable for each input, spaces trimmed from right
	foreach($_POST as $name => $value)
	{
		$_SESSION['register'][$name] = rtrim($value);
		//validate user input to check if empty or only spaces were entered
		if(strlen($_SESSION['register'][$name])===0)
		{
			$_SESSION['register_errors'][$name] = "Cannot Be Blank";
		}
	}
	//Additional registration error checking
	if(filter_var($_SESSION['register']['email'], FILTER_VALIDATE_EMAIL)===false){
		$_SESSION['register_errors']['email'] = "Email Is Not Valid";
	}
	$esc_email = mysqli_real_escape_string($connection, $_SESSION['register']['email']);
	$esc_email = strtolower($esc_email);
	$checkForEmail = "SELECT email FROM users WHERE email = '{$esc_email}'";

	$result = mysqli_query($connection,$checkForEmail);

	$user = mysqli_fetch_array($result, MYSQLI_ASSOC);

	if($esc_email === $user['email'])
	{
		$_SESSION['register_errors']['email'] = "Email Is Already Registered";
	}

	if($_SESSION['register']['password'] !== $_SESSION['register']['confirm_password'])
	{
		$_SESSION['register_errors']['confirm_password'] = "Password and Confirmation Do Not Match";
	}
	//If no errors process registration
	if(empty($_SESSION['register_errors']))
	{
		$esc_first_name = mysqli_real_escape_string($connection, ucwords($_SESSION['register']['first_name']));
		$esc_last_name = mysqli_real_escape_string($connection, ucwords($_SESSION['register']['last_name']));
		//$salt = bin2hex(openssl_random_pseudo_bytes(22));
		$encrypted_password = md5($_SESSION['register']['password']);
		$registerUserQuery = "INSERT INTO users(first_name, last_name, email, password, created_at, updated_At)
							  VALUES ('{$esc_first_name}','{$esc_last_name}','{$esc_email}','{$encrypted_password}', NOW(), NOW())";
		if(mysqli_query($connection, $registerUserQuery))
		{
			$_SESSION['success']['register'] = "You have successfully registered. Login to continue";
			unset($_SESSION['register']);
			header('Location:login.php');
		} 
		else
		{
			$_SESSION['register_errors']['register'] = "Registration failed, please retry";
			header('Location:login.php');
		}
	} 
	else
	{
		header('Location:login.php');
	}
}

//Login Process
if(isset($_POST['login_submit']) && ($_POST['login_submit'] == 'login')){
	foreach($_POST as $name => $value)
	{
		$_SESSION['login'][$name] = rtrim($value);
		//validate user input to check if empty or only spaces were entered
		if(strlen($_SESSION['login'][$name])===0)
		{
			$_SESSION['login_errors'][$name] = "Email/Password Cannot Be Blank";
		}
	}
	if(empty($_SESSION['login_errors']))
	{
		$esc_email = mysqli_real_escape_string($connection, $_POST['email']);
		$esc_email = strtolower($esc_email);
		$checkForEmail = "SELECT * FROM users WHERE email = '{$esc_email}'";
		$result = mysqli_query($connection,$checkForEmail);

		$user = mysqli_fetch_array($result, MYSQLI_ASSOC);
		if(!empty($user))
		{
			$encrypted_password = md5($_POST['password']);
			if($encrypted_password != $user['password'])
			{
				$_SESSION['login_errors']['email'] = "Email/Password Do Not Match";
				header('Location:login.php');
			} 
			else if ($encrypted_password == $user['password'])
			{
				$_SESSION['success']['login'] = "You Have Successfully Logged In";
				$_SESSION['logged_user'] = array("id" => $user['id'], 'first_name' => $user['first_name'], 'last_name' => $user['last_name']);
				unset($_SESSION['login']);
				header('Location:index.php');
			}
		}
		else
		{
			$_SESSION['login_errors']['email'] = "Email/Password Do Not Match";
			header('Location:login.php');
		}

	} 
	else
	{
		header('Location:login.php');
	}
}

//Message Post
if(isset($_POST['message_post']) && ($_POST['message_post'] == 'message')){
	$_SESSION['message'] = rtrim($_POST['message']);
	if(strlen($_SESSION['message']===0))
	{
		$_SESSION['message_errors'] = "Message is BLANK BLANK DAMMIT BLANK!?!?!";
		header('Location:index.php');
	}
	if(empty($_SESSION['message_errors']))
	{
		$esc_message = mysqli_real_escape_string($connection, $_SESSION['message']);
		$messageQuery = "INSERT INTO messages(user_id, message, created_at,updated_at)
						 VALUES ('{$_SESSION['logged_user']['id']}','{$_SESSION['message']}',NOW(),NOW())";
		if(mysqli_query($connection,$messageQuery))
		{
			$_SESSION['success']['message'] = "Message Successfully Posted";
			unset($_SESSION['message']);
			header('Location:index.php');
		}

	}
	else
	{
		header('Location:index.php');
	}
}

//Comment POST
if(isset($_POST['comment_post']) && ($_POST['comment_post'] == 'comment')){
	$_SESSION['comment'] = rtrim($_POST['comment']);
	if(strlen($_SESSION['comment']===0))
	{
		$_SESSION['comment_errors'] = "Comment is BLANK BLANK DAMMIT BLANK!?!?!";
		header('Location:index.php');
	}
	if(empty($_SESSION['comment_errors']))
	{
		$esc_message = mysqli_real_escape_string($connection, $_SESSION['comment']);
		$messageQuery = "INSERT INTO comments(user_id, message_id, comment, created_at,updated_at)
						 VALUES ('{$_SESSION['logged_user']['id']}','{$_POST['message_id']}','{$_SESSION['comment']}',NOW(),NOW())";
		if(mysqli_query($connection,$messageQuery))
		{
			$_SESSION['success']['comment'] = "Comment Successfully Posted";
			unset($_SESSION['comment']);
			header('Location:index.php');
		}

	}
	else
	{
		header('Location:index.php');
	}
}

//Logoff
if(isset($_GET['id']) && ($_GET['id']=='logout')){
	unset($_SESSION);
	session_destroy();
	session_start();
	header('Location:login.php');
}

//Delete Comment
if(isset($_POST['delete_comment']) && ($_POST['delete_comment'] == 'delete')){
	$checkUserId = "SELECT user_id FROM comments WHERE id = {$_POST['comment_id']}";
	$result = mysqli_query($connection, $checkUserId);
	$result = mysqli_fetch_array($result,MYSQLI_ASSOC);
	if($_SESSION['logged_user']['id']===$result['user_id'])
	{
		$deleteComment = "DELETE FROM comments WHERE id = {$_POST['comment_id']}";
		if(mysqli_query($connection,$deleteComment))
		{
			$_SESSION['success']['comment_deleted'] = "You Have Successfully Uncontributed Your Comment.  Keep Going.";
			header('Location:index.php');
		}
		else
		{
			$_SESSION['delete_errors'] = "Comment has failed to be deleted. Stop breaking my code.";
			header('Location:index.php');
		}
	}
	else
	{
		echo "YOU NOT THE RIGHT USER GET OUT OF HERE!";
	}
}
else
{
	header('Location:index.php');
}


//Delete Message
if(isset($_POST['delete_message']) && ($_POST['delete_message'] == 'delete')){
	$checkUserId = "SELECT user_id FROM messages WHERE id = {$_POST['message_id']}";
	$result = mysqli_query($connection, $checkUserId);
	$result = mysqli_fetch_array($result,MYSQLI_ASSOC);
	if($_SESSION['logged_user']['id']===$result['user_id'])
	{
		$deleteMessage = "DELETE FROM messages WHERE id = {$_POST['message_id']}";
		if(mysqli_query($connection,$deleteMessage))
		{
			$_SESSION['success']['comment_deleted'] = "You Have Successfully Uncontributed Your Message.  Keep Going.";
			header('Location:index.php');
		}
		else
		{
			$_SESSION['delete_errors'] = "Message has failed to be deleted. Stop breaking my code.";
			header('Location:index.php');
		}
	}
	else
	{
		echo "YOU NOT THE RIGHT USER GET OUT OF HERE!";
	}
}
else
{
	header('Location:index.php');
}
?>