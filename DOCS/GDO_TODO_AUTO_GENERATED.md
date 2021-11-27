# GDO/Address/GDT_Phone.php:
- [ ] validate existing country phone codes. validate plausible length.
- [ ] write a phone-validator module that uses gdo6-sms to validate a phone.


# GDO/Address/GDT_VAT.php:
- [ ] tax validation, depending on country...


# GDO/Address/GDT_ZIP.php:
- [ ] implement validator based on city and country in the current gdo, if it has a city and country column. Maybe some optional validatesZIP($bool).


# GDO/Admin/Method/ClearCache.php:
- [ ] move to module core.


# GDO/Admin/Method/Install.php:
- [ ] Automatic DB migration for GDO. triggered by re-install module.


# GDO/Backup/Module_Backup.php:
- [ ] During an import, we want to change some config.php settings when successful; domain, db, etc.


# GDO/BBCode/BBDecoder.php:
- [ ] implement


# GDO/Birthday/Module_Birthday.php:
- [ ] implement.


# GDO/Bootstrap5/BS5Icon.php:
- [ ] actually write the renderer and do the mapping.


# GDO/Bootstrap5/Module_Bootstrap5.php:
- [ ] Optional bs5 icon provider.


# GDO/BootstrapTheme/Module_BootstrapTheme.php:
- [ ] override js gdo error function to show a nice dialog with a nice stacktrace.


# GDO/Captcha/Module_Captcha.php:
- [ ] Add a hidden field captcha_title/ctitle that may not be filled out.


# GDO/CLI/CLI.php:
- [ ] use machines IP
- [ ] use machines host name.
- [ ] use output of locale command?


# GDO/CLI/Module_CLI.php:
- [ ] Move CLI utils into this folder.


# GDO/Core/Debug.php:
- [ ] move?
- [ ] uppercase static members


# GDO/Core/GDO.php:
- [ ] Find a way to only remove memcached entries for this single GDO.
- [ ] Temp memory tables not working? => remove
- [ ] throw error on unknown initial vars.


# GDO/Core/GDO_Module.php:
- [ ] Would be nice to have no default dependencies, so a minimal install is possible.
- [ ] here is the spot to enable json for genereic templates.
- [ ] move to module gdo6-licenses


# GDO/Core/GDT.php:
- [ ] GDT::isPrimary() is weirdly used in Database types and Buttons. Rename the button one to isButtonPrimiary(). Document the weird quirk for GDT_AutoIncrement? (or where was the weird primary quirk?)


# GDO/Core/GDT_Hook.php:
- [ ] GDO_Module: Instead of looping over all modules, modules shall hook into GDT_Hook onInit(). This should speed up a bit.


# GDO/Core/Logger.php:
- [ ] faster way without foreach...


# GDO/Core/Method.php:
- [ ] Rename init() to onInit()
- [ ] get rid and move it to GDT_Checkbox. New option: allowToggleAll(true)
- [ ] rename to onInit()


# GDO/Core/Website.php:
- [ ] possible without key but same functionality?
- [ ] strings as params? addMeta($name, $content, $mode, $overwrite)


# GDO/Date/GDT_Timestamp.php:
- [ ] make GDT_Timestamp->getValue() also return a Datetime?
- [ ] rename $millis to $precision in GDT_Timestamp.


# GDO/Date/Test/DateTest.php:
- [ ] test if this date can be parsed in german and english.


# GDO/Date/Time.php:
- [ ] Time - Make the function names better. They shall reflect if they are for db or for display.
- [ ] parseDateIso is broken a bit, because strlen($date) might differ across languages.


# GDO/DB/Cache.php:
- [ ] no result should return null?


# GDO/DB/Database.php:
- [ ] Implement auto alter table... very tricky!
- [ ] should always return an instance?
- [ ] support postgres? This can be achieved via making module DB a separate module. Just need to move any GDT there, that does db creation code. Tricky for Maps/GDT_Position?


# GDO/DB/GDT_Checkbox.php:
- [ ] what about real checkboxes? Not a single one wanted/needed? UI Sugar?


# GDO/DB/GDT_Int.php:
- [ ] make a challenge: create test cases for patterns, require the user to write a webservice that parses them all correctly.


# GDO/DB/Query.php:
- [ ] Rename function


# GDO/DB/Result.php:
- [ ] rename to fetchVar()


