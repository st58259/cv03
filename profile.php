<?php
session_start();


$servername = "localhost";
$userdb = "root";
$passdb = "";
$inRegErr = false;
$outRegErr = false;
$inLogErr = false;


try {
	$conn = new PDO("mysql:host=$servername;dbname=cv", $userdb, $passdb);
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	if(isset($_GET["logout"])) {
		session_unset();
	}

	if(isset($_POST["edit"])) {
		if(!empty($_POST["email"])) {
			$username = $_SESSION["username"];
			$email = $_POST["email"];

			$stmt = $conn->prepare("UPDATE users SET email = :email WHERE username = :username");
			$stmt->bindParam(':username', $username);
			$stmt->bindParam(':email', $email);
			$stmt->execute();
		}
	}


	$stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
	$stmt->bindParam(':username', $_SESSION["username"]);
	$stmt->execute();

	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
	foreach(new RecursiveArrayIterator($stmt->fetchAll()) as $k=>$v) {
		$userid = $v["id"];
		$username = $_SESSION["username"];
		$email = $v["email"];
		$role = $v["role"];
	}

}catch(PDOException $e) {
	echo "Connection failed: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Profile</title>
	<link rel="stylesheet" type="text/css" href="profile.css">
</head>
<body>
	<div class="menu">
		<h2>PROFILE</h2>
		<nav>
			<?php
			if($_SESSION["role"] == 1) {
				echo '<a href="users.php">Users</a>';
			}
			?>
			
			<a href="shop.php">Catalog</a>
			<a href="cart.php">Cart</a>
			<a href="orders.php">Orders</a>
			<a href="profile.php">Profile</a>
			<a href="shop.php?logout=true">Logout</a>
		</nav>
	</div>



	<?php



	?>


	<div class="cont">
		<div class="loginCont">
			<div class="logB">Profil</div>
			<form class="loginForm" action="profile.php" method="post">
				<?php 
				echo '<input type="text" placeholder="username" name="username" disabled="true" value="'.$username.'">';
				echo '<input type="password" placeholder="password" name="password" value="*****" disabled="true">';
				echo '<input type="email" placeholder="email" name="email" value="'.$email.'">';
				?>

				<input type="submit" name="edit" value="Edit">
			</form>
		</div>
	</div>


</body>
</html>