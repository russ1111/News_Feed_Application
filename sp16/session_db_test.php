<?php 
/**
 * session_db_test.php is test page for database session storage solution, session_db_inc.php
 * 
 * session_db_inc.php uses a database to store session info, and override default file/cookie based session handler
 * 
 * Example based on work of Larry Ullmann, "PHP5 Advanced", Script 3.2 - sessions.php
 *
 * Since sessions are by default dependent on cookies, when a user shuts them off PHP must append
 * the session ID to the querystring (a security risk). Storing session data in a DB remedies this.
 *
 * Also, if you are in a multi-server environment (server farm) you'll be required to use a DB for session storage, 
 * as by default each web server stores it's own session data and return trips could access a different server.
 * 
 * Please view session_db_inc.php for deployment details
 *
 * @package nmSession
 * @author Bill Newman (via Larry Ullman) <williamnewman@gmail.com>
 * @version 1.0 2009/05/01
 * @link http://www.newmanix.com/ 
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License ("OSL") v. 3.0
 * @see session_db_inc.php 
 * @see config_inc.php
 * @see common_inc.php 
 * @see conn_inc.php 
 * @todo none
 */

require 'inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials 
startSession();
?>
<html>
<head>
	<title><?=smartTitle();?></title>
</head>
<body>
<h2 align="center"><?=smartTitle();?></h2>
<p>Test of DB based session include file, <b>session_db_inc.php</b>, based on the work of Larry Ullman, 'PHP5 Advanced', example 3.1</p>
<?php

# Store trivial data in the session, if none present
if (empty($_SESSION)) {
	#load session data - each is now a DB replace into
	$anArray = array('I\'m','text','in','an','array');
	$_SESSION['anArray'] = $anArray;
	$_SESSION['aFloat'] = 3615684.45;
	$_SESSION['aString'] = 'I\' Annette!  (I mean, a string.)';
	$anArray[] = 'Now the array is in the object!'; # Add to the array before entering it into our test object
	$anArray[] = 23;
	$anArray[] = 64;
	$anArray[] = 'Hike!';
	$_SESSION['myObject'] = new myClass($anArray);
	
	# Indicate success
	echo '<p>Session data successfully stored!</p>';
	
} else { // echo the already-stored data.
	echo '<p>Session data exists prior to page load:<pre>' . print_r($_SESSION, 1) . '</pre></p>';
}

# If querystring loaded, destroy session
if (isset($_GET['destroy'])) {
	$_SESSION = ""; #happens inside session_eliminate(), but not if handling via file/cookie
	session_destroy();
	echo '<p>Session destroyed.  Session data should now return an empty array.</p>';
} else {# Show link to allow destuction of session
	echo '<p align="center"><a href="' . basename($_SERVER['PHP_SELF']) . '?destroy=true">Destroy Session</a></p>';
}

// print out the session data:
echo '<p>Current Session Data:<pre>' . print_r($_SESSION, 1) . '</pre></p>';

# A trivial class to test session storage capability
class myClass{
 
	function __construct($myArray){
		$this->myArray = $myArray;
		$this->myString = "I'm a string in myClass";
	}
} // end of class
?>
</body>
</html>