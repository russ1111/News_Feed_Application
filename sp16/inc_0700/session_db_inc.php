<?php 
/**
 * session_db_inc.php uses a database to store session info, and override default file/cookie based session handler
 * 
 * Example based on work of Larry Ullmann, author of "PHP5 Advanced", Script 3.1 - db_sessions.inc.php
 *
 * Since sessions are by default dependent on cookies, when a user shuts them off PHP must append
 * the session ID to the querystring (a security risk). Storing session data in a DB remedies this.
 *
 * Also, if you are in a multi-server environment (server farm) you'll be required to use a DB for session storage, 
 * as by default each web server stores it's own session data and return trips could access a different server.
 *
 * Version 1.21 fixes idbIn() wrapper function in session_eliminate() to clean data via mysqli_ connection
 *
 * Version 1.2 updates to mysqli connections for all
 *
 * Below is the DB table to store session data:
 *
 * <code>
 *	CREATE TABLE nm_sessions ( 
 *	SessionID CHAR(32) NOT NULL, 
 *	SessionData TEXT, 
 *	LastAccessed TIMESTAMP NOT NULL, 
 *	PRIMARY KEY (SessionID) 
 *	);
 * </code>
 * 
 * note the prefix "nm_" which should instead match your table prefix in config_inc.php
 *
 * To deploy, place a reference to this include file in config_inc.php.  To go back to 
 * file-system based sessions, merely comment out the include file reference.
 *
 * This will clear all current sessions (logout logged in folks) so do this at a time of low use.
 *
 * SECURITY WARNING: Inside session_open() access level is set to 'admin'.  If you have enabled multiple MySQL users, I recommend changing 
 * access level to 'update' instead!
 *
 * @package nmSession
 * @author Bill Newman (via Larry Ullman) <williamnewman@gmail.com>
 * @version 1.21 2010/07/26
 * @link http://www.newmanix.com/ 
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License ("OSL") v. 3.0
 * @see session_db_test.php 
 * @see config_inc.php
 * @todo none
 */
ini_set('session.use_trans_sid', false); # Turns off querystring session handling - off by default by PHP 4.3.4
$iConn = NULL;  #var is created outside all functions to be globally available

#overwrite default session handler
session_set_save_handler('session_open', 'session_close', 'session_read', 'session_write', 'session_eliminate', 'session_clean');
startSession(); #DB requires session started

/** 
 * session_open() is called when session_start() is called in a script. 
 * 
 * Opens a connection to the DB and prepares to manipulate session data.
 *
 * @global object database connection open to manipulate session data
 *
 * @return boolean
 * @todo none
*/
function session_open() {
	global $iConn; # Global connection to DB
	if(empty($iConn) || !is_resource($iConn))
	{# mysqli connection to DB:
		$iConn = IDB::conn();
	}
	return true;
}# End session_open()

 
/** 
 * session_close() closes the session DB connection
 *
 * @global object database connection open to manipulate session data
 *
 * @return boolean
 * @todo none
*/
function session_close() {
	global $iConn;# Global connection to DB
	if(is_resource($iConn)){return mysqli_close($iConn);}else{return true;}
}# End session_close()

/** 
 * session_read() retrieves session data from DB
 *
 * @global object database connection open to retrieve session data
 * @param string $sid ID string to identify current session
 * @return array Session Data returned as an array, or a string
 * @todo none
*/
function session_read($sid) {
	global $iConn; # Global connection to DB
	if(!is_resource($iConn)){$iConn = IDB::conn();}

 	# Identify session data from $sid
 	$sql = sprintf('SELECT SessionData FROM ' . PREFIX . 'sessions WHERE PHPSessID="%s"', idbIn($sid,$iConn)); 
	$result = mysqli_query($iConn,$sql) or die(trigger_error(mysqli_error($iConn) . " sql: " . $sql, E_USER_ERROR));
	if (mysqli_num_rows($result) > 0)
	{# Access data as array via list(), and return
		list($sessionData) = mysqli_fetch_array($result, MYSQLI_NUM);
		return $sessionData;
	} else { # Return empty string;
		return '';
	}
} # End session_read()

/** 
 * session_write() updates or inserts session data
 *
 * @global object database connection open to write session data
 * @param string $sid ID string to identify current session
 * @param array $data Array of session data
 * @return integer Number of rows updated/inserted - should be one
 * @todo none
*/
function session_write($sid, $data) {
	global $iConn;# Global connection to DB
	if(!is_resource($iConn)){$iConn = IDB::conn();}
	# Update/insert session data
	$sql = sprintf('REPLACE INTO ' . PREFIX . 'sessions (PHPSessID, SessionData,LastAccessed) VALUES ("%s", "%s",NOW())', idbIn($sid,$iConn), idbIn($data,$iConn)); 
 	mysqli_query($iConn,$sql);
	
	# Return number of rows updated/inserted
	return mysqli_affected_rows($iConn);

} # End session_write()

/** 
 * session_eliminate() overrides default session_destroy() function, deletes session DB data
 *
 * @global object database connection open to delete session data
 * @param string $sid ID string to identify current session
 * @return integer Number of rows deleted - should be one
 * @todo none
*/
function session_eliminate($sid) {
	global $iConn; # Global connection to DB
	if(!is_resource($iConn)){$iConn = IDB::conn();}
	
	# Delete SQL
 	$sql = sprintf('DELETE FROM ' . PREFIX . 'sessions WHERE PHPSessID="%s"', idbIn($sid,$iConn)); 
	mysqli_query($iConn,$sql) or die(trigger_error(mysqli_error($iConn) . " sql: " . $sql, E_USER_ERROR));
	
	# Setting a session to an empty array safely clears all data
	$_SESSION = array();

	return mysqli_affected_rows($iConn);

} # End session_eliminate()

// Define the clean_session() function:
// This function takes one argument: a value in seconds.
/** 
 * session_clean() housekeeping deletes old session DB data
 *
 * @global object database connection open to delete session data
 * @param integer $expire ID number of seconds to keep session data
 * @return integer Number of rows deleted - should be one
 * @todo none
*/
function session_clean($expire) {
	global $iConn; # Global connection to DB
	if(!is_resource($iConn)){$iConn = IDB::conn();}
	# SQL to delete old sessions
 	$sql = sprintf('DELETE FROM ' . PREFIX . 'sessions WHERE DATE_ADD(LastAccessed, INTERVAL %d SECOND) < NOW()', (int) $expire); 
	mysqli_query($iConn,$sql) or die(trigger_error(mysqli_error($iConn) . " sql: " . $sql, E_USER_ERROR));

	return mysqli_affected_rows($iConn);

} # End session_clean()
?>