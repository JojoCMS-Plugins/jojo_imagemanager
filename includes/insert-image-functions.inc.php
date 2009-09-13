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

if (!function_exists('rscandir')) {
function rscandir($dir = './', $sort = 0) {
    global $files;

    $dir_open = @ opendir($dir);

    if (! $dir_open)
        return false;
    if (file_exists($dir) && ($dir != '.') && ($dir != '..') && ($dir != '.svn') ) {
      if (!is_file($dir)) {
            $files[str_replace(_BASEDIR.'/','',$dir)] = str_replace(_BASEDIR.'/','',$dir);
        } else {
            return false;
        }
    }
    while (($dir_content = readdir($dir_open)) !== false) {
        if ( ($dir_content != '.') & ($dir_content != '..') ){
            rscandir($dir.$dir_content.'/');
        }

    }
    return $files;
}
}

if (!function_exists('rscandirfolder')) {
function rscandirfolder($dir) {
    global $folders;

    $dir_open = @ opendir($dir);

    if (! $dir_open)
        return false;
    if (file_exists($dir) && ($dir != '.') && ($dir != '..') && ($dir != '.svn') ) {
      if (!is_file($dir)) {
            $files[str_replace(_BASEDIR.'/','',$dir)] = str_replace(_BASEDIR.'/','',$dir);
        } else {
            return false;
        }
    }
    while (($dir_content = readdir($dir_open)) !== false) {
        if ( ($dir_content != '.') & ($dir_content != '..') ){
            rscandir($dir.$dir_content.'/');
        }
  }
    return $files;
}
}




function getImageList($folder='') {
  global $smarty;
    $cleanfolder = preg_replace('/^downloads(.*)/', '$1', $folder);
    $cleanfolder = trim($cleanfolder,'/');
    $folder = trim($folder,'/');
    $smarty->assign('folder', $folder);
    $smarty->assign('cleanfolder', $cleanfolder);
    echo 'folder='.$folder;
    $res = scandir(realpath(_DOWNLOADDIR.'/'.$folder));

    $resources = array();
    $i = 0;
    foreach ($res as $filename) {
        if ( ($filename != '.') && ($filename != '..') && ($filename != 'Thumbs.db') && ($filename != '.svn') ) {

        //Get filetype icon to display
        $filetype = Jojo::getFileExtension($filename);

        if ( ($filetype == 'jpg') || ($filetype == 'jpeg') || ($filetype == 'gif') || ($filetype == 'png') ) {

          if (file_exists(_BASEPLUGINDIR.'/jojo_core/images/cms/filetypes/'.$filetype.'.gif')) { //display logo image (dependent on file extension) if one exists, otherwise use the default (txt)
              $icon = 'images/cms/filetypes/'.$filetype.'.gif';
          } else {
              $icon = 'images/cms/filetypes/default.gif';
          }

          $resources[$i]['name'] = $filename;
          $resources[$i]['icon'] = $icon;
          //Decide on thumbnail
          if ( ($filetype == 'jpg') || ($filetype == 'jpeg') || ($filetype == 'gif') || ($filetype == 'png') ) {
            $resources[$i]['thumbnail'] = "images/150/$cleanfolder/$filename";
          } else {
            $resources[$i]['thumbnail'] = $icon;
          }
          $resources[$i]['filename'] = $filename;
          $resources[$i]['filepath'] = $cleanfolder.'/'.$filename;

          $i++;
          }
        }
    }

  return $resources;
}


function getImageList2($folder='') {
  global $smarty;
  echo 'folder='.$folder;
  //if ($folder == '') {$folder = 'downloads';}
    $cleanfolder = str_replace('downloads','',$folder);
    $cleanfolder = trim($cleanfolder,'/');
    $folder = trim($folder,'/');
    $smarty->assign('folder', $folder);
    $smarty->assign('cleanfolder', $cleanfolder);
    echo 'folder='.$folder;
    $res = scandir(_DOWNLOADDIR.'/../'.$folder);

    $resources = array();
    $i = 0;
    foreach ($res as $resource) {
        if ( ($resource != '.') && ($resource != '..') && ($resource != 'Thumbs.db') && ($resource != '.svn') ) {

        //Get filetype icon to display

        $filename = $resource;
        $filetype = strtolower(Jojo::getFileExtension($resource));

        if ( ($filetype == 'jpg') || ($filetype == 'jpeg') || ($filetype == 'gif') || ($filetype == 'png') ) {

          if (file_exists(_BASEPLUGINDIR."/jojo_core/images/cms/filetypes/".$filetype.".gif")) { //display logo image (dependent on file extension) if one exists, otherwise use the default (txt)
            $icon = "images/cms/filetypes/".$filetype.".gif";
          } else {
            $icon = "images/cms/filetypes/default.gif";
          }

          $resources[$i]['name'] = $resource;
          $resources[$i]['icon'] = $icon;
          //Decide on thumbnail
          if ( ($filetype == 'jpg') || ($filetype == 'jpeg') || ($filetype == 'gif') || ($filetype == 'png') ) {
            $resources[$i]['thumbnail'] = "images/80/".$cleanfolder."/".$filename;
          } else {
            $resources[$i]['thumbnail'] = $icon;
          }
          $resources[$i]['filename'] = $filename;
          $resources[$i]['filepath'] = $cleanfolder."/".$filename;

          $i++;
          }
        }
    }

  return $resources;
}