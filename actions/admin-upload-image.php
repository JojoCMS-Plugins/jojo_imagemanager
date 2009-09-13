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

/* reset the upload form */
$name = Jojo::getFormData('arg1', '');
if (($name == 'reset') || isset($_GET['reset'])) {
    $frajax = new frajax();
    $frajax->title = 'Upload Image - ' . _SITETITLE;
    $frajax->sendHeader();
    $frajax->assign('upload-form','innerHTML',$smarty->fetch('admin/image-upload-form.tpl'));
    $frajax->script('parent.resetUpload();');

    $frajax->sendFooter();
    exit();
}

if (isset($_GET['jqUploader']) && ($_GET['jqUploader'] == 1)) {

    /* match key in GET to an authenticated user */
    $key = $_GET['key'];
    $users = Jojo::selectQuery("SELECT userid FROM {user} WHERE SHA1(CONCAT(us_login,'salt',us_password)) = ?", $key);
    if (!count($users)) {
        $frajax->alert('Unable to authenticate this file upload. If you think this is in error, please contact the webmaster.');
        exit();
    }
    /* test permissions */
    //$page = Jojo_Plugin::getPage(false, 'admin/images', false);
    $page = Jojo_Plugin::getPage(Jojo::parsepage('admin/images'));
    foreach ($users as $user) {
        /* get the groups for this user */
        $userid = $user['userid'];
        $membership = Jojo::selectQuery("SELECT * FROM {usergroup_membership} WHERE userid = ?", $userid);
        $groups = array('everyone');
        foreach ($membership as $m) {
            $groups[] = $m['groupid'];
        }
        /* ensure the admin/images page is visible to this user */
        if (!$page->perms->hasPerm($groups, 'view')) {
            $frajax->alert('Unable to authenticate this file upload. If you think this is in error, please contact the webmaster.');
            exit();
        }
    }

    //$uploadDir = dirname(__FILE__) . '/files/';
    //$uploadFile = $uploadDir . basename($_FILES['Filedata']['name']);
    if (($_GET['folder']=='null') || ($_GET['folder']=='undefined')) $_GET['folder'] = '';
    $uploadDir = !empty($_GET['folder']) ? _DOWNLOADDIR.'/'.trim($_GET['folder'],'/') : _DOWNLOADDIR;
    $uploadDir = rtrim($uploadDir,'/');
    $uploadFile = $uploadDir.'/'.basename($_FILES['Filedata']['name']);
    Jojo::insertQuery("REPLACE INTO {option} SET op_name='foo', op_value=?", array($uploadFile));
    if ($_FILES['Filedata']['name']) {
        if (move_uploaded_file ($_FILES['Filedata']['tmp_name'], $uploadFile)) {

            //$frajax->assign('upload-form','innerHTML',$smarty->fetch('admin/image-upload-form.tpl'));
            //$frajax->script('parent.resetUpload();');
            return $uploadFile;

        } else $frajax->alert('Error: unable to move temp file to '.$uploadFile);
    } else {
        if ($_FILES['Filedata']['error']) {
        return $_FILES['Filedata']['error'];
        //$frajax->alert('Error: '.$_FILES['Filedata']['error']);
        }
    }

    exit();
}

/* ensure users of this function have access to the admin page */
$page = Jojo_Plugin::getPage(Jojo::parsepage('admin'));
if (!$page->perms->hasPerm($_USERGROUPS, 'view')) {
    echo "You do not have permission to use this function";
    exit();
}

$name = Jojo::getFormData('arg1', '');

$frajax = new frajax();
$frajax->title = 'Upload Image - ' . _SITETITLE;
$frajax->sendHeader();

