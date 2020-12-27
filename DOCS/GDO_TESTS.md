# GDO6 Unit Testing

Modules can have a Test/ folder that is automatically used when invoking

    gdo6/gdo_test.sh

## Testing configuration

Please run tests in an own test installation, because files/ are overwritten.
There are not much tests yet, some are in modules like "Mettwitze" which are my own sites. But i started to write tests in quite late process of gdo6, and now add a test when a problem occurs. 


## Testing sequence

The tests depend on each other.
Module Tests are executed by module priority.
The process is as follows.
Core is installed for the core database tables.
Language and Country are installed for more tables.
User is installed for more tables.
"Tests" module is executed which run basic as well as automated tests on all modules.
All other module tests are run in priority order.
