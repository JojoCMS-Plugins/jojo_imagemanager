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

$folder = Jojo::getFormData('arg1', '');
if ($folder=='undefined') $folder = '';

$absolutefolder = strtr(realpath(rtrim(_DOWNLOADDIR.'/'.$folder,'/')),'\\','/');
/* ensure we haven't ventured above /downloads/ */
if (str_ireplace(_DOWNLOADDIR, '', $absolutefolder) == $absolutefolder) {
    $absolutefolder = _DOWNLOADDIR;
}
$relativefolder = trim(str_ireplace(_DOWNLOADDIR, '', $absolutefolder),'/');



$frajax = new frajax();
$frajax->title = 'Get folder contents - ' . _SITETITLE;
$frajax->sendHeader();

//$resources = getImageList($folder);

//$output = $smarty->fetch('insert-image-files.tpl');


$filesfolders = scandir($absolutefolder);
sort($filesfolders);

$n = count($filesfolders);

$parentfolders = array();
$folders = array();
$files = array();

if (!empty($relativefolder)) {
    $resource['filename'] = '..';
    $resource['icon'] = 'images/cms/admin/folder.gif';
    $resource['title'] = 'up';
    $resource['type'] = 'folder';
    $parentfolders[] = $resource;
}

for ($i=0;$i<$n;$i++) {
    $f = $filesfolders[$i];
    $resource = array();
    if (($f=='.') || ($f=='..') || ($f=='.svn')) {
        //ignore these folders
    } elseif (is_dir($absolutefolder.'/'.$f)) {
        $resource['filename'] = $f;
        $resource['icon'] = 'images/cms/admin/folder.gif';
        $resource['title'] = $f;
        $resource['type'] = 'folder';
        $folders[] = $resource;
    } else {
        $resource['filename'] = $f;
        $resource['icon'] = 'images/s80/'.$f;
        $resource['title'] = $f;
        $resource['type'] = 'file';
        $d = getimagesize($absolutefolder.'/'.$f);
        $resource['width'] = $d[0];
        $resource['height'] = $d[1];
        $resource['filesize'] = Jojo::roundBytes(filesize($absolutefolder.'/'.$f),1);
        if ($d[0] && $d[1]) $files[] = $resource;
    }
}
$folders = array_merge($parentfolders, $folders);


$smarty->assign('folder',$folder);
$smarty->assign('relativefolder',$relativefolder);
$smarty->assign('absolutefolder',$absolutefolder);
$smarty->assign('folders',$folders);
$smarty->assign('files',$files);

$output = $smarty->fetch('insert-image-files.tpl');

$frajax->assign("files", "innerHTML", $output);
$frajax->script("parent.$('#files a.lightbox').click(function(){parent.$('#image-details').html(
'<img src=\"'+parent.$(this).attr('href')+'\" alt=\"\" style=\"float:left\"/><div><button onclick=\"frajax(\'admin-delete-image\',\''+parent.$(this).attr('href')+'\');return false;\">Delete</button></div>'
);
parent.jpop(parent.$('#image-details'),800,500);
return false;});");
$frajax->script("parent.listColumns()");

$frajax->sendFooter();