<?php

namespace Goletter\Ueditor\Http\Controllers;

use Illuminate\Http\Request;
use Goletter\Ueditor\Contracts\Ueditor;
use Illuminate\Routing\Controller;
use JWTAuth;
use Exception;

class UeditorController extends Controller
{
    protected $ueditor;

    public function __construct(Ueditor $ueditor)
    {
        $this->ueditor = $ueditor;
    }

    public function init(Request $request)
    {
        $action = $request->input('action');

        if (!method_exists($this, $action)) {
            return ['state' => '您的请求不存在'];
        }

        if ($action != 'config') {
            try {
                $token = $request->input('token');
                JWTAuth::setToken($token)->authenticate();
            } catch (Exception $exception) {
                return ['state' => '没有权限操作'];
            }
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
