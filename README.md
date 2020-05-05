# UEditor
百度UEditor服务端(定时维护)

## 安装

1) 打开终端执行下面命令:
```php
composer require goletter/ueditor
```

2) 打开 ```config/app.php``` 然后将下面内容添加到 ```providers``` 数组中:
```php
Goletter\Ueditor\UeditorServiceProvider::class,
```

3) 将下面内容添加到 ```config/app.php``` 文件的 ```aliases``` 数组中:
```php
'Ueditor' => Goletter\Ueditor\Facades\Ueditor::class,
```

4) 在终端执行下面命令:
```php
php artisan vendor:publish --provider="Goletter\Ueditor\UeditorServiceProvider"
```

## 配置Route
```php
    Route::match(['post', 'get'], 'ueditor/server', [
        'uses' => '\Goletter\Ueditor\Http\Controllers\UeditorController@init',
    ]);
```

## 配置Ueditor
```
    window.UEDITOR_CONFIG = {
      serverUrl: '/ueditor/server',
      ...,
    }
```