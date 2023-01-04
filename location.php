<html>
<head>
        <title>Location</title>
    </head>

    <body>
    <form action = 'mainMenu.php'>
        <input type="submit" value="BACK" />
    </form>

    <h1>LOCATION</h1>
    <h2>Add: </h2>
    <form method= "POST" action = 'location.php'>
    <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
    City: <input type="text" name="city"> <br /> <br />
    Province: <input type="text" name="province"> <br /> <br />
    Population: <input type="number" name="population"> <br /> <br />

    <input type = "submit" value = "Add Location" name="insertSubmit" /></p>
    </form>

    <h2>Update:</h2>
        <form method="POST" action="location.php">
            <input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
            Province: <input type="text" name="updateProvince"> <br /> <br />
            City: <input type="text" name="oldCity"> <br /> <br />
            Update city: <input type="text" name="newName"> <br /><br />

            <input type="submit" value="Update" name="updateSubmit"></p>
        </form>

    <h2>Reset:</h2>
   	 <form method="GET" action="location.php">
             <input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
        	<input type="submit" value="Reset" name="reset"></p>
    </form>

        <h2>Display:</h2>
    <form method="GET" action="location.php">
        <input type="hidden" id="displayTupleRequest" name="displayTupleRequest">
        <input type="submit" value="Display" name="displayTuples"></p>
    </form>

    <h2>Forecast:</h2>
<form method= "POST" action = 'weather.php'>
    <input type = "submit" value = "Forecast" />
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
    echo "<br>Retrieved data from table Location:<br>";
    echo "<table>";
    echo "<tr><th>City</th><th>Province</th><th>Population</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[1] . "</td><td>" . $row[0] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";
}

function connectToDB() {
    global $db_conn;

    // Your username is ora_(CWL_ID) and the password is a(student number). For example,
    // ora_platypus is the username and a12345678 is the password.
    $db_conn = OCILogon("ora_nacheung", "a33380130", "dbhost.students.cs.ubc.ca:1522/stu");

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

    $old_city = $_POST['oldCity'];
    $province = $_POST['updateProvince'];
    $new_city = $_POST['newName'];

    // you need the wrap the old name and new name values with single quotations
    executePlainSQL("UPDATE Location SET city='" . $new_city . "' WHERE city='" . $old_city . "' AND province='" .$province . "'");
    OCICommit($db_conn);
}

function handleResetRequest() {
    global $db_conn;
    // Drop old table
    executePlainSQL("DROP TABLE Location");

    // Create new table
    echo "<br> creating new table <br>";
    executePlainSQL("CREATE TABLE Location (city char(20) PRIMARY KEY, province char(30) PRIMARY KEY, population int)");
    OCICommit($db_conn);
}

function handleInsertRequestLocation() {
    global $db_conn;

    //Getting the values from user and insert data into the table
    $tuple = array (
        ":bind1" => $_POST['city'],
        ":bind2" => $_POST['province'],
        ":bind3" => $_POST['population']
    );

    $alltuples = array (
        $tuple
    );

    executeBoundSQL("insert into Location values (:bind2, :bind1, :bind3)", $alltuples);
    OCICommit($db_conn);
}

function handleCountRequest() {
    global $db_conn;

    $result = executePlainSQL("SELECT Count(*) FROM Location");

    if (($row = oci_fetch_row($result)) != false) {
        echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
    }
}

function handleDisplayRequest() {
    global $db_conn;
    $result = executePlainSQL("SELECT * FROM Location");
    printResult($result);
}

// HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('resetTablesRequest', $_POST)) {
            handleResetRequest();
        } else if (array_key_exists('updateQueryRequest', $_POST)) {
            handleUpdateRequest();
        } else if (array_key_exists('insertQueryRequest', $_POST)) {
            handleInsertRequestLocation();
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
        } else if (array_key_exists('displayTuples', $_GET)) {
            handleDisplayRequest();
        }

        disconnectFromDB();
    }
}

if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
    handlePOSTRequest();
} else if (isset($_GET['countTupleRequest']) || isset($_GET['displayTupleRequest'])) {
    handleGETRequest();
}
?>
</body>
</html>
