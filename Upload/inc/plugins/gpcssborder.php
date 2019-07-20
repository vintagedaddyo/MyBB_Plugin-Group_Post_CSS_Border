<?php
/***************************************************************************
 *
 *  Group Post CSS Border plugin (/inc/plugins/gpcssborder.php)
 *  Authors: Jammerx2, Vintagedaddyo
 *  Copyright: © 2019
 *  Website:
 *
 *  Vintagedaddyo: http://community.mybb.com/user-6029.html
 *  
 *  License: license.txt
 *
 *  Allows you to add custom CSS border for each groups posts.
 *
 *  MyBB Version: 1.8
 *
 *  Plugin Version: 1.1
 *
 ***************************************************************************/

if(!defined("IN_MYBB"))
	die("This file cannot be accessed directly.");

//Add Hooks

$plugins->add_hook('admin_user_groups_edit', 'gpcssborder');
$plugins->add_hook('admin_user_groups_edit_commit', 'gpcssborder_do');
$plugins->add_hook("postbit_prev", "gpcssborder_post_prev");
$plugins->add_hook("postbit_pm", "gpcssborder_post_pm");
$plugins->add_hook("postbit_announcement", "gpcssborder_post_announcement");
$plugins->add_hook("postbit", "gpcssborder_post");


function gpcssborder_info()
{

//Plugin Description

    global $lang;

    $lang->load("gpcssborder");
    
    $lang->gpcssborder_Desc = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" style="float:right;">' .
        '<input type="hidden" name="cmd" value="_s-xclick">' . 
        '<input type="hidden" name="hosted_button_id" value="AZE6ZNZPBPVUL">' .
        '<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">' .
        '<img alt="" border="0" src="https://www.paypalobjects.com/pl_PL/i/scr/pixel.gif" width="1" height="1">' .
        '</form>' . $lang->gpcssborder_Desc;

    return Array(
        'name' => $lang->gpcssborder_Name,
        'description' => $lang->gpcssborder_Desc,
        'website' => $lang->gpcssborder_Web,
        'author' => $lang->gpcssborder_Auth,
        'authorsite' => $lang->gpcssborder_AuthSite,
        'version' => $lang->gpcssborder_Ver,
        'compatibility' => $lang->gpcssborder_Compat
    );
}

function gpcssborder_activate()
{

//Create Tables

global $mybb, $db;

	$db->query("ALTER TABLE `".TABLE_PREFIX."usergroups` ADD `gpcssborder1` VARCHAR(1500) NOT NULL");
	$db->query("ALTER TABLE `".TABLE_PREFIX."usergroups` ADD `gpcssborder2` VARCHAR(1500) NOT NULL");

	include MYBB_ROOT."/inc/adminfunctions_templates.php";


// Postbit Activate

find_replace_templatesets("postbit", "#".preg_quote("class=\"post {\$unapproved_shade}\" style=\"{\$post_visibility}\"")."#i", "class=\"post {\$unapproved_shade}\" style=\"{\$post_visibility} {\$post['gpcssborder1']}\"");
	

// Postbit Classic Activate

find_replace_templatesets("postbit_classic", "#".preg_quote("class=\"post classic {\$unapproved_shade}\" style=\"{\$post_visibility}\"")."#i", "class=\"post classic {\$unapproved_shade}\" style=\"{\$post_visibility} {\$post['gpcssborder2']}\"");

}

function gpcssborder_deactivate()
{

//Drop Tables

global $mybb, $db;

	$db->query("ALTER TABLE ".TABLE_PREFIX."usergroups DROP `gpcssborder1`");
	$db->query("ALTER TABLE ".TABLE_PREFIX."usergroups DROP `gpcssborder2`");

	include MYBB_ROOT."/inc/adminfunctions_templates.php";


// Postbit Deactivate

find_replace_templatesets("postbit", "#".preg_quote("style=\"{\$post_visibility} {\$post['gpcssborder1']}\"")."#i", "style=\"{\$post_visibility}\"", 0);


// Postbit Classic Deactivate

find_replace_templatesets("postbit_classic", "#".preg_quote("style=\"{\$post_visibility} {\$post['gpcssborder2']}\"")."#i", "style=\"{\$post_visibility}\"", 0);

}

function gpcssborder()
{

//Add Hook

global $plugins;

$plugins->add_hook("admin_formcontainer_output_row", "gpcssborder_row");
}

function gpcssborder_do()
{
	
global $db, $mybb, $usergroup;
	
	$update_array = array(
		"gpcssborder1" => $db->escape_string($mybb->input['gpcssborder1']),
		"gpcssborder2" => $db->escape_string($mybb->input['gpcssborder2']),		
	);

	$db->update_query("usergroups", $update_array, "gid='".intval($usergroup['gid'])."'");

}

function gpcssborder_row(&$pluginargs)
{

//Add Row

global $db, $mybb, $lang, $user, $form, $form_container, $usergroup;

    $lang->load("gpcssborder");

if($pluginargs['title'] == $lang->misc)
{
	//Setting 1

		$gpcssborder1 = array(
			$form->generate_text_area('gpcssborder1', $usergroup['gpcssborder1'], array()),
			);
			
		$form_container->output_row("{$lang->gpcssborder_1_Title}", "{$lang->gpcssborder_1_Description}", "<div class=\"group_settings_bit\">".implode("</div><div class=\"group_settings_bit\">", $gpcssborder1)."</div>");

	//Setting 2

		$gpcssborder2 = array(
			$form->generate_text_area('gpcssborder2', $usergroup['gpcssborder2'], array()),
			);
			
		$form_container->output_row("{$lang->gpcssborder_2_Title}", "{$lang->gpcssborder_2_Description}", "<div class=\"group_settings_bit\">".implode("</div><div class=\"group_settings_bit\">", $gpcssborder2)."</div>");

}
}

function gpcssborder_post_prev(&$post)
{
	global $db, $mybb, $postbit, $templates;
	
	$group = usergroup_permissions($post['usergroup']);
	
	$post['gpcssborder1'] = $group['gpcssborder1'];
	$post['gpcssborder2'] = $group['gpcssborder2'];

	eval("\$postbit = \"".$templates->get("postbit")."\";");
	
}

function gpcssborder_post_pm(&$post)
{
	global $db, $mybb, $postbit, $templates;
	
	$group = usergroup_permissions($post['usergroup']);
	
	$post['gpcssborder1'] = $group['gpcssborder1'];
	$post['gpcssborder2'] = $group['gpcssborder2'];

	eval("\$postbit = \"".$templates->get("postbit")."\";");
	
}

function gpcssborder_post_announcement(&$post)
{
	global $db, $mybb, $postbit, $templates;
	
	$group = usergroup_permissions($post['usergroup']);
	
	$post['gpcssborder1'] = $group['gpcssborder1'];
	$post['gpcssborder2'] = $group['gpcssborder2'];

	eval("\$postbit = \"".$templates->get("postbit")."\";");
	
}

function gpcssborder_post(&$post)
{
	global $db, $mybb, $postbit, $templates;
	
	$group = usergroup_permissions($post['usergroup']);
	
	$post['gpcssborder1'] = $group['gpcssborder1'];
	$post['gpcssborder2'] = $group['gpcssborder2'];

	eval("\$postbit = \"".$templates->get("postbit")."\";");
	
}

?>