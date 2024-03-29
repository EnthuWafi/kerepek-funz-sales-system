<?php
session_start();
require("../../includes/functions.inc.php");


customer_login_required();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $postedToken = $_POST["token"];
    if(!empty($postedToken)){
        if(isTokenValid($postedToken)){
            // Process form
            if (isset($_POST["product_id"])){
                $productID = htmlspecialchars($_POST["product_id"]);
                $cart = $_SESSION["cart"] ?? [];

                $itemExist = false;

                foreach ($cart as $itemKey => $itemValue) {
                    if ($itemValue["product"]["product_id"] == $productID){
                        unset($cart[$itemKey]);
                        $itemExist = true;
                        break;
                    }
                }

                if ($itemExist){
                    $_SESSION["cart"] = $cart;
                    makeToast("success", "Product removed from Cart", "Success");
                }
                else {
                    makeToast("error", "Something went wrong, item was not found!", "Error");
                }

            }
        }
        else{
            makeToast("warning", "Please refrain from attempting to resubmit previous form", "Warning");
        }
    }

    header("Location: /order/cart.php");
    die();
}

displayToast();
$cart = $_SESSION["cart"] ?? [];
$cost = 0;

$token = getToken();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php head_tag_content(); ?>
    <link rel="stylesheet" href="/assets/css/progress.css">
    <title>Kerepek Funz | Shopping Cart</title>
</head>
<body>
<div class="container-fluid">
    <div class="row flex-nowrap">
        <div class="col-auto px-0">
            <?php side_bar() ?>
        </div>
        <main class="col ps-md-2 pt-2">
            <?php header_bar("Shopping Cart") ?>

            <!-- todo DASHBOARD here  -->
            <div class="container">
                <div id="msform">
                    <!-- progressbar -->
                    <ul id="progressbar">
                        <li class="active"><strong>Cart</strong></li>
                        <li><strong>Checkout</strong></li>
                        <li><strong>Finish</strong></li>
                    </ul>
                </div>
                <div class="row mt-4 gx-4 ms-3">
                    <div class="shadow p-3 mb-3 bg-body rounded col-lg-8  me-3">
                        <div class="ro mb-2">
                            <span class="h3">My Shopping Cart</span>
                        </div>
                        <div class="container">
                            <div class="row mb-5 overflow-auto">
                                <?php
                                if (!empty($cart)){
                                    $count = 0;
                                    foreach ($cart as $item) {
                                        $product = $item["product"];
                                        $quantity = $item["quantity"];
                                        $cost += $product["product_price"] * $quantity;
                                        //price
                                        $productCost = number_format((float)$product["product_price"], 2, '.', '');

                                        if ($count % 4 == 0) {
                                            echo "<div class='row'>";
                                        }
                                        $count++;
                                        echo "
<div class='card p-3 mb-3'>
<div class='row g-0'>
    <div class='col-md-4'>
        <img src='{$product["product_image"]}' class='object-fit-fill card-img-top' alt='Product Image'>
    </div>
    <div class='col-md-8'>
        <div class='card-body'>
            <h5 class='card-title'>{$product["product_name"]}</h5>
            <p class='card-text'>Product Code: {$product["product_code"]}</p>
           
            <p class='card-text'>Price: RM{$productCost}</p>
            
            <span class='card-detail'>Quantity: {$quantity}</span>
            <form action='/order/cart.php' method='post'>     
                <input type='hidden' name='product_id' value='{$product["product_id"]}'>
                <input type='hidden' name='token' value='$token'>
                <button type='submit' class='btn btn-dark text-white float-end'><i class='bi bi-trash'></i> Remove from Cart</button>
            </form>
          </div>  
    </div>
</div>                                      
</div>                              
";
                                        if ($count % 4 == 0) {
                                            echo "</div>";
                                        }
                                    }
                                    if ($count % 4 != 0){
                                        echo "</div>";
                                    }
                                }
                                else {
                                    echo "<span class='text-center fs-4 mt-2'>No item</span>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="shadow p-4 mb-3 bg-body rounded col h-25 ">
                        <div class="row">
                            <span class="h4">Total Price Details:</span>
                        </div>
                        <div class="row my-2 px-4">
                            <?php
                            $subTotal = number_format((float)$cost, 2, ".", ",");
                            $delivery = $subTotal > 0 ? 5 : 0;
                            $deliveryStr = number_format((float)$delivery, 2, ".", ",");
                            $total = $cost + $delivery;
                            $total = number_format((float)$total, 2, ".", ",");
                            ?>
                            <div class="col text-start">
                                <div class="row">
                                    <span>Subtotal:</span>
                                </div>
                                <div class="row">
                                    <span>Delivery Fee:</span>
                                </div>
                            </div>
                            <div class="col text-end">
                                <div class="row">
                                    <span>RM<?= $subTotal?></span>
                                </div>
                                <div class="row">
                                    <span>RM<?= $deliveryStr ?></span>
                                </div>
                            </div>
                            <hr class="m-1">
                            <div class="col text-start">
                                <div class="row">
                                    <span class="fw-bold">Total:</span>
                                </div>
                            </div>
                            <div class="col text-end">
                                <div class="row">
                                    <span class="fw-bold">RM<?= $total?></span>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4 px-4">
                            <a type="button" class="btn btn-outline-primary" href="/order/checkout.php">Check-out</a>
                        </div>
                    </div>
                </div>
            </div>


            <?php footer(); ?>
        </main>

    </div>
</div>
<?php body_script_tag_content();?>
</body>

</html>