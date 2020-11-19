<?php
session_start();

if($_SESSION["role"] != 1) {
	header("location: index.php");
}



	$servername = "localhost";
	$userdb = "root";
	$passdb = "";
	$inRegErr = false;
	$outRegErr = false;
	$inLogErr = false;

	$table = "";


	try {

		$conn = new PDO("mysql:host=$servername;dbname=cv", $userdb, $passdb);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


		if(isset($_POST["update"])) {
			if(!empty($_POST["email"]) || !empty($_POST["role"])) {
				$email = $_POST["email"];
				$id = $_POST["id"];
				$role = $_POST["role"];
	  				
				$stmt = $conn->prepare("UPDATE users SET email = :email, role = :role WHERE id = :id");
				$stmt->bindParam(':id', $id);
				$stmt->bindParam(':email', $email);
				$stmt->bindParam(':role', $role);
				$stmt->execute();
			}
		}



		$stmt = $conn->prepare("SELECT * FROM users");
	  	$stmt->execute();

	  	$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
		foreach(new RecursiveArrayIterator($stmt->fetchAll()) as $k=>$v) {
		    $table .= '<div class="line"><div class="sp">'.$v["id"].'</div><div class="sp1">'.$v["username"].'</div><div class="sp2">'.$v["email"].'</div><div class="sp3"><a href="users.php?edit='.$v["id"].'">edit</a></div></div>';
		}

		$username = "";
		$email = "";
		$role = 0;
		$id = "";

		$edit = "";

		if(isset($_GET["edit"])) {
			

			$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
			$stmt->bindParam(':id', $_GET["edit"]);
	  		$stmt->execute();

	  		$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
			foreach(new RecursiveArrayIterator($stmt->fetchAll()) as $k=>$v) {
			    $edit = '<input type="text" placeholder="username" name="username" disabled="true" value="'.$v["username"].'">
			    		<input type="email" placeholder="email" name="email" value="'.$v["email"].'">
			    		<input type="text" placeholder="role" name="role" value="'.$v["role"].'">
			    		<input type="text" placeholder="id" name="id" value="'.$v["id"].'" hidden="true">';
			} 
		} else {
			$edit = '<input type="text" placeholder="username" name="username" disabled="true" value="">
		    	<input type="email" placeholder="email" name="email" value="">
		    	<input type="text" placeholder="role" name="role" value="">';
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
		<nav>
			<?php
				if($_SESSION["role"] == 1) {
					echo '<a href="users.php">Users</a>';
					echo '<a href="cart.php">Cart</a>
        			<a href="orders.php">Orders</a>
        			<a href="profile.php">Profile</a>
        			<a href="shop.php?logout=true">Logout</a>';
				} else {
					header('Location : shop.php?logout=true');
				}
			?>
			
		</nav>
	</div>

	<div class="cn">

		<?php
			echo $table;
		?>

	</div>

	<div class="loginContU">
			<div class="logB">Profile</div>
		    <form class="loginForm" action="users.php" method="post">
		      <?php 
		    		echo $edit;
		    	?>
		      <input type="submit" name="update" value="Edit">
		    </form>
		</div>



	<?php



	?>

</body>
</html>