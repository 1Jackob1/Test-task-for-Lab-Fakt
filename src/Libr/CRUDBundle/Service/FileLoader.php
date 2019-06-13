<?php


namespace Libr\CRUDBundle\Service;


class FileLoader
{
    public function saveFile($src, $dist){
        try{
            move_uploaded_file($src, $dist);
        } catch (\Exception $e){
            throw $e;
        }
        return 1;
    }

}