# GDO6 Unit Testing

Modules can have a Test/ folder that is automatically used when invoking

    gdo6/gdo_test.sh

## Testing configuration

The gdo_test.sh will create a files_test/ folder and load config from protected/config_test.php. The production files are not overwritten.
There are not much tests yet, and some are in modules like "Mettwitze" which are my own sites.
I started to write tests in quite late process of gdo6, and now i sometimes add a test when a problem occurs, usually in a high level module. 


## Testing sequence

The tests depend on each other.
Module Tests are executed by module priority.
The process is as follows.
Core is installed for the core database tables.
Language and Country are installed for more tables.
User is installed for more tables.
"Tests" module is executed which run basic as well as automated tests on all modules.
All other module tests are run in priority order.
