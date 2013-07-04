 <?php
 session_start();
 require_once('includes/connect.php');
 require_once('includes/user.class.php');
 require_once('includes/files.class.php');
 ?><!doctype html>
<html ng-app>
	<head>
		<meta name="description" content="Pseudify allows you to convert simple pseudo code into any language. 
		Give it a go today; it's free.">
		<meta name="robots" content="all">
		<title>Pseudify </title>
		<?php
			if(isset($_SESSION['files']['current_file_name']) && !empty($_SESSION['files']['current_file_name'])){
		?>
				<title><?=$_SESSION['files']['current_file_name'];?> | Pseudify</title>
		<?php
			} else {
		?>
				<title>Pseudify</title>
		<?php
			}
		?>
		<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
		<script src="js/bootstrap.js"></script>
		<script type="text/javascript" src="js/jquery.rotate.js"></script>
		<script type="text/javascript" src="js/jquery.easing.js"></script>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.0.5/angular.min.js"></script>
		<link href='http://fonts.googleapis.com/css?family=Cantarell:700' rel='stylesheet' type='text/css'>
		<link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="css/bootstrap.css" />
		<link rel="stylesheet" href="css/style.css">
	</head>
	<body>
		<div id="sidebar">
			<div class="sidebar_container">
				<span class="sidebar_header"><p><a href="#" class="title hoverOpacity">pseudify</a></p></span>
				<div id="menu_area">
					<div id="menu_button_area">
						<p>
							<a href="#" title="Save Current File" id="saveFileLink" class="hoverOpacity" onclick="save();">
								<i class="icon-hdd icon-white"></i>
							</a>
							<a href="#" title="Create New File" id=" newFileLink" class="hoverOpacity" onclick="$('#newFile').modal();">
								<i class="icon-file icon-white"></i>
							</a>
							<a href="#" title="Download Options" id="downloadListLink" class="hoverOpacity" onclick="$('#downloadList').modal(); ">
								<i class="icon-download-alt icon-white"></i>
							</a>
							<?php
								if($user::is_logged_in() === true){
									?>
									<a href="ajax.php?logout" title="Logout" id="loginRegisterLink" class="hoverOpacity">
										<i class="icon-off icon-white"></i>
									</a>
									<?php
								} else {
									?>
									<a href="#" title="Login / Register" id="loginRegisterLink" class="hoverOpacity" onclick="$('#loginRegister').modal();">
										<i class="icon-user icon-white"></i>
									</a>
							<?php
								}
							?>
						</p>
					</div>
					<button id="menu_search">View Docs&nbsp;&raquo;</button>
				</div>
			</div>
			<div class="sidebar_container">
			<?php
				if($user::is_logged_in() === true){
					$uid = $_SESSION['uid'];
?>				
				<span class="sidebar_header"><p><?=$user::getUsername($uid);?>'s Files</p></span>
				<ul class="projects">
					<?php

						$file_array = $files::getFilesByUser($uid);
						if($file_array === false){
							echo '<center><span class="sidebar_header"><p>You don\'t have any files.</p></span></center>';
						} else {
							foreach($file_array as $file){
					?>
					<li class="close_open_files">
						<div class="projects_container" data-visibility='0'>
						<span class=""><a href="#"><i class="icon icon-white icon-chevron-right"></i></a></span>
						<span class="filename hoverOpacity"><a href="#"><?=$file['filename'];?></a></span>
							<ul class="projects_file_list">
								<li>
									<span class="filename hoverOpacity">
										<a href="#" onclick="file_open(<?=$file['id'];?>);">Open</a>
									</span>
								</li>
								<li>
									<span class="filename hoverOpacity">
										<a href="#" onclick="prep_file_rename(<?=$file['id'];?>);">Rename</a>
									</span>
								</li>
								<li>
									<span class="filename hoverOpacity">
										<a href="#" onclick="prep_file_delete(<?=$file['id'];?>);">Delete</a>
									</span>
								</li>
							</ul>
						</div>
					</li>
					<?php } } ?>
				</ul> 
				<?php
					} else {
?>
				<span class="sidebar_header"><p>Login to Use Pseudify</p></span>
				<?php
					}
				?>
				<span class="status">
					<span class="sidebar_header"><p>Status</p></span>
					<?php
						if(isset($_SESSION['status']['text'])){
							$status = trim($_SESSION['status']['text']);
						} else {
							$status = "Dormant";
						}
						if(isset($_SESSION['status']['color'])){
							$status_colour = trim($_SESSION['status']['color']);
						} else {
							$status_colour = "grey";
						}
					?>
					<span class="status_text" style="color: <?=$status_colour;?>"><?=$status;?></span>
				</span>
			</div>
		</div>
		<div id="linenosblock">
				<div class="linenos" data-row-number="1"><span class="number">1</span><span class="current">&rarr;</span></div>
			</div>
		<form id="codearea" method="post" action="process.php">
			<textarea id="textarea" name="code" spellcheck="false" onkeyup="placeNewLineNumbers(); getLineNumber(this);" onmouseup="placeNewLineNumbers(); getLineNumber(this);" placeholder="Your pseudo code goes here."><?=(isset($_SESSION['files']['pseudo_code']))?$_SESSION['files']['pseudo_code'] : '';?></textarea> 
		</form>
		<div id="docs">
			<div id="docs-container">
				<h2>Pseudify Docs</h2>
				<strong>Note: All spaces are important</strong>
				<p>With pseudify, there is no end of line delimeter (such as PHP's semi-colon). Instead, each new statement must be on a separate line.</p>
				<div id="Basics">
					<h3>Basics</h3>
					<div id="string">
						<h4>Strings</h4>
						<p>A string must be surrounded by <strong>double</strong> quotes.</p>
						<pre class="docs-code"><span class="red">"This is a string"</span></pre>
						<small>Multiline strings may not work yet. Instead, please use \n or &lt;br /&gt; inside of the string</small>
					</div>
					<div id="number">
						<h4>numbers</h4>
						<p>A number can be decimal, integer or scientific.</p>
						<pre class="docs-code"><span class="purple">4.2</span>
<span class="purple">4E+7</span>
<span class="purple">6</span></pre>
					</div>
					<div id="print">
						<h4>Output</h4>
						<p>To print a message to the screen, use the output command</p>
						<pre class="docs-code"><span class="blue">output</span> Value</pre>
						<p>Value can be a variable, string or number</p>
					</div>
				</div>
				<hr /><div id="Variables">
					<h3>Variables</h3>
					<p>Variables are declared as follows</p>
					<pre class="docs-code"><span class="blue">Variable</span>[<span class="red">"name"</span>] = <span class="purple">Value</span></pre>
					<p>Value can either be an number, string or variable.</p>
				</div>
				<hr />
				<div id="Iteration">
					<h3>Iteration</h3>
					<div id="for-loop">
						<h4>For Loop</h4>
						<p>For loops are used for iterating through a specified list or range.</p>
						<pre class="docs-code"><span class="blue">For</span> <span class="blue">Variable</span>[<span class="red">"x"</span>] = <span class="purple">Integer</span> ; <span class="blue">Variable</span>[<span class="red">"x"</span>] &lt; <span class="purple">Integer</span> ; <span class="blue">Variable</span>[<span class="red">"x"</span>] <span class="blue">inc then</span>
	<span class="gray">&hellip; Statement</span>
<span class="blue">Endfor</span></pre>
					</div>
					<div id="while-loop">
						<h4>While Loop</h4>
						<p>While loops are used for iterating through a statement, whilst it meets a certain condition.</p>
						<pre class="docs-code"><span class="blue">While</span> <span class="blue">Variable</span>[<span class="red">"x"</span>] &lt; <span class="purple">Integer</span> <span class="blue">then</span>
	<span class="gray">&hellip; Statement</span>
	<span class="gray">&hellip; <span class="blue">Variable</span>[<span class="red">"x"</span>] <span class="blue">inc</span></span>
<span class="blue">Endwhile</span></pre>
					</div>
				</div>
				<hr />
				<div id="selection">
					<h3>Selection</h3>
					<div id="if-statement">
						<h4>If Statement</h4>
						<p>Selection statements are used for comparison, often between a variable and a value.</p>
						<pre class="docs-code"><span class="blue">if</span> <span class="blue">Variable</span>[<span class="red">"name"</span>] == <span class="purple">Value</span> <span class="blue">then</span>
	<span class="gray">&hellip; Statement</span>
<span class="blue">endif</span>
						</pre>
					</div>
					<div id="else-statement">
						<h4>Else Statement</h4>
						<p>Selection statements are used for comparison, often between a variable and a value. Else allows you to have a fall back, for any occurences when the if statement doesn't catch the value.</p>
						<pre class="docs-code"><span class="blue">if</span> <span class="blue">Variable</span>[<span class="red">"name"</span>] == <span class="purple">Value</span> <span class="blue">then</span>
	<span class="gray">&hellip; Statement</span>
<span class="blue">else</span>
	<span class="gray">&hellip; Statement</span>
<span class="blue">endif</span>
						</pre>
					</div>
					<div id="elif-statement">
						<h4>Else if Statement</h4>
						<p>Selection statements are used for comparison, often between a variable and a value. Else allows you to have a fall back, for any occurences when the if statement doesn't catch the value.<br />Elseif allows you to run multiple checks before the fallback is called.</p>
						<pre class="docs-code"><span class="blue">if</span> <span class="blue">Variable</span>[<span class="red">"name"</span>] == <span class="purple">Value</span> <span class="blue">then</span>
	<span class="gray">&hellip; Statement</span>
<span class="blue">elseif</span> <span class="blue">Variable</span>[<span class="red">"name"</span>] == <span class="purple">Value</span> <span class="blue">then</span>
	<span class="gray">&hellip; Statement</span>
<span class="blue">else</span>
	<span class="gray">&hellip; Statement</span>
<span class="blue">endif</span>
						</pre>
					</div>
				</div>
			</div>
		</div>
		<div id="newFile" class="modal fade hide">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Open New File</h3>
			</div>
			<div class="modal-body">
				<form name="newFileForm" ng-controller="CtrlNewFileForm">
					<label for="newFileFormFileName">File Name</label>
					<input type="text" id="newFileFormFileName" name="newFileFormFileName" ng-model="filename" required /><br />
					<button type="submit" class="btn" onclick="checkNewFileFormFileName('{{filename}}'); $('#newFile').modal('hide');">Create</button>
				</form>
			</div>
		</div>
		<div id="renameFile" class="modal fade hide">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Rename File</h3>
			</div>
			<div class="modal-body">
				<form name="renameFileForm" ng-controller="CtrlRenameFileForm">
					<label for="renameFileFormFileName">File Name</label>
					<input type="text" id="renameFileFormFileName" name="renameFileFormFileName" ng-model="filename" required /><br />
					<button type="submit" class="btn" onclick="file_rename('{{filename}}'); $('#renameFile').modal('hide');">Rename</button>
				</form>
			</div>
		</div>
		<div id="downloadList" class="modal fade hide" ng-controller="CtrlDownloadListForm">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3>Download Options</h3>
			</div>
			<div class="modal-body">
				<p>Download your file:</p>
				<p>Filename</p>
				<input type="text" name="downloadListFileName" ng-model="filename" id="downloadListFileName" value="Untitled" /><br/>
				<small>Leaving this blank will make the file be called 'Untitled'</small>
				<p>Language</p>
				<select id="downloadListLanguage" ng-model="language">
				<?php
					foreach($files::get_languages() as $language){
						echo '<option value="'.$language['extension'].'">'.$language['name'].'</option>';
					}
				?>
				</select>
				<p><a href="#" onclick="$('#codearea').attr('action','process.php?filename={{filename}}&amp;language={{language}}'); $('#codearea').submit();">Download File</a></p>
			</div>
		</div>
		<div id="loginRegister" class="modal fade hide">
			<div class="modal-header">
				<center><h3>Login and Register</h3></center>
			</div>
			<div class="modal-body">
				<form action="ajax.php?login" method="post"name="loginForm" style="width:50%;float:left;">
					<h4>Login</h4>
					<label for="loginFormUsername">Username</label>
					<?php
						if(isset($_SESSION['flash']['login_username'])){
							$value = $_SESSION['flash']['login_username'];
						} else {
							$value = '';
						}
					?>
					<input type="text" id="loginFormUsername" pattern=".{5,20}" title="Between 5 and 20 characters" name="loginFormUsername" value="<?=$value;?>" required /><br />
					<?php
						if(isset($_SESSION['error']['login_username'])){
							echo '<p id="usrerror" class="text-error">Username doesn\'t exist</p>';
						}
					?>
					<label for="loginFormPassword">Password</label>
					<input type="password" id="loginFormPassword" pattern=".{3,}" title="Three or more characters" name="loginFormPassword" required /><br />
					<?php
						if(isset($_SESSION['error']['login_details'])){
							echo '<p class="text-error">Username and password do not match out records.</p>';
						}
					?>
					<button type="submit" class="btn">Login</button>
				</form>
				<form action="ajax.php?register" method="post" name="registerForm" style="width:50%;float:left;">
					<h4>Register</h4>
					<label for="registerFormUsername">Username</label>
					<input type="text" id="registerFormUsername" pattern=".{5,20}" title="Between 5 and 20 characters" value="<?=(isset($_SESSION['flash']['register_username']))?$_SESSION['flash']['register_username']:'';?>" name="registerFormUsername" required /><br />
					<?php
						if(isset($_SESSION['error']['register_username'])){
							echo '<p id="usrerror" class="text-error">Username already exists.</p>';
						}
					?>
					<label for="registerFormPassword">Password</label>
					<input type="password" id="registerFormPassword" pattern=".{3,}" title="Three or more characters" name="registerFormPassword" required /><br />
					<?php
						if(isset($_SESSION['error']['register_password'])){
							echo '<p id="pwderror" class="text-error">Passwords Don\'t match.</p>';
						}
					?>
					<label for="registerFormPassword2">Password Confirm</label>
					<input type="password" id="registerFormPassword2"pattern=".{3,}" title="Three or more characters" name="registerFormPassword2" required /><br />
					<?php
						if(isset($_SESSION['error']['register_misc'])){
							echo '<p class="text-error">Login Failed. Please try again later.</p>';
						}
					?>
					<button type="submit" class="btn">Register</button>
				</form>
			</div>
		</div>
		<div id="deleteFileConfirm" class="modal fade hide">
			<div class="modal-header">
				<h3>Are you sure you want to delete this file?</h3>
			</div>
			<div class="modal-body">
				<p>This action cannot be undone.</p>
			</div>
			<div class="modal-footer">
				<a href="#" onclick="$('#deleteFileConfirm').modal('hide');" class="btn">Cancel</a>
				<a href="#" onclick="file_delete(); $('#deleteFileConfirm').modal('hide');" class="btn btn-success">Save</a>
			</div>
		</div>
		<script type="text/javascript" src="js/script.js"></script> 
		<script type="text/javascript" src="js/watch.js"></script>
		<script type="text/javascript">

			$(document).ready(function(){
				var docs_open = 0;

				window.current_file = {name : 'Untitled' , id : 1};
				<?php
					if(isset($_SESSION['files']['current_file_id'])){
				?>
						window.current_file_id = <?=$_SESSION['files']['current_file_id'];?>;
						window.file_is_open = 1;
				<?php
					} else {
				?>
						window.current_file_id = null;
						window.file_is_open = 0;
				<?php
					}
				?>
				window.is_logged_in = <?=($user::is_logged_in()==true)?'true':'false';?>;
				if(window.is_logged_in == false){
					$('#loginRegister').modal({
						backdrop: 'static',
						keyboard: false
					});
				}
				window.current_file.watch('id',function(id,oldval,newval){
					window.current_file_id = newval;
					// get filename
				})

				
			<?php
				if(isset($_SESSION['errors'])){
			?>		
					errors = <?php 
						if(isset($_SESSION['errors']) && !empty($_SESSION['errors'])){
							echo $_SESSION['errors'];
						} else {
							echo '[]';
						}
					?>;
					$(window).load(function(){
						for(var e = 0; e < errors.length; e++){
							$('div[data-row-number=' + errors[e].line+']').addClass('error');
						}
					});
					
				<?php
				} else {
					?>
					// Workaround. Return characters are shown on initial page load. This function helps format it properly.
 					file_open(<?=$_SESSION['files']['current_file_id'];?>);
 			<?php
				}
			?>
			placeNewLineNumbers();
			});
 			
		</script>
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-40233525-1', 'pseudify.com');
		  ga('send', 'pageview');

		</script>
	</body>
</html>
<?php
	unset($_SESSION['status']);
	unset($_SESSION['error']);
	unset($_SESSION['flash']);
?>
