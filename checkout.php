<?php
	ob_start();
	session_start();
	require_once 'config/connect.php';
	if(!isset($_SESSION['customer']) & empty($_SESSION['customer'])){
		header('location: login.php');
	}
include 'inc/header.php'; 
include 'inc/nav.php'; 
$uid = $_SESSION['customerid'];
$cart = $_SESSION['cart'];
$ship = "Free";
if(!empty($_POST['shipping']))
{
	$ship = $_POST['shipping'];
}


$shippingcost = 0;
if ($ship == "Home") { $shippingcost = 5; $total = $total + $shippingcost;}

if(isset($_POST) & !empty($_POST['agree'])){
	if($_POST['agree'] == true){
		$country = filter_var($_POST['country'], FILTER_SANITIZE_STRING);
		$fname = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);
		$lname = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);
		$company = filter_var($_POST['company'], FILTER_SANITIZE_STRING);
		$address1 = filter_var($_POST['address1'], FILTER_SANITIZE_STRING);
		$address2 = filter_var($_POST['address2'], FILTER_SANITIZE_STRING);
		$city = filter_var($_POST['city'], FILTER_SANITIZE_STRING);
		$state = filter_var($_POST['state'], FILTER_SANITIZE_STRING);
		$phone = filter_var($_POST['phone'], FILTER_SANITIZE_NUMBER_INT);
		$payment = filter_var($_POST['payment'], FILTER_SANITIZE_STRING);
		$shipping = filter_var($_POST['shipping'], FILTER_SANITIZE_STRING);
		$zip = filter_var($_POST['zipcode'], FILTER_SANITIZE_NUMBER_INT);
        $fbalance= $_POST['fbalance'];
		$sql = "SELECT * FROM usersmeta WHERE uid=$uid";
		$res = mysqli_query($connection, $sql);
		$r = mysqli_fetch_assoc($res);
		$count = mysqli_num_rows($res);
		if($count == 1){
			//update data in usersmeta table
			$usql = "UPDATE usersmeta SET balance='$fbalance' WHERE uid=$uid";
			$ures = mysqli_query($connection, $usql) or die(mysqli_error($connection));
			if($ures){

				$total = 0;
				$totalquantity=0;
				foreach ($cart as $key => $value) {
					//echo $key . " : " . $value['quantity'] ."<br>";
					$ordsql = "SELECT * FROM products WHERE id=$key";
					$ordres = mysqli_query($connection, $ordsql);
					$ordr = mysqli_fetch_assoc($ordres);

					$total = $total + ($ordr['price']*$value['quantity']) + $shippingcost;
					
				}

				echo $iosql = "INSERT INTO orders (uid, totalprice, orderstatus, shipping, paymentmode) VALUES ('$uid', '$total', 'Order Placed','$shipping', '$payment')";
				$iores = mysqli_query($connection, $iosql) or die(mysqli_error($connection));
				if($iores){
					//echo "Order inserted, insert order items <br>";
					$orderid = mysqli_insert_id($connection);
					foreach ($cart as $key => $value) {
						//echo $key . " : " . $value['quantity'] ."<br>";
						$ordsql = "SELECT * FROM products WHERE id=$key";
						$ordres = mysqli_query($connection, $ordsql);
						$ordr = mysqli_fetch_assoc($ordres);

						$pid = $ordr['id'];
						$productprice = $ordr['price'];
						$quantity = $value['quantity'];


						$orditmsql = "INSERT INTO orderitems (pid, orderid, productprice, pquantity) VALUES ('$pid', '$orderid', '$productprice', '$quantity')";
						$orditmres = mysqli_query($connection, $orditmsql) or die(mysqli_error($connection));
						//if($orditmres){
							//echo "Order Item inserted redirect to my account page <br>";
						//}
					}
					//Update  Remaing stock
				foreach ($cart as $key => $value){
				$ordsql = "SELECT * FROM products WHERE id=$key";
						$ordres = mysqli_query($connection, $ordsql);
						$ordr = mysqli_fetch_assoc($ordres);
						$pqty = $ordr['qty'];
						$quantity = $pqty - $value['quantity'];
				
				$sql = "UPDATE products SET qty='$quantity' WHERE id = $key";
		         $res = mysqli_query($connection, $sql)or die(mysqli_error($connection));
				}
					
				}
				unset($_SESSION['cart']);
				header("location: my-account.php");
			}
		}else{
			//insert data in usersmeta table
			$isql = "INSERT INTO usersmeta (country, firstname, lastname, address1, address2, city, state, zip, company, mobile, balance, uid) VALUES ('$country', '$fname', '$lname', '$address1', '$address2', '$city', '$state', '$zip', '$company', '$phone', '$fbalance', '$uid')";
			$ires = mysqli_query($connection, $isql) or die(mysqli_error($connection));
			if($ires){

				$total = 0;
				foreach ($cart as $key => $value) {
					//echo $key . " : " . $value['quantity'] ."<br>";
					$ordsql = "SELECT * FROM products WHERE id=$key";
					$ordres = mysqli_query($connection, $ordsql);
					$ordr = mysqli_fetch_assoc($ordres);

					$total = $total + ($ordr['price']*$value['quantity']) + $shippingcost;
					
				}

				echo $iosql = "INSERT INTO orders (uid, totalprice, orderstatus, shipping, paymentmode) VALUES ('$uid', '$total', 'Order Placed', '$shipping', $payment')";
				$iores = mysqli_query($connection, $iosql) or die(mysqli_error($connection));
				if($iores){
					//echo "Order inserted, insert order items <br>";
					$orderid = mysqli_insert_id($connection);
					foreach ($cart as $key => $value) {
						//echo $key . " : " . $value['quantity'] ."<br>";
						$ordsql = "SELECT * FROM products WHERE id=$key";
						$ordres = mysqli_query($connection, $ordsql);
						$ordr = mysqli_fetch_assoc($ordres);

						$pid = $ordr['id'];
						$productprice = $ordr['price'];
						
						$quantity = $value['quantity'];


						$orditmsql = "INSERT INTO orderitems (pid, orderid, productprice, pquantity) VALUES ('$pid', '$orderid', '$productprice', '$quantity')";
						$orditmres = mysqli_query($connection, $orditmsql) or die(mysqli_error($connection));
						
					}
					//Update  Remaing stock
				foreach ($cart as $key => $value){
					$ordsql = "SELECT * FROM products WHERE id=$key";
						$ordres = mysqli_query($connection, $ordsql);
						$ordr = mysqli_fetch_assoc($ordres);
						$pqty = $ordr['qty'];
						$quantity = $pqty - $value['quantity'];
				
				$sql = "UPDATE products SET qty='$quantity' WHERE id = $key";
		         $res = mysqli_query($connection, $sql)or die(mysqli_error($connection));
				}
				}
				
				
				unset($_SESSION['cart']);
				header("location: my-account.php");
			}

		}
	}

}

