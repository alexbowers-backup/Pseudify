<?php
	// Set HTTP Headers
	session_start();
	session_cache_limiter('nocache');
	header('Expires: ' . gmdate('r', 0));
	header('Content-type: application/json');
	require('includes/connect.php');
	require('includes/files.class.php');
	require('includes/user.class.php');
	// If the user has sent using the register form.
	if(isset($_GET['register'])){
		// Check if the username exists in thedatabase
		$return = $user::check_user_exists($_POST['registerFormUsername']);
		// Store username entered for if there is an error, it will be stored in the input field.
		$_SESSION['flash']['register_username'] = $_POST['registerFormUsername'];
		// If username exists, return error.
		if($return === true){
			$_SESSION['error']['register_username'] = "Username already exists";
		} else {
			// If password and password confirmation are the same
			if($_POST['registerFormPassword'] === $_POST['registerFormPassword2']){
				// Register the users account
				$response = $user::register($_POST['registerFormUsername'],$_POST['registerFormPassword']);
				// Get the id of the user who just registered.
				$userid = $user::get_user_id($_POST['registerFormUsername'],$_POST['registerFormPassword']);
				if($userid !== false){
					// Create a default file
					if($files::create_first_file($userid) === false){
						// return file creation error
						$_SESSION['error']['register_misc'] = "File creation failed.";
					}
					// Log the user in
					$user::login($userid);
				} else {
					// return login failed error.
					$_SESSION['error']['register_misc'] = "Login Failed";
				}
			} else {
				// Return passwords don't match error.
				$_SESSION['error']['register_password'] = "Passwords don't match";
			}
		}
		header('Location: index.php');
	}
	// If the user has submitted the login form
	if(isset($_GET['login'])){
		// Check if the username exists
		$return = $user::check_user_exists($_POST['loginFormUsername']);
		// Temporary store the username for if an error happens. Populates the login username field upon error.
		$_SESSION['flash']['login_username'] = $_POST['loginFormUsername'];
		if($return === false){
			// return error username doesn't exist.
			$_SESSION['error']['login_username'] = "Username doesn't exist";
		} else {
			// get the users id from the data provided by the login form.
			$userid = $user::get_user_id($_POST['loginFormUsername'],$_POST['loginFormPassword']);
			if($userid !== false){
				// Load first file (with smallest ID, eg. first created)
				if($files::get_first_file($userid) === false){
					// send error text.
					$_SESSION['status']['text'] = "File Load Failed";
					$_SESSION['status']['color'] = "red";
				} else {
					// Send status text
					$_SESSION['status']['text'] = "Loading File";
					$_SESSION['status']['color'] = "white";
				}
				// Login the form.
				$user::login($userid);
			} else {
				// Return the username and password not matching error
				$_SESSION['error']['login_details'] = "Username and password do not match out records.";
			}
		}
		header('Location: index.php');
	}
	// if the user or browser has saved the file
	if(isset($_GET['save'])){
		// Check that code and file_id has been submitted.
		if(isset($_POST['code']) && isset($_POST['file_id'])){
			// Protect against SQL injection
			$code = trim(mysql_real_escape_string($_POST['code']));
			$file_id = (int) trim(mysql_real_escape_string($_POST['file_id']));
			$user_id = (int) mysql_real_escape_string($_SESSION['uid']);
			// File is submitted to be saved
			if($files::save_file($file_id,$user_id,$code) === true){
				// Return message
				echo json_encode(array('txt' => 'Saved','clr' => 'white'));
			} else {
				// return error
				echo json_encode(array('txt' => 'Failed to save.', 'clr' => 'red'));
			}
		} else {
			// return error
			echo json_encode(array('txt' => 'No code submitted.', 'clr' => 'red'));
		}
	}
	// If the user has requested a file be opened
	if(isset($_GET['open'])){
		// Check that the user submitted a file_id
		if(isset($_POST['file_id'])){
			// SQL injection protection
			$file_id = (int) trim(mysql_real_escape_string($_POST['file_id']));
			$user_id = (int) mysql_real_escape_string($_SESSION['uid']);
			// Load file
			if($files::get_file($file_id,$user_id) === true){
				// return message
				echo json_encode(array('data' => array('filename' => $_SESSION['files']['current_file_name'], 'file_id' => $_SESSION['files']['current_file_id'], 'code' => $_SESSION['files']['pseudo_code']), 'txt' => 'File Loading.','clr' => 'white'));
			} else {
				// return error
				echo json_encode(array('data' => false, 'txt' => 'File doesn\'t exist.','clr' => 'red'));
			}
		} else {
			// return error
			echo json_encode(array('data' => false,'txt' => 'You didn\'t choose a file.','clr' => 'red'));
		}
	}
	// If the user has submitted a rename form
	if(isset($_GET['rename'])){
		// Check that the user submitted a filename, and the file_id is provided.
		if(isset($_POST['file_id']) && isset($_POST['filename'])){
			// SQL injection protection.
			$file_id = (int) trim(mysql_real_escape_string($_POST['file_id']));
			$file_name = trim(mysql_real_escape_string($_POST['filename']));
			$user_id = (int) mysql_real_escape_string($_SESSION['uid']);
			// Rename file
			if($files::rename_file($file_id,$user_id,$file_name) === true){
				// return message
				echo json_encode(array('txt' => 'File name updated. Refresh.', 'clr' => 'white'));
			} else {
				// return error
				echo json_encode(array('txt' => 'Unable to update the filename', 'clr' => 'red'));
			}
		} else {
			// return error
			echo json_encode(array('txt' => 'You didn\'t specify the file name.','clr' => 'red'));
		}
	}
	// If the user has requested to delete a file
	if(isset($_GET['delete'])){
		// Check that the usr provides a file_id (automatically sent, unless an attack attempt)
		if(isset($_POST['file_id'])){
			// SQL injection protection
			$file_id = (int) mysql_real_escape_string($_POST['file_id']);
			$user_id = (int) mysql_real_escape_string($_SESSION['uid']);
			// Delete file
			if($files::delete_file($file_id,$user_id) === true){
				// Load new file
				if($files::get_first_file($user_id) === true){
					echo 'loaded';
				} else {
					// If no file exists, then create a new file and load.
					if($files::create_first_file($user_id) === true){
						echo 'loaded';
					} else {
						echo 'loaded';
					}
				}
			} else {
				echo 'loaded';
			}
		}
	}
	// If user has requested to create a new file
	if(isset($_GET['newfile'])){
		// The filename the user submitted
		if(isset($_POST['filename'])){
			// SQL injection protection 
			$user_id = (int) mysql_real_escape_string($_SESSION['uid']);
			$filename = mysql_real_escape_string($_POST['filename']);
			// Create a new file
			if($files::new_file($filename,$user_id) === true){
				// return message
				echo json_encode(array('err' => false,'txt' => 'File creation Successful.', 'clr' => 'white'));
			} else {
				// return error
				echo json_encode(array('err' => true,'txt' => 'File creation failed.', 'clr' => 'red'));
			}
		} else {
			// return error
			echo json_encode(array('err' => true,'txt' => 'Please provide a filename', 'clr' => 'red'));
		}
	}
	// If user has requested to logout
	if(isset($_GET['logout'])){
		$user::logout();
		// redirect after logout.
		header('Location: index.php');
	}