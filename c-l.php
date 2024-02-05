<?php


// 确保提供了操作类型参数
if (!isset($_GET['action'])) {
    die("请提供一个操作类型参数，例如 ?action=list&path=/");
}

$action = $_GET['action'];
$path = isset($_GET['path']) ? $_GET['path'] : '.';

header('Content-Type: application/json');

function listDirectory($dir) {
    $result = [];
    foreach (scandir($dir) as $filename) {
        if ($filename == '.' || $filename == '..') continue;
        $filePath = realpath($dir . DIRECTORY_SEPARATOR . $filename);
        $entry = ['name' => $filename, 'path' => $filePath];

        if (is_dir($filePath)) {
            // 如果是目录，则递归调用listDirectory()函数获取目录内部的文件和目录
            $entry['type'] = 'directory';
            $entry['contents'] = listDirectory($filePath);
        } else {
            $entry['type'] = 'file';
        }

        $result[] = $entry;
    }
    return $result;
}


function createDirectory($path) {
    if (!file_exists($path)) {
        if (mkdir($path, 0777, true)) {
            return "目录创建成功";
        } else {
            return "目录创建失败";
        }
    } else {
        return "目录已存在";
    }
}

function deleteDirectory($dirPath) {
    if (is_dir($dirPath)) {
        $objects = scandir($dirPath);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                $objPath = $dirPath . "/" . $object;
                if (is_dir($objPath)) {
                    deleteDirectory($objPath);
                } else {
                    unlink($objPath);
                }
            }
        }
        rmdir($dirPath);
        return "目录删除成功";
    } else {
        return "指定的路径不是目录";
    }
}

switch ($action) {
    case 'list':
        echo json_encode(['directoryContents' => listDirectory($path)], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        break;
    case 'read':
        if (is_file($path)) {
            echo json_encode(['content' => file_get_contents($path)], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['error' => '指定的路径不是文件'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        break;
        
        //远程下载用户提交的url的文件并把它保存到目录下
    case 'download':
        if (isset($_GET['url'])) {
            $url = $_GET['url'];
            $newFilePath = $path . '/' . basename($url);
            if (file_put_contents($newFilePath, file_get_contents($url))) {
                echo json_encode(['result' => '文件下载成功'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['error' => '文件下载失败'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(['error' => '未提供URL'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        break;
    case 'rename':
        if (isset($_GET['newName'])) {
            $newPath = dirname($path) . '/' . $_GET['newName'];
            if (rename($path, $newPath)) {
                echo json_encode(['result' => '重命名成功'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['error' => '重命名失败'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(['error' => '未提供新名称'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        break;
    case 'create':
        if (isset($_GET['type']) && $_GET['type'] == 'directory') {
            echo json_encode(['result' => createDirectory($path)], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            // 默认为创建空文件
            if (file_put_contents($path, '') !== false) {
                echo json_encode(['result' => '文件创建成功'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['error' => '文件创建失败'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
        }
        break;
    case 'delete':
        if (is_dir($path)) {
            echo json_encode(['result' => deleteDirectory($path)], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } elseif (is_file($path)) {
            if (unlink($path)) {
                echo json_encode(['result' => '文件删除成功'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['error' => '文件删除失败'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(['error' => '指定的路径不存在'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        break;
    case 'write':
        if (isset($_POST['content'])) {
            if (file_put_contents($path, $_POST['content']) !== false) {
                echo json_encode(['result' => '文件写入成功'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['error' => '文件写入失败'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode(['error' => '未提供写入内容'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
        break;
    default:
        echo json_encode(['error' => '未知操作'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