$sql = "SELECT * FROM usersmeta WHERE uid=$uid";
$res = mysqli_query($connection, $sql);
$r = mysqli_fetch_assoc($res);
$totalqty=0;
$r2=0;
foreach ($cart as $key => $value) {
						
						$ordsql = "SELECT * FROM products WHERE id=$key";
						$ordres = mysqli_query($connection, $ordsql);
$r2 = mysqli_fetch_assoc($ordres);
$totalqty=$r2['qty'];
}
?>

	
	<!-- SHOP CONTENT -->
	<section id="content">
		<div class="content-blog">
					<div class="page_header text-center">
						<h2>Shop - Checkout</h2>
						<p>Dear Customer We Value You</p>
					</div>
<form method="post">
<div class="container">
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
				    <h2>Your Balance: $ <?php echo $r['balance']; ?></h2>
				    <?php
				   
					?>
				</div>
				
			</div>
			
			<div class="space30"></div>
			<h4 class="heading">Your order</h4>
			
			<table class="table table-bordered extra-padding">
				<tbody>
					<tr>
						<th>Cart Subtotal</th>
						<td><span class="amount">$ <?php echo $total-$shippingcost; ?></span></td>
					</tr>
					<tr>
						<th>Shipping and Handling</th>
						<td>
						    <form method="post">
						    <select name="shipping" required onchange="this.form.submit()">
						        <option value="">Please Select Shipping Option</option>
						        <option value="Free" <?php if ($ship == "Free") { echo "selected";}?> >Vist Shop </option>
						        <option value="Home" <?php if ($ship == "Home") { echo "selected";}?> >Home Delivery</option>
						    </select>
						    <?php echo ' '.$shippingcost. ' $'; ?>
						    </form>
						</td>
					</tr>
					<tr>
						<th>Order Total</th>
						<td><strong><span class="amount">$ <?php echo $total ?></span></strong> </td>
					</tr>
				</tbody>
			</table>
			
			<div class="clearfix space30"></div>
			<h4 class="heading">Remaning Balance: $ 
					<?php
					$fbalance = $r['balance'] - $total;
					echo $fbalance;
					?></h4>
			<h4 class="heading">Remaning Stock:  
					<?php
					$rqty = $totalqty - $value['quantity'];
					echo $rqty;
					?> pcs</h4>
					<input type="hidden" name="fbalance" value="<?php echo $fbalance; ?>">
			<div class="clearfix space20"></div>
			<input type="hidden" name="payment" value="bln">
			<?php
			
			?>
				<div class="space30"></div>
				
					<input name="agree" required id="checkboxG2" class="css-checkbox" type="checkbox" value="true"><span>I've read and accept the <a href="#">terms &amp; conditions</a></span>
				
				<div class="space30"></div>
				<?php
				if ($fbalance >=0 & $rqty>=0) {
				echo '<input type="submit" class="button btn-lg" value="Pay Now">';
				}
				else if($rqty<0) {
					echo "<h4>You Ordered much quantity than currently available stock</h4>";
				}
				
				else
				{
				    echo "<h4>You don't have enough balance</h4>";
				}
				?>
			</div>
		</div>		
</form>		
		</div>
	</section>
	 
<?php include 'inc/footer.php' ?>
