<style>
  body {
  padding-top: 20px;
  text-align: center;
  font-family: sans-serif;
  background-color: #aaa;
}
h2 {
  text-align: left;
  font-size: 2em;
  padding-left: 2.5em
}
span {
  background: #fd0;
}
tr {
 padding-left: 2em; 
}
</style>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Revenue</title>
</head>
<body>
<?php

 $dbc = mysqli_connect('localhost', 'root', '', 'Timmy_Car_Rental')
 
 or die('Error connecting to MySQL Database');
 
 $query = 'SELECT I.LOCATION_ID,
            LO.LOCATION_CITY,
            LO.LOCATION_STATE,
            SUM((DATEDIFF(RENT_DATE, RETURN_DATE) * COST_PER_DAY) + 
              ((MILES_AFTER - MILES_BEFORE) * COST_PER_MILE) + 
              REVENUE_FROM_DAMAGES + 
              BASE_PRICE) AS TOTAL_REVENUE
            FROM INVOICE AS I
            INNER JOIN LINE AS L ON L.INVOICE_ID = I.INVOICE_ID
            INNER JOIN VEHICLE AS V ON L.VEHICLE_VIN LIKE V.VEHICLE_VIN
            INNER JOIN RENTAL_CLASS AS B ON B.CLASS_ID = V.CLASS_ID
            INNER JOIN LOCATION AS LO ON LO.LOCATION_ID = I.LOCATION_ID
            INNER JOIN (SELECT I.LOCATION_ID, SUM(DMG_COST) AS REVENUE_FROM_DAMAGES 
                        FROM INVOICE AS I 
                        LEFT OUTER JOIN LINE AS L
                          ON I.INVOICE_ID = L.INVOICE_ID
                        LEFT OUTER JOIN DAMAGE_LIST AS DL
                          ON DL.LINE_ID = L.LINE_ID
                        LEFT OUTER JOIN DAMAGE AS D
                          ON D.DMG_ID = DL.DMG_ID
                        GROUP BY I.LOCATION_ID) AS D ON D.LOCATION_ID = I.LOCATION_ID
            GROUP BY I.LOCATION_ID, LO.LOCATION_CITY, LO.LOCATION_STATE;';

 
$result = mysqli_query($dbc, $query)

or die('Error selecting the data');

if (mysqli_num_rows($result) > 0) {
    //table headers:
          echo '<table BORDER="3"    WIDTH="50%"   CELLPADDING="10" CELLSPACING="2" ALIGN="center" VALIGN="middle">';
          echo '<TH COLSPAN="3"><h2>Total Revenue by Location</h2></TH>';
          echo "<tr>"; 
          echo "<td>ID</td>"; 
          echo "<td>Location</td>"; 
          echo "<td>Total Revenue</td>";
          echo "</tr>"; 
    
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
          echo '<tr>'; 
          echo '<td>' . $row['LOCATION_ID'] . '</td>'; 
          echo "<td>" . $row['LOCATION_CITY'] . ", " . $row['LOCATION_STATE'] . "</td>"; 
          echo "<td>" . $row['TOTAL_REVENUE'] . "</td>";
          echo "</tr>"; 
    }
    //close table after all data entered
    echo "</table>";
} else {
    echo "0 results";
}

mysqli_close($dbc);
?> 


</body>
</html>
