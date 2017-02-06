## Task Management (With Hierarchy) Sample Application using Laravel PHP Framework

This sample Task management application uses diffrent laravel framework features/modules.
User authentication is skipped for demo purposes.
A task can be created and its child can be created.

There is one main controller TaskControllor.php and one service TaskService.php.
Following features has been used to built this demo
MVC,IOC (dependency injection),Eloquent ORM ,Blade Template, Transaction Management

Interface injection is used to inject business services into controller. Because it is good design principle "program for interface" than "program for implementation". Also repository is injected in service layer to ensure DAO pattern.

**This project is for demonstration purpose only.**



### License

This  project is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