# GDO/DB/WithObject.php:
- [ ] check
- [ ] unused, implement composite CRUD forms?


# GDO/Docs/Method/Generate.php:
- [ ] actually launch the generator
- [ ] create a gdo6-proc module that handles async requests from website to proc with progress bar.


# GDO/Docs/Module_Docs.php:
- [ ] make a bin/generate.sh


# GDO/Dog/GDT_DogUser.php:
- [ ] authenticated - only select registered and authenticated users
- [ ] deleted - also select deleted users
- [ ] registrered - only select registered users


# GDO/Facebook/php-graph-sdk/src/Facebook/Authentication/AccessTokenMetadata.php:
@todo v6: Remove this method


# GDO/Facebook/php-graph-sdk/src/Facebook/autoload.php:
@todo v6: Remove support for 'FACEBOOK_SDK_V4_SRC_DIR'


# GDO/Facebook/php-graph-sdk/src/Facebook/Facebook.php:
@todo v6: Throw an InvalidArgumentException if "default_graph_version" is not set


# GDO/Facebook/php-graph-sdk/src/Facebook/FacebookBatchRequest.php:
- [ ] Does Graph support multiple uploads on one endpoint?


# GDO/Facebook/php-graph-sdk/src/Facebook/FacebookBatchResponse.php:
- [ ] With PHP 5.5 support, this becomes array_column($response['headers'], 'value', 'name')
- [ ] replace with array_column() when PHP 5.5 is supported.


# GDO/Facebook/php-graph-sdk/src/Facebook/FacebookRequest.php:
- [ ] Refactor code above with this


# GDO/Facebook/php-graph-sdk/src/Facebook/FacebookResponse.php:
- [ ] Remove this after Graph 2.0 is no longer supported
@todo v6: Remove this method


# GDO/Facebook/php-graph-sdk/src/Facebook/GraphNodes/Collection.php:
@todo v6: Remove this method


# GDO/Facebook/php-graph-sdk/src/Facebook/GraphNodes/GraphList.php:
@todo v6: Remove this class


# GDO/Facebook/php-graph-sdk/src/Facebook/GraphNodes/GraphNode.php:
- [ ] Add auto-casting to AccessToken entities.


# GDO/Facebook/php-graph-sdk/src/Facebook/GraphNodes/GraphObject.php:
@todo v6: Remove this class


# GDO/Facebook/php-graph-sdk/src/Facebook/GraphNodes/GraphObjectFactory.php:
@todo v6: Remove this class


# GDO/Facebook/php-graph-sdk/tests/GraphNodes/CollectionTest.php:
@todo v6: Remove this assertion


# GDO/Facebook/php-graph-sdk/tests/GraphNodes/GraphObjectFactoryTest.php:
@todo v6: Remove this test


# GDO/File/FileUtil.php:
- [ ] reorder params as $source, $dest


# GDO/File/ImageResize.php:
- [ ] Use imagemagick and a system/exec call. PHP needs too much mem.


# GDO/File/Module_File.php:
- [ ] Make Module_File an optional module.
- [ ] remove Cronjob dependency by scaling images on the fly. Add cronjob dependencies where necessary.


# GDO/Form/GDT_AntiCSRF.php:
- [ ] verify crypto
- [ ] verify crypto.


