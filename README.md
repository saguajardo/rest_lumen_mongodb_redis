Api Rest using Lumen, MongoDB and Redis
=======================================

This project consists of the generation of an API Rest with Lumen, using mongoDB as a database. The list of stored records is cached with Redis.

Table of contents
-----------------
* [Requirements](#requirements)
* [Installation](#installation)
* [Configuration](#configuration)
* [Examples](#examples)

Requirements
------------

This project was developed using php v7.2 in Windows 8, with xampp server.

***MongoDB PHP Driver***: Make sure you have the MongoDB PHP driver installed. You can find installation instructions at http://php.net/manual/en/mongodb.installation.php

You can find the mongoDB dll for php v7.2 in the directory src/dependencies/mongodb_dll.

Then you must enable the extension in the "Dynamic extensions" section of the php.ini file

```
extension=php_mongodb.dll
```

**WARNING**: The old mongo PHP driver is not supported anymore in versions >= 3.0.

***Installing MongoDB***: Below is the link to the documentation on how to install Mongo DB https://docs.mongodb.com/manual/tutorial/install-mongodb-on-windows/

***Installing Redis***:

The file redis-2.4.5-win32-win64.zip is located in the src/dependencies directory. The content of the zip should be extracted in the directory C:\redis. Finally, execute the file redis-server.exe of the corresponding version (32bits or 64bits).

***INFO***: For the execution of the project, I use the Chrome Advance REST Client extension

Installation
------------

The repository should be cloned in the directory C:\xampp\htdocs\ (the directory will depend on the installed php server, in my case corresponds to xampp).

```
git clone https://github.com/saguajardo/rest_lumen_mongodb_redis.git rest
```

This will create the rest\ directory in C:\xampp\htdocs\

Configuration
-------------

Rename the ".env.example" file located in src/project/ directory by ".env". In this file you can find the connection settings to mongoDB

```
DB_CONNECTION=mongodb
DB_HOST=localhost
DB_PORT=27017
DB_DATABASE=db_mongodb
DB_USERNAME=username_mongodb
DB_PASSWORD=123456
```

***INFO***: These credentials are obtained from the installation of mongoDB

***Create Collection DB***: To create the collection in mongoDB, you must execute the following command artisan in the directory C:\xampp\htdocs\redis\project\ from the command prompt (cmd).

```
php artisan migrate
php artisan db:seed
```

Examples
--------

Once the installation steps are complete, the Chrome Advance Rest Client extension must be run.

***Available methods***: The available methods of the api are the following:

 Method | URL													| Parameters
:-------|:------------------------------------------------------|------------------------------------------------------------
 GET    | http://localhost/rest/src/project/public/api/v1/task  | {}
 POST   | http://localhost/rest/src/project/public/api/v1/list	| {id, due_date, completed, date_creation, date_update, next}
 POST   | http://localhost/rest/src/project/public/api/v1/task	| {due_date, title, description, completed}
 PUT    | http://localhost/rest/src/project/public/api/v1/task	| {id, due_date, title, description, completed}
 DELETE | http://localhost/rest/src/project/public/api/v1/task	| {id}

The parameters must be sent in JSON format, as shown below:

```
{
 "id": "5b5a387e1b5a72056c0078b3",
 "due_date": "2018-07-30",
 "completed": true,
 "date_complete": "2018-07-30",
 "date_update": "2018-07-30"
}
```