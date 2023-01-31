<html>
<head>
    <title>Search Extreme Weather Events</title>
</head>
<body>
<form action = 'extremeWeatherEvents.php'>
    <input type = "submit" value = "BACK" />
</form>

<h1>Search Extreme Weather Events</h1>
<form method = "GET" action = 'searchExtremeWeatherEvents.php'>
<h2>Filter Events: </h2>
Event Type: <input type="text" name="findEventType"> <br /> <br />

<h2>Include columns:</h2>
    <input type="hidden" name="includeEventID" value="0">
Event ID: <input type="checkbox" name="includeEventID" value="1"> <br /><br />
    <input type="hidden" name="includeEventType" value="0">
Type: <input type="checkbox" name="includeEventType" value="1"> <br /><br />
    <input type="hidden" name="includeDangerLevel" value="0">
Danger Level: <input type="checkbox" name="includeDangerLevel" value="1"> <br /><br />
    <input type="hidden" id="findTupleRequest" name="findTupleRequest">
    <input type ="submit" value = "Find Events" name = "displayFindTuples" />
</form>

<hr />

<h2>Find events:</h2>
<form method = "GET" action = "searchExtremeWeatherEvents.php">
    <input type = "hidden" value = "groupBy" name = "groupBy">
    Find the danger levels with at least 2 events:
    <input type = "submit" value = "Search" name = "having"></p>
</form>
<form method = "GET" action = "searchExtremeWeatherEvents.php">
    <input type = "hidden" value = "groupBy" name = "groupBy">
Find the event types for which their average danger level is the minimum over all events types:
    <input type = "submit" value = "Search" name = "nested"></p>
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
    echo "<br>Retrieved data from table Extreme Weather Events:<br>";
    echo "<table>";
    echo "<tr><th>EventID</th><th>EventType</th><th>DangerLevel</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[2] . "</td><td>" . $row[1] . "</td><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";
}

function printResultDangerLevel($result) { //prints results from a select statement
    echo "<br>Search Danger Level:<br>";
    echo "<table>";
    echo "<tr><th>Level</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";
}

