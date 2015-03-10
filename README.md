# 1915framework
Super minimal PHP framework combining ideas from Symfony 1 + WordPress

# Setup
- Load on your webserver and point your virtual host to the '/web' folder.
- In 'lib/_vars.php' decide if you want to use a databse with $use_db.
  - in 'lib/database.php' add your databse settings
- The initial module is home with an action of index

# Varables in 'lib/_vars'
- '$use_db' = (false) if you want to use a database with your site. This will load in the mysql class
- '$index_fallback' = (true) if you want the default index action to run if none are found (changable per module in modules.php)
- '$home_module' = ('home') 
- '$login_module' = ('login') allows you to specify where to send people if they are not logged in and hit a secure module
- '$fof_text' = (string) 404 Text if module or action is not found

# URL Structure
- The main website urls look like this: website.com/$module/$action/$id/$uri[4]/$url[5]/etc.....
- $module will dive in the modules/$module folder and then look for an $action file with the naming convention a.$action.php
- The view used will look for v.$action.php
- I have shortened the variables from the original symfony structure because after you get confortable with the system, the filenames are less important. Lots of systems overcomplicate to help new users which just adds bloat IMO.

# Modules in lib/modules
- The main $template is naturally 'main'. You can specify different templates on a per module basis. Like:
-   $templates['home']    = "home";
-   $templates['login']   = "login";
- Ajax calls automaticall use the 'blank' template
- The $secure_modules array contains all the modules that need to be secured.
- The $index_fallbacks variable allows you to set fallback options on a per module basis
-   $index_fallbacks['home'] = false;

# Global
- There is a global.php file that gets called before we dive into action files. This is the place to add custom needs with if or switch statements. You can create whatever kind of convention you need.

# SCSS
- This base was designed with SCSS and CodeKit in mind. There is a scss folder in the root with an _hg.scss and main.scss.
- In my sites I import the _hg into the main and send them to /web/css/main.css with a map. You can delete this folder and just use main.css or remove it all together. 1915framework doesn't care what you do. It just wants to help where it can.

# Debug
- I use the dBug class that is made based on the ColdFusion debugger. It's fantastic. You can call it like this 'dbug($obj)' and it will display nicely what you have there.
- As an added bonus in the dbug function it checks to make sure $is_local is true so that debugging outputs won't display on your live site.
-   $is_local is set in lib/application and is defaulted to any HTTP HOST with .dev
