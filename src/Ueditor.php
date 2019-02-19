<?php

namespace Yanthink\Ueditor;

use Closure;
use Exception;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Factory as Validator;
use Yanthink\Ueditor\Contracts\Ueditor as UeditorInteface;

class Ueditor implements UeditorInteface
{
    protected $request;

    protected $validator;

    protected $storage;

    protected $resolvePath;

    protected $config;

    protected $fieldName;

    protected $allowFiles;

    protected $allowMimes;

    protected $maxSize;

    protected $pathFormat;

    protected $catcherLocalDomain;

    protected $disk;

    protected $stateInfo;

    public function __construct(Request $request, Validator $validator, Storage $storage, array $config)
    {
        $this->request = $request;

        $this->validator = $validator;

        $this->storage = $storage;

        $this->config = $config;

        $this->disk = $this->config['disk'];
    }

    public function setResolvePath($callback)
    {
        $this->resolvePath = $callback;

        return $this;
    }

    public function getUploadConfig()
    {
        return $this->config['upload'];
    }

    public function uploadImage()
    {
        $this->fieldName = $this->config['upload']['imageFieldName'];
        $this->allowFiles = $this->config['upload']['imageAllowFiles'];
        $this->allowMimes = $this->config['upload']['imageAllowMimes'];
        $this->maxSize = $this->config['upload']['imageMaxSize'];
        $this->pathFormat = $this->config['upload']['imagePathFormat'];

        return $this->upFile();
    }

    public function uploadScrawl()
    {
        $this->fieldName = $this->config['upload']['scrawlFieldName'];
        $this->maxSize = $this->config['upload']['scrawlMaxSize'];
        $this->pathFormat = $this->config['upload']['scrawlPathFormat'];

        return $this->upBase64();
    }

    public function catchImage()
    {
        $this->fieldName = $this->config['upload']['catcherFieldName'];
        $this->allowFiles = $this->config['upload']['catcherAllowFiles'];
        $this->allowMimes = $this->config['upload']['catcherAllowMimes'];
        $this->maxSize = $this->config['upload']['catcherMaxSize'];
        $this->pathFormat = $this->config['upload']['catcherPathFormat'];
        $this->catcherLocalDomain = $this->config['upload']['catcherLocalDomain'];

        return $this->saveRemote();
    }

    public function uploadVideo()
    {
        $this->fieldName = $this->config['upload']['videoFieldName'];
        $this->allowMimes = $this->config['upload']['videoAllowMimes'];
        $this->allowFiles = $this->config['upload']['videoAllowFiles'];
        $this->maxSize = $this->config['upload']['videoMaxSize'];
        $this->pathFormat = $this->config['upload']['videoPathFormat'];

        return $this->upFile();
    }

    public function uploadFile()
    {
        $this->fieldName = $this->config['upload']['fileFieldName'];
        $this->allowFiles = $this->config['upload']['fileAllowFiles'];
        $this->allowMimes = $this->config['upload']['fileAllowMimes'];
        $this->maxSize = $this->config['upload']['fileMaxSize'];
        $this->pathFormat = $this->config['upload']['filePathFormat'];

        return $this->upFile();
    }

    public function listImage()
    {
        $allowFiles = $this->config['upload']['imageManagerAllowFiles'];
        $listSize = $this->config['upload']['imageManagerListSize'];
        $path = $this->config['upload']['imageManagerListPath'];

        return $this->processList($allowFiles, $listSize, $path);
    }

    public function listFile()
    {
        $allowFiles = $this->config['upload']['fileManagerAllowFiles'];
        $listSize = $this->config['upload']['fileManagerListSize'];
        $path = $this->config['upload']['fileManagerListPath'];

        return $this->processList($allowFiles, $listSize, $path);
    }

    protected function upFile()
    {
        try {
            $file = $this->request->file($this->fieldName);

            $rule = [
                $this->fieldName => 'required|mimes:' . join(',', $this->allowMimes) . '|max:' . $this->maxSize,
            ];

            $validator = $this->validator->make([$this->fieldName => $file], $rule);

            if ($validator->fails()) {
                abort(422, $validator->getMessageBag()->first());
            }

            if (!$file->isValid()) {
                abort(422, '文件无效');
            }

            $fileSize = $file->getClientSize();
            $ext = $file->getClientOriginalExtension();
            $filename = $file->getClientOriginalName();

            $path = $this->getPath($filename);

            $file->storeAs(dirname($path), basename($path), $this->disk);

            return [
                'state' => 'SUCCESS',
                'url' => $this->getUrl($path),
                'title' => $filename,
                'original' => $filename,
                'type' => $ext,
                'size' => $fileSize,
            ];

        } catch (Exception $exception) {
            return [
                'state' => $exception->getMessage(),
            ];
        }
    }

