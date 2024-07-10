<?php
$db_host = "34.41.141.8";
$db_user = "ngp";
$db_password = "pfq@qeb!DTD8czx3hrw";
$db_name = "meckdems_ngp";

$conn = pg_connect("host=$db_host dbname=$db_name user=$db_user password=$db_password");

if (!$conn) {
  echo '<error>Could not connect to the database.</error>';
}

if (!empty($_GET['customer_id'])) {
  //$customer_id = pg_escape_string($_GET['customer_id']);
  $filter = '"VanID"='.$_GET['customer_id'];
} elseif (!empty($_GET['first_name'])) {
  //$first_name = pg_escape_string($_GET['first_name']);
  $filter = '"FirstName"='.$_GET['first_name'];
} elseif (!empty($_GET['last_name'])) {
  //$last_name = pg_escape_string($_GET['last_name']);
  $filter = '"LastName"='.$_GET['last_name'];
} else {
  $filter = '1=0';
}


$sql = <<<SQL
SELECT
  "VanID",
  "FirstName",
  "LastName"
FROM
  "ngpsync"."Contacts"
SQL;
$result = pg_query($conn, $sql);

//$matches = pg_fetch_all($result);
//echo $matches;


header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="ISO-8859-1"'."?".">";
?>
<livelookup version="1.0" columns="customer_id, first_name, last_name">
  <?php 
  while ($row = pg_fetch_assoc($result)): ?>
  <customer>
    <customer_id><?php echo $row['VanID'];?></customer_id>
    <first_name><?php echo $row['FirstName'];?></first_name>
    <last_name><?php echo $row['LastName'];?></last_name>
  </customer>
  <?php endwhile; ?>
</livelookup>