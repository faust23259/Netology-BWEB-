<?php
// Базовая директория (например, текущая папка скрипта)
$baseDir = __DIR__;

// Получаем путь из параметра URL (например, ?path=/folder)
$path = isset($_GET['path']) ? $_GET['path'] : '';
$fullPath = realpath($baseDir . '/' . $path);

// Проверка безопасности: путь должен быть внутри базовой директории
if ($fullPath === false || strpos($fullPath, $baseDir) !== 0) {
    die('Недопустимый путь');
}

// Функция для получения списка файлов и папок
function getDirContents($dir) {
    $results = [];
    $files = scandir($dir);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $filePath = $dir . '/' . $file;
        $results[] = [
            'name' => $file,
            'type' => is_dir($filePath) ? 'folder' : 'file',
            'size' => is_file($filePath) ? filesize($filePath) : 0,
            'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
            'path' => str_replace(realpath(__DIR__), '', $filePath)
        ];
    }

    return $results;
}

$contents = getDirContents($fullPath);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Структура папки: <?php echo htmlspecialchars($path ?: '/'); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .folder { font-weight: bold; }
        a { text-decoration: none; color: #007bff; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2>Папка: <?php echo htmlspecialchars($path ?: '/'); ?></h2>
    <table>
        <tr>
            <th>Имя</th>
            <th>Тип</th>
            <th>Размер (байт)</th>
            <th>Дата изменения</th>
        </tr>
        <?php foreach ($contents as $item): ?>
            <tr class="<?php echo $item['type']; ?>">
                <td>
                    <?php if ($item['type'] === 'folder'): ?>
                        <a href="?path=<?php echo urlencode(trim($item['path'], '/')); ?>"><?php echo htmlspecialchars($item['name']); ?></a>
                    <?php else: ?>
                        <?php echo htmlspecialchars($item['name']); ?>
                    <?php endif; ?>
                </td>
                <td><?php echo $item['type'] === 'folder' ? 'Папка' : 'Файл'; ?></td>
                <td><?php echo $item['type'] === 'file' ? $item['size'] : '-'; ?></td>
                <td><?php echo $item['modified']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>