<?php

namespace Yanthink\Ueditor\Http\Controllers;

use Illuminate\Http\Request;
use Yanthink\Ueditor\Contracts\Ueditor;
use Illuminate\Routing\Controller;

class UeditorController extends Controller
{
    protected $ueditor;

    public function __construct(Ueditor $ueditor)
    {
        $this->ueditor = $ueditor;
        // $this->ueditor->setResolvePath(function($path) {
        //     return \Storage::disk('qiniu')->downloadUrl($path);
        // });
    }

    public function init(Request $request)
    {
        $action = $request->input('action');

        if (!method_exists($this, $action)) {
            return ['state' => '您的请求不存在'];
        }

        return $this->{$action}();
    }

    public function config()
    {
        return $this->ueditor->getUploadConfig();
    }

    public function uploadImage()
    {
        return $this->ueditor->uploadImage();
    }

    public function uploadScrawl()
    {
        return $this->ueditor->uploadScrawl();
    }

    public function catchImage()
    {
        return $this->ueditor->catchImage();
    }

    public function uploadVideo()
    {
        return $this->ueditor->uploadVideo();
    }

    public function uploadFile()
    {
        return $this->ueditor->uploadFile();
    }

    public function listImage()
    {
        return $this->ueditor->listImage();
    }

    public function listFile()
    {
        return $this->ueditor->listFile();
    }
}
