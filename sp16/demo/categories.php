<?php
#categories.php

# '../' works for a sub-folder.  use './' for the root  
require '../inc_0700/config_inc.php'; #provides configuration, pathing, error handling, db credentials 
 
# SQL statement
$sql = "select Category, CategoryID, Description from Categories";

#Fills <title> tag. If left empty will default to $PageTitle in config_inc.php  
$config->titleTag = 'Newsfeed RSS App';

#Fills <meta> tags.  Currently we're adding to the existing meta tags in config_inc.php
$config->metaDescription = 'Seattle Central\'s ITC250 News Feed Links are made with pure PHP! ' . $config->metaDescription;
$config->metaKeywords = 'Links,PHP,Fun,Regular Expressions,'. $config->metaKeywords;


# END CONFIG AREA ---------------------------------------------------------- 

get_header(); #defaults to theme header or header_inc.php
?>
<h3 align="center">Categories</h3>

<p><b><h4 align="center">Welcome to the Newsfeed App</h4></b></p>
<p align="center">Below are the categories which link to subcategories and return current news stories</p>

 
<?php

# connection comes first in mysqli (improved) function
$result = mysqli_query(IDB::conn(),$sql) or die(trigger_error(mysqli_error(IDB::conn()), E_USER_ERROR));

if(mysqli_num_rows($result) > 0)
{#records exist - process
    
	while($row = mysqli_fetch_assoc($result))
	{# process each row

             if($row['CategoryID'] == 1){
            echo '<div align="center"><a href="' . VIRTUAL_PATH . 'demo/science_news.php?id=' . (int)$row['CategoryID'] . '">' . dbOut($row['Category']) . '</a></div>'; 
        }
        
        elseif($row['CategoryID'] == 2){
            echo '<div align="center"><a href="' . VIRTUAL_PATH . 'demo/world_news.php?id=' . (int)$row['CategoryID'] . '">' . dbOut($row['Category']) . '</a></div>'; 
        }
        
        else{
            echo '<div align="center"><a href="' . VIRTUAL_PATH . 'demo/health_news.php?id=' . (int)$row['CategoryID'] . '">' . dbOut($row['Category']) . '</a></div>'; 
        }
       
	} 
    
  
}else{#no records
    echo "<div align=center>What! No links?  There must be a mistake!!</div>";	
}
@mysqli_free_result($result);
?>
<br/><br/><br/><br/>
<p><a href="https://github.com/russ1111/P3_Newsfeed">github</a></p>
<p><a href="https://docs.google.com/document/d/1nch2fkraNEpoOOFRu7AKgOh_WzZoTNqKgMIU7STsbbQ/edit?pref=2&pli=1">worklog</a></p>

<?
get_footer(); #defaults to theme footer or footer_inc.php
?>
