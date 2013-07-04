<?php
	$user = new User;
	class User {
		/**
		 * is_logged_in
		 * @return boolean 
		 */
		public static function is_logged_in(){
			if(isset($_SESSION['uid'])){
				if(self::user_id_exists($_SESSION['uid'])){
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		}
		/**
		 * user_id_exists
		 * @param  int $uid [user id name]
		 * @return bool  
		 */
		public static function user_id_exists($uid){
			$query = mysql_query('SELECT `id` FROM `members` WHERE `id` = '.$uid);
			if(mysql_num_rows($query) > 0){
				return true;
			} else {
				return false;
			}
		}/**
		 * 	LOGOUT 
		 * 	@return  bool
		 */
		public static function logout(){
			unset($_SESSION['uid']);
			unset($_SESSION['files']);
			return true;
		}
		/**
		 * getUsername
		 * @param  int $uid user id number
		 * @return resource      [mysql resource]
		 */
		public static function getUsername($uid){
			$query = mysql_query('SELECT `username` FROM `members` WHERE `id` = '.mysql_real_escape_string($uid));
			return mysql_result($query,0);
		}
		public static function check_user_exists($username) {
			$query = mysql_query('SELECT `id` FROM `members` WHERE `username` = "'.mysql_real_escape_string($username).'"');
			if(mysql_num_rows($query) > 0){
				return true;
			} else {
				return false;
			}
		}
		/**
		 *	 Salt.php must be ran before you can use hash function
		 *  	 @param  string  [The string you pass in, will be hashed]
		 *              @return string [128-bit encryption hash]
		 */
		public static function hash($string){
			$salt = file_get_contents('private/salt.txt');
			return hash_hmac('whirlpool',$string,$salt);
		}
		/**
		 * register
		 * @param  text $username username
		 * @param  text $password password
		 * @return bool           
		 */
		public static function register($username,$password){
			$query = mysql_query('INSERT INTO `members` (`username`,`password`) VALUES ("'.mysql_real_escape_string($username).'","'.self::hash($password).'")');
			if($query === true){
				return true;
			} else {
				return false;
			}
		}
		/**
		 * get_user_id
		 * @param  text $username username
		 * @param  text $password password
		 * @return int           user id
		 */
		public static function get_user_id($username,$password){
			$query = mysql_query('SELECT `id` FROM `members` WHERE `username` = "'.mysql_real_escape_string($username).'" AND `password` = "'.self::hash($password).'"');
			if(mysql_num_rows($query) > 0){
				$uid = mysql_result($query,0);
				return $uid;
			} else {
				return false;
			}
		}
		/**
		 * login
		 * @param  int $uid user id number
		 * @return none      
		 */
		public static function login($uid) {
			$_SESSION['uid'] = $uid;
		}
	}