<?php

# START ERROR HANDLING (show or hide page errors, turn on/off error logging)---------------------------------------------
# We can un-comment the line below to either see default errors (1) or shut off visual errors completely (0). 
//ini_set('error_reporting', E_ALL | E_STRICT);  # E_ALL | E_STRICT = currently tracking all errors & warnings
define('SHOW_ALL_ERRORS', true); # TRUE = SHOW ALL SITE ERRORS - if FALSE must be logged in as ADMIN to view errors
define('LOG_ALL_ERRORS', true); # TRUE = TRACK ALL ERRORS IN ERROR LOG FILE (UPDATED 7/14 FOR ZEPHIR!)
$default_error_reporting = 2048; #overwritten by $error_reporting on page basis, 2048 = strict, 2047 = not quite strict,
$default_error_handler = 'custom'; #can be set to 'custom', 'php' or 'none' - can be overwritten on individual page basis
$config = new stdClass; #standard class allows dynamic property assignment - used to store data across themes
if(!isset($error_reporting)){$config->error_reporting = $default_error_reporting;}else{$config->error_reporting = $error_reporting;}
if(!isset($error_handler)){$error_handler = $default_error_handler;}
loadErrorHandler($error_handler); 
# END ERROR HANDLING (show or hide page errors, turn on/off error logging)-----------------------------------------------  
 
# START SETTINGS (php.ini overrides & other enviroment settings)---------------------------------------------------------
ob_start();  #buffers our page to be prevent header errors. Call before INC files or ANY html!
date_default_timezone_set('America/Los_Angeles'); #sets default date/timezone for this website
//ini_set('session.save_path','/home/classes/horsey01/sessions'); #optional folder set to 0700 outside webroot to store session data
//ini_set('session.cookie_domain', '.seattlecentral.edu'); # "dot" (period) then domain name - apply session cookies to subdomains, incl www!
header("Cache-Control: no-cache");header("Expires: -1");#Helps stop browser & proxy caching
# END SETTINGS (php.ini overrides & other enviroment settings)------------------------------------------------------------ 

# START CONSTANTS & PATHS (universal file paths & values)-----------------------------------------------------------------
/* automatic path settings - use the following 4 path settings for placing all code in one application folder */ 
define('VIRTUAL_PATH', 'http://russcode.com/itc250/P3/sp16/'); # Virtual (web) 'root' of application for images, JS & CSS files
define('PHYSICAL_PATH', '/home/russch9/russcode.com/itc250/P3/sp16/'); # Physical (PHP) 'root' of application for file & upload reference
define('INCLUDE_PATH', PHYSICAL_PATH . 'inc_0700/'); # Path to PHP include files - INSIDE APPLICATION ROOT
//define('INCLUDE_PATH', '/home/classes/horsey01/inc_cotlets/'); #Path to PHP include files - OUTSIDE WEB ROOT
define('LOG_PATH', INCLUDE_PATH . 'log/'); # Log files are stored in the PHP include folder
define('ADMIN_PATH', VIRTUAL_PATH . 'admin/'); # Admin files are in subfolder
define('SUPPORT_EMAIL', 'rschne07@seattlecentral.edu'); # Email of site support
define('PREFIX', 'sp16_'); #Prefix to add uniqueness to your DB table names.  Limits hackability, naming collisions
define('THIS_PAGE', basename($_SERVER['PHP_SELF'])); # Current page name, stripped of folder info - (saves resources)
# END CONSTANTS & PATHS (universal file paths & values)--------------------------------------------------------------------

# START INCLUDES (reference include files)-------------------------------------------------------------------
include INCLUDE_PATH . 'credentials_inc.php'; # Stores DB credentials - part of nmCommon package
include INCLUDE_PATH . 'common_inc.php'; # Provides common utility functions - part of nmCommon package
include INCLUDE_PATH . 'custom_inc.php'; # Provides spot for custom utility functions - part of nmCommon package
include INCLUDE_PATH . 'MyAutoLoader.php'; #Allows multiple versions of AutoLoaded classes
include INCLUDE_PATH . 'session_db_inc.php'; #Session database handling include file
# END INCLUDES (reference include files)---------------------------------------------------------------------

