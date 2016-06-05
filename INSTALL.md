# Installing Backup Pro Manager #

Below are the instructions to get Backup Pro Manager installed from the Github repository. 

## Requirements ##

Backup Pro Manager requires the below server setup:

1. Apache 2.X
	1. Requires its own vhost (will not work as subdirectory of existing vhost)
2. PHP >= 5.4.17
	1. Intl Extension is required
	2. MySQL PDO Extension is required
	3. Composer
3. MySQL >= 5.2

## Manual Installation Instructions ##

For the most part, Backup Pro Manager is just like any other installed software:

1. Setup vhost to install.YOURDOMAIN.com
2. Create MySQL database, username, and password
3. Configure code to connect to the database
	1. Rename `config/local.php.dist` to `config/local.php` and modify settings within
4. Run `composer update` from console in installation directory
5. Run `php public\index.php migration apply` from console in installation directory
5. Ensure the permissions are writable on the `data\*` directory
6. Crack a <strike>beer</strike> paper bag of champagne. 
 
## Credentials ##

To log into the newly installed Backup Pro Manager, use the below credentials:

### Administrator (LOTS of projects) ###
Email: default@mithra62.com<br />
Pass: 123456