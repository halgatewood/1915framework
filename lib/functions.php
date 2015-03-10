<?php 

// dbug
// call like dbug($variable) instead of new dBug($variable)
function dbug($str, $die = false) 
{ 
	global $is_local;
	if( !$is_local ) return false;
	new dBug($str); 
	if($die) die;
}


// escapeSQL
// returns an object, clean up a post for database input: $post = escapeSQL($_POST);
function escape_sql($datas = array())
{
	class rtn {};
	foreach($datas as $data => $val)
	{
		$rtn->$data = addslashes(trim($val));
	}
	return $rtn;
}


// redirect
// simple redirect and die script
function redirect($location)
{
	header("Location: $location");
	die;
}


// is_ajax
// check if request is coming through an ajax call
function is_ajax() 
{
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']=="XMLHttpRequest");
}


// is_user_logged_in
function is_user_logged_in()
{
	// DO WHATEVER CHECKS YOU NEED HERE
	return false;
}


// get_module
function get_module()
{
	global $uri;
	$module = "home";
	if( isset($uri[1]) AND $uri[1] != "") { $module = $uri[1];}
	return $module;
}


// get_action
function get_action()
{
	global $uri;
	$action = "index";
	if( isset($uri[2]) AND $uri[2] != "" AND $uri[2] != "page" ) $action = $uri[2];
	return $action;
}


// get_primary_key
function get_primary_key()
{
	global $uri;
	return (int) (isset($uri[3]) AND is_numeric($uri[3])) ? $uri[3] : null;
}


// get_page
function get_page()
{
	global $uri;
	$page = 1;
	if( isset($_GET['page']) )
	{ 
		$page = (int) $_GET['page']; 
	}
	else
	{
		// LOOP uri LOOKING FOR /page/4
		for( $p=1; $p < count($uri); $p++ ) { if( isset( $uri[$p] ) AND $uri[$p] == "page") { $page = (int) $uri[($p+1)]; } }
	}
	return $page;
}


// the_body_class
function the_body_class()
{
	global $body_class;
	$classes = (array) $body_class;
	if( $classes )
	{
		echo " class=\"" . implode(' ', $classes) . "\"";
	}
}

