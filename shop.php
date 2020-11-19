<?php
session_start();
ob_start();

$servername = "localhost";
$userdb = "root";
$passdb = "";

$cat = "";

//$_SESSION["cart"][0]["quantity"] = 0;

//$_SESSION["cart"];
//session_destroy();
///session_unset();




try {
  $conn = new PDO("mysql:host=$servername;dbname=cv", $userdb, $passdb);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if(isset($_GET["logout"])) {
    session_unset();
  }

  function addToCart($productId)
  {
    if(!isset($_SESSION["cart"])) {
     $_SESSION["cart"][$productId]["quantity"] = 1;
   } else {
    if (!array_key_exists($productId, $_SESSION["cart"])) {
      $_SESSION["cart"][$productId]["quantity"] = 1;
    } else {
      $_SESSION["cart"][$productId]["quantity"]++;
    }
  }
}


if(isset($_GET["action"])) {
  if ($_GET["action"] == "add" && !empty($_GET["id"])) {
    if(!isset($_SESSION["username"])) {
      header("Location: login.php");
    }
    addToCart($_GET["id"]);
  }

  if ($_GET["action"] == "remove" && !empty($_GET["id"])) {
    if(!isset($_SESSION["username"])) {
      header("Location: login.php");
    }
    removeFromCart($_GET["id"]);
  }

  if ($_GET["action"] == "delete" && !empty($_GET["id"])) {
    if(!isset($_SESSION["username"])) {
      header("Location: login.php");
    }
    deleteFromCart($_GET["id"]);
  }
}
















$stmt = $conn->prepare("SELECT * FROM items");
$stmt->execute();

$result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
foreach(new RecursiveArrayIterator($stmt->fetchAll()) as $k=>$v) {

  $cat .= '<div class="catalog-item">
  <div class="catalog-img">
  ' . $v["img"] . '
  </div>
  <h3>
  ' . $v["name"] . '
  </h3>
  <div>
  ' . $v["price"] . '
  </div>
  <a href="shop.php?action=add&id=' . $v["id"] . '" class="catalog-buy-button">
  Buy
  </a>
  </a>
  </div>
  </div>';

}




}catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

?>

<html>
<head>
  <title>ESHOP</title>
  <link rel="stylesheet" type="text/css" href="shop.css">
</head>
<body>

  <section>
    <div class="menu">
      <h2>CATALOG</h2>
      <nav>
        <a href="shop.php">Catalog</a>

        <?php 
        if(isset($_SESSION["username"])) {
          echo '<a href="cart.php">Cart</a>
          <a href="orders.php">Orders</a>
          <a href="profile.php">Profile</a>
          <a href="shop.php?logout=true">Logout</a>';
        } else {
          echo '<a href="login.php">Login</a>';
        }
        ?>
      </nav>
    </div>
  </section>
  <section id="catalog-items">

    <?php

    echo $cat;

    /*foreach ($catalog as $item) {
      echo '
      <div class="catalog-item">
      <div class="catalog-img">
      ' . $item["img"] . '
      </div>
      <h3>
      ' . $item["name"] . '
      </h3>
      <div>
      ' . $item["price"] . '
      </div>
      <a href="/?action=add&id=' . $item["id"] . '" class="catalog-buy-button">
      Buy
      </a>
      </div>';

    }*/


    //var_dump($_SESSION)

    //phpinfo();

    ?>
  </section>
</body>
</html>