function printResultType($result) { //prints results from a select statement
    echo "<br>Search Result:<br>";
    echo "<table>";
    echo "<tr><th>EventType</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]"
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

    $old_name = $_POST['oldName'];
    $new_name = $_POST['newName'];

    // you need the wrap the old name and new name values with single quotations
    executePlainSQL("UPDATE demoTable SET name='" . $new_name . "' WHERE name='" . $old_name . "'");
    OCICommit($db_conn);
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

function handleDisplayHavingRequest() {
    global $db_conn;
    $result = executePlainSQL("SELECT e.dangerLevel FROM ExtremeWeatherEvent e GROUP BY e.dangerLevel HAVING COUNT(*) > 1");
    printResultDangerLevel($result);
}

function handleDisplayNestedRequest() {
    global $db_conn;
    $result = executePlainSQL("SELECT eventType, avg(dangerLevel) FROM ExtremeWeatherEvent e GROUP BY eventType HAVING AVG(dangerLevel) <= all (SELECT AVG(e.dangerLevel) FROM ExtremeWeatherEvent e GROUP BY e.eventType)");
    printResultType($result);
}

function printResultAll($result) {
	echo "<br>Found and selected columns:<br>";
    echo "<table>";
    echo "<tr><th>EventID</th><th>EventType</th><th>DangerLevel</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";
}

function handleDisplayFindRequest() {
    global $db_conn;
    $type = $_GET['findEventType'];
    $colID = $_GET['includeEventID'];
    $colType = $_GET['includeEventType'];
    $colLevel = $_GET['includeDangerLevel'];

    if ("" . $colID . "" == 1 && "" . $colType . "" == 1 && "" . $colLevel . "" == 1) {
        $result1 = executePlainSQL("SELECT * FROM ExtremeWeatherEvent WHERE eventType = '" . $type . "'");
        printResult($result1);
    }

    if ("" . $colID . "" == 1 && "" . $colType . "" == 0 && "" . $colLevel . "" == 1) {
        $result2 = executePlainSQL("SELECT e.eventID, e.dangerLevel FROM ExtremeWeatherEvent e WHERE e.eventType = '" . $type . "'");
        echo "<br>Found and selected id and level:<br>";
    echo "<table>";
    echo "<tr><th>EventID</th><th>DangerLevel</th></tr>";

    while ($row = OCI_Fetch_Array($result2, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";
    }

    if ("" . $colID . "" == 1 && "" . $colType . "" == 1 && "" . $colLevel . "" == 0) {
        $result3 = executePlainSQL("SELECT e.eventID, e.eventType FROM ExtremeWeatherEvent e WHERE e.eventType = '" . $type . "'"); 
        echo "<br>Found and selected id and type:<br>";
    echo "<table>";
    echo "<tr><th>EventID</th><th>EventType</th></tr>";

    while ($row = OCI_Fetch_Array($result3, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";   
}

    if ("" . $colID . "" == 1 && "" . $colType . "" == 0 && "" . $colLevel . "" == 0) {
        $result4 = executePlainSQL("SELECT e.eventID FROM ExtremeWeatherEvent e WHERE e.eventType = '" . $type . "'");
        echo "<br>Found and selected id:<br>";
    echo "<table>";
    echo "<tr><th>EventID</th></tr>";

    while ($row = OCI_Fetch_Array($result4, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>"; 
}

    if ("" . $colID . "" == 0 && "" . $colType . "" == 1 && "" . $colLevel . "" == 1) {
        $result5 = executePlainSQL("SELECT e.eventType, e.dangerLevel FROM ExtremeWeatherEvent e WHERE e.eventType = '" . $type . "'");
        echo "<br>Retrieved data from table Extreme Weather Events:<br>";
    echo "<table>";
    echo "<tr><th>EventType</th><th>DangerLevel</th></tr>";

    while ($row = OCI_Fetch_Array($result5, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";    
}

    if ("" . $colID . "" == 0 && "" . $colType . "" == 1 && "" . $colLevel . "" == 0) {
        $result6 = executePlainSQL("SELECT e.eventType FROM ExtremeWeatherEvent e WHERE e.eventType = '" . $type . "'");
echo "<br>Found and selected type:<br>";
    echo "<table>";
    echo "<tr><th>EventType</th></tr>";

    while ($row = OCI_Fetch_Array($result6, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td></tr>"; //or just use "echo $row[0]"
    }

    echo "</table>";    
}

    if ("" . $colID . "" == 0 && "" . $colType . "" == 0 && "" . $colLevel . "" == 1) {
        $result7 = executePlainSQL("SELECT e.dangerLevel FROM ExtremeWeatherEvent e WHERE e.eventType = '" . $type . "'");
echo "<br>Found and selected level:<br>";
    echo "<table>";
    echo "<tr><th>DangerLevel</th></tr>";

    while ($row = OCI_Fetch_Array($result7, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td><td>"; //or just use "echo $row[0]"
    }

    echo "</table>";    
}

    if ("" . $colID . "" == 0 && "" . $colType . "" == 0 && "" . $colLevel . "" == 0) {
        $result8 = "";
        printResult($result8);
    }
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
        } else if (array_key_exists('displayFindTuples', $_GET)) {
            handleDisplayFindRequest();
        } else if (array_key_exists('nested', $_GET)) {
            handleDisplayNestedRequest();
        } else if (array_key_exists('having', $_GET)) {
            handleDisplayHavingRequest();
        }

        disconnectFromDB();
    }
}

if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
    handlePOSTRequest();
} else if (isset($_GET['countTupleRequest']) || isset($_GET['displayTupleRequest']) || isset($_GET['groupBy']) || isset($_GET['findTupleRequest'])) {
    handleGETRequest();
}
?>
</body>
</html>
