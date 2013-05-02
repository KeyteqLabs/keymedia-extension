# KeyMedia integration for eZ Publish

## Installation

### Dependencies
_eZ on the Edge_ extension from [Github](https://github.com/KeyteqLabs/ezote)

### Checkout from github

	git clone git@github.com:KeyteqLabs/keymedia-extension.git /my/ez/extension/keymedia

### Navigate to the extension.

	cd /my/ez/extension/keymedia

### Install sql

	mysql -u username -p -h host databasename < sql/mysql/install.sql

### Regenerate autoloads and clear cache

	php bin/php/ezpgenerateautoloads.php; php bin/php/ezcache.php --clear-all --purge
	
### Connect to KeyMedia

Head over to _Admin Dashboard_ -> _KeyMedia_ and add your KeyMedia API connection information.

### Make keymedia available in eZOe

In your ezoe.ini in settings/override you must add the following:

[EditorSettings]
Plugins[]=keymedia

[EditorLayout]
Buttons[]=keymedia

The keymedia button could be placed anywhere in the editor. See the eZOe doc on how to arrange buttons

## Usage

### Lookup on tags
```php
<?php
$backend = Backend::first(array('id' => $backendId));
// One tag
$results = $backend->tagged('a tag');
// Multiple tags
$results = $backend->tagged(array('first', 'second'));
// Match either tag
$results = $backend->tagged(array('first', 'second'), array('operator' => 'or'));
// Limit results
$results = $backend->tagged(array('first', 'second'), array('limit' => 1));
```

### Test it in your browser

Its simple! Just head over to _mydomain.com/ezote/delegate/keymedia/user_test/tags_ and it will read your installations available KeyMedia connections
and let you do tag search in them.
You can look at this example class for code as well, its in _modules/user_test/UserTest.php_.

## Upgrade old beta version

KeyMedia was initially named *ezr_keymedia*, and if you have a copy of the extension named as that you must do a few steps:

* Change git-repo to the aforementioned one (if running from git)
* Git pull (if running from git)
* Rename extension; `mv extension/ezr_keymedia extension/keymedia`
* Run sql-upgrade; `mysql -u username -p -h host databasename < sql/mysql/upgrade-1.0.sql`
* Regenerate autoloads and purge cache
