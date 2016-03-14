<?php
/**
 * mithra62 - Backup Pro Server
 *
 * @copyright	Copyright (c) 2016, mithra62, Eric Lamb.
 * @link		http://backup-pro.com/
 * @version		1.0
 * @filesource ./module/Base/src/Base/Model/EventManagerInterfaceConstants.php
 */
namespace Base\Model;

use Zend\EventManager\EventManagerAwareInterface;

/**
 * Event Manager Interface Constants
 *
 * Contains all the Event Hook Names used within the Models
 *
 * @package BackupProServer\Model
 * @author Eric Lamb <eric@mithra62.com>
 */
interface EventManagerInterfaceConstants extends EventManagerAwareInterface
{

    const EventPasswordUpdatePre = 'password.update.pre';

    const EventPasswordUpdatePost = 'password.update.post';

    const EventUserAddPre = 'user.add.pre';

    const EventUserAddPost = 'user.add.post';

    const EventUserUpdatePre = 'user.update.pre';

    const EventUserUpdatePost = 'user.update.post';

    const EventUserRemovePre = 'user.remove.pre';

    const EventUserRemovePost = 'user.remove.post';

    const EventUserLogoutPre = 'user.logout.pre';

    const EventUserLogoutPost = 'user.logout.post';

    const EventUserLoginPre = 'user.login.pre';

    const EventUserLoginPost = 'user.login.post';

    const EventUserRoleAddPre = 'user.role.add.pre';

    const EventUserRoleAddPost = 'user.role.add.post';

    const EventUserRoleUpdatePre = 'user.role.update.pre';

    const EventUserRoleUpdatePost = 'user.role.update.post';

    const EventUserRoleRemovePre = 'user.role.remove.pre';

    const EventUserRoleRemovePost = 'user.role.remove.post';

    const EventSettingsUpdatePre = 'settings.update.pre';

    const EventSettingsUpdatePost = 'settings.update.post';

    const EventSettingsDefaultsSetPre = 'settings.defaults.set.pre';

    const EventSettingsDefaultsSetPost = 'settings.defaults.set.post';
 // Nothing to exit or return or override!!
    const EventSettingsCompiledGetPre = 'settings.compiled.get.pre';

    const EventSettingsCompiledGetPost = 'settings.compiled.get.post';

    const EventUserDataDefaultsSetPre = 'user_data.defaults.set.pre';

    const EventUserDataDefaultsSetPost = 'user_data.defaults.set.post';
 // Nothing to exit or return or override!!
                                                                        
    // database events
    const EventDbSelectPre = 'db.select.pre';

    const EventDbSelectPost = 'db.select.post';

    const EventDbUpdatePre = 'db.update.pre';

    const EventDbUpdatePost = 'db.update.post';

    const EventDbInsertPre = 'db.insert.pre';

    const EventDbInsertPost = 'db.insert.post';

    const EventDbRemovePre = 'db.remove.pre';

    const EventDbRemovePost = 'db.remove.post';
    
    const EventSiteAddPre = 'site.insert.pre';
    const EventSiteAddPost = 'site.insert.post';
}