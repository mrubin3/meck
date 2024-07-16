<?php
header('Content-type: text/xml');
?>
<?xml version="1.0" encoding="utf-8"?>
<livelookup version="1.0" columns="customer_id, last_name">
  <customer>
    <customer_id><?php echo 'New Test'; ?></customer_id>
    <last_name>test2</last_name>
    <phone>555-222-3344</phone>
  </customer>
</livelookup>    
