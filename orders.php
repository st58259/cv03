<?php
session_start();
ob_start();

$servername = "localhost";
$userdb = "root";
$passdb = "";

$orders = "";

try {
  $conn = new PDO("mysql:host=$servername;dbname=cv", $userdb, $passdb);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if(isset($_GET["logout"])) {
    session_unset();
  }


  $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = :uid");
  $stmt->bindParam(':uid', $_SESSION["id"]);
  $stmt->execute();

  $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
  foreach(new RecursiveArrayIterator($stmt->fetchAll()) as $k=>$v) {

    //var_dump($v["order_id"]);

    $orders .= '<div class="order-date">'. $v["date_"] .'</div>';

    $stmt2 = $conn->prepare("SELECT img, name, item_order.price, quantity, order_id, item_id FROM item_order INNER JOIN items ON item_order.item_id = items.id WHERE order_id = :orid");
    $stmt2->bindParam(':orid', $v["id"]);
    $stmt2->execute();

    $result2 = $stmt2->setFetchMode(PDO::FETCH_ASSOC);
    foreach(new RecursiveArrayIterator($stmt2->fetchAll()) as $kk=>$vv) {
      $orders .= '
          <div class="cart-item">
          <div class="cart-img">
          ' . $vv["img"] . '
          </div>
          <div class="cart-name">
          ' . $vv["name"] . '
          </div>
          <div class="cart-control">
          <div class="cart-quantity">
          ' . ($vv["quantity"]) . '
          </div>
          <div class="cart-price">
          ' . $vv["price"] . '
          </div>
          </div>
          </div>';
    }
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
      <h2>ORDERS</h2>
      <nav>
        <a href="shop.php">Catalog</a>
        <a href="cart.php">Cart</a>
        <a href="orders.php">Orders</a>
        <?php 
        if(isset($_SESSION["username"])) {
          echo '<a href="profile.php">Profile</a>';
          echo '<a href="shop.php?logout=true">Logout</a>';
        } else {
          echo '<a href="login.php">Login</a>';
        }
        ?>
      </nav>
    </div>
  </section>
  <section id="cart-items">

    <?php

    echo $orders;

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

    ?>
  </section>
</body>
</html>