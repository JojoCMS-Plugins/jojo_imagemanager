<?php
/**
 *                    Jojo CMS
 *                ================
 *
 * Copyright 2007-2008 Harvey Kane <code@ragepank.com>
 * Copyright 2007-2008 Michael Holt <code@gardyneholt.co.nz>
 * Copyright 2007 Melanie Schulz <mel@gardyneholt.co.nz>
 *
 * See the enclosed file license.txt for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Harvey Kane <code@ragepank.com>
 * @author  Michael Cochrane <mikec@jojocms.org>
 * @author  Melanie Schulz <mel@gardyneholt.co.nz>
 * @license http://www.fsf.org/copyleft/lgpl.html GNU Lesser General Public License
 * @link    http://www.jojocms.org JojoCMS
 * @package jojo_core
 */

class Jojo_Plugin_Admin_images extends Jojo_Plugin
{

    function _getContent()
    {
        global $smarty, $_USERID;

        Jojo_Plugin_Admin::adminMenu();

        /* prepare a key for authenticating the flash request */
        $users = Jojo::selectQuery("SELECT us_login, us_password FROM {user} WHERE userid = ?", array($_USERID));
        if (count($users)) {
            $key = sha1($users[0]['us_login'] . 'salt' . $users[0]['us_password']);
        }
        $smarty->assign('key', $key);

        $content = array();
        $content['head'] = $smarty->fetch('admin/images_head.tpl');
        $content['content'] = $smarty->fetch('admin/images.tpl');
        $content['css'] = $smarty->fetch('admin/images_css.tpl');
        $content['javascript'] = $smarty->fetch('admin/images_js.tpl');

        return $content;
    }
}