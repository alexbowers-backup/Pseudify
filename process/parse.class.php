<?php
/**
 * 	This class holds all the code related to parsing and converting the pseudo code to tokens, and tokens to code.
 */
class parse {
		public $T_ERROR;
		public $T_STR;
		public $T_NUM;
		public $T_VAR;
		public $queue;
	/**
	 * 	Parse constructor / initializer.
	 *  	@param string  $string [Pseudo code as a string]
	 *             @param boolean $debug  default true [Whether to output debug information in file downloaded]
	 */
	function __construct($string){
		$this->string = $string;
		$this->T_ERROR = new SplQueue();
		$this->T_STR = new SplQueue();
		$this->T_NUM = new SplQueue();
		$this->T_VAR = new SplQueue();
		$this->queue = new SplQueue();
		require_once('process/grammar_tree.php');
	}
	/**
	*	 nenqueue [Add to a declared queue]
	*  	@param  string $data [The token to be stored in a queue]
	*              @param  string $variable [The name of the queue to be stored into] 
	 */
	public function nenqueue($data,$variable){
		// Dynamic add to queues
		$this->{$variable}->enqueue($data);
	}
	/**
	 * 	error_return [Returns the Line numbers of any errors within the file]
	 *  	@return string [JSON encoded array or false]
	 */
	public function error_return(){
		$line_number = 1;
		foreach($this->queue as $token){
			// For each newline token, increase line counter by 1
			if($token == 'T_NEW_LINE'){
				$line_number++;
			}
			// if there is an error token, add to error array
			if($token == 'T_ERROR'){
				$err[] = array('line' => $line_number);
			}
		}
		// For every error in the T_ERROR queue, add to the array
		foreach($this->T_ERROR as $v){
			$err[] = array('line' => $v);
		}
		if(!empty($err)){
			// Send json encoded string to browser with all array error lines in it.
			return json_encode($err);
		}
		return false;
	}
	/**
	 * error_check
	 * @return SplQueue object
	 */
	public function error_check(){
		if($this->T_ERROR->isEmpty()){
			return $this->T_ERROR;
		} else {
			return false;
		}
	}
	/**
	 * to_code
	 * @param  string $language language name
	 * @return string [converted code in their chosen language syntax]
	 */
	public function to_code($language){
		require_once('languages/'.$language.'.php');
		$tab_count = 0;
		
		$output = $code['T_START'] . "\n";

		$output .= $code['T_START_COMMENT'] . "\n";
		$output .= "\t Date Created: " . date("l jS F Y, g:i:s T");
		$output .= "\n\t Processed By: http://pseudify.com";
		$output .= "\n" . $code['T_END_COMMENT'] . "\n\n";
		$newlinebool = false;
		$place_tabs = true;
		$delim = false;
		// Go through the queue, for each token
		foreach($this->queue as $token){
			// If it isn't a new line element then force deliminator (if language uses / requires)
			if($token !== 'T_NEW_LINE'){
				$delim = true;
			}
			if($token == 'T_ELSE_START'){
				$tab_count--;
			}
			if($token == 'T_ELIF_START'){
				$tab_count--;
			}
			// Place the tab.
			// Creates pretty indentation for readable code.
			if($place_tabs == true){
				for($i = 0; $i < $tab_count; $i++){
					$output .= "\t";
				}
			}
			$place_tabs = false;
			if($token == 'T_OPEN_BLOCK'){
				$output .= $code[$token];
				$delim = false;
				$tab_count++;
			}elseif($token == 'T_CLOSE_BLOCK'){
				$output = substr($output,0,-1);
				$output .= $code[$token];
				$delim = false;
				$tab_count--;
			}elseif($token == 'T_ELSE_START'){
				$delim = false;
				$output .= $code[$token];
				$tab_count++;
			}elseif($token == 'T_ELIF_START'){
				$delim = false;
				$output .= $code[$token];
				$tab_count++;
			} elseif($token == 'T_VAR'){
				$temp = $code[$token];
				// Replace the placeholder 'value' with the name of the variable from T_VAR queue
				$output .= preg_replace('/\[value\]/',$this->T_VAR->dequeue(),$temp);
				$delim = true;
			} elseif($token == 'T_STR'){
				$temp = $code[$token];
				// Replace the placeholder 'value' with the content of the string from T_STR queue
				$output .= preg_replace('/\[value\]/',$this->T_STR->dequeue(),$temp);
				$delim = true;
			} elseif($token == 'T_NEW_LINE'){
				if($delim == true){
					$output = substr($output,0,-1);
					$output .= $code[$token] . "\n";
					$delim = false;
				} else {
					$output .= "\n";
				}
				$place_tabs = true;
			} elseif($token == 'T_PRINT'){
				$temp = $code[$token];
				if(preg_match('/\[value\]/',$temp)){
					$t2 = next($this);
					die($token . " : " . $t2 . " : " . $temp);
					if($t2 == 'T_STR'){
						$temp2 = $code[$t2];
						// Replace the placeholder 'value' with the name of the variable from T_VAR queue
						$output2 .= preg_replace('/\[value\]/',$this->T_STR->dequeue(),$temp2);
					} elseif($t2 == 'T_VAR') {
						$temp2 = $code[$t2];
						// Replace the placeholder 'value' with the name of the variable from T_VAR queue
						$output2 .= preg_replace('/\[value\]/',$this->T_VAR->dequeue(),$temp2);
					} elseif($t2 == 'T_NUM') {
						$temp2 = $code[$t2];
						// Replace the placeholder 'value' with the name of the variable from T_VAR queue
						$output2 .= preg_replace('/\[value\]/',$this->T_NUM->dequeue(),$temp2);
					} else {
						$output2 = "null"; // If it doesn't work.
					}
					$output .= preg_replace('/\[value\]/',$output2,$temp);
				} else {
					$output .= $temp;
				}
				$delim = true;
			} elseif($token == 'T_NUM'){
				$temp = $code[$token];
				$output .= preg_replace('/\[value\]/',$this->T_NUM->dequeue(),$temp);
				$delim = true;
			} else {
				$output .= $code[$token];
				$delim = true;
			}

			$output .= " ";
		}
		$tab_count--;
		// Align the closing tags to be in proper inentation (remove the spaces)
		$output = rtrim($output);
		$output .= "\n\n";
		$output .= $code['T_END_FILE'];
		return $output;
	}
	/**
	 * tree_search
	 *
	 *	Tokenizes the pseudo code to tokens.
	 * 
	 * @param  string $input pseudo code segment.
	 * @param  array $tree  syntax tree
	 * @return  string       [token name]
	 */
	public function tree_search($input, $tree) {
		// If the current array layer has a key of 'token' then...
		if(array_key_exists('token',$tree)){ 
			// If the regular expression in the current array layer matches, and stores any capture group data into $returned_data variable
			if(preg_match($tree['regex'],$input,$returned_data) == true){
				// Check the returned data and return the token
				self::check_return($tree['token'],$returned_data);
				return $tree['token'];
			} else {
				return null;
			}
		} else {
			// Check the regular expression at the current layer
			if(preg_match($tree['regex'], $input) == true){
				// Go through the current layer
				foreach($tree as $i => $j){
					if(is_array($j)){
						// Recursively run the code for the new layer
						$result =  $this->tree_search($input, $j);
						if($result != null){
							return $result;
						}
					}
				}
			}
		}
	}
	/**
	 * line_syntax_check
	 *
	 *	Seperates tokens to lines, and checks grammar per line
	 * 
	 * @param  string $string       [string of tokens from SplQueue for each line of code]
	 * @param  array $grammar_tree grammar tree array
	 * @return bool               
	 */
	public function line_syntax_check($string, $grammar_tree){
		foreach($grammar_tree as $k => $v){
			// Check the regular expressions in the grammar tree against the string passed.
			if(preg_match($v,$string)){
				return true;
			}
		}
		return false;
	}
	/**
	 * syntax_check
	 * 	Checks the syntax 
	 * @return none
	 */
	public function syntax_check(){
		$queue = $this->queue;
		$line = 1;
		$string = "";
		$bool = false;
		foreach($queue as $k => $v){
			// If current token is a new line, then check the grammar of the current string
			// which is stored as a string of tokens
			if($v == 'T_NEW_LINE'){
				$string = trim($string);
				// Send the line to be grammar checked.
				if($this->line_syntax_check($string,$this->grammar_tree) === true){
					$string = '';
				} else {
					$bracket_count--;
					$string = '';
					
					if($bool === true){
						// Add the current line to the error queue
						$this->nenqueue($line,'T_ERROR');
					}

				}
				$line++;
				$bool = false;
			} else {
				$bool = true;
				$string .= $v . ' ';
			}
		}
		return false;
	}
	/**
	 * check_return
	 * @param  string $token the token passed
	 * @param  string $data  
	 * @return none        
	 */
	private function check_return($token, $data){
		// All possible queues
		$token_variables = array('T_ERROR','T_STR','T_NUM','T_VAR');
		// If the queue that should be added, exists in the array above, add to the queue.
		if(in_array($token,$token_variables)){
			$this->nenqueue($data['data'], $token);	
		} else {
			return false;
		}
	}
}
