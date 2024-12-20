<!DOCTYPE html>
<html>

<head>
    <title>Gifft</title>
    <style>
        body {
            font-family: "Arial";
        }
    </style>    
</head>

<body>

<?php 
require_once("dbconnect.php"); 
?>

<?php
// Check if the form is submitted and retreive InvoiceID from POST
if (isset($_POST['InvoiceID'])) {
    $InvoiceID = $_POST['InvoiceID'];
  // Show the prompt if InvoiceID isn't provided yet
} else {
    echo "<h2>Please enter an Invoice ID: </h2>";
    echo "<form action='gifft.php' method='POST'>
            <input type='text' name='InvoiceID'>
            <input type='submit'>
          </form>";
    exit;
}
?>

<!-- Display banner -->
<img src="images/gifft.png">

<hr/>

<h2>Invoice Summary</h2>

<?php
// Query to retreive InvoiceID, date of invoice, total cost of all Items, difference in price customer paid and total cost
$query = "select invoice.InvoiceID, invoice.InvoiceData,
          SUM(tran.quantity * stock.Price) AS ItemsTotal, 
          ((invoice.TotalPay) - SUM(tran.quantity * stock.Price)) as ChangeInCost
    from cust, invoice, tran, stock
    where cust.CustomerID = invoice.CustomerID 
          and invoice.InvoiceID = tran.InvoiceID 
          and tran.Stockcode = stock.Stockcode 
          and invoice.InvoiceID = $InvoiceID
    group by invoice.InvoiceID;";

// Execute query and fetch results into $row
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result);

// Display retreived query
echo "<table width='100%'>";

    echo "<tr>";
        echo "<td align = 'left'>Invoice ID:</td>";
        echo "<td align = 'right'>". $row["InvoiceID"]."</td>";
    echo "</tr>";

    echo "<tr>";
        echo "<td align = 'left'>Invoice Date:</td>";
        echo "<td align = 'right'>" . $row["InvoiceData"]. "</td>";
    echo "</tr>";

    echo "<tr>";
        echo "<td align = 'left'>Item(s) total:</td>";
        echo "<td align = 'right'>£" . $row["ItemsTotal"]. "</td>";
    echo "</tr>";

    // Check if difference in price customer paid and actual price is below 0 (discount) or above 0 (refund)
    if ($row["ChangeInCost"] < 0 ) {
        echo "<tr>";
            echo "<td align = 'left'>Item(s) discount:</td>";
            echo "<td align = 'right'>£" . $row["ChangeInCost"] . "</td>";
        echo "</tr>";
    } else if ($row["ChangeInCost"] > 0 ) {
        echo "<tr>";
            echo "<td align = 'left'>Item(s) refund:</td>";
            echo "<td align = 'right'>£" . $row["ChangeInCost"] . "</td>";
        echo "</tr>";
    }

    // Every InvoiceID has Free Shipping
    echo "<tr>";
        echo "<td align = 'left'>Shipping:</td>";
        echo "<td align = 'right'>ALWAYS FREE</td>";
    echo "</tr>";

echo "</table>";
?>

<hr/>

<?php
// Query to retreive amount customer paid for the invoice
$query = "select invoice.TotalPay
    	  from invoice
          where InvoiceID = $InvoiceID;";

// Execute query and fetch results into $row
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result);

// Display TotalPay
echo "<table width = 100%>";

    echo "<tr>";
        echo "<td align = 'left'>Invoice Total:</td>";
        echo "<td align = 'right'>£". $row["TotalPay"] ."</td>";
    echo "</tr>";

echo "</table>";
?>

<h2>Shipping Address</h2>

<?php
// Query to retreive customer name, address and country connected to invoice
$query = "select cust.CustomerName, cust.Address, cust.Country
    	  from cust, invoice
          where cust.CustomerID = invoice.CustomerID and invoice.InvoiceID = $InvoiceID;";

// Execute query and fetch results into $row          
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result);

echo "<table width = 100%>";

    echo "<tr>";
        echo "<td align = 'left'>". $row["CustomerName"] . ", " . $row["Address"] . ", " . $row["Country"] . "</td>";
    echo "</tr>";

echo "</table>";
?>

<h2> Payment Method </h2>

<?php
// Query to fetch payment method and invoice date
$query = "select PayMethod, InvoiceData
    	  from invoice
          where InvoiceID = $InvoiceID;";

// Execute query and fetch results into $row
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result);

// Display payment method with encryption image
echo "<table width = 100%>";

    echo "<tr>";
        echo "<td align='left'><img src='images/encrypted.png'></td>";
        echo "<td align='right'><span style='font-weight: bold; text-transform: uppercase'>". $row["PayMethod"] . "</span>, paid on " . $row["InvoiceData"] . "</td>";
    echo "</tr>";

echo "</table>";
?>

<?php
$query = "select SUM(tran.Quantity) AS TotalQuantity
          from invoice, tran, stock
          where invoice.InvoiceID = tran.InvoiceID and tran.Stockcode = stock.Stockcode and invoice.InvoiceID = $InvoiceID;";
// Execute query and fetch results into $row
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result);

echo "<h2>Item Details (" . $row["TotalQuantity"] . ")</h2>"
?>

<?php
// Query to fetch item descriptions, quantities, and prices
$query = "select stock.Description, tran.Quantity, stock.Price
          from invoice, tran, stock
          where invoice.InvoiceID = tran.InvoiceID and tran.Stockcode = stock.Stockcode and invoice.InvoiceID = $InvoiceID;";

$result = mysqli_query($conn, $query);

// Display each item's details in a table
echo "<table width = 100%>";
while ($row = mysqli_fetch_array($result))
{
    echo "<tr>";
        echo "<td align='left'>" . $row["Description"] . "</td>";
        echo "<td>x" . $row["Quantity"] . "</td>";
	    echo "<td align='right'>£" . $row["Price"] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>

</br>

<!-- Security Certification Image -->
<img src='images/security.png'>

<p>
    Gifft is committed to protecting your payment information. We follow PCI DSS standards, 
	use strong encryption, and perform regular reviews of its system to protect your privacy.
</p>

<!-- Payment Protection Image -->
<img src='images/PCIDSS.png' width = 100%>

<hr/>
<br/>

<!-- Form to query a different InvoiceID -->
<form action="gifft.php" method="POST">
    <span style='font-weight: bold;'>Query</span> Invoice #: 
    <input type="text" name="InvoiceID">
    <input type="submit">
</form>

</body>
</html>