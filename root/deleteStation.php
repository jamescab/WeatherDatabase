<html>
<head>
        <title>Delete News Station</title>
    </head>
    <body>
	<form action="news.php">
	    <input type="submit" value="BACK" />
	</form>
	<h1>DELETE NEWS STATION</h1>
	<form method="GET" action="deleteStation.php">
	    <input type="hidden" id="displayTupleRequest" name="displayTupleRequest">
	    <input type="submit" value="Display" name="displayTuples"></p>
	</form>
<form method="POST" action="deleteStation.php"> <!--refresh page when submitted-->
  <input type="hidden" id="deleteStationRequest" name="deleteStationRequest">
    Station ID: <input type="text" name="stnId"><br /><br />
  <input type="submit" value="Delete" name="deleteSubmit"></p>
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

        function printNewsStationResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from table NewsStation:<br>";
            echo "<table>";
            echo "<tr><th>stationID</th><th>stationName</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"
            }

            echo "</table>";
        }
	function printBroadcastsResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from table BroadcastsTo:<br>";
            echo "<table>";
            echo "<tr><th>stationID</th><th>Province</th><th>City</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>";
            }

            echo "</table>";
        }

  function printReceivesResult($result) { //prints results from a select statement
            echo "<br>Retrieved data from table Receives:<br>";
            echo "<table>";
            echo "<tr><th>stationID</th><th>forecastID</th></tr>";

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>";
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

        function handleDeleteStationRequest() {
            global $db_conn;

            $stationId = $_POST['stnId'];

            executePlainSQL("DELETE FROM NewsStation WHERE stationID = '" . $stationId . "'");
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


        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM NewsStation");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of tuples in NewsStation: " . $row[0] . "<br>";
            }
        }

		function handleDisplayRequest() {
			global $db_conn;
			$result = executePlainSQL("SELECT * FROM NewsStation");
			printNewsStationResult($result);
		}

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('deleteStationRequest', $_POST)) {
                    handleDeleteStationRequest();
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
				}

                disconnectFromDB();
            }
        }

    if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])|| isset($_POST['deleteSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['countTupleRequest']) || isset($_GET['displayTupleRequest'])) {
            handleGETRequest();
        }
		?>

	</body>
</html>
