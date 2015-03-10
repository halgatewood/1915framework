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
