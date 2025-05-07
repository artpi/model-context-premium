# Notes about developer environment.

## Setting up the plugin.

Setting up this part, this rip of the plugin. To set it up properly, you have to rename all instances of template in the following files:
- `plugin.php` should be renamed to your plugin slug
  -  In plugin.php you also have to rename the plugin name
  -  Change the text domain to plugin slug
-  `package.json` should refer to your plugin slug
   -  It should also refer to whatever you rename plugin.php to
- `phpcs.xml` should refer to your plugin slug
- `composer.json` should refer to your plugin slug
