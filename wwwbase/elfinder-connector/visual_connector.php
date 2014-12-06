<?php

error_reporting(0); // Set E_ALL for debuging

include_once __DIR__ . '/elFinderConnector.class.php';
include_once __DIR__ . '/elFinder.class.php';
include_once __DIR__ . '/elFinderVolumeDriver.class.php';
include_once __DIR__ . '/elFinderVolumeFTP.class.php';
include_once __DIR__ . '/elFinderLogger.class.php';

include_once __DIR__ . '/../../phplib/util.php';
include_once __DIR__ . '/elFinderModToDB.class.php';

/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/

function access($attr, $path, $data, $volume) {
  return (strpos(basename($path), '.')) === 0       // if file/folder begins with '.' (dot)
    ? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
    :  null;                                    // else elFinder decide it itself
}

$myLogger = new elFinderSimpleLogger('../../log/visuallog');
$myModder = new elFinderModToDB();

// https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
$opts = array(
  'debug' => true,
  'bind'	=> array(
    'mkdir mkfile rename duplicate upload rm paste' => array($myLogger, 'log'),
    'upload rm rename paste' => array($myModder, 'action')
    ),
  'roots' => array(
    array(
      'driver'        => 'FTP',
      'host'          => Config::get('static.host'),
      'user'          => Config::get('static.user'),
      'pass'          => Config::get('static.password'),
      'path'          => Config::get('static.path') . 'img/visual/',
      'timeout'       => Config::get('static.timeout'),
      'URL'           => Config::get('static.url') . 'img/visual/',
      'alias'         => 'Ilustrații definiții',
      'accessControl' => 'access', // disable and hide dot starting files (OPTIONAL)
      'uploadAllow'   => array('image'), // mimetypes allowed to upload
      'disabled'      => array('resize', 'mkfile', 'duplicate'), // list of not allowed commands
      'imgLib'        => 'gd', // image manipulation library (imagick, mogrify, gd)
      'tmbPath'       => '.elfTmb', // directory name for image thumbnails. Set to "" to avoid thumbnails generation
    )
  )
);

// run elFinder
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();
