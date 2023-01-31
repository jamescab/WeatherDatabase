<html>
<head>
        <title>News Stations</title>
    </head>

    <body>
	<form action = "mainMenu.php">
          <input type = "submit" value = "BACK" />
        </form>
        <h1>NEWS STATIONS</h1>
        <form method="GET" action="news.php">
      	    <input type="hidden" id="displayTupleRequest" name="displayTupleRequest">
      	    <input type="submit" value="Display Tables" name="displayTuples"></p>
      	</form>
        <form action = "updateStation.php">
          <input type = "submit" value = "Update News Station" />
        </form>
	<form action = "addStation.php">
          <input type = "submit" value = "Add News Station" />
        </form>
	<form action = "deleteStation.php">
          <input type = "submit" value = "Delete News Station" />
        </form>
        <form method="GET" action="news.php"> <!--refresh page when submitted-->
          <input type="hidden" id="getStationsInProvince" name="getStationsInProvince">
            Find the names of news stations in the province: <input type="text" name="prov">
          <input type="submit" value="Search" name="displayStationsInProvince"></p>
        </form>
        <form method="GET" action="news.php"> <!--refresh page when submitted-->
          <input type="hidden" id="getStationsInProvince" name="getStationsInProvince">
            Find the names of news stations which broadcast to all of the cities in the province: <input type="text" name="prov">
          <input type="submit" value="Search" name="displayStationsAllCities"></p>
        </form>
        <form method="GET" action="news.php"> <!--refresh page when submitted-->
          <input type="hidden" id="getStationsInProvince" name="getStationsInProvince">
            Find the number of news stations which broadcast to each province:
          <input type="submit" value="Go" name="aggregateStationsByProvince"></p>
        </form>
        <?php
      		//this tells the system that it's no longer just parsing html; it's now parsing PHP

              $success = True; //keep track of errors so it redirects the page only if there are no errors
              $db_conn = NULL; // edit the login credentials in connectToDB()
              $show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

              function debugAlertMessage($message) {
                  global $show_debug_alert_messages;

                  if ($show_debug_alert_messages) {
                      echo "<script type='text/javascript'>alert('" . $message . "');</script>";
                  }
              }

              function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
                  //echo "<br>running ".$cmdstr."<br>";
                  global $db_conn, $success;

                  $statement = OCIParse($db_conn, $cmdstr);
                  //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

                  if (!$statement) {
                      echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                      $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
                      echo htmlentities($e['message']);
                      $success = False;
                  }

                  $r = OCIExecute($statement, OCI_DEFAULT);
                  if (!$r) {
                      echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                      $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
                      echo htmlentities($e['message']);
                      $success = False;
                  }

      			return $statement;
      		}

              function executeBoundSQL($cmdstr, $list) {
                  /* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
      		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
      		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
      		See the sample code below for how this function is used */

      			global $db_conn, $success;
      			$statement = OCIParse($db_conn, $cmdstr);

                  if (!$statement) {
                      echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
                      $e = OCI_Error($db_conn);
                      echo htmlentities($e['message']);
                      $success = False;
                  }

                  foreach ($list as $tuple) {
                      foreach ($tuple as $bind => $val) {
                          //echo $val;
                          //echo "<br>".$bind."<br>";
                          OCIBindByName($statement, $bind, $val);
                          unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
      				}

                      $r = OCIExecute($statement, OCI_DEFAULT);
                      if (!$r) {
                          echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
                          $e = OCI_Error($statement); // For OCIExecute errors, pass the statementhandle
                          echo htmlentities($e['message']);
                          echo "<br>";
                          $success = False;
                      }
                  }
              }

              function printResult($result) { //prints results from a select statement
                  echo "<br>Retrieved data from table NewsStation:<br>";
                  echo "<table>";
                  echo "<tr><th>station name</th></tr>";

                  while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                      echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"
                  }

                  echo "</table>";
              }

              function printAggregateResult($result) { //prints results from a select statement
                  echo "<br>Count of news stations in each province:<br>";
                  echo "<table>";
                  echo "<tr><th>province</th><th>count</th></tr>";

                  while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                      echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"
                  }

                  echo "</table>";
              }
              function printNewsStation($result) { //prints results from a select statement
                  echo "<br>Retrieved data from table NewsStation:<br>";
                  echo "<table>";
                  echo "<tr><th>station id</th><th>station name</th></tr>";

                  while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                      echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"
                  }

                  echo "</table>";
              }

              function printBroadcasts($result) { //prints results from a select statement
                  echo "<br>Retrieved data from table BroadcastsTo:<br>";
                  echo "<table>";
                  echo "<tr><th>stationID</th><th>province</th><th>city</th></tr>";

                  while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                      echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td></tr>"; //or just use "echo $row[0]"
                  }

                  echo "</table>";
              }

              function printLocations($result) { //prints results from a select statement
                  echo "<br>Retrieved data from table Location:<br>";
                  echo "<table>";
                  echo "<tr><th>province</th><th>city</th><th>population</th></tr>";

                  while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                      echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td></tr>"; //or just use "echo $row[0]"
                  }

                  echo "</table>";
              }


              function connectToDB() {
                  global $db_conn;

                  // Your username is ora_(CWL_ID) and the password is a(student number). For example,
      			// ora_platypus is the username and a12345678 is the password.
                  //Login info removed for security reasons. Enter login info here before running app.
    		  //$db_conn = OCILogon();

                  if ($db_conn) {
                      debugAlertMessage("Database is Connected");
                      return true;
                  } else {
                      debugAlertMessage("Cannot connect to Database");
                      $e = OCI_Error(); // For OCILogon errors pass no handle
                      echo htmlentities($e['message']);
                      return false;
                  }
              }

              function disconnectFromDB() {
                  global $db_conn;

                  debugAlertMessage("Disconnect from Database");
                  OCILogoff($db_conn);
              }
      	function handleUpdateRequest() {
                  global $db_conn;

                  $stn_id = $_POST['stationId'];
                  $stn_name = $_POST['newName'];

                  // you need the wrap the old name and new name values with single quotations
                  executePlainSQL("UPDATE NewsStation SET stationName='" . $stn_name . "' WHERE stationID='" . $stn_id . "'");
                  OCICommit($db_conn);
              }

              function handleInsertEmployeeRequest() {
                  global $db_conn;

                  //Getting the values from user and insert data into the table
                  $tuple = array (
                    ":bind1" => $_POST['stnId'],
                    ":bind2" => $_POST['employeeId'],
                    ":bind3" => $_POST['employeeName'],
                    ":bind4" => $_POST['employeeEmail'],
                    ":bind5" => $_POST['employeePosition']
                  );

                  $alltuples = array (
                      $tuple
                  );

                  executeBoundSQL("insert into EmployeeNorm1 values (:bind4, :bind3)", $alltuples);
                  executeBoundSQL("insert into EmployeeNorm2 values (:bind1, :bind2, :bind4, :bind5)", $alltuples);
                  OCICommit($db_conn);
              }

              function handleInsertReceivesRequest(){
                global $db_conn;

                //Getting the values from user and insert data into the table
                $tuple = array (
                  ":bind1" => $_POST['stnId'],
                  ":bind2" => $_POST['forId']
                );

                $alltuples = array (
                    $tuple
                );

                executeBoundSQL("insert into Receives values (:bind1, :bind2)", $alltuples);
                OCICommit($db_conn);
              }

              function handleDeleteEmployeeRequest() {
                  global $db_conn;

                  $employeeEmail = $_POST['employeeEmail'];

                  executePlainSQL("DELETE FROM EmployeeNorm1 WHERE employeeEmail = '" . $employeeEmail . "'");
                  OCICommit($db_conn);
              }
              function handleInsertBroacastRequest() {
                global $db_conn;

                //Getting the values from user and insert data into the table
                $tuple = array (
                  ":bind1" => $_POST['stnId'],
                  ":bind2" => $_POST['city'],
                  ":bind3" => $_POST['prov']
                );

                $alltuples = array (
                    $tuple
                );

                executeBoundSQL("insert into BroadcastsTo values (:bind1, :bind3, :bind2)", $alltuples);
                OCICommit($db_conn);
              }

              function handleResetRequest() {
                  global $db_conn;
                  // Drop old table
                  executePlainSQL("DROP TABLE NewsStation");

                  // Create new table
                  echo "<br> creating new table <br>";
                  executePlainSQL("CREATE TABLE NewsStation (stationID int PRIMARY KEY, stationName char(20))");
                  OCICommit($db_conn);
              }

              function handleEmployeeRequest() {
                  global $db_conn;

                  //Getting the values from user and insert data into the table
                  $tuple = array (
                      ":bind1" => $_POST['stnID'],
                      ":bind2" => $_POST['stnNm']
                  );

                  $alltuples = array (
                      $tuple
                  );

                  executeBoundSQL("insert into NewsStation values (:bind1, :bind2)", $alltuples);
                  OCICommit($db_conn);
              }

              function handleCountRequest() {
                  global $db_conn;

                  $result = executePlainSQL("SELECT Count(*) FROM NewsStation");

                  if (($row = oci_fetch_row($result)) != false) {
                      echo "<br> The number of tuples in NewsStation: " . $row[0] . "<br>";
                  }
              }

          function handleDisplayStationsInProvince() {
            global $db_conn;
            $prov = $_GET['prov'];
            $result = executePlainSQL("SELECT n.stationName FROM NewsStation n, BroadcastsTo b WHERE n.stationID = b.stationID AND b.province = '" . $prov . "'");
            printResult($result);
          }

          function handleAggregateStationsByProvince() {
            global $db_conn;
            $result = executePlainSQL("SELECT Temp.province, COUNT(Temp.stationID) FROM (SELECT DISTINCT b.province, b.stationID FROM BroadcastsTo b) Temp GROUP BY Temp.province");
            printAggregateResult($result);
          }

          function handleDisplayStationsAllCities() {
            global $db_conn;
            $prov = $_GET['prov'];
            $result = executePlainSQL("SELECT n.stationName FROM NewsStation n WHERE NOT EXISTS ((SELECT l.city, l.province FROM Location l WHERE l.province = '". $prov ."') MINUS (SELECT c.city, c.province FROM BroadcastsTo c WHERE c.stationID = n.stationID AND c.province = '". $prov ."' ))");
            printResult($result);
          }

      		function handleDisplayRequest() {
      			global $db_conn;
      			$result = executePlainSQL("SELECT * FROM NewsStation");
      			printNewsStation($result);
            $broadcastsResult = executePlainSQL("SELECT * FROM BroadcastsTo");
            printBroadcasts($broadcastsResult);
            $locationResult = executePlainSQL("SELECT * FROM Location");
            printLocations($locationResult);

      		}

              // HANDLE ALL POST ROUTES
      	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
              function handlePOSTRequest() {
                  if (connectToDB()) {
                      if (array_key_exists('resetTablesRequest', $_POST)) {
                          handleResetRequest();
                      } else if (array_key_exists('updateQueryRequest', $_POST)) {
                          handleUpdateRequest();
                      } else if (array_key_exists('insertEmployeeRequest', $_POST)) {
                          handleInsertEmployeeRequest();
                      } else if (array_key_exists('deleteEmployeeRequest', $_POST)) {
                        handleDeleteEmployeeRequest();
                      } else if (array_key_exists('insertBroadcastLocation', $_POST)) {
                        handleInsertBroacastRequest();
                      } else if (array_key_exists('insertReceives', $_POST)) {
                        handleInsertReceivesRequest();
                      }

                      disconnectFromDB();
                  }
              }

              // HANDLE ALL GET ROUTES
      	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
              function handleGETRequest() {
                  if (connectToDB()) {
                      if (array_key_exists('countTuples', $_GET)) {
                          handleCountRequest();
                      } else if (array_key_exists ('displayTuples', $_GET)) {
      					             handleDisplayRequest();
                      } else if (array_key_exists ('displayStationsInProvince', $_GET)) {
                             handleDisplayStationsInProvince();
                      } else if (array_key_exists ('displayStationsAllCities', $_GET)) {
                             handleDisplayStationsAllCities();
                      } else if (array_key_exists ('aggregateStationsByProvince', $_GET)) {
                             handleAggregateStationsByProvince();
                      }
                      disconnectFromDB();
                  }
              }

      		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])|| isset($_POST['deleteSubmit'])) {
                  handlePOSTRequest();
              } else if (isset($_GET['countTupleRequest']) || isset($_GET['displayTupleRequest'])|| isset($_GET['getStationsInProvince'])) {
                  handleGETRequest();
              }

      		?>
    </body>
</html>
