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
  //$filter = 'a."VanID"='.$_GET['customer_id'];
} elseif (!empty($_GET['first_name'])) {
  $first_name = pg_escape_string($_GET['first_name']);
  $filter = "a.\"FirstName\" = '$first_name'";
  //$filter = 'a."FirstName"='.$_GET['first_name'];
} elseif (!empty($_GET['last_name'])) {
  $last_name = pg_escape_string($_GET['last_name']);
  $filter = "a.\"LastName\" = '$last_name'";
  //$filter = 'a."LastName"='.$_GET['last_name'];
} elseif (!empty($_GET['phone'])) {
  $phone = pg_escape_string($_GET['phone']);
  $filter = "e.\"Phone\" = '$phone'";
  //$filter = 'e."Phone"='.$_GET['phone'];
} else {
  $filter = '1=0';
}


$sql = <<<SQL

SELECT
  a."VanID",
  a."FirstName",
  a."LastName",
  a."DateCreated",
  b."Amount",
  c."Nickname",
  d."RecurringContributionStatusID" AS "Recurring",
  d."RecurringAmount",
  e."Phone",
  f."ContactSourceName" AS "ContactSource",
  h."ActivistCodeName"
FROM
  "ngpsync"."Contacts" AS a
LEFT JOIN
  (SELECT
    "VanID",
    SUM("Amount") AS "Amount"
  FROM
    "ngpsync"."ContactsContributions"
  WHERE
    DATE("DateReceived") >= CURRENT_DATE - INTERVAL '1 year' --filters TO WITHIN LAST year
  GROUP BY
    "VanID" ) AS b
ON
  a."VanID" = b."VanID"
LEFT JOIN
  "ngpsync"."ContactsAdditionalContactInformation" AS c
ON
  a."VanID" = c."VanID"
LEFT JOIN
  "ngpsync"."ContactsRecurringContributions" AS d
ON
  a."VanID" = d."VanID"
LEFT JOIN
  "ngpsync"."ContactsPhones" AS e
ON
  a."VanID" = e."VanID"
LEFT JOIN
  "ngpsync"."ContactSources" AS f
ON
  a."ContactSourceID" = f."ContactSourceID"
LEFT JOIN
  "ngpsync"."ContactsActivistCodes" AS g
ON
  a."VanID" = g."VanID"
LEFT JOIN
  "ngpsync"."ActivistCodes" AS h
ON
  g."ActivistCodeID" = h."ActivistCodeID"
WHERE
  $filter
  AND a."LastName" IS NOT NULL
  AND a."FirstName" IS NOT NULL
  AND ( (h."ActivistCodeName" = '23-25 SEC Member')
    OR (h."ActivistCodeName" = '23-25 Pct Chair')
    OR (h."ActivistCodeName" = '23-25 Vice Pct Chair')
    OR (h."ActivistCodeName" = '23-25 CEC Member')
    OR (h."ActivistCodeName" = '23-25 Pct S/T')
    OR (h."ActivistCodeName" = '23 Elected Official')
    OR (h."ActivistCodeName" IS NULL))
GROUP BY
  a."VanID",
  a."FirstName",
  a."LastName",
  a."DateCreated",
  b."Amount",
  c."Nickname",
  "Recurring",
  d."RecurringAmount",
  e."Phone",
  "ContactSource",
  h."ActivistCodeName"
SQL;
$result = pg_query($conn, $sql);


header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"'."?".">\n";
?>
<livelookup version="1.0" columns="first_name, last_name, phone">
  <?php while ($row = pg_fetch_assoc($result)): ?>
  <customer>
    <customer_id><?php echo $row['VanID'];?></customer_id>
    <first_name><?php echo $row['FirstName'];?></first_name>
    <last_name><?php echo $row['LastName'];?></last_name>
    <Custom14><?php echo $row['DateCreated'];?></Custom14>
    <Custom11><?php echo $row['Amount'];?></Custom11>
    <Custom19><?php echo $row['Nickname'];?></Custom19>
    <Custom15><?php echo $row['Recurring'];?></Custom15>
    <Custom20><?php echo $row['RecurringAmount'];?></Custom20>
    <phone><?php echo $row['Phone'];?></phone>
    <Custom13><?php echo $row['ContactSource'];?></Custom13>
    <Custom18><?php echo $row['ActivistCodeName'];?></Custom18>
  </customer>
  <?php endwhile; ?>
</livelookup>
