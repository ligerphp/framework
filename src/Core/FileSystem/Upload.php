<?php

use League\Flysystem\FilesystemInterface;

class Upload {

  public function single($uploadname,FilesystemInterface $filesystem){

    $stream = fopen($_FILES[$uploadname]['tmp_name'], 'r+');
    $filesystem->writeStream(
        'uploads/'.$_FILES[$uploadname]['name'],
        $stream
    );
    if (is_resource($stream)) {
        fclose($stream);
    }
  }
}