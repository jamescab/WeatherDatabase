<html>
<head>
    <title>Add Extreme Weather Events</title>
</head>
<body>
<h1>Add Extreme Weather Events</h1>
<form action = 'extremeWeatherEvents.php'>
    <input type = "submit" value = "BACK" />
</form>

<h2>Add Extreme Weather Events:</h2>
<form method= "POST" action = 'addExtremeWeatherEvents.php'>
    <input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
    Event ID: <input type="number" name="eventID"> <br /> <br />
    Event Type: <input type="text" name="eventType"> <br /> <br />
    Danger level: <input type="number" name="dangerLevel"> <br /> <br />
    <input type = "submit" value = "Add Event" name = "insertSubmit" /></form></p>
</form>

<form method= "POST" action = 'addExtremeWeatherEvents.php'>
    <input type = "hidden" value = "resetTablesRequest" name = "resetTablesRequest">
    <input type = "submit" value = "Reset" name = "reset"></p>
</form>

<form method= "GET" action = 'addExtremeWeatherEvents.php'>
    <input type = "hidden" value = "displayTupleRequest" name = "displayTupleRequest">
    <input type = "submit" value = "Display" name = "displayTuples"></p>
</form>

</hr>
<h2>Advisories:</h2>
<form action = 'advisories.php'>
    <input type = "submit" value = "Add Advisory" />
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
    echo "<br>Retrieved data from table Extreme Weather Event:<br>";
    echo "<table>";
    echo "<tr><th>EventID</th><th>EventType</th><th>DangerLevel</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[2] . "</td><td>" . $row[1] . "</td><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";
}

function connectToDB() {
    global $db_conn;

    // Your username is ora_(CWL_ID) and the password is a(student number). For example,
    // ora_platypus is the username and a12345678 is the password.
    //Login info removed for security reasons. Enter login info here before running app
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

function handleResetRequest() {
    global $db_conn;
    // Drop old table
    executePlainSQL("DROP TABLE ExtremeWeatherEvent");

    // Create new table
    echo "<br> creating new table <br>";
    executePlainSQL("CREATE TABLE ExtremeWeatherEvent (dangerLevel int, eventType char(10), eventID int PRIMARY KEY)");
    OCICommit($db_conn);
}

function handleInsertEWRequest() {
    global $db_conn;

    //Getting the values from user and insert data into the table
    $tuple = array (
        ":bind1" => $_POST['eventID'],
        ":bind2" => $_POST['eventType'],
        ":bind3" => $_POST['dangerLevel']
    );

    $alltuples = array (
        $tuple
    );

    executeBoundSQL("insert into ExtremeWeatherEvent values (:bind3, :bind2, :bind1)", $alltuples);
    OCICommit($db_conn);
}

function handleCountRequest() {
    global $db_conn;

    $result = executePlainSQL("SELECT Count(*) FROM ExtremeWeatherEvent");

    if (($row = oci_fetch_row($result)) != false) {
        echo "<br> The number of tuples in ExtremeWeatherEvent: " . $row[0] . "<br>";
    }
}

function handleDisplayRequest() {
    global $db_conn;
    $result = executePlainSQL("SELECT * FROM ExtremeWeatherEvent");
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
            handleInsertEWRequest();
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
