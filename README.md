# KeyMedia integration for eZ Publish

## Installation

### Dependencies
_eZ on the Edge_ extension from [Github](https://github.com/KeyteqLabs/ezote)

### Install sql

	mysql -u username -p -h host < sql/mysql/install.sql

### Regenerate autoloads and clear cache

	php bin/php/ezpgenerateautoloads.php; php bin/php/ezcache.php --clear-all
	
__Your now ready to rock!__
