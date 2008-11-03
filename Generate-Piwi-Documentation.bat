rem Delete old documentation
cd doc/api/default
del *.html

rem Generate new documentation
cd ../../../doc-generator
php phpdoc.php piwi-settings.ini
cd ../