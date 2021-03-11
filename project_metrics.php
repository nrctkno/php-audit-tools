<?php
/*
usage:
$ php project_metrics.php 
$ php project_metrics.php /my/base/dir/
*/

function getFilesInfo($dir, $exclude, $progess, $tops = 20)
{
	$dir = str_replace(DIRECTORY_SEPARATOR, '/', $dir);

	$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

	$count = 0;
	$per_extension = [];
	$top_sizes = [];

	foreach ($iterator as $file) {
		if ($file->isDir()) continue;

		$path = str_replace($dir, '', str_replace(DIRECTORY_SEPARATOR, '/', $file->getPathname()));

		$ext = $file->getExtension();

		try {
			$size = $file->getSize();
		} catch (\Exception $e) {
			$size = 0;
		}

		$count++;
		isset($per_extension[$ext]) ? $per_extension[$ext]++ : $per_extension[$ext] = 0;

		$top_sizes[$path] = $size;
		arsort($top_sizes);
		$top_sizes = array_slice($top_sizes, 0, $tops);

		$progess($count);
	}

	arsort($per_extension);
	$top_sizes = array_map(function ($e) {
		return round($e / 1024, 2);
	}, $top_sizes);

	return [
		'count' => $count,
		'top_extensions' => array_slice($per_extension, 0, $tops),
		'top_sizes_in_kb' => $top_sizes,
	];
}


$arg_dir = (count($_SERVER['argv']) > 1 ? $_SERVER['argv'][1] : '.');
$arg_excludes = (count($_SERVER['argv']) > 2 ? $_SERVER['argv'][2] : '');

empty($dir) || is_dir($dir) || die('invalid directory');

$excludes = [];

//---

$info = getFilesInfo($arg_dir, $excludes, function ($count) {
	echo $count . "\r";
});

echo "\n" . json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
