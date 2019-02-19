<?php
return [
    /* 前后端通信相关的配置 */
    'disk' => 'public',
    'upload' => [
        // 上传图片配置项
        'imageActionName' => 'uploadImage',
        'imageFieldName' => 'upImage',
        'imageMaxSize' => 2048000,
        'imageAllowFiles' => ['.png', '.jpg', '.jpeg', '.gif', '.bmp'],
        'imageAllowMimes' => ['png', 'jpg', 'jpeg', 'gif', 'bmp'],
        'imageCompressEnable' => true,
        'imageCompressBorder' => 736,
        'imageInsertAlign' => 'none',
        'imageUrlPrefix' => '',
        'imagePathFormat' => 'ueditor/images/{yyyy}{mm}{dd}/{time}{rand:6}',

        // 涂鸦图片上传配置项
        'scrawlActionName' => 'uploadScrawl',
        'scrawlFieldName' => 'upScrawl',
        'scrawlPathFormat' => 'ueditor/images/{yyyy}{mm}{dd}/{time}{rand:6}',
        'scrawlMaxSize' => 2048000,
        'scrawlUrlPrefix' => '',
        'scrawlInsertAlign' => 'none',

        // 截图工具上传
        'snapscreenActionName' => 'uploadImage',
        'snapscreenPathFormat' => 'ueditor/images/{yyyy}{mm}{dd}/{time}{rand:6}',
        'snapscreenUrlPrefix' => '',
        'snapscreenInsertAlign' => 'none',

        // 抓取远程图片配置
        'catcherLocalDomain' => ['127.0.0.1', 'localhost', 'img.baidu.com'],
        'catcherActionName' => 'catchImage',
        'catcherFieldName' => 'source',
        'catcherPathFormat' => 'ueditor/images/{yyyy}{mm}{dd}/{time}{rand:6}',
        'catcherUrlPrefix' => '',
        'catcherMaxSize' => 2048000,
        'catcherAllowFiles' => ['.png', '.jpg', '.jpeg', '.gif', '.bmp'],
        'catcherAllowMimes' => ['png', 'jpg', 'jpeg', 'gif', 'bmp'],

        // 上传视频配置
        'videoActionName' => 'uploadVideo',
        'videoFieldName' => 'upVideo',
        'videoPathFormat' => 'ueditor/videos/{yyyy}{mm}{dd}/{time}{rand:6}',
        'videoUrlPrefix' => '',
        'videoMaxSize' => 102400000,
        'videoAllowFiles' => ['.flv', '.swf', '.mkv', '.mp4', '.mp3'],
        'videoAllowMimes' => ['flv', 'swf', 'mkv', 'mp4', 'mp3'],

        // 上传文件配置
        'fileActionName' => 'uploadFile',
        'fileFieldName' => 'upFile',
        'filePathFormat' => 'ueditor/files/{yyyy}{mm}{dd}/{time}{rand:6}',
        'fileUrlPrefix' => '',
        'fileMaxSize' => 51200000,
        'fileAllowFiles' => ['.rar', '.zip', '.gz', '.bz2', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.pdf'],
        'fileAllowMimes' => ['rar', 'zip', 'gz', 'bz2', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf'],

        // 列出指定目录下的图片
        'imageManagerActionName' => 'listImage',
        'imageManagerListPath' => 'ueditor/images',
        'imageManagerListSize' => 20,
        'imageManagerUrlPrefix' => '',
        'imageManagerInsertAlign' => 'none',
        'imageManagerAllowFiles' => ['.png', '.jpg', '.jpeg', '.gif', '.bmp'],

        // 列出指定目录下的文件
        'fileManagerActionName' => 'listFile',
        'fileManagerListPath' => 'ueditor/files',
        'fileManagerUrlPrefix' => '',
        'fileManagerListSize' => 20,
        'fileManagerAllowFiles' => ['.rar', '.zip', '.gz', '.bz2', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.pdf']
    ],
];
