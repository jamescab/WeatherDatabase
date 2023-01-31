<html>
<head>
        <title>Recipients</title>
    </head>
    <body>
        <h1>RECIPIENTS</h1>
		
		<form action="mainMenu.php">
			<input type="submit" value="BACK TO MENU" />
		</form>
		
		<form action="advisories.php">
			<input type="submit" value="Back to Advisories" />
		</form>
		
		<h2>Reset</h2>
        <p>Reset Recipients (initializes table)</p>
        <form method="POST" action="recipients.php">
            <!-- if you want another page to load after the button is clicked, you have to specify that page in the action parameter -->
            <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
            <p><input type="submit" value="Reset" name="reset"></p>
        </form>
		
		<hr />
		
		<h2>Add Recipients</h2>
		
		<h3>Add Centre</h3>
        <form method="POST" action="recipients.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertCentreQueryRequest" name="insertCentreQueryRequest">
			Recipient ID: <input type="text" name="recID"> <br /><br />
			Phone Number: <input type="text" name="phone"> <br /><br />
			Centre ID: <input type="text" name="cenID"> <br /><br />
			Centre Name: <input type="text" name="cenName"> <br /><br />
            Centre Address: <input type="text" name="cenAddress"> <br /><br />
            Centre Population: <input type="text" name="cenPopulation"> <br /><br />
            <input type="submit" value="Add Centre" name="insertSubmit"></p>
        </form>
		
		<h3>Add Person</h3>
        <form method="POST" action="recipients.php"> <!--refresh page when submitted-->
            <input type="hidden" id="insertPersonQueryRequest" name="insertPersonQueryRequest">
			Recipient ID: <input type="text" name="recID"> <br /><br />
			Phone Number: <input type="text" name="phone"> <br /><br />
			Person SIN: <input type="text" name="perSin"> <br /><br />
			Person Name: <input type="text" name="perName"> <br /><br />
            Person Address: <input type="text" name="perAddress"> <br /><br />
            <input type="submit" value="Add Person" name="insertSubmit"></p>
        </form>
		
		<hr />
		
		<h2>Recipient View Options</h2>
		
		<form method="GET" action="recipients.php"> <!--refresh page when submitted-->
            <input type="hidden" id="displayRecipientTupleRequest" name="displayRecipientTupleRequest">
            <input type="submit" value="Show Recipients" name="displayRecipientTuples"></p>
        </form>
		
		<h3>Display centres by attributes</h3>
		<h4>(Selecting no attributes or all attributes shows table with all attributes)</h4>
        <form method="GET" action="recipients.php"> <!--refresh page when submitted-->
            <input type="hidden" id="displayCentreTupleRequest" name="displayCentreTupleRequest">
			Show ID: <input type="checkbox" name="cenIDShow"> <br /><br />
			Show Name: <input type="checkbox" name="cenNameShow"> <br /><br />
			Show Address: <input type="checkbox" name="cenAddressShow"> <br /><br />
			Show Population: <input type="checkbox" name="cenPopulationShow"> <br /><br />
            <input type="submit" value="Show Centres" name="displayCentreTuples"></p>
        </form>
		
		<h3>Display people by attributes</h3>
		<h4>(Selecting no attributes or all attributes shows table with all attributes)</h4>
		<form method="GET" action="recipients.php">
			<input type="hidden" id="displayPersonTupleRequest" name="displayPersonTupleRequest">
			Show SIN: <input type="checkbox" name="perSinShow"> <br /><br />
			Show Name: <input type="checkbox" name="perNameShow"> <br /><br />
			Show Address: <input type="checkbox" name="perAddressShow"> <br /><br />
			<input type="submit" value="Show People" name="displayPersonTuples"></p>
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

        function printRecipientResult($result) {
			echo "<br>Retrieved data from Recipient:<br>";
			echo "<table>";
			echo "<tr><th>ID</th><th>Phone Number</th></tr>";
			
			while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>";
            }

            echo "</table>";
		}
		
		function printCentre1Result($result) {
			echo "<br>Retrieved data from CentreNorm1:<br>";
			echo "<table>";
			echo "<tr><th>Recipient ID</th><th>Phone Number</th></tr>";
			
			while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>";
            }

            echo "</table>";
		}
		
		function printCentre2Result($result) { //prints results for Centre from a select statement
            echo "<br>Retrieved data from CentreNorm2:<br>";
            echo "<table>";
			$attributes = "";
			if (array_key_exists('cenIDShow', $_GET)) {
				$attributes = $attributes . "<th>Centre ID</th>";
			}
			
			if (array_key_exists('cenNameShow', $_GET)) {
				$attributes = $attributes . "<th>Name</th>";
			}
			
			if (array_key_exists('cenPopulationShow', $_GET)) {
				$attributes = $attributes . "<th>Population</th>";
			}
			
			if (array_key_exists('cenAddressShow', $_GET)) {
				$attributes = $attributes . "<th>Address</th>";
			}
			if (empty($attributes)) {
				$attributesFinal = "<tr><th>ID</th><th>Name</th><th>Population</th><th>Address</th></tr>";
            } else {
				$attributesFinal = "<tr>" . $attributes . "</tr>";
			}
			echo $attributesFinal; //Decides which attribute names to show based on user input

            while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td></tr>";
            }

            echo "</table>";
        }
		
		function printPerson1Result($result) {
			echo "<br>Retrieved data from PersonNorm1:<br>";
			echo "<table>";
			echo "<tr><th>Recipient ID</th><th>SIN</th></tr>";
			
			while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
                echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>";
            }

            echo "</table>";
		}
		
		function printPerson2Result($result) { //prints results for Person from a select statement
            echo "<br>Retrieved data from PersonNorm2:<br>";
            echo "<table>";
			$attributes = "";
			if (array_key_exists('perSinShow', $_GET)) {
				$attributes = $attributes . "<th>SIN</th>";
			}
			
			if (array_key_exists('perNameShow', $_GET)) {
				$attributes = $attributes . "<th>Name</th>";
			}
			
			if (array_key_exists('perAddressShow', $_GET)) {
				$attributes = $attributes . "<th>Address</th>";
			}
			if (empty($attributes)) {
				$attributesFinal = "<tr><th>SIN</th><th>Name</th><th>Address</th></tr>";
            } else {
				$attributesFinal = "<tr>" . $attributes . "</tr>";
			}
			echo $attributesFinal; //Decides which attribute names to show based on user input

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
		
		function handleResetRequest() {
            global $db_conn;
            // Drop old table
			executePlainSQL("DROP TABLE PersonNorm1");
			executePlainSQL("DROP TABLE PersonNorm2");
			executePlainSQL("DROP TABLE CentreNorm1");
			executePlainSQL("DROP TABLE CentreNorm2");
            executePlainSQL("DROP TABLE Recipient");

            // Create new table
            echo "<br> creating new table <br>";
            executePlainSQL("CREATE TABLE Recipient (recipientID int PRIMARY KEY, phoneNum int)");
			executePlainSQL("CREATE TABLE CentreNorm2 (centreID int PRIMARY KEY, centreName char(30), centrePopulation int, centreAddress char(30))");
			executePlainSQL("CREATE TABLE CentreNorm1 (recipientID int PRIMARY KEY REFERENCES Recipient(recipientID), centreID int REFERENCES CentreNorm2(centreID))");
			executePlainSQL("CREATE TABLE PersonNorm2 (sin int PRIMARY KEY, personName char(30), address char(30))");
			executePlainSQL("CREATE TABLE PersonNorm1 (recipientID int PRIMARY KEY REFERENCES Recipient(recipientID), sin int REFERENCES PersonNorm2(sin))");
            OCICommit($db_conn);
        }
		
		function handleInsertCentreRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['recID'],
                ":bind2" => $_POST['phone'],
				":bind3" => $_POST['cenID'],
				":bind4" => $_POST['cenName'],
				":bind5" => $_POST['cenAddress'],
				":bind6" => $_POST['cenPopulation']
            );

            $alltuples = array (
                $tuple
            );

            executeBoundSQL("insert into Recipient values (:bind1, :bind2)", $alltuples);
			executeBoundSQL("insert into CentreNorm2 values (:bind3, :bind4, :bind6, :bind5)", $alltuples);
			executeBoundSQL("insert into CentreNorm1 values (:bind1, :bind3)", $alltuples);
            OCICommit($db_conn);
        }
		
		function handleInsertPersonRequest() {
            global $db_conn;

            //Getting the values from user and insert data into the table
            $tuple = array (
                ":bind1" => $_POST['recID'],
                ":bind2" => $_POST['phone'],
				":bind3" => $_POST['perSin'],
				":bind4" => $_POST['perName'],
				":bind5" => $_POST['perAddress']
            );

            $alltuples = array (
                $tuple
            );

            executeBoundSQL("insert into Recipient values (:bind1, :bind2)", $alltuples);
			executeBoundSQL("insert into PersonNorm2 values (:bind3, :bind4, :bind5)", $alltuples);
			executeBoundSQL("insert into PersonNorm1 values (:bind1, :bind3)", $alltuples);
            OCICommit($db_conn);
        }

        function handleCountRequest() {
            global $db_conn;

            $result = executePlainSQL("SELECT Count(*) FROM Recipient");

            if (($row = oci_fetch_row($result)) != false) {
                echo "<br> The number of Recipients: " . $row[0] . "<br>";
            }
        }
		
		function handleDisplayRecipientRequest () {
			global $db_conn;
			$result = executePlainSQL("SELECT * FROM Recipient");
			printRecipientResult($result);
		}
		
		function handleDisplayCentreRequest() {
			global $db_conn;
			$selections = "";
			if (array_key_exists('cenIDShow', $_GET)) {
				$selections = $selections . " centreID,";
			}
			
			if (array_key_exists('cenNameShow', $_GET)) {
				$selections = $selections . " centreName,";
			}
			
			if (array_key_exists('cenPopulationShow', $_GET)) {
				$selections = $selections . " centrePopulation,";
			}
			
			if (array_key_exists('cenAddressShow', $_GET)) {
				$selections = $selections . " centreAddress,";
			}
			
			$selections = rtrim($selections, ",");
			if (empty($selections)) {
				$query = "SELECT * FROM CentreNorm2";
			} else {
				$query = "SELECT" . $selections . " FROM CentreNorm2";
			}
			$result1 = executePlainSQL("SELECT * FROM CentreNorm1");
			$result2 = executePlainSQL($query);
			printCentre1Result($result1);
			printCentre2Result($result2);
		}
		
		function handleDisplayPersonRequest() {
			global $db_conn;
			$selections = "";
			if (array_key_exists('perSinShow', $_GET)) {
				$selections = $selections . " sin,";
			}
			
			if (array_key_exists('perNameShow', $_GET)) {
				$selections = $selections . " personName,";
			}
			
			if (array_key_exists('perAddressShow', $_GET)) {
				$selections = $selections . " address,";
			}
			
			$selections = rtrim($selections, ",");
			if (empty($selections)) {
				$query = "SELECT * FROM PersonNorm2";
			} else {
				$query = "SELECT" . $selections . " FROM PersonNorm2";
			}
			$result1 = executePlainSQL("SELECT * FROM PersonNorm1");
			$result2 = executePlainSQL($query);
			printPerson1Result($result1);
			printPerson2Result($result2);
		}

        // HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handlePOSTRequest() {
            if (connectToDB()) {
                if (array_key_exists('resetTablesRequest', $_POST)) {
                    handleResetRequest();
                } else if (array_key_exists('updateQueryRequest', $_POST)) {
                    handleUpdateRequest();
                } else if (array_key_exists('insertCentreQueryRequest', $_POST)) {
                    handleInsertCentreRequest();
                } else if (array_key_exists('insertPersonQueryRequest', $_POST)) {
                    handleInsertPersonRequest();
                }

                disconnectFromDB();
            }
        }

        // HANDLE ALL GET ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
        function handleGETRequest() {
            if (connectToDB()) {
                if (array_key_exists ('displayRecipientTuples', $_GET)) {
					handleDisplayRecipientRequest();
				} else if (array_key_exists ('displayCentreTuples', $_GET)) {
					handleDisplayCentreRequest();
				} else if (array_key_exists ('displayPersonTuples', $_GET)) {
					handleDisplayPersonRequest();
				}

                disconnectFromDB();
            }
        }

		if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
            handlePOSTRequest();
        } else if (isset($_GET['displayRecipientTupleRequest']) || isset($_GET['displayCentreTupleRequest']) || isset($_GET['displayPersonTupleRequest'])) {
            handleGETRequest();
        }
		?>
	</body>
</html>
