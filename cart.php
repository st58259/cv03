<?php
session_start();
ob_start();

$servername = "localhost";
$userdb = "root";
$passdb = "";

$cat = "";

try {
  $conn = new PDO("mysql:host=$servername;dbname=cv", $userdb, $passdb);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if(isset($_GET["logout"])) {
    session_unset();
  }



  function addToCart($productId)
  {
    if (!array_key_exists($productId, $_SESSION["cart"])) {
      $_SESSION["cart"][$productId]["quantity"] = 1;
    } else {
      $_SESSION["cart"][$productId]["quantity"]++;
    }
  }

  function removeFromCart($productId)
{
  if (array_key_exists($productId, $_SESSION["cart"])) {
    if ($_SESSION["cart"][$productId]["quantity"] <= 1) {
      unset($_SESSION["cart"][$productId]);
      if(count($_SESSION["cart"]) == 0) {
        unset($_SESSION["cart"]);
      }
    } else {
      $_SESSION["cart"][$productId]["quantity"]--;
      if(count($_SESSION["cart"]) == 0) {
        unset($_SESSION["cart"]);
      }
    }
  }
}

function deleteFromCart($productId)
{
  unset($_SESSION["cart"][$productId]);
  if(count($_SESSION["cart"]) == 0) {
        unset($_SESSION["cart"]);
    }
}



  if(isset($_GET["action"])) {
    if ($_GET["action"] == "add" && !empty($_GET["id"])) {
      addToCart($_GET["id"]);
    }

    if ($_GET["action"] == "remove" && !empty($_GET["id"])) {
      removeFromCart($_GET["id"]);
    }

    if ($_GET["action"] == "delete" && !empty($_GET["id"])) {
      deleteFromCart($_GET["id"]);
    }

    if ($_GET["action"] == "order") {
      $stmt = $conn->prepare("INSERT INTO orders (user_id, date_) VALUES (:id, :d)");

      $date = date("Y-m-d");

      var_dump($_SESSION["id"]);

      $stmt->bindParam(':id', $_SESSION["id"]);
      $stmt->bindParam(':d', $date);
      $stmt->execute();

      $stmt = $conn->prepare("SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'cv' AND TABLE_NAME = 'orders'");
      $stmt->execute();

      $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
      foreach(new RecursiveArrayIterator($stmt->fetchAll()) as $k=>$v) {
        $l_id = $v;
      }


      //var_dump($l_id);
      $last_id = number_format($l_id["AUTO_INCREMENT"] - 1);

      
      foreach ($_SESSION["cart"] as $key => $value) {

       


        $stmt = $conn->prepare("SELECT * FROM items WHERE id = $key");
        $stmt->execute();

        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        foreach(new RecursiveArrayIterator($stmt->fetchAll()) as $k=>$v) {

          $pr = $v["price"] * $value["quantity"];
          //var_dump($pr);


          $stmt = $conn->prepare("INSERT INTO item_order (order_id, item_id, quantity, price) VALUES (:orid, :itid, :qua, :price)");
          $stmt->bindParam(':orid', $last_id);
          $stmt->bindParam(':itid', $v["id"]);
          $stmt->bindParam(':qua', $value["quantity"]);
          $stmt->bindParam(':price', $pr);
          $stmt->execute();
          


          
        }
      }

     

      unset($_SESSION["cart"]);
      header('Location: shop.php');
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
      <h2>CART</h2>
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

    $totalPrice = 0;

    if(isset($_SESSION["cart"])) {

      //var_dump($_SESSION);

      foreach ($_SESSION["cart"] as $key => $value) {

        $stmt = $conn->prepare("SELECT * FROM items WHERE id = $key");
        $stmt->execute();

        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        foreach(new RecursiveArrayIterator($stmt->fetchAll()) as $k=>$v) {

          $totalPrice = $totalPrice + ($value["quantity"] * $v["price"]);
          echo '
          <div class="cart-item">
          <div class="cart-img">
          ' . $v["img"] . '
          </div>
          <div class="cart-name">
          ' . $v["name"] . '
          </div>
          <div class="cart-control">
          <div class="cart-price">
          ' . $v["price"] . '
          </div>
          <div class="cart-quantity">
          ' . ($value["quantity"]) . '
          </div>
          <div class="cart-quantity">
          ' . ($value["quantity"] * $v["price"]) . '
          </div>
          <a href="cart.php?action=add&id=' . $v["id"] . '" class="cart-button">
          +
          </a>
          <a href="cart.php?action=remove&id=' . $v["id"] . '" class="cart-button">
          -
          </a>
          <a href="cart.php?action=delete&id=' . $v["id"] . '" class="cart-button">
          x
          </a>
          </div>
          </div>';
        }
      }
      echo "<div id='cart-total-price'>Total price: $totalPrice</div>";
    }

    //var_dump($_SESSION);

    ?>
  </section>

  <?php 
  if(isset($_SESSION["cart"])) {
    echo '<a href="cart.php?action=order" class="order-button">Order</a>';
  }
  ?>

</body>
</html>