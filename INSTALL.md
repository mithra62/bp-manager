# Installing Base MojiTrac 2 #

Below are the instructions to get MojiTrac 2 installed from the Github repository. 

## Requirements ##

MojiTrac 2 requires the below server setup:

1. Apache 2.X
	1. Requires its own vhost (will not work as subdirectory of existing vhost)
2. PHP >= 5.4.17
	1. Intl Extension is required
	2. MySQL PDO Extension is required
	3. Composer
3. MySQL >= 5.2

## Manual Installation Instructions ##

For the most part, MojiTrac 2 is just like any other installed software:

1. Setup vhost to install.YOURDOMAIN.com*
2. Create MySQL database, username, and password
	1. Import `data\moji.sql` to your database
3. Configure code to connect to the database
	1. Rename `config/local.php.dist` to `config/local.php` and modify settings within
4. Run `composer update` from console in installation directory
5. Run `php public\index.php migration apply` from console in installation directory
5. Ensure the permissions are writable on the `data\*` directory
6. Crack a <strike>beer</strike> paper bag of champagne. 

*If you want to use a different subdomain be sure to update the `accounts` table to match your desired setting.
 
## Credentials ##

To log into the newly installed MojiTrac, use the below credentials:

### Administrator (LOTS of projects) ###
Email: default@mojitrac.com<br />
Pass: 123456