# CONTENT CONFIGURATION AREA (theme, content areas & nav arrays for header/footer )-----------------------------------------
$config->theme = 'Bootswatch'; #default theme (header/footer combo) see 'Themes' folder for others and info
$config->style = 'cerulean.css'; #currently only Bootswatch Theme uses style to switch look & feel
$config->slogan = 'Cotlets are Awesome!';
$config->metaDescription = 'Welcome to the Cotlets website.  We split off from Applets.  But We are better.';
$config->metaKeywords = 'Cotlets,Apricots,Turkish Delight,database,mysql,php';
$config->metaRobots = 'no index, no follow';
$config->banner = 'My Cotlet Banner'; #goes inside header - can be overwritten
$config->copyright = 'Cotlets, Not Applets, &copy; 2014 - ' . date('Y'); #goes inside footer - can be overwritten

$config->sidebar1 = '
<h3 align="center">Sidebar 1</h3>
';
$config->sidebar2 = '<h3 align="center">Sidebar 2</h3>'; #sidebars can be overwritten (or added to) in individual pages
if(startSession() && isset($_SESSION['AdminID']))
{#add admin logged in info to sidebar
	$config->sidebar2 .= '<p align="center">' . $_SESSION['Privilege'] . ' <b>' . $_SESSION['FirstName'] . '</b> is logged in.</p>';
	$config->sidebar2 .= '<p align="center"><a href="' . $config->adminDashboard . '">ADMIN</a></p>';
	$config->sidebar2 .= '<p align="center"><a href="' . $config->adminLogout . '">LOGOUT</a></p>';
}	
$config->sidebar2 .= '
<p>Here is our sidebar area which is inside a header or footer include file. You can change it in the main config file or 
change it on a page by page basis by altering config settings inside individual pages.</p> 
';
#add Admin link to nav1 if not Bootswatch theme
if(startSession() && isset($_SESSION['AdminID']) && $config->theme != 'Bootswatch'){$nav1[$config->adminDashboard] = "ADMIN~Go to Administrative Page";}#admin page added to link only if logged in
#nav1 is the main navigation - tilde separator below splits text of link from title attribute
$nav1['index.php'] = "Home~A model for building largely static web pages";
$nav1['surveys/'] = "Surveys~The entrance to our Survey App";
$nav1['demo/demo_shared.php'] = "MySQLi Shared~A demo page for building mysqli shared connection based applications.";
$nav1['demo/demo_pdo.php'] = "PDO~A demo page for building PDO connection based applications.";
$nav1['demo/demo_contact.php'] = "Contact~A demo for building postback forms";
$config->nav1 = $nav1;  #add to global config object - now available in all header/footers
$config->tableEditor = ADMIN_PATH . 'nmEdit.php'; # Table Editor part of nmEdit package
# CONTENT CONFIGURATION AREA (theme, content areas & nav arrays for header/footer )-----------------------------------------

# GENERAL CONFIG AREA ENDS HERE ############################################################################################

# DEFAULT EMPTY DATA STARTS HERE -------------------------------------------------------------------------------------------
$config->loadhead = ''; #can be used to add js & css to bottom of head tag
if(!isset($config->benchmarking)){$config->benchmarking = '';}
# DEFAULT EMPTY DATA ENDS HERE ---------------------------------------------------------------------------------------------

# START ERROR HANDLING FUNCTIONS (error handling/logging functions)--------------------------------------------------------- 

function loadErrorHandler($handler)
{
	$handler = strtolower($handler);
	global $config;
	switch($handler)
	{
		case 'custom':
			set_error_handler ('myErrorHandler'); # Override the default PHP error handler with our own.
			break;
		case 'php';
			ini_set('display_errors', 1); # 1 = display PHP errors
			error_reporting($config->error_reporting);
			if(LOG_ALL_ERRORS)
			{
				ini_set('log_errors', 1); # 1 turns on error logging, 0 shuts it off
				ini_set('error_log', LOG_PATH . 'error_log'); #places PHP errors into a folder at this location	
			}
			break;		
		default:
			ini_set('display_errors', 0); # 0 turns off PHP error reporting entirely
			if(LOG_ALL_ERRORS)
			{
				ini_set('log_errors', 1); # 1 turns on error logging, 0 shuts it off
				ini_set('error_log', LOG_PATH . 'error_log'); #places PHP errors into a folder at this location	
			}			
	}	
}# End loadErrorHandler()



