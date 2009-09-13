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

/* ensure users of this function have access to the admin page */
$page = Jojo_Plugin::getPage(Jojo::parsepage('admin'));
if (!$page->perms->hasPerm($_USERGROUPS, 'view')) {
  echo "You do not have permission to use this function";
  exit();
}

$filename = Jojo::getFormData('arg1', '');
if ($filename == 'undefined') $filename = '';

$filename = preg_replace('%^images/[swh]?[0-9]+/(.*)$%im', '$1', $filename); //get the filename rather than the uri

$frajax = new frajax();
$frajax->title = 'Delete image - ' . _SITETITLE;
$frajax->sendHeader();

if (Jojo::fileExists(_DOWNLOADDIR.'/'.$filename)) {
    $result = @unlink(_DOWNLOADDIR.'/'.$filename);
    $result = true;
}

if ($result) {
    $frajax->alert($filename.' deleted.');
    $frajax->script('parent.refreshImages('.ltrim(dirname($filename),'.').')');
    $frajax->script('parent.closeJpop()');
} else {
    $frajax->alert('Deleting '._DOWNLOADDIR.'/'.$filename.' failed.');
}

$frajax->sendFooter();