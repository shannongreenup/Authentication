<?php
session_start();

$page = "welcome";

$username_error = "";

$password_error = "";

$changed = "";


if((isset($_GET['user']) && $_GET["user"] == "register") && !isset($_SESSION["login"])):
	$page = "register";
	if($_SERVER["REQUEST_METHOD"] == "POST"):
		if(!isset($_POST["username"]) || empty($_POST["username"])):
			$username_error = "Username is blank.";
		endif;
		if(!isset($_POST["password"]) || empty($_POST["password"])):
			$password_error = "Password is blank.";
		endif;
	
		if(!empty($_POST["username"]) && !empty($_POST["password"])):
			if(!file_exists("users.txt")):
				$register = fopen("users.txt","a");
				fwrite($register, $_POST["username"] . "," . $_POST["password"]);
				fclose($register);
				header("Location: ?user=login");
				die();
			else:
				$register = fopen("users.txt","a");
				fwrite($register, "\n" . $_POST["username"] . "," . $_POST["password"]);
				fclose($register);
				header("Location: ?user=login");
				die();
			endif; 
		endif;
	endif;
elseif((isset($_GET['user']) && $_GET["user"] == "login") && !isset($_SESSION["login"])):
	$page = "login";
	if($_SERVER["REQUEST_METHOD"] == "POST"):
		if(!isset($_POST["username"]) || empty($_POST["username"])):
			$username_error = "Username is blank."; 
		endif;
		
		if(!isset($_POST["password"]) || empty($_POST["password"])):
			$password_error = "Password is blank.";
		endif;
		
		if((!empty($_POST["username"]) && !empty($_POST["password"]))):
			$login = fopen("users.txt","r+");
			
			while($users = fgets($login)):
				$user = explode(",", trim($users));
				if($user[0] == $_POST["username"] && $user[1] == $_POST["password"]):
					$_SESSION["login"] = true;
					$_SESSION["username"] = $_POST["username"];
					$page = "loggedin";
				endif;
			endwhile;
			fclose($login);
		endif;
	endif;
elseif((isset($_GET['user']) && $_GET["user"] == "reset") && isset($_SESSION["login"])):
	$page = "reset";
	$getPass = $username = $password = "";
	if($_SERVER["REQUEST_METHOD"] == "POST"):
		if(!isset($_POST["resetpassword"]) || empty($_POST["resetpassword"])):
			$password_error = "Password is blank.";
		elseif(isset($_POST["resetpassword"])):
			$search = fopen("users.txt","r");
			$line = array();
			while(!feof($search)):
				$getPass = explode(',', trim(fgets($search)));
				
				$username = $getPass[0];
				$password = $getPass[1];
				
				if($username == $_SESSION["username"]):
					$password = $_POST["resetpassword"];
				endif;
				
				$line[] = $username;
				$line[] = $password;
			endwhile;
			
			fclose($search);

			$reset = fopen("users.txt","w");
			for($i=0; $i < count($line); $i++):
				if(($i % 2 != 0)  && ($i < (count($line) - 1))):
					fwrite($reset, $line[$i] . "\n");
				elseif($i % 2 == 0):
					fwrite($reset, $line[$i] . ",");				
				elseif($i % 2 != 0):
					fwrite($reset, $line[$i]);
				endif;
			endfor;
			
			fclose($reset);
				
			$changed = "Password changed.";
		endif;
	endif;
elseif((isset($_GET['user']) && $_GET["user"] == "logout") && isset($_SESSION["login"])):
	$page = "welcome";
	session_unset();
	session_destroy();
elseif(isset($_SESSION["login"])):
	$page = "loggedin";
endif;
?>

<!DOCTYPE html>
<html>
<body>
<?php if($page == "welcome"): ?>
	Welcome! <br>
	<a href="?user=login">Login</a> | <a href="?user=register">Register</a>
<?php elseif($page == "login"): ?>
Login <br>
<form method ="POST" action="">
  <label for="fname">Username:</label>
  <input type="text" id="username" name="username">
  <?php echo $username_error; ?><br><br>
  <label for="lname">Password:</label>
  <input type="password" id="password" name="password">
  <?php echo $password_error; ?><br><br>
  <input type="submit" value="Login"><br><br>
  <a href="?user=register">Register</a>
</form>
<?php elseif($page == "register"): ?>
Register <br>
<form method ="POST" action="">
  <label for="fname">Username:</label>
  <input type="text" id="username" name="username">
  <?php echo $username_error; ?><br><br>
  <label for="lname">Password:</label>
  <input type="password" id="password" name="password">
  <?php echo $password_error; ?><br><br>
  <input type="submit" value="Register">
</form>
<?php elseif($page == "loggedin"): ?>
You are logged in <?php echo $_SESSION["username"]; ?>! <br>
<a href="?user=reset">Reset Password</a> | <a href="?user=logout">Logout</a>
<?php elseif($page == "reset"): ?>
Reset Password <br>
<?php echo $changed . "<br>"; ?>
<form method ="POST" action="">
  <label for="lname">Password:</label>
  <input type="password" id="resetpassword" name="resetpassword">
  <?php echo $password_error; ?><br><br>
  <input type="submit" value="Reset">
</form>
<a href="?user=login">Back</a>
<?php endif; ?>
</body>
</html>

