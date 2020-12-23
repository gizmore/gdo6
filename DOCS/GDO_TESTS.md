# GDO6 Unit Testing

Modules can have a Test/ folder that is automatically used when invoking

    gdo6/gdo_test.sh

    
## Testing configuration

Please make sure to create a config under protected/config_unit_test.php.


## Testing sequence

The tests depend on each other.
Module Tests are executed by module priority.
The process is as follows.
https://github.com/gizmore/gdo6/blob/master/GDO/Core/Test/AutoCoverageTest.php from module Core is the first test that is executed.
It creates 4 test users with different permissions and does some automated tests, also methods that have no parameters.
Next is the language module that configures 3 supported languages.
