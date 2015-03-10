<?php
	
	
// SYSTEM VARS
$now 				= time();
$ip 				= $_SERVER['REMOTE_ADDR']; 
$request_uri 		= $_SERVER['REQUEST_URI'];
$uri  				= explode("/", $request_uri);
$is_local 			= strpos($_SERVER['HTTP_HOST'], ".dev") !== false ? true : false;
$body_class 		= array();


// LIB
require_once(dirname(__FILE__)."/_vars.php");
require_once(dirname(__FILE__)."/dbug.php");
require_once(dirname(__FILE__)."/modules.php");


// DATABASE
if( $use_db ) 
{
	require_once(dirname(__FILE__)."/database.php");
	require_once(dirname(__FILE__)."/mysql.php");
	if( isset($db_params) ) $db = new mysql( $db_params );
}


// FUNCTIONS
require_once(dirname(__FILE__)."/functions.php");


// MODULES & ACTIONS
$page 	= get_page();
$module = get_module();
$action = get_action();
$id 	= get_primary_key(); 


// CLASSES
$body_class[] = "module-$module";
$body_class[] = "action-$action";
if( $id ) $body_class[] = "id-$id";


// CHECK SECURE
if( in_array($module, (array) $secure_modules) AND !is_user_logged_in()) redirect("/" . $login_module);


// GLOBAL ACTIONS
require_once(dirname(__FILE__)."/global.php");


// MODULE INDEX FALLBACK
if( $index_fallbacks AND $index_fallbacks[$module] !== "") $index_fallback = $index_fallbacks[$module];


// ACTION
$has_action = false;
if(is_dir(dirname(__FILE__)."/../modules/$module"))
{
	if(file_exists(dirname(__FILE__)."/../modules/$module/a.$action.php"))
	{
		require_once(dirname(__FILE__)."/../modules/$module/a.$action.php");
		$has_action = true;
		
	}
	else if($index_fallback AND file_exists(dirname(__FILE__)."/../modules/$module/a.index.php"))
	{
		require_once(dirname(__FILE__)."/../modules/$module/a.index.php");
		$has_action = true;
	}
}


// VIEWS
$has_view = false;
$throw_404 = false;
if(is_dir(dirname(__FILE__)."/../modules/$module"))
{
	if(file_exists(dirname(__FILE__)."/../modules/$module/v.$action.php"))
	{
		ob_start();
		include(dirname(__FILE__)."/../modules/$module/v.$action.php");
		$has_view = true;
		$view = ob_get_contents();
		ob_end_clean();
	}
	else if($index_fallback AND file_exists(dirname(__FILE__)."/../modules/$module/v.index.php"))
	{
		ob_start();
		include(dirname(__FILE__)."/../modules/$module/v.index.php");
		$has_view = true;
		$view = ob_get_contents();
		ob_end_clean();
	}
	if(!$has_view) { $throw_404 = true; }	
}
else
{
	$throw_404 = true;
}


// 404
if( $throw_404 ) 
{
	if(!headers_sent()) { header("HTTP/1.0 404 Not Found"); }
	$view = $fof_text;
	$body_class[] = "error-404";
}


// LAYOUT
if( is_ajax() )
{
	$has_layout = false;
	require_once(dirname(__FILE__) . "/../templates/blank.php");
	$has_layout = true;
}
else
{
	$has_layout = false;
	if( isset($templates[$module]) ) $template = $templates[$module];
	if(file_exists(dirname(__FILE__) . "/../templates/" . $template . ".php"))
	{
		$body_class[] = "template-$template";
		require_once(dirname(__FILE__) . "/../templates/" . $template . ".php");
		$has_layout = true;
	}
}

// NO TEMPLATE
if( !$has_layout ) 
{
	if( $is_local ) { echo "Template '$template' not found"; }
	else { echo $fof_text; }
}
