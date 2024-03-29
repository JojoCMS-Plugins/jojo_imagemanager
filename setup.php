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
 * @package jojo_imagemanager
 */

// Manage Images
if (!Jojo::selectRow("SELECT pageid FROM {page} WHERE pg_url = 'admin/images'")) {
    echo "Adding <b>Manage Images</b> Page to menu<br />";
    Jojo::insertQuery("INSERT INTO {page} SET pg_title = 'Manage Images', pg_link = 'Jojo_Plugin_Admin_Images', pg_url = 'admin/images', pg_parent = ?, pg_order=2, pg_mainnav='yes', pg_secondarynav='no', pg_footernav='no', pg_breadcrumbnav='yes', pg_sitemapnav='no', pg_xmlsitemapnav='no', pg_index='no'", array($_ADMIN_CONTENT_ID));
}