if (isset($_FILES['uploadimage'])) {

    $filename = $_FILES['uploadimage']['name'];

    /* We must not allow PHP files to be uploaded to the server - dangerous */
    $ext = Jojo::getFileExtension($filename);
    if ( ($ext == 'php') || ($ext == 'php3') || ($ext == 'php4') || ($ext == 'inc') || ($ext == 'phtml')) {
        $frajax->alert('You cannot upload PHP files into this system for security reasons. If you really need to, please Zip them first and upload the Zip file.');
        $frajax->sendFooter();
        exit();
    }

    //Check error codes
    switch ($_FILES['uploadimage']['error']) {
        case UPLOAD_ERR_INI_SIZE: //1
               $error = "The uploaded file exceeds the maximum size allowed (1Mb)";
            break;
        case UPLOAD_ERR_FORM_SIZE: //2
               $error = "The uploaded file exceeds the maximum size allowed in PHP.INI";
            break;
        case UPLOAD_ERR_PARTIAL: //3
               $error = "The file has only been partially uploaded. There may have been an error in transfer, or the server may be having technical problems . ";
            break;
        case UPLOAD_ERR_NO_FILE: //4 - this is only a problem if it's a required field
            $error = "File missing";
            break;
        case 6: // UPLOAD_ERR_NO_TMP_DIR - for some odd reason the constant wont work
            $error = "There is no temporary folder on the server";
            //log for administrator
            break;
        case UPLOAD_ERR_OK: //0
            //check for empty file
            if($_FILES['uploadimage']["size"] == 0) {
                $error = "The uploaded file is empty . ";
            }
            if (!is_uploaded_file($_FILES['uploadimage']['tmp_name'])) { //improve this code when you have time - will work, but needs fleshing out
                $frajax->alert('The write permissions may not be set correctly on this folder. Please contact the administrator.');
                exit();
            }


               /* Rename files on the way up to be search engine friendly - no spaces, no caps, no special chars */
              $pieces = explode('.',$filename);
              if (count($pieces) > 1) {
                $newfilename = '';
                $n = count($pieces)-1;
                for ($i = 0;$i<$n;$i++) {
                  $newfilename .=  Jojo::cleanURL($pieces[$i]);
                }
                $newfilename .= '.'.strtolower($pieces[count($pieces)-1]);
                $filename = $newfilename;
              }


               /* All appears good, so attempt to move to final resting place */

            if (!isset($_POST['destinationfolder'])) $_POST['destinationfolder'] = '';
            $destination = _DOWNLOADDIR.'/'.$_POST['destinationfolder'];
            $destination = rtrim($destination,'/').'/'.basename($filename);
            /* ensure the destination is within _DOWNLOADDIR */
            if (!preg_match('%^'._DOWNLOADDIR.'(.*)\\z%im', $destination)) {
                $frajax->alert('Destination folder ('.$destination.') out of bounds');
                exit();
           }

            //Ensure file does not already exist on server, rename if it does

            if (move_uploaded_file($_FILES['uploadimage']['tmp_name'], $destination)) {
              $frajax->alert('File uploaded');


              /* reload parts of the UI */
              /* file list */
              require_once(dirname(dirname(__FILE__)) . '/includes/insert-image-functions.inc.php');
              $resources = getImageList($folder);
              $smarty->assign('resources',$resources);
              $output = $smarty->fetch('insert-image-files.tpl');
              $frajax->assign("files", "innerHTML",$output);
              /* preview image */
              /*----------------BEGIN BIG NASTY HACK--------------------*/
              //This code is copied from admin-get-image-info.php - this needs to be put in a class or function to avoid this really bad code re-use
              $resource = array();

              $filetype = strtolower(Jojo::getFileExtension($filename));

            if (file_exists(_BASEPLUGINDIR . '/jojo_core/images/cms/filetypes/' . $filetype . '.gif')) {
                $filelogo = "images/cms/filetypes/" . $filetype . " . gif";
            } else {
                $filelogo = "images/cms/filetypes/default.gif";
            }
            $resource['icon'] = $filelogo;

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
            $previewsize = 200;
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

/*
$frajax->assign("preview", "src",$resource['preview']);
$frajax->assign("info", "innerHTML",$info);
$frajax->assign("h1", "innerHTML",substr(basename($filename), 0, 35));
$frajax->assign("filename", "value",basename($filename));
$frajax->assign("fullname", "value",$filename);
$frajax->assign("pathname", "value",dirname($filename));
*/

/*----------------END BIG NASTY HACK--------------------*/




            } else {
                $frajax->alert('The file upload failed. Please contact the webmaster . ');
                exit();
            }
            break;
        default:
            //this code shouldn't execute - 0 should be the default
    }

} else {
    echo "";
}
$frajax->script("parent.uploadFinished()");
$frajax->sendFooter();
