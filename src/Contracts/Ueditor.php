<?php

namespace Yanthink\Ueditor\Contracts;

interface Ueditor
{
    public function getUploadConfig();

    public function uploadImage();

    public function uploadScrawl();

    public function catchImage();

    public function uploadVideo();

    public function uploadFile();

    public function listImage();

    public function listFile();

    public function setResolvePath($callback);
}
