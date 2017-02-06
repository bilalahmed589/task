## Task Management (With Hierarchy) Sample Application using Laravel PHP Framework

This sample Task management application uses diffrent laravel framework features/modules.
User authentication is skipped for demo purposes.
A task can be created and its child can be created.

There is one main controller TaskControllor.php and one service TaskService.php.
Following features has been used to built this demo
MVC,IOC (dependency injection),Eloquent ORM ,Blade Template, Transaction Management

Interface injection is used to inject business services into controller. Because it is good design principle "program for interface" than "program for implementation". Also repository is injected in service layer to ensure DAO pattern.

Below 2 steps are the required to run this test.

For database settings these is a file at root of folder with name ".env". Set db connections there.
Setup virtual host in apache
            <VirtualHost *:80>
                   ServerName tasks.com
                   DocumentRoot "path-to-project/task/public/"
                   <Directory "path-to-project/task/public/">
                   </Directory>
   </VirtualHost>
* Please note project folder name is "task". You need to point public folder as virtual host.


**This project is for demonstration purpose only.**



### License

This  project is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
