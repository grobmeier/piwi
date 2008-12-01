Test must be not be invoked from this directory.
You have to run the tests from the parent directory, to ensure that dependencies are resolved correctly.

To run all tests invoke:
php test/TestRunner.php SERVER

To run all tests with covering report invoke:
php test/TestRunner.php SERVER REPORT_DIRECTORY PHPCOVERAGE_HOME=test/spikephpcoverage-0.8.2/src


REPORT_DIRECTORY: Is the directory where the reports will be placed
SERVER:           Is the host where the PIWI demo page is located



To generate the covering report, XDebug must be enabled (and Zend disabled).
To activate XDebug, you must add an entry to php.ini.
On Windows, use: zend_extension_ts="c:\php\ext\php_xdebug-2.0.1-5.2.1.dll"
On Unix, use: zend_extension="/usr/lib/apache2/modules/xdebug.so"