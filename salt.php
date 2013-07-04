<?php
	if(file_exists('private/salt.txt')){
		echo 'The salt has already been generated';
	} else {
		$file_output = 'private/salt.txt';
		$salt = openssl_random_pseudo_bytes(1024);
		file_put_contents($file_output, $salt);
		echo 'The salt has been written';
	}
	
?>