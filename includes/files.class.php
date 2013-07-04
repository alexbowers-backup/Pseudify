<?php
	require('connect.php');
	$files = new Files();
	class Files {
		/**
		 * getFilesByUser
		 * @param  int [user id number]
		 * @return array [list of filenames and their ID]
		 */
		public static function getFilesByUser($uid){
			$query = mysql_query('SELECT `filename`,`id` FROM `files` WHERE `user_id` = '.mysql_real_escape_string($uid));
			if(mysql_num_rows($query) > 0){
				while($row = mysql_fetch_assoc($query)){
					$return[] = array('filename' => $row['filename'], 'id' => $row['id']);
				}
				return $return;
			} else {
				return false;
			}
		}
		/**
		 * create_first_file
		 * @param  [int] $uid [user id number]
		 * @return [bool]
		 */
		public static function create_first_file($uid){
			$query = mysql_query('INSERT INTO `files` (`user_id`,`filename`) VALUES ('.$uid.',"Index")');
			if($query === true){
				$_SESSION['files']['current_file_id'] = mysql_insert_id();
				return true;
			} else {
				return false;
			}
		}
		/**
		 * get_first_file
		 * @param  int $uid user id number
		 * @return bool     
		 */
		public static function get_first_file($uid){
			$query = mysql_query('SELECT `id`,`filename`,`pseudo_code` FROM `files` WHERE `user_id` = '.$uid. ' LIMIT 0,1');
			if(mysql_num_rows($query) > 0){
				$row = mysql_fetch_assoc($query);
				$_SESSION['files']['current_file_id'] = $row['id'];
				$_SESSION['files']['current_file_name'] = $row['filename'];
				$_SESSION['files']['pseudo_code'] = $row['pseudo_code'];
				return true;
			} else {
				return false;
			}
		}
		/**
		 * save_file
		 * @param  int $file_id file id number
		 * @param  int $user_id user id number
		 * @param  text $code    pseudo code submitted
		 * @return bool        
		 */
		public static function save_file($file_id, $user_id,$code){
			$query = mysql_query('UPDATE `files` SET `pseudo_code` = "'.$code.'" WHERE `id`= '.$file_id.' AND `user_id`= '.$user_id);
			$_SESSION['files']['pseudo_code'] = $code;
			if($query === true){
				return true;
			} else {
				return false;
			}
		}
		/**
		 * delete_file
		 * @param  int $file_id file id number
		 * @param  int $user_id user id number
		 * @return bool          
		 */
		public static function delete_file($file_id, $user_id){
			$query = mysql_query('DELETE FROM `files` WHERE `user_id` = '.$user_id .' AND `id` = '.$file_id);
			if($query === true){
				return true;
			} else {
				return false;
			}
		}
		/**
		 * get_file
		 * @param  int $file_id file id number
		 * @param  int $uid     user id number
		 * @return bool          
		 */
		public static function get_file($file_id,$uid){
			$query = mysql_query('SELECT `pseudo_code`,`filename` FROM `files` WHERE `id`='.$file_id.' AND `user_id` = '.$uid);
			if(mysql_num_rows($query) > 0){
				$row = mysql_fetch_assoc($query);
				$_SESSION['files']['current_file_id'] = $file_id;
				$_SESSION['files']['current_file_name'] = $row['filename'];
				$_SESSION['files']['pseudo_code'] = $row['pseudo_code'];
				return true;
			} else {
				return false;
			}
		}
		/**
		 * get_languages
		 * @return array [list of name and extensions]
		 */
		public static function get_languages(){
			$query = mysql_query('SELECT `name`,`extension` FROM `languages`');
			if(mysql_num_rows($query) > 0){
				while($row = mysql_fetch_assoc($query)){
					$return[] = array('name' => $row['name'], 'extension' => $row['extension']);
				}
				return $return;
			} else {
				return false;
			}
		}
		/**
		 * rename_file
		 * @param  int $file_id   file id name
		 * @param  int $uid       user id name
		 * @param  text $file_name file name
		 * @return bool
		 */
		public static function rename_file($file_id,$uid,$file_name){
			$query = mysql_query('UPDATE `files` SET `filename` = "'.$file_name.'" WHERE `id` = '.$file_id.' AND `user_id` = '.$uid);
			if($query === true){
				return true;
			} else {
				return false;
			}
		}
		/**
		 * new_file
		 * @param  text $filename file name
		 * @param  int $uid      user id number
		 * @return bool           
		 */
		public static function new_file($filename,$uid){
			$query = mysql_query('INSERT INTO `files` (`filename`,`user_id`) VALUES ("'.$filename.'",'.$uid.')');
			if($query === true){
				return true;
			} else {
				return false;
			}
		}
		/**
		 * get_filename
		 * @param  int $file_id file id number
		 * @param  int $user_id user id number
		 * @return bool          
		 */
		public static function get_filename($file_id,$user_id){
			$query = mysql_query('SELECT `filename` FROM `files` WHERE `user_id` = '.$user_id. ' AND `id` = '.$file_id);
			if(mysql_num_rows($query) > 0){
				$row = mysql_fetch_assoc($query);
				$_SESSION['misc']['filename'] = $row['filename'];
				return true;
			} else {
				return false;
			}
		}
	}