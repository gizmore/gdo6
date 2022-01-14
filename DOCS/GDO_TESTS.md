# GDO6 Unit Testing

Modules can have a Test/ folder that is automatically used when invoking `./gdo_test.sh`.

You can launch individual module tests via `./gdo_test.sh <module>`. Core modules are always tested.

It is recommended to install the modules gdo6-tests and gdo6-test-methods for automated test-cases. 

    ./gdoadm.sh provide Tests
    ./gdoadm.sh install Tests
    ./gdoadm.sh provide TestMethods
    ./gdoadm.sh install TestMethods


## Testing configuration

The gdo_test.sh will create a `files_test/` folder and load config from `protected/config_test.php`. The production files are not overwritten.


## Testing sequence

The tests depend on each other.
Module Tests are executed by module priority.
The process is as follows.
Core is installed for the core database tables.
Language and Country are installed for more tables.
User is installed for more tables.
"Tests" module is executed which run basic tests.
There is a final module [Module_TestMethods](https://github.com/gizmore/gdo6-test-methods) that runs automated tests on all trivial Methods. It also checks some very trivial GDT and GDO behaviour. Is nullcheck broken? Can they convert from var to value to var without changes?
It plugs all parameters with mock values. A GDT usually has a mock value that mostly always passes a validation.


## Setup a test machine

Configure your `protected/config_test.php`.
Use `./gdoadm.sh provide_all` and `./gdoadm.sh install_all` to download and install all publicy avaible gdo6 modules. There are some automated testing modules for instance, and some core functions are only tested in a few site modules.
Use `composer update` to install phpunit dependencies.
Run `./gdo_test.sh` to run tests for all installed modules. Many don't have any at all, but the runtime is okay for a test.
