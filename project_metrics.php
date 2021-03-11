<?php
/*
usage:
$ php project_metrics.php 
$ php project_metrics.php /my/base/dir/
*/
function strStartsWithAny(string $str, array $arr)
{
	foreach ($arr as $value) {
		if (strpos($str, $value) === 0) return true;
	}
	return false;
}

function getFilesInfo(string $dir, array $exclude, array $count_lines, $progess, int $tops = 20)
{
	$dir = str_replace(DIRECTORY_SEPARATOR, '/', $dir);

	$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

	$count = 0;
	$per_extension = [];
	$top_sizes = [];
	$top_lines = [];

	foreach ($iterator as $file) {
		if ($file->isDir()) continue;

		$fullname = $file->getPathname();
		$path = str_replace($dir, '', str_replace(DIRECTORY_SEPARATOR, '/', $fullname));

		if (strStartsWithAny($path, $exclude)) continue;

		$ext = $file->getExtension();

		try {
			$size = $file->getSize();
		} catch (\Exception $e) {
			$size = null;
		}

		$count++;
		isset($per_extension[$ext]) ? $per_extension[$ext]++ : $per_extension[$ext] = 0;

		$top_sizes[$path] = $size;
		arsort($top_sizes);
		$top_sizes = array_slice($top_sizes, 0, $tops);

		if (in_array($ext, $count_lines)) {
			try {
				$stream = new \SplFileObject($fullname);
				$stream->setFlags(SplFileObject::READ_AHEAD);
				$stream->seek(PHP_INT_MAX);

				$top_lines[$path] = $stream->key() + 1;
				arsort($top_lines);
				$top_lines = array_slice($top_lines, 0, $tops);
			} catch (\Exception $e) {
				echo $fullname . ": couldn't read the file\n";
			}
		}

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
		'top_lines' => $top_lines,
	];
}


$arg_dir = (count($_SERVER['argv']) > 1 ? $_SERVER['argv'][1] : '.');
$arg_excludes = (count($_SERVER['argv']) > 2 ? $_SERVER['argv'][2] : '');

empty($dir) || is_dir($dir) || die('invalid directory');

$exclude = ['.git/', 'app/cache/', 'vendor/'];
$count_lines = ['php'];

//---

$info = getFilesInfo($arg_dir, $exclude, $count_lines, function ($count) {
	echo $count . "\r";
});

echo "\n" . json_encode($info, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
