rem Deleting current documentation
cd ../piwi/apidocs/default
del *.html

cd ../
del *.html

cd ../../doc-generator

rem Generating new documentation
php phpdoc.php piwi-settings.ini