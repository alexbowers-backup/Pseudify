<?php

/**
 * 	Sessions are being used to enable data to be passed between the front-end and the backend.
 */
session_start();
unset($_SESSION['errors']); // Clearing the errors session, since code has been resubmitted, current errors no longer apply.

/**
 * 	If code has been submitted for download, and is not empty, store the code within a session in order to display it when the user returns.
 * 	If no code has been submitted, a status is returned so the user can see what happened.
 */
if(isset($_POST['code']) && !empty($_POST['code'])){
	$string = $_POST['code'];
	$_SESSION['files']['pseudo_code'] = $string; 
	/**
	 * 	If the user submitted a language preference, then use this language.
	 *  	If not, then use php as default.
	 */
	if(isset($_GET['language']) && !empty($_GET['language'])){
		/**
		 * 	An array of available languages is compiled from the database.
		 *  	If the language the user chose isn't available, php is used.
		 */
		$language = trim($_GET['language']);
	} else {
		$language = 'php';
	}
	// Check filename is set
	if(isset($_GET['filename']) && !empty($_GET['filename'])){
		$filename = trim($_GET['filename']);
	} else {
		$filename = 'Untitled';
	}
} else {
	$_SESSION['status']['text'] = "No Code Submitted";
	$_SESSION['status']['color'] = 'red';
	header('Location: index.php'); // Redirect the user back to the index page.
}
/**
 * 	Include the syntax tree, which is a multidimensional array. 
 * 	This tree stores the pseudo code to token arrays and regular expressions.
 * 	More details on each regular expression provided within the file.
 */
require_once('process/syntax_tree.php');
/**
 * 	Include the parse class.
 */
require('process/parse.class.php');
/**
 *  	Separate the code provided by end of line. 
 *  	Each new line appears as a new index in an array.
 */
$array = explode(PHP_EOL, $string);
$queue = new SplQueue();
$errors = array();
/**
 * 	For every key value pair from array, separate them by spaces, unless surrounded by quotes (assumed string)
 * 	Store the return values in array2.
 */
foreach($array as $k => $v){
	preg_match_all('/"(?:\\\\.|[^\\\\"])*"|\S+/',$v, $array2[$k]);
}
/**
 *	Create a new parse object. 
 *	Initalized with the code passed by string.
 */
$parser = new Parse($string); 
$errorbool = false;
/**
 * 	Separate the multidimensional array into 1 dimension. 
 * 	Due to the complex nature of multiple lines with multiple tokens, three nested foreach loops are required.
 */
foreach($array2 as $k => $v){
	// new line seperator
	foreach($v as $j => $w){
		// Space seperator
		foreach($w as $l => $x){
			if($errorbool === false){
				// Use token to check if a regular expression matches in tree. Fetches the token or null.
				$result = $parser->tree_search($x,$tree);
				// If null, then there is no token matching. 
				// Because no token matches, it is an error. 
				if(is_null($result)){
					$result = 'T_ERROR';
					$errorbool = true;
				}
				// Add token to the queue
				$parser->nenqueue($result,'queue');
			}
		}
		// Since the code was seperated by lines in input, output should have lines too. This keeps the line count the same as the input.
		$parser->nenqueue('T_NEW_LINE','queue');
		// Reset the errorbool after every line.
		// Since only one error needs to be alerted per line maximum, resetting per line makes more sense than per token.
		$errorbool = false;
	}
}

/**
 * 	The entire code has been seperated by tokens now, so a syntax check can be ran on the tokens.
 */
$parser->syntax_check();
/**
 * 	If there were no errors, set the HTTP headers to automatically download the file output, and send the code to be converted.
 */
if($parser->T_ERROR->isEmpty() && $parser->error_return() === false){
	header('Pragma: public'); 
	header('Expires: 0'); 
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Content-Type: text/html');
	header('Content-Disposition: attachment; filename="'.$filename.'.'.$language.'"');
	header('Content-Transfer-Encoding: binary');
	header('Connection: close');
	/**
	 * 	Convert tokens to the chosen language.
	 */
	echo $parser->to_code($language);
	$_SESSION['status']['text'] = "Processed";
	$_SESSION['status']['color'] = 'green';
} else {
	/**
	 * 	Send the error list in Javascript Object Notation to the front end.
	 */
	$_SESSION['status']['text'] = "Syntax Error";
	$_SESSION['status']['color'] = 'red';
	$_SESSION['errors'] = $parser->error_return(); 
	header('Location: index.php');
}