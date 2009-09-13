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


$frajax = new frajax();
$frajax->title = 'Get folder contents - ' . _SITETITLE;
$frajax->sendHeader();

global $smarty;

require_once('../includes/insert-image-functions.inc.php');
$filename = str_replace('downloads/', '',$filename);

$resource = array();

$filetype = getfileextension($filename);
if (file_exists(_BASEPLUGINDIR . '/jojo_core/images/cms/filetypes/' . $filetype . '.gif')) {
    $resource['icon'] = "images/cms/filetypes/" . $filetype . " . gif";
} else {
    $resource['icon'] = "images/cms/filetypes/default.gif";
}

    //Get preview size image if jpg, gif, png
    if ( ($filetype == 'jpg') || ($filetype == 'jpeg') || ($filetype == 'gif') || ($filetype == 'png') ) {
        if (file_exists(_DOWNLOADDIR . "/" . $filename)) {


            //Get Image Dimensions
            $imagesize = getimagesize(_DOWNLOADDIR . "/" . $filename);
            if (!$imagesize) { //this would happen for a file that is labelled as an image, but isn't a valid format
                $resource['dimensions'] = "The image does not appear to be a valid format";
            } else {
                $resource['dimensions'] .= $imagesize[0]." x " . $imagesize[1]." pixels";
            }
            $previewsize = 180;
            if (($imagesize[0] > $previewsize) || ($imagesize[0] > $previewsize)) {
              $resource['preview'] = 'images/w'.$previewsize.'/' . $filename;
          } else {
              $resource['preview'] = 'images/default/' . $filename;
          }



        }
    }

//Get Filesize
$filesize = filesize(_DOWNLOADDIR . "/" . $filename);
$resource['filesize'] =  Jojo::roundBytes($filesize, 1);

$info = "<b>File Size</b>: " . $resource['filesize']."<br />\n<b>Dimensions:</b> " . $resource['dimensions']."<br />\n";

$frajax->assign("preview", "src",$resource['preview']);
$frajax->assign("info", "innerHTML",$info);
$frajax->assign("h1", "innerHTML",substr(basename($filename), 0, 35));
$frajax->assign("filename", "value",basename($filename));
$frajax->assign("fullname", "value",$filename);
$frajax->assign("pathname", "value",dirname($filename));


$frajax->sendFooter();