# GDO/Form/GDT_Form.php:
- [ ] remove ugly static api. :(


# GDO/Form/GDT_Select.php:
- [ ] rename to emptyVar


# GDO/gdo6-captcha/Module_Captcha.php:
- [ ] Add a hidden field captcha_title/ctitle that may not be filled out.


# GDO/JPGraph/jpgraph/src/graph/LinearTicks.php:
@todo  s=2:20,12  s=1:50,6  $this->major_step:$nbr


# GDO/JPGraph/jpgraph/src/lang/de.inc.php:
@todo translate
@todo translate into German


# GDO/Language/Trans.php:
- [ ] move
- [ ] separate calls. maybe cache should not be cleared quickly? no idea. Make performance tests for language loading on init.


# GDO/Mail/Method/Send.php:
- [ ] implement


# GDO/PaymentBank/GDT_BIC.php:
- [ ] Implement BIC check


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/CheckUserPrivileges.php:
@todo collect $GLOBALS['db_to_create'] into an array,
@todo fix to get really all privileges, not only explicitly defined for this user
@todo if we find CREATE VIEW but not CREATE, do not offer
@todo we should not break here, cause GRANT ALL *.*


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Config/Forms/BaseForm.php:
@todo This should be abstract, but that does not work in PHP 5


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Controllers/Table/RelationController.php:
@todo should be: $server->db($db)->table($table)->primary()


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Controllers/Table/StructureController.php:
@todo if someone selects A_I when altering a column we need to check:
@todo optimize in case of multiple fields to modify


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Core.php:
@todo add some more var types like hex, bin, ...?


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Database/Search.php:
@todo    can we make use of fulltextsearch IN BOOLEAN MODE for this?


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/DatabaseInterface.php:
@todo    move into ListDatabase?
@todo    move into Table


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Dbal/DbalInterface.php:
@todo    move into ListDatabase?
@todo    move into Table


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Display/Results.php:
@todo    currently this is called twice unnecessary
@todo    ignore LIMIT and ORDER in query!?
@todo    make maximum remembered queries configurable
@todo    move/split into SQL class!?
@todo $where_clause could be empty, for example a table
@todo May be problematic with same field names
@todo for other future table types
@todo move this to a central place


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/File.php:
@todo   add support for compression plugins
@todo   move file read part into readChunk() or getChunk()
@todo move check of $cfg['TempDir'] into Config?
@todo when uploading a file into a blob field, should we also consider using


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Footer.php:
@todo    coming from /server/privileges, here $db is not set,
@todo    coming from /server/privileges, here $table is not set,


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Gis/GisVisualization.php:
@todo Should return JSON to avoid eval() in gis_data_editor.js


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Html/Generator.php:
@todo    use $pos from $_url_params


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Import.php:
@todo    Handle the error case more elegantly
@todo    Handle the error cases more elegantly
@todo BOM could be used for charset autodetection


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/InsertEdit.php:
@todo check if we could replace by "db_|tbl_" - please clarify!?
@todo clarify the meaning of the "textfield" class and explain
@todo not sure what should be done at this point, but we must not
@todo with functions this is not so easy, as you can basically


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/ListAbstract.php:
@todo add caching


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/ListDatabase.php:
@todo this object should be attached to the PMA_Server object


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Mime.php:
@todo Maybe we could try to use fileinfo module if loaded


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Navigation/NavigationTree.php:
@todo describe a scenario where this code is executed


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/ParseAnalyze.php:
@todo if there are more than one table name in the Select:


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Plugins/Export/ExportSql.php:
@todo remove indexes from CREATE TABLE


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Plugins/Export/Helpers/Pdf.php:
@todo do not deactivate completely the display
@todo force here a LIMIT to avoid reading all rows


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Plugins/Import/ImportCsv.php:
@todo       add an option for handling NULL values
@todo maybe we could add original line to verbose


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Plugins/Import/ImportOds.php:
@todo       Importing of accented characters seems to fail
@todo       Pretty much everything


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Plugins/Import/ImportXml.php:
@todo       Improve efficiency
@todo    Generating a USE here blocks importing of a table


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Plugins/Schema/Pdf/PdfRelationSchema.php:
@todo find optimal width for all formats


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Properties/Options/OptionsPropertyGroup.php:
@todo    modify descriptions if needed, when the options are integrated


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Properties/Plugins/ExportPluginProperties.php:
@todo    modify descriptions if needed, when the plug-in properties are integrated


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Query/Compatibility.php:
@todo difference between 'TEMPORARY' and 'BASE TABLE'
@todo guess CHARACTER_MAXIMUM_LENGTH from COLUMN_TYPE
@todo guess CHARACTER_OCTET_LENGTH from CHARACTER_MAXIMUM_LENGTH


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/RecentFavoriteTable.php:
- [ ] Change the release version in table pma_recent


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Sql.php:
@todo Can we know at this point that this is InnoDB,
@todo In countRecords(), MaxExactCount is also verified,


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Table.php:
@todo    move into class PMA_Column
@todo Can't get duplicating PDFs the right way. The
@todo DatabaseInterface::getTablesFull needs to be merged
@todo add check for valid chars in filename on current system/os
@todo make use of Message and Error
@todo on the interface, some js to clear the default value when the
@todo revise this code when we support cross-db relations


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Theme.php:
@todo add the possibility to make a theme depend on another theme
@todo make all components optional - get missing components from 'parent' theme


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Tracker.php:
@todo use stristr instead of strstr


# GDO/PhpMyAdmin/phpmyadmin/libraries/classes/Util.php:
@todo add more compatibility cases (ORACLE for example)


# GDO/PhpMyAdmin/phpmyadmin/libraries/common.inc.php:
@todo should be done in PhpMyAdmin\Config


# GDO/PhpMyAdmin/phpmyadmin/test/classes/ConfigTest.php:
@todo Is this version really expected?
@todo Test actually preferences loading


# GDO/PhpMyAdmin/phpmyadmin/vendor/php-webdriver/webdriver/lib/Remote/DesiredCapabilities.php:
@todo Remove in next major release (BC)
@todo Remove side-effects - not change ie. ChromeOptions::CAPABILITY from instance of ChromeOptions to an array


# GDO/PhpMyAdmin/phpmyadmin/vendor/php-webdriver/webdriver/lib/Remote/WebDriverCommand.php:
@todo In 2.0 force parameters to be an array, then remove is_array() checks in HttpCommandExecutor


# GDO/PhpMyAdmin/phpmyadmin/vendor/php-webdriver/webdriver/lib/WebDriverCapabilities.php:
@todo Remove in next major release (BC)


# GDO/PhpMyAdmin/phpmyadmin/vendor/php-webdriver/webdriver/lib/WebDriverElement.php:
@todo Add in next major release (BC)


# GDO/PhpMyAdmin/phpmyadmin/vendor/php-webdriver/webdriver/lib/WebDriverOptions.php:
@todo @deprecated remove in 2.0


# GDO/PhpMyAdmin/phpmyadmin/vendor/php-webdriver/webdriver/lib/WebDriverTargetLocator.php:
@todo Add in next major release (BC)


# GDO/PhpMyAdmin/phpmyadmin/vendor/phpdocumentor/reflection-docblock/src/DocBlock/StandardTagFactory.php:
@todo this method should be populated once we implement Annotation notation support.


# GDO/PhpMyAdmin/phpmyadmin/vendor/phpunit/php-code-coverage/src/StaticAnalysis/CodeUnitFindingVisitor.php:
@todo Handle default values */


# GDO/PhpMyAdmin/phpmyadmin/vendor/phpunit/phpunit/src/Framework/TestSuite.php:
@todo refactor usage of numTests in DefaultResultPrinter


# GDO/PhpMyAdmin/phpmyadmin/vendor/phpunit/phpunit/src/Util/Annotation/DocBlock.php:
- [ ] this catch is currently not valid, see https://github.com/phar-io/version/issues/16 */
@todo This constant should be private (it's public because of TestTest::testGetProvidedDataRegEx)


# GDO/QRCode/Method/Render.php:
- [ ] Image render size does not seem to supported by this lib.


# GDO/QRCode/php-qrcode/src/QROptionsTrait.php:
@todo throw or ignore silently?


# GDO/Register/Module_Register.php:
- [ ] Guest to Member conversion.


# GDO/Table/GDT_Sort.php:
- [ ] on GDO with non auto-increment this will crash.
- [ ] use count(*) for sorting?


# GDO/Table/GDT_Table.php:
- [ ] GDT_Enum is not searchable yet.
- [ ] implement getPageFor() ArrayResult");
- [ ] what about ordered and sorted and filtered?


# GDO/TBS/GDT_TBS_GroupmasterIcon.php:
- [ ] The formula is wrong. On original TBS the badges are given differently.


# GDO/TBS/Module_TBS.php:
- [ ] BBDecoder in Module_TBSBBMessage


# GDO/ThemeSwitcher/Test/ThemeSwitcherTest.php:
- [ ] Make gdo_test.php run over multiple themes (all?)


# GDO/UI/htmlpurifier/extras/ConfigDoc/HTMLXSLTProcessor.php:
@todo Rename to transformToXHTML, as transformToHTML is misleading


# GDO/UI/htmlpurifier/extras/FSTools/File.php:
@todo Throw an exception if file doesn't exist


# GDO/UI/htmlpurifier/library/HTMLPurifier/AttrDef/CSS/Composite.php:
@todo Make protected


# GDO/UI/htmlpurifier/library/HTMLPurifier/AttrDef/CSS/Multiple.php:
@todo Make protected


# GDO/UI/htmlpurifier/library/HTMLPurifier/AttrDef/Enum.php:
@todo Make protected


# GDO/UI/htmlpurifier/library/HTMLPurifier/Config.php:
@todo Reconsider some of the public member variables


# GDO/UI/htmlpurifier/library/HTMLPurifier/ContentSets.php:
@todo Unit test


# GDO/UI/htmlpurifier/library/HTMLPurifier/CSSDefinition.php:
@todo Refactor duplicate elements into common class (probably using


# GDO/UI/htmlpurifier/library/HTMLPurifier/DefinitionCache/Serializer.php:
@todo Make protected


# GDO/UI/htmlpurifier/library/HTMLPurifier/DefinitionCache.php:
@todo Create a separate maintenance file advanced users can use to
@todo Implement memcached


# GDO/UI/htmlpurifier/library/HTMLPurifier/Filter/ExtractStyleBlocks.php:
@todo Extend to indicate non-text/css style blocks


# GDO/UI/htmlpurifier/library/HTMLPurifier/Generator.php:
@todo Make some of the more internal functions protected, and have
@todo Refactor interface so that configuration/context is determined
@todo This really ought to be protected, but until we have a facility


# GDO/UI/htmlpurifier/library/HTMLPurifier/HTMLDefinition.php:
@todo Give this its own class, probably static interface


# GDO/UI/htmlpurifier/library/HTMLPurifier/HTMLModule/Tidy.php:
@todo Figure out how to protect some of these methods/properties
@todo Wildcard matching and error reporting when an added or


# GDO/UI/htmlpurifier/library/HTMLPurifier/HTMLModule.php:
@todo Consider making some member functions protected


# GDO/UI/htmlpurifier/library/HTMLPurifier/Injector/AutoParagraph.php:
@todo Ensure all states are unit tested, including variations as well.
@todo Make a graph of the flow control for this Injector.


# GDO/UI/htmlpurifier/library/HTMLPurifier/Injector.php:
@todo Allow injectors to request a re-run on their output. This


# GDO/UI/htmlpurifier/library/HTMLPurifier/Language.php:
@todo Implement conditionals? Right now, some messages make
@todo Make it private, fix usage in HTMLPurifier_LanguageTest


# GDO/UI/htmlpurifier/library/HTMLPurifier/LanguageFactory.php:
@todo Serialized cache for languages


# GDO/UI/htmlpurifier/library/HTMLPurifier/Lexer/DirectLex.php:
@todo Reread XML spec and document differences.


# GDO/UI/htmlpurifier/library/HTMLPurifier/Lexer/DOMLex.php:
@todo data and tagName properties don't seem to exist in DOMNode?


# GDO/UI/htmlpurifier/library/HTMLPurifier/Lexer.php:
@todo Consider making protected


# GDO/UI/htmlpurifier/library/HTMLPurifier/Printer/ConfigForm.php:
@todo Rewrite to use Interchange objects


# GDO/UI/htmlpurifier/library/HTMLPurifier/Printer/HTMLDefinition.php:
@todo Also add information about internal state


# GDO/UI/htmlpurifier/library/HTMLPurifier/Strategy/FixNesting.php:
@todo Enable nodes to be bubbled out of the structure.  This is


# GDO/UI/htmlpurifier/library/HTMLPurifier/TokenFactory.php:
@todo Port DirectLex to use this


# GDO/UI/htmlpurifier/library/HTMLPurifier/URIScheme/mailto.php:
@todo Filter allowed query parameters
@todo Validate the email address


# GDO/UI/htmlpurifier/library/HTMLPurifier.php:
@todo We need an easier way to inject strategies using the configuration


# GDO/UI/htmlpurifier/maintenance/old-extract-schema.php:
@todo Extract version numbers.


# GDO/UI/htmlpurifier/tests/FSTools/FileSystemHarness.php:
@todo Make an automatic FSTools mock or something


# GDO/UI/htmlpurifier/tests/HTMLPurifier/AttrDef/URITest.php:
@todo Aim for complete code coverage with mocks


# GDO/UI/htmlpurifier/tests/HTMLPurifier/ErrorsHarness.php:
@todo Make the callCount variable actually work, so we can precisely


# GDO/UI/htmlpurifier/tests/HTMLPurifier/Filter/ExtractStyleBlocksTest.php:
@todo Assimilate CSSTidy into our library


# GDO/UI/htmlpurifier/tests/HTMLPurifier/LanguageTest.php:
@todo Fix usage of HTMLPurifier_Language->_loaded using something else


# GDO/UI/htmlpurifier/tests/PHPT/Reporter/SimpleTest.php:
@todo Figure out if Suites can be named


# GDO/UI/htmlpurifier/tests/PHPT/Section/PRESKIPIF.php:
@todo refactor this code into PHPT_Util class as its used in multiple places
@todo refactor to PHPT_CodeRunner


# GDO/User/GDO_User.php:
- [ ] This triggers a bug when all admin users are reloaded they poison the cache with outdated data. 


# GDO/Util/Common.php:
- [ ] Move to another file?


# GDO/Websocket/gwf4-ratchet/cboden/ratchet/src/Ratchet/AbstractConnectionDecorator.php:
@todo It sure would be nice if I could make most of this a trait...


# GDO/Websocket/gwf4-ratchet/cboden/ratchet/src/Ratchet/WebSocket/Version/RFC6455/Frame.php:
@todo Consider not checking mask, always returning the payload, masked or not
@todo Consider returning new Frame
@todo This is untested, make sure the substr is right - trying to return the frame w/o the overflow


# GDO/Websocket/gwf4-ratchet/cboden/ratchet/src/Ratchet/WebSocket/Version/RFC6455/HandshakeVerifier.php:
@todo Check the spec to see what the encoding of the key could be
@todo Currently just returning invalid - should consider returning appropriate HTTP status code error #s
@todo Find out if I can find the master socket, ensure the port is attached to header if not 80 or 443 - not sure if this is possible, as I tried to hide it
@todo Once I fix HTTP::getHeaders just verify this isn't NULL or empty...or maybe need to verify it's a valid domain??? Or should it equal $_SERVER['HOST'] ?
@todo Ran in to a problem here...I'm having HyBi use the RFC files, this breaks it!  oops
@todo The spec says we don't need to base64_decode - can I just check if the length is 24 and not decode?
@todo Write logic for this method.  See section 4.2.1.8
@todo Write logic for this method.  See section 4.2.1.9


# GDO/Websocket/gwf4-ratchet/cboden/ratchet/src/Ratchet/WebSocket/Version/RFC6455/Message.php:
@todo Also, I should perhaps check the type...control frames (ping/pong/close) are not to be considered part of a message


# GDO/Websocket/gwf4-ratchet/cboden/ratchet/src/Ratchet/WebSocket/Version/RFC6455.php:
@todo Unicode: return mb_convert_encoding(pack("N",$u), mb_internal_encoding(), 'UCS-4BE');


# GDO/Websocket/gwf4-ratchet/cboden/ratchet/src/Ratchet/WebSocket/Version/VersionInterface.php:
@todo Change to use other classes, this will be removed eventually


# GDO/Websocket/gwf4-ratchet/cboden/ratchet/src/Ratchet/WebSocket/WsServerInterface.php:
@todo This method may be removed in future version (note that will not break code, just make some code obsolete)


# GDO/Websocket/gwf4-ratchet/cboden/ratchet/tests/unit/WebSocket/Version/RFC6455/FrameTest.php:
@todo Could use some clean up in general, I had to rush to fix a bug for a deadline, sorry.
@todo I I wrote the dataProvider incorrectly, skipping for now
@todo Move this test to bottom as it requires all methods of the class
@todo Not yet testing when second additional payload length descriptor
@todo getMaskingKey, getPayloadStartingByte don't have tests yet


# GDO/Websocket/gwf4-ratchet/symfony/http-foundation/Session/Storage/Handler/PdoSessionHandler.php:
@todo implement missing advisory locks


# gdoadm.php:
- [ ] write a repl configurator.


# vendor/phpdocumentor/reflection-docblock/src/DocBlock/StandardTagFactory.php:
@todo this method should be populated once we implement Annotation notation support.


# vendor/phpunit/php-code-coverage/src/StaticAnalysis/CodeUnitFindingVisitor.php:
@todo Handle default values */


# vendor/phpunit/phpunit/src/Framework/TestSuite.php:
@todo refactor usage of numTests in DefaultResultPrinter


# vendor/phpunit/phpunit/src/Util/Annotation/DocBlock.php:
- [ ] this catch is currently not valid, see https://github.com/phar-io/version/issues/16 */
@todo This constant should be private (it's public because of TestTest::testGetProvidedDataRegEx)