function myErrorHandler ($e_number, $e_message, $e_file, $e_line, $e_vars)
{
	#comment out one of the two $errFile variables - one for a single file, one for a new file every day
	$errFile =  'error_log_' . date('m-d-Y') . '.txt'; #new error file created every day
	#$errFile =  'error_log'; # same single error log file as PHP will write errors
	static $counter = 0; # Will identify if myError() was called more than once
	$counter++;
	if(LOG_ALL_ERRORS)
	{# Copy all error info to error log file
		try	{
	    	$errInfo = "[" . date('M-d-Y G:i:s') . "] $e_message in $e_file on line $e_line" . "\n";
		    fileWrite(LOG_PATH . $errFile,'a+',$errInfo);
			
			if(substr(sprintf('%o', fileperms(LOG_PATH . $errFile)), -4)!= '0700')
			{//check file permission - if NOT set to 0700, fix it!
				chmod(LOG_PATH . $errFile,0700);
			}
		} catch (Exception $e) {
		    //echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
	}
	if (SHOW_ALL_ERRORS || isset($_SESSION['AdminID']))
	{# Display generic error message, with support email from config file
		printDeveloperError($e_file,$e_line,$e_message,$counter); 
	}else{# Show errors directly on page.  (troubleshooting purposes only!)
		if($counter < 2) { printUserError($e_file,$e_line); } #only print one error message to user
	}
}# End myErrorHandler()


function createErrorCode($myFile,$myLine)
{
	$mySlash = strrpos($myFile,"/"); //find position of last slash in path
	$myFile = substr($myFile,$mySlash + 1);  //strip off all but file name
	$myFile = substr($myFile, 0, strripos($myFile, '.'));//remove extension
	$myFile = strtoupper($myFile); //change to upper case
	$vowels = array("A", "E", "I", "O", "U", "Y");  //array of vowels to remove
	$myFile = str_replace($vowels, "", $myFile); //remove vowels
	$myFile = str_replace("_", "x", $myFile); //replace underscore with "x"
	return $myFile . "x" . $myLine;  //CNFGNCx50
}# End createErrorCode()


function printUserError($myFile,$myLine)
{
	$errorCode = createErrorCode($myFile,$myLine); //Create error code out of file name & line number
	echo '<h2 align="center">Our page has encountered an error!</h2>';
	echo '<table align="center" width="50%" style="border:#F00 1px solid;"><tr><td align="center">';
	echo 'Please try again, or email support at <b>' . SUPPORT_EMAIL . '</b>,<br /> and let us know you are receiving ';
	echo 'the following Error Code: <b>' . $errorCode . '</b><br />';
	echo 'This will help us identify the problem, and fix it as quickly as possible.<br />';
	echo 'Thank you for your assistance and understanding!<br />';
	echo 'Sincerely,<br />Support Staff<br />';
	echo '<a href="index.php">Exit</a></td></tr></table>';
	get_footer(); #add footer info!
	die(); #one error is enough!
}# End printUserError()


function printDeveloperError($myFile,$myLine,$errorMsg,$counter)
{
	# Build the error message.
	echo '<div class="error">';  # No body or closing HTML allows multiple errors to show
	echo 'Error in file: <b>\'' . $myFile . '\'</b> on line: <font color="blue"><b>' . $myLine . '</b></font> '; 
	echo 'Error message: <font color="red"><b>' . $errorMsg . '</b></font><br /><br />';
	
	 #only print one instance of backtrace of debug data:
	if($counter < 2) { echo 'BackTrace: <font color="purple"><pre>' . print_r(debug_backtrace(),1) . '</pre></font><br /><br />'; }
	echo '</div><br />'; 
	
}# End printDeveloperError()


function fileWrite($fileName,$myMode,$myStr)
{
$isOpen = fopen($fileName,$myMode);
  if($isOpen)
  {
        fwrite($isOpen,$myStr);
        fclose($isOpen);
        return TRUE;
  }else{
        return FALSE;
  }
}#End fileWrite()

# END ERROR HANDLING FUNCTIONS (error handling/logging functions)---------------------------------------------------------- 
#no closing PHP tag, on purpose