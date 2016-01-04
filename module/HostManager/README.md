# MojiTrac HostManager Module #

This module is responsible for everything related to MojiTrac as a hosted platform. 

The basic principle is to limit all SQL calls by an `account_id` column on the tables that shouldn't be shared, use an `accounts` and `accounts2users` table as lookups to ensure all data is compartmentalized where appropriate. This is done by injecting an Event onto the SQL hooks (defined in `\Base\Model\BaseModel`) to append an `account_id` column onto all SQL queries. 

## Installation ##

Once the module is installed like any other Zend Framework 2 module, be sure to rename `module.local.config.php.dist` to `module.local.config.php` and add any overrides needed from `module.config.php`. 

### Configuration Details ###

`sub_primary_url` is the main domain host your MojiTrac is installed on. For example, the default is `.mojitrac.com` for domains like `test.mojitrac.com` and `install1.mojitrac.com`. 

`master_host_account` is the account all subsequent accounts are based on. All settings and configuration, in User Roles and Options, are used when creating new accounts. It should always match to an existing account.

### Adding New Tables ###

Since not all tables will require limiting by account, for example `users` and `users_data` are shared across accounts, and the rules for limiting by account can be different per table, each table will require its own class to contain the logic. Any tables that don't have a class will be ignored by the module. 

All classes must extend `\HostManager\Model\Sql\SqlAstract` which contains some generic handling and should be stored on the file system at `module\HostManager\src\HostManager\Model\Sql`. Each table class should be named for the table it affects (for example, the `tasks` table would have a class called `Tasks`) with capitalized first letter and camel casing to replace underscores (`user_data` would be `UserData`), and contain the below methods:

1. `Select`
2. `Update`
3. `Delete`
4. `Insert`

Which define the logic for appending the `account_id` limiting logic to the SQL calls. All of the base methods are defined in the Abstract class ready to be overridden if need be, but the default should handle most situations. 