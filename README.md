#C-L PHP API

此文件可以提供一个api来远程操作你的网站


### 1. 列出目录内容

- `action=list`

要列出指定目录中的所有文件和子目录，发送一个GET请求，将`action`参数设置为`list`，并可选地提供`path`参数来指定要操作的目录路径。

示例代码（PHP）：
```php
<?php

$url = 'http://api.example.com/file-api.php?action=list&path=/my-directory';

$response = file_get_contents($url);
$data = json_decode($response, true);

// 处理响应数据
if (isset($data['directoryContents'])) {
    foreach ($data['directoryContents'] as $item) {
        echo "名称: " . $item['name'] . "\n";
        echo "类型: " . $item['type'] . "\n";
        echo "路径: " . $item['path'] . "\n\n";
    }
} else {
    echo "无法获取目录内容。\n";
}
?>
```

### 2. 读取文件内容

- `action=read`

要读取指定文件的内容，发送一个GET请求，将`action`参数设置为`read`，并提供`path`参数以指定要操作的文件路径。

示例代码（PHP）：
```php
<?php

$url = 'http://api.example.com/file-api.php?action=read&path=/my-directory/file1.txt';

$response = file_get_contents($url);
$data = json_decode($response, true);

// 处理响应数据
if (isset($data['content'])) {
    echo "文件内容: " . $data['content'];
} else {
    echo "无法读取文件内容。\n";
}
?>
```

### 3. 创建目录或文件

- `action=create`

要创建一个新的目录或文件，发送一个GET请求，将`action`参数设置为`create`，并提供`type`参数以指定要创建的文件类型（`directory`表示目录，其他表示文件），同时可选地提供`path`参数来指定要操作的目录路径。

示例代码（PHP）：
```php
<?php

$url = 'http://api.example.com/file-api.php?action=create&type=directory&path=/my-directory/new-folder';

$response = file_get_contents($url);
$data = json_decode($response, true);

// 处理响应数据
if (isset($data['result'])) {
    echo $data['result'];
} else {
    echo "无法创建目录。\n";
}
?>
```

### 4. 删除目录或文件

- `action=delete`

要删除指定的目录或文件，发送一个GET请求，将`action`参数设置为`delete`，并提供`path`参数以指定要操作的目录或文件路径。

示例代码（PHP）：
```php
<?php

$url = 'http://api.example.com/file-api.php?action=delete&path=/my-directory/file1.txt';

$response = file_get_contents($url);
$data = json_decode($response, true);

// 处理响应数据
if (isset($data['result'])) {
    echo $data['result'];
} else {
    echo "无法删除目录或文件。\n";
}
?>
```

### 5. 重命名文件

- `action=rename`

要重命名指定的文件，发送一个GET请求，将`action`参数设置为`rename`，并提供`path`参数以指定要操作的文件路径，同时提供`newName`参数以指定新的文件名。

示例代码（PHP）：
```php
<?php

$url = 'http://api.example.com/file-api.php?action=rename&path=/my-directory/file1.txt&newName=new-name.txt';

$response = file_get_contents($url);
$data = json_decode($response, true);

// 处理响应数据
if (isset($data['result'])) {
    echo $data['result'];
} else {
    echo "无法重命名文件。\n";
}
?>
```

### 6. 远程下载文件

- `action=download`

要从给定的URL下载文件，并将其保存在指定目录中，发送一个GET请求，将`action`参数设置为`download`，并提供`url`参数以指定要下载的文件的URL，同时提供`path`参数以指定要保存文件的目录路径。

示例代码（PHP）：
```php
<?php

$url = 'http://api.example.com/file-api.php?action=download&url=http://example.com/file.pdf&path=/my-directory';

$response = file_get_contents($url);
$data = json_decode($response, true);

// 处理响应数据
if (isset($data['result'])) {
    echo $data['result'];
} else {
    echo "无法下载文件。\n";
}
?>
```

### 7. 写入文件内容

- `action=write`

要向指定的文件写入内容，发送一个POST请求，将`action`参数设置为`write`，并提供`path`参数以指定要操作的文件路径。同时，在POST数据中提供`content`参数以指定要写入的内容。

示例代码（PHP）：
```php
<?php

$url = 'http://api.example.com/file-api.php?action=write&path=/my-directory/file1.txt';

$data = array(
    'content' => 'Hello, World!'
);

$options = array(
    'http' => array(
        'method' => 'POST',
        'header' => 'Content-type: application/x-www-form-urlencoded',
        'content' => http_build_query($data)
    )
);

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);
$data = json_decode($response, true);

// 处理响应数据
if (isset($data['result'])) {
    echo $data['result'];
} else {
    echo "无法写入文件。\n";
}
?>
```
