# KeyMedia integration for eZ Publish

## Installation

### Dependencies
_eZ on the Edge_ extension from [Github](https://github.com/KeyteqLabs/ezote)

### Install sql

	mysql -u username -p -h host databasename < sql/mysql/install.sql

### Regenerate autoloads and clear cache

	php bin/php/ezpgenerateautoloads.php; php bin/php/ezcache.php --clear-all
	
### Connect to KeyMedia

Head over to _Admin Dashboard_ -> _KeyMedia_ and add your KeyMedia API connection information.

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

Its simple! Just head over to _mydomain.com/ezote/delegate/ezr_keymedia/user_test/tags_ and it will read your installations available KeyMedia connections
and let you do tag search in them.
You can look at this example class for code as well, its in _modules/user_test/UserTest.php_.
