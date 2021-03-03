<?php
/*
usage:
$ php show_composer_lock_reqs.php dev|prod
$ php show_composer_lock_reqs.php dev|prod /my/base/dir/ (note the slash at the end)
*/

$env = (count($_SERVER['argv']) > 1 ? $_SERVER['argv'][1] : 'prod');
$dir = (count($_SERVER['argv']) > 2 ? $_SERVER['argv'][2] : '');

if (!in_array($env, ['dev', 'prod'])) {
	die('invalid environment');
}

$envs = ['require'];
if ($env == 'dev') {
	array_push($envs, 'require-dev');
}

empty($dir) || is_dir($dir) || die('invalid directory');

$path = $dir . 'composer.lock';
file_exists($path) || die('composer.lock not found');

$packages = json_decode(file_get_contents($path), true)['packages'];

$requirements = [];

foreach ($packages as $definition) {

	if (!array_key_exists($definition['name'], $requirements)) {
		$requirements[$definition['name']] = [];
	}

	$deps = [];
	foreach ($envs as $env) {
		$deps = array_merge(
			(isset($definition[$env]) ? $definition[$env] : []),
		);
	}

	$requirements[$definition['name']][$definition['version']] = ['(root)'];

	$deps = (isset($definition[$env]) ? $definition[$env] : []);

	foreach ($deps as $dep => $version) {
		if (!array_key_exists($dep, $requirements)) {
			$requirements[$dep] = [];
		}
		if (!array_key_exists($version, $requirements[$dep])) {
			$requirements[$dep][$version] = [];
		}
		array_push(
			$requirements[$dep][$version],
			$definition['name']
		);
	}
}

ksort($requirements);

echo json_encode($requirements, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
