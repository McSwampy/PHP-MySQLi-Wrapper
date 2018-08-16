# MySQLi-Wrapper
Created By McSwampy 2018

This is a class created as a wrapper for PHP MySQLi
Copy class.MySQLi.php to your required directory.
NOTE: Open class.MySQLi.php and set the server, username, password and default database before use to avoid unnecessary code
```
Functions:
	SQL::I()->query(string "SQL Statement")
	SQL::I()->update(array "Data", string "Table Name", array "Where", array "Like", string "Extra")
	SQL::I()->getRecord(array "Columns", string "Table Name", array "Where", array "Like", string "Extra")
	SQL::I()->switchDB(string "Database", [boolean "Once"])
	SQL::I()->insert(array "Data", string "Table Name")
	SQL::I()->changeUser(string "Username", string "Password")
	SQL::I()->refresh()
```
	
Next Update
	SQL::I()->ssl(key, cert, ca, caPath, cipher)
	
Usage:
	WITH SERVER DETAILS CHANGED IN CLASS FILE
	
	// Get results by pure SQL query string
	$sql = "SELECT * FROM `TableName` LIMIT 1000";
	$result = SQL::I()->query($sql);
	
	// Update data in a table
	$updateArray			= [
		'NAME'				=> 'The new user name',
		'PASSWORD'			=> md5('The new user password');
	];
	$whereArray				= [
		'USER_ID'			=> '32'
	];
	SQL::I()->update($updateArray, 'users_table', $whereArray);
	
	// Retrieve information from table
	$getArray				= [
		'NAME',
		'PASSWORD',
		'CREATION_DATE',
		'STATUS'
	];
	$whereArray				= [
		'USER_ID'			=> '32'
	];
	$data = SQL::I()->getRecord($getArray, 'users_table', $whereArray);
	
	// Switch between databases
	SQL::I()->switchDB('old_database');
	$oldUser = SQL::I()->update(['NAME'=>'New Name'], 'users_table', ['USER_ID'=>'32']);
	SQL::I()->switchFB('new_database');
	$newUser = SQL::I()->update(['NAME'=>'New Name'], 'users_table', ['USER_ID'=>'32']);
	// Or when you want to do one query in a new database and have the following queries back in the default database
	SQL::I()->switchDB('old_database', true);
	$oldUser = SQL::I()->update(['NAME'=>'New Name'], 'users_table', ['USER_ID'=>'32']);
	$newUser = SQL::I()->update(['NAME'=>'New Name'], 'users_table', ['USER_ID'=>'32']);
	
	// Inserting new information in your MySQL database
	$newArray				= [
		'NAME'				=> 'New user name',
		'PASSWORD'			=> md5('New User Password'),
		'CREATION_DATE'		=> date('Y-m-d H:m:i'),
		'STATUS'			=> 1,
		'USER_ID'			=> '32'
	];
	SQL::I()->insert($newArray, 'users_table');
	
	// Changing your user of the current MySQL connection
	SQL::I()->changeUser('root', 'password');
	
	IF SERVER INFORMATION IS NOT CHANGED IN THE CLASS FILE, YOU NEED TO SPECIFY THE SERVER DETAILS BEFORE ANY FUNCTION CALLS
	
	SQL::I()->server		= 'localhost';
	SQL::I()->username		= 'root';
	SQL::I()->password		= 'toor';
	SQL::I()->database		= 'mysql_database';
	OPTIONAL - default is 3306
	SQL::I()->port			= '3306';