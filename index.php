<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Main</title>
	<link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>
	<?php 

	$servername = "localhost";
	$userdb = "root";
	$passdb = "";
	$inRegErr = false;
	$outRegErr = false;
	$inLogErr = false;


	try {
		$conn = new PDO("mysql:host=$servername;dbname=cv", $userdb, $passdb);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		if(isset($_POST["logout"])) {
			session_destroy();
			echo $_SESSION["role"];
		}
		

		if(isset($_POST["reg"])) {
			if(empty($_POST["username"]) || empty($_POST["password"]) || empty($_POST["email"])) {
				$inRegErr = true;
			} else {
				$username = $_POST["username"];
				$password = $_POST["password"];
				$password = password_hash($password, PASSWORD_BCRYPT);
				$email = $_POST["email"];
				$role = 0;


				$stnt = $conn->prepare("SELECT username FROM users WHERE username = :username");
				$stnt->bindParam(':username', $username);
  				$stnt->execute();
  				$rows = $stnt->rowCount();

  				if($rows > 0) {
  					$outRegErr = true;
  				}

  				if(!$outRegErr) {
					$stnt = $conn->prepare("INSERT INTO users (username, password, email, role)
					VALUES (:username, :password, :email, :role)");

					$stnt->bindParam(':username', $username);
					$stnt->bindParam(':password', $password);
					$stnt->bindParam(':email', $email);
					$stnt->bindParam(':role', $role);

					$stnt->execute();
				
					/*$sql = "INSERT INTO users (username, password, email, role)
					VALUES ('$username', '$password', '$email', '$role')";
				
					$conn->exec($sql);*/


					$stmt = $conn->prepare("SELECT id, username, password, role FROM users");
	  				$stmt->execute();

	  				$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
				    foreach(new RecursiveArrayIterator($stmt->fetchAll()) as $k=>$v) {
				        echo $v["id"];
				    }
			    }
			}
		} else if(isset($_POST["log"])) {
			if(empty($_POST["username"]) || empty($_POST["password"])) {
				$inLogErr = true;
			} else {

				$username = $_POST["username"];
				$password = $_POST["password"];
				$role = 0;


				$stnt = $conn->prepare("SELECT username, password, role FROM users WHERE username = :username");
				$stnt->bindParam(':username', $username);
  				$stnt->execute();
  				$rows = $stnt->rowCount();


  				if($rows < 0) {
  					$inLogErr = true;
  					echo "nope";
  				} else {
  					$result = $stnt->setFetchMode(PDO::FETCH_ASSOC);
				    foreach(new RecursiveArrayIterator($stnt->fetchAll()) as $k=>$v) {
				        $passHashed = $v["password"];
				        $role = $v["role"];
				    }

				    if (password_verify($password, $passHashed)) {

		            $_SESSION["username"] = $username;
		            $_SESSION["role"] = $role;
		            echo "yes";
		           header("location: profile.php");


  				} else {
  					echo "string";
  				}
  			}

			}
		}







 	}catch(PDOException $e) {
	  echo "Connection failed: " . $e->getMessage();
	}

	?>

	<div class="cont">
		<div class="loginCont">
			<div class="logB">Login</div>
		    <form class="loginForm" action="index.php" method="post">
		      <input type="text" placeholder="username" name="username">
		      <input type="password" placeholder="password" name="password">
		      <input type="submit" name="log" value="Login">
		    </form>
		</div>
		<div class="regCont">
			<div class="logB">Register</div>
		    <form class="regForm" action="index.php" method="post">
		      <input type="text" placeholder="username" name="username">
		      <input type="password" placeholder="password" name="password">
		      <input type="email" placeholder="email" name="email">
		      <input type="submit" name="reg" value="Register">
		      <?php 
			      if($inRegErr) {
			      	echo '<p class="err">Incorrect input data</p>';
			      }
			      if($outRegErr) {
			      	echo '<p class="err">Username already in use</p>';
			      }
		      ?>
		    </form>
		</div>
	</div>
</body>
</html>