    protected function upBase64()
    {
        try {
            $base64Data = $this->request->input($this->fieldName);
            $img = base64_decode($base64Data);
            $fileSize = strlen($img);

            if ($fileSize > $this->maxSize) {
                abort(422, '图片大小不能大于' . ($this->maxSize / 1024) . 'kb');
            }

            $filename = 'scrawl.png';

            $path = $this->getPath($filename);

            $this->storage->disk($this->disk)->put($path, $img);

            return [
                'state' => 'SUCCESS',
                'url' => $this->getUrl($path),
                'title' => $filename,
                'original' => $filename,
                'type' => 'png',
                'size' => $fileSize,
            ];

        } catch (Exception $exception) {
            return [
                'state' => $exception->getMessage(),
            ];
        }
    }

    protected function saveRemote()
    {
        try {
            $list = [];

            $source = $this->request->input($this->fieldName);
            foreach ($source as $imgUrl) {
                $imgUrl = htmlspecialchars($imgUrl);
                $imgUrl = str_replace('&amp;', '&', $imgUrl);

                if (!in_array(array_get(parse_url($imgUrl), 'host'), $this->catcherLocalDomain)) {
                    continue;
                }

                $filename = basename($imgUrl);

                // http开头验证
                if (strpos($imgUrl, 'http') !== 0) {
                    abort(422, 'url不是http协议');
                }

                // 获取请求头并检测死链
                $heads = @get_headers($imgUrl, 1);
                if (!(stristr($heads[0], '200') && stristr($heads[0], 'OK'))) {
                    abort(422, '未找到图片');
                }

                // 格式验证(扩展名验证和Content-Type验证)
                $fileType = pathinfo($filename, PATHINFO_EXTENSION);
                if (!in_array('.' . $fileType, $this->allowFiles) || !isset($heads['Content-Type']) || !stristr($heads['Content-Type'], 'image')) {
                    abort(422, '未找到图片或图片格式不正确');
                }

                // 打开输出缓冲区并获取远程图片
                ob_start();
                $context = stream_context_create(['http' => ['follow_location' => false]]);
                readfile($imgUrl, false, $context);
                $img = ob_get_contents();
                ob_end_clean();

                $fileSize = strlen($img);

                if ($fileSize > $this->maxSize) {
                    abort(422, '图片大小不能大于' . ($this->maxSize / 1024) . 'kb');
                }

                $path = $this->getPath($filename);

                $this->storage->disk($this->disk)->put($path, $img);

                $list = [
                    'state' => 'SUCCESS',
                    'url' => $this->getUrl($path),
                    'title' => $filename,
                    'original' => $filename,
                    'type' => 'png',
                    'size' => $fileSize,
                    'source' => $imgUrl,
                ];

            }

            return [
                'state' => count($list) ? 'SUCCESS' : 'ERROR',
                'list' => $list
            ];
        } catch (Exception $exception) {
            return [
                'state' => $exception->getMessage(),
            ];
        }
    }

    protected function processList($allowFiles, $listSize, $path)
    {
        $allFiles = $this->storage->disk($this->disk)->allFiles($path);

        $files = [];
        foreach ($allFiles as $file) {
            if (ends_with($file, $allowFiles)) {
                $files[] = [
                    'url' => $this->getUrl($file),
                ];
            }
        }

        $size = $this->request->input('size', $listSize);
        $start = $this->request->input('start', 0);

        if (!count($files)) {
            return [
                'state' => 'no match file',
                'list' => [],
                'start' => $start,
                'total' => count($files),
            ];
        }

        $len = count($files);

        $list = array_slice($files, $start, $size);

        return [
            'state' => 'SUCCESS',
            'list' => $list,
            'start' => $start,
            'total' => $len
        ];
    }

    protected function getUrl($path)
    {
        $resolvePath = $this->resolvePath;
        if ($resolvePath && $resolvePath instanceof Closure) {
            return $resolvePath($path);
        }

        return $this->storage->disk($this->disk)->url($path);
    }

    protected function getPath($filename)
    {
        $d = explode('-', date('Y-y-m-d-H-i-s'));
        $format = ltrim($this->pathFormat);
        $format = str_replace('{yyyy}', $d[0], $format);
        $format = str_replace('{yy}', $d[1], $format);
        $format = str_replace('{mm}', $d[2], $format);
        $format = str_replace('{dd}', $d[3], $format);
        $format = str_replace('{hh}', $d[4], $format);
        $format = str_replace('{ii}', $d[5], $format);
        $format = str_replace('{ss}', $d[6], $format);
        $format = str_replace('{time}', time(), $format);
        $format = str_replace('{filename}', pathinfo(basename($filename), PATHINFO_BASENAME), $format);
        if (preg_match('/\{rand\:([\d]*)\}/i', $format, $matches)) {
            $format = preg_replace('/\{rand\:[\d]*\}/i', str_random($matches[1] ?: 6), $format);
        }

        return $format . '.' . pathinfo(basename($filename), PATHINFO_EXTENSION);
    }
}
