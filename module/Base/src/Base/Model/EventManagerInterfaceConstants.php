<?php
/**
 * mithra62 - MojiTrac
 *
 * @author		Eric Lamb <eric@mithra62.com>
 * @copyright	Copyright (c) 2014, mithra62, Eric Lamb.
 * @link		http://mojitrac.com/
 * @version		2.0
 * @filesource 	./module/Base/src/Base/Model/
 */

namespace Base\Model;

use Zend\EventManager\EventManagerAwareInterface;

/**
 * Event Manager Interaface Constants
 *
 * Contains all the Event Hook Names used within the Moji Models
 *
 * @package 	MojiTrac\Model
 * @author		Eric Lamb <eric@mithra62.com>
 * @filesource 	./module/Base/src/Base/Model/EventManagerInterfaceConstants.php
 */
interface EventManagerInterfaceConstants extends EventManagerAwareInterface
{
	const EventPasswordUpdatePre = 'password.update.pre';
	const EventPasswordUpdatePost = 'password.update.post';
	
	//context ids (company.X, project.X)
	const EventProjectUpdatePre = 'project.update.pre';
	const EventProjectUpdatePost = 'project.update.post';
	const EventProjectRemovePre = 'project.remove.pre';
	const EventProjectRemovePost = 'project.remove.post';
	const EventProjectAddPre = 'project.add.pre';
	const EventProjectAddPost = 'project.add.post';
	const EventProjectAddTeamPre = 'project.addteam.pre';
	const EventProjectAddTeamPost = 'project.addteam.post';
	const EventProjectRemoveTeamPre = 'project.removeteam.pre';
	const EventProjectRemoveTeamPost = 'project.removeteam.post';
	const EventProjectRemoveTeamMemberPre = 'project.removeteammember.pre';
	const EventProjectRemoveTeamMemberPost = 'project.removeteammember.post';
	
	const EventTaskUpdatePre = 'task.update.pre';
	const EventTaskUpdatePost = 'task.update.post';
	const EventTaskAddPre = 'task.add.pre';
	const EventTaskAddPost = 'task.add.post';
	const EventTaskRemovePre = 'task.remove.pre';
	const EventTaskRemovePost = 'task.remove.post';	
	const EventTaskAssignPre = 'task.assign.pre';
	const EventTaskAssignPost = 'task.assign.post';
	
	const EventCompanyUpdatePre = 'company.update.pre';
	const EventCompanyUpdatePost = 'company.update.post';
	const EventCompanyAddPre = 'company.add.pre';
	const EventCompanyAddPost = 'company.add.post';
	const EventCompanyRemovePre = 'company.remove.pre';
	const EventCompanyRemovePost = 'company.remove.post';
	
	const EventContactUpdatePre = 'company.contact.update.pre';
	const EventContactUpdatePost = 'company.contact.update.post';
	const EventContactAddPre = 'company.contact.add.pre';
	const EventContactAddPost = 'company.contact.add.post';
	const EventContactRemovePre = 'company.contact.remove.pre';
	const EventContactRemovePost = 'company.contact.remove.post';
	
	const EventInvoiceAddPre = 'company.invoice.add.pre';
	const EventInvoiceAddPost = 'company.invoice.add.post';
	const EventInvoiceUpdatePre = 'company.invoice.update.pre';
	const EventInvoiceUpdatePost = 'company.invoice.update.post';
	const EventInvoiceRemovePre = 'company.invoice.remove.pre';
	const EventInvoiceRemovePost = 'company.invoice.remove.post';
	
	const EventInvoiceLineItemAddPre = 'company.invoice.lineitem.add.pre';
	const EventInvoiceLineItemAddPost = 'company.invoice.lineitem.add.post';
	const EventInvoiceLineItemUpdatePre = 'company.invoice.lineitem.update.pre';
	const EventInvoiceLineItemUpdatePost = 'company.invoice.lineitem.update.post';
	const EventInvoicesLineItemRemovePre = 'company.invoice.lineitem.remove.pre';
	const EventInvoicesLineItemRemovePost = 'company.invoice.lineitem.remove.post';
	
	const EventFileUpdatePre = 'file.update.pre';
	const EventFileUpdatePost = 'file.update.post';
	const EventFileAddPre = 'file.add.pre';
	const EventFileAddPost = 'file.add.post';
	const EventFileRemovePre = 'file.remove.pre';
	const EventFileRemovePost = 'file.remove.post';	
	const EventFileRevisionAddPre = 'file.revision.add.pre';
	const EventFileRevisionAddPost = 'file.revision.add.post';
	const EventFileRevisionRemovePre = 'file.revision.remove.pre';
	const EventFileRevisionRemovePost = 'file.revision.remove.post';
	
	const EventNoteUpdatePre = 'note.update.pre';
	const EventNoteUpdatePost = 'note.update.post';
	const EventNoteAddPre = 'note.add.pre';
	const EventNoteAddPost = 'note.add.post';
	const EventNoteRemovePre = 'note.remove.pre';
	const EventNoteRemovePost = 'note.remove.post';		
	
	const EventBookmarkUpdatePre = 'bookmark.update.pre';
	const EventBookmarkUpdatePost = 'bookmark.update.post';
	const EventBookmarkAddPre = 'bookmark.add.pre';
	const EventBookmarkAddPost = 'bookmark.add.post';
	const EventBookmarkRemovePre = 'bookmark.remove.pre';
	const EventBookmarkRemovePost = 'bookmark.remove.post';	
	
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
	const EventSettingsDefaultsSetPost = 'settings.defaults.set.post'; //Nothing to exit or return or override!!
	const EventSettingsCompiledGetPre = 'settings.compiled.get.pre';
	const EventSettingsCompiledGetPost = 'settings.compiled.get.post';
	
	const EventUserDataDefaultsSetPre = 'user_data.defaults.set.pre';
	const EventUserDataDefaultsSetPost = 'user_data.defaults.set.post'; //Nothing to exit or return or override!!
	
	const EventActivityLogAddPre = 'activitylog.add.pre';
	const EventActivityLogAddPost = 'activitylog.add.post';
	
	//database events
	const EventDbSelectPre = 'db.select.pre';
	const EventDbSelectPost = 'db.select.post';
	const EventDbUpdatePre = 'db.update.pre';
	const EventDbUpdatePost = 'db.update.post';
	const EventDbInsertPre = 'db.insert.pre';
	const EventDbInsertPost = 'db.insert.post';
	const EventDbRemovePre = 'db.remove.pre';
	const EventDbRemovePost = 'db.remove.post';

}