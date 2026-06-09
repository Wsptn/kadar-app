<?php
$files = [
    'd:/laragon/www/kadar-app/resources/views/pokok/pengurus/index.blade.php',
    'd:/laragon/www/kadar-app/resources/views/pokok/pengurus/edit.blade.php',
    'd:/laragon/www/kadar-app/resources/views/pokok/pengurus/create.blade.php',
    'd:/laragon/www/kadar-app/resources/views/master/pendidikan/index.blade.php',
    'd:/laragon/www/kadar-app/resources/views/master/pendidikan/edit.blade.php',
    'd:/laragon/www/kadar-app/resources/views/master/tugas/index.blade.php',
    'd:/laragon/www/kadar-app/resources/views/master/tugas/edit.blade.php'
];
foreach($files as $file) {
    if(file_exists($file)){
        $content = file_get_contents($file);
        $content = str_replace(['->id_tugas', '->id_pendidikan'], ['->id', '->id'], $content);
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
