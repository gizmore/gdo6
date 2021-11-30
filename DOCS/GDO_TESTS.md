# GDO6 Unit Testing

Modules can have a Test/ folder that is automatically used when invoking

    gdo6/gdo_test.sh


## Testing configuration

The gdo_test.sh will create a files_test/ folder and load config from protected/config_test.php. The production files are not overwritten.


## Testing sequence

The tests depend on each other.
Module Tests are executed by module priority.
The process is as follows.
Core is installed for the core database tables.
Language and Country are installed for more tables.
User is installed for more tables.
"Tests" module is executed which run basic tests.
There is a final module *Module_TestMethods* that runs automated tests on all trivial Methods, GDT and GDO.
It plugs all parameters with mock values. A GDT usually has a mock value that mostly always passes a validation.


## Setup a test machine

Use `./gdoadm.sh provide_all` to download all avaible gdo6 modules.
Use `composer update` to install phpunit dependency.
