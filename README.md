Pseudify
=========

*PLEASE NOTE: currently only works stably on Chrome*

###What is it?

Pseudify is a service that allows you to write code in pseudo code (with a specific syntax available on [pseudify] [1] or below) 

  - Error Reporting
  - Basic Syntax Support

To try it out visit [pseudify] [1].

###How to install it yourself


Copy and paste all of the files from ```/pseudify``` into ```/var/www```. These are now visible from your servers IP address.

Create a file in `includes/connect.php` and make sure you have the following code in there.
  
	mysql_connect(host,username,password);

host: localhost
	
username: *created when installing mysql*
	
password: *created when installing mysql*

	mysql_query("CREATE DATABASE name");
	
name: *The name of the website you wish to run. Can be anything.*
	
Run the file in the browser, and it will create the database. If there are any problems, often mistyped username or password, the browser will display the problems.

Now remove the `mysql_query()` you created, and replace it with the following:

	mysql_select_db(name);

Where name is the name of the database you created.

Now, in the browser, visit `install.php`, this will install all of the mysql tables needed for the program to work. 

Now, delete `install.php` and you are good to go.

Syntax
-
*All spaces are crucial.*
####Basics
#####Strings
A string must be surrounded by *double* quotes.

    "This is a string"
    
Multiline strings may not work yet. Instead, please use \n or <br /> inside of the string.

##### Numbers
A number can be decimal, integer or scientific.

    4.2
    4E+7
    6
    
##### Output
To print a message to the screen, use the output command.

    output Value

#### Variables
Variables are declared as follows
    
    Variable["name"] = Value
    
Value can be either a number, string or variable.

#### Iteration
##### For Loop
For loops are used for iterating through a specified list or range. 

    For variable["x"] = Integer ; Variable["x"] < Integer ; Variable["x"] step then
        ... Statement
    endfor
    
There are a few options for this choice which are changable.

- Integer can be a variable
- < can be any of the following
    -   <
    -   >
    -   <=
    -   >=
- step can consist of:
    -   inc (same as += 1)
    -   dec (same as -= 1)
    -   += integer
    -   -= integer

#####While Loop
While loops are used for iterating through a statement whilst it meets a certain condition.
    
    While variable["x"] < Integer then
        ... statement
        ... Variable["x"] inc
    endwhile

####Selection
#####If statement
Selection statements are used for comparison, often between a variable and a value, or another variable.
    
    if variable["name] == Value then
        ... Statement
    endif
    
This is a comparison, you can also compare identical statements using `===`
Value can consist of number, variable or string.

#####Else
    
    if variable["name"] == Value then
        ... Statement
    else
        ... Statement
    endif

#####Elseif
    
    if variable["name"] == Value then
        ... statement
    elseif variable["name"] == value then
        ... statement
    else
        ... statement
    endif

####Thank you for looking at Pseudify.

*Please report any issues via [github][2].*

Feel free to contact me via:

- [Twitter][3] 
- [Google+][4]

Thank you.



  [1]: http://pseudify.com
  [2]: https://github.com/alexbowers/Pseudify/issues
  [3]: http://twitter.com/bowersbros
  [4]: http://gplus.to/bowersbros
  

    
