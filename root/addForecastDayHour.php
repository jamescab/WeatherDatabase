<html>
<head>
    <title>Add Forecast Day/Hour</title>
</head>

<body>
<form action = 'weather.php'>
    <input type = "submit" value = "BACK" />
</form>

<h1>Add Forecast Day Hour</h1>
<form method="POST" action="addForecastDayHour.php">
    <input type="hidden" id="insertQueryRequestDay" name="insertQueryRequestDay">

    <h2> Daily: </h2>
    Forecast ID: <input type="number" name="addIDDay"> <br /> <br />
    Day: <input type="date" name="addDate"> <br /> <br />
    High Temp: <input type="number" name="addHigh"> <br /> <br />
    Low Temp: <input type="number" name="addLow"> <br /> <br />
    Weather: <input type="text" name="addWeather"> <br /> <br />
    <input type = "submit" value = "Add Daily Forecast" name = "insertSubmit"></p>
</form>
<form method="GET" action="addForecastDayHour.php">
    <input type="hidden" id="displayTupleRequestDay" name="displayTupleRequestDay">
    <input type="submit" value="Display Day" name="displayTuplesDay"></p>
</form>
<form method="GET" action="addForecastDayHour.php">
    <input type="hidden" id="resetTablesRequestDay" name="resetTablesRequestDay">
    <input type="submit" value="Reset" name="reset"></p>
</form>

<form method="POST" action="addForecastDayHour.php">
    <input type="hidden" id="insertQueryRequestHour" name="insertQueryRequestHour">
    <h2> Hourly: </h2>
    Forecast ID: <input type="number" name="addIDHour"> <br /> <br />
    Day: <input type="date" name="addDateHour"> <br /> <br />
    Time: <input type="time" name="addTimeHour"> <br /> <br />
    Temp: <input type="number" name="addTemp"> <br /> <br />
    Weather: <input type="text" name="addWeatherHour"> <br /> <br />

    <input type = "submit" value = "Add Hourly Forecast" name = "insertSubmit"></p>
</form>
<form method="GET" action="addForecastDayHour.php">
    <input type="hidden" id="displayTupleRequestHour" name="displayTupleRequestHour">
    <input type="submit" value="Display Hour" name="displayTuplesHour"></p>
</form>
<form method="GET" action="addForecastDayHour.php">
    <input type="hidden" id="resetTablesRequestHour" name="resetTablesRequestHour">
    <input type="submit" value="Reset" name="reset"></p>
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

function printResultDay($result) { //prints results from a select statement
    echo "<br>Retrieved data from table ForecastDay:<br>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Day</th><th>HighTemp</th><th>LowTemp</th><th>Weather</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td></tr>";
    }

    echo "</table>";
}

function printResultHour($result) { //prints results from a select statement
    echo "<br>Retrieved data from table ForecastHour:<br>";
    echo "<table>";
    echo "<tr><th>ID</th><th>Day</th><th>Time</th><th>Temp</th><th>Weather</th></tr>";

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<tr><td>" . $row[0] . "</td><td>" . $row[1] . "</td><td>" . $row[2] . "</td><td>" . $row[3] . "</td><td>" . $row[4] . "</td></tr>";
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

function handleResetRequestDay() {
    global $db_conn;
    // Drop old table
    executePlainSQL("DROP TABLE ForecastDay");

    // Create new table
    echo "<br> creating new table <br>";
    executePlainSQL("CREATE TABLE ForecastDay (addIDDay int PRIMARY KEY, addDate date PRIMARY KEY, addHigh int, addLow int, addWeather string)");
    OCICommit($db_conn);
}

function handleResetRequestHour() {
    global $db_conn;
    // Drop old table
    executePlainSQL("DROP TABLE ForecastHour");

    // Create new table
    echo "<br> creating new table <br>";
    executePlainSQL("CREATE TABLE ForecastHour (addIDHour int PRIMARY KEY, addDateHour date PRIMARY KEY, addTimeHour time PRIMARY KEY, addTemp int, addWeatherHour char(20))");
    OCICommit($db_conn);
}

function handleInsertRequestDay() {
    global $db_conn;

    $tuple = array (
            ":bind1" => $_POST['addIDDay'],
        ":bind2" => $_POST['addDate'],
        ":bind3" => $_POST['addHigh'],
        ":bind4" => $_POST['addLow'],
        ":bind5" => $_POST['addWeather']
    );

    $alltuples = array (
        $tuple
    );

    executeBoundSQL("insert into ForecastDay values (:bind1, :bind2, :bind3, :bind4, :bind5)", $alltuples);
    OCICommit($db_conn);
}

function handleInsertRequestHour() {
    global $db_conn;

    $tuple = array (
        ":bind1" => $_POST['addIDHour'],
        ":bind2" => $_POST['addDateHour'],
        ":bind3" => $_POST['addTimeHour'],
        ":bind4" => $_POST['addTemp'],
        ":bind5" => $_POST['addWeatherHour']
    );

    $alltuples = array (
        $tuple
    );

    executeBoundSQL("insert into ForecastHour values (:bind1, :bind2, :bind3, :bind4, :bind5)", $alltuples);
    OCICommit($db_conn);
}

function handleDisplayRequestDay() {
    global $db_conn;
    $result = executePlainSQL("SELECT * FROM ForecastDay");
    printResultDay($result);
}

function handleDisplayRequestHour() {
    global $db_conn;
    $result = executePlainSQL("SELECT * FROM ForecastHour");
    printResultHour($result);
}

function handleCountRequest() {
    global $db_conn;

    $result = executePlainSQL("SELECT Count(*) FROM ForecastDay");

    if (($row = oci_fetch_row($result)) != false) {
        echo "<br> The number of tuples in ForecastDay: " . $row[0] . "<br>";
    }
}

// HANDLE ALL POST ROUTES
// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('resetTablesRequestDay', $_POST)) {
            handleResetRequestDay();
        } else if (array_key_exists('resetTablesRequestHour', $_POST)){
            handleResetRequestHour();
        }else if (array_key_exists('updateQueryRequest', $_POST)) {
            handleUpdateRequest();
        } else if (array_key_exists('insertQueryRequestDay', $_POST)) {
            handleInsertRequestDay();
        } else if (array_key_exists('insertQueryRequestHour', $_POST)) {
            handleInsertRequestHour();
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
        } else if (array_key_exists('displayTuplesDay', $_GET)) {
            handleDisplayRequestDay();
        } else if (array_key_exists('displayTuplesHour', $_GET)) {
            handleDisplayRequestHour();
        }

        disconnectFromDB();
    }
}

if (isset($_POST['reset']) || isset($_POST['updateSubmit']) || isset($_POST['insertSubmit'])) {
    handlePOSTRequest();
} else if (isset($_GET['countTupleRequest']) || isset($_GET['displayTupleRequestDay']) || isset($_GET['displayTupleRequestHour'])) {
    handleGETRequest();
}
?>
</body>
</html>
