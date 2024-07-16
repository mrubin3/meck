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
  $customer_id = pg_escape_string($_GET['customer_id']);
  $filter = "a.\"VanID\" = '$customer_id'";
} elseif (!empty($_GET['last_name'])) {
  $last_name = pg_escape_string($_GET['last_name']);
  $filter = "a.\"LastName\" = '$last_name'";
} elseif (!empty($_GET['phone'])) {
  $phone = pg_escape_string($_GET['phone']);
  $filter = "e.\"Phone\" = '$phone'";
} 
  
//if (empty($filter)) {
//    echo '<error>No valid filter provided.</error>';
//    exit;  // Stop further execution if no valid filter is set
//}

$sql = <<<SQL

SELECT
  a."VanID",
  a."LastName",
  e."Phone"
FROM
  "ngpsync"."Contacts" AS a
LEFT JOIN
  "ngpsync"."ContactsPhones" AS e
ON
  a."VanID" = e."VanID"
WHERE
  $filter
  AND a."LastName" IS NOT NULL
SQL;
$result = pg_query($conn, $sql);

//if (!$result) {
//    echo '<error>Query execution failed.</error>';
//    exit;
//}

header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"'."?".">\n";
?>
<livelookup version="1.0" columns="customer_id, last_name">
  <?php while ($row = pg_fetch_assoc($result)): ?>
  <customer>
    <customer_id><?php echo $row['VanID'];?></customer_id>
    <last_name><?php echo $row['LastName'];?></last_name>
    <phone><?php echo $row['Phone'];?></phone>
  </customer>
  <?php endwhile; ?>
</livelookup>    
