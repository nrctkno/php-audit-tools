<?php

function getParam($name, $default = null)
{
    return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
}

function getStyleForLine(string $line): string
{
    $styles = [
        'linerem' => 'background-color: #ffdddd;',
        'lineadd' => 'background-color: #ddffdd;',
        'meta' => 'background-color: #effaff; font-style: italic;',
        'command' => 'background-color: #555555; color: white; display: block; margin-top: 3em;',
        'notes' => 'font-style: italic;',
        'default' => 'color: #777777;',
    ];

    $rules = [
        'diff ' => 'command',
        '@' => 'meta',
        'index' => 'meta',
        '---' => 'meta',
        '+++' => 'meta',
        '-' => 'linerem',
        '+' => 'lineadd',
        '\ ' => 'meta',
    ];
    
    $style = $styles['default'];

    foreach ($rules as $key => $rule) {
        if (strpos($line, $key) === 0) {
            $style = $styles[$rule];
            break;
        }
    }

    return 'font-family: monospace; white-space: pre;' . $style;
}

function processGitDiffOutput(array $lines)
{
    foreach ($lines as $line) {
        $style = getStyleForLine($line);
        echo '<span style="' . $style . '">' . htmlentities($line) . "</span>\r\n";
    }
}

$path = getParam('path');

if (is_null($path)) {
    echo 'path not set. Try with <a href="?path=my/local/path/to/git/repo">?path=my/local/path/to/git/repo</a>';
    exit(0);
}

$output = [];
$retcode = [];

chdir($path);
exec('git diff', $output, $retcode);
?>

<!doctype html>
<html>
    <head>
        <title>Git diff renderer</title>
    </head>
    <body>
        <p>Path: <?php echo $path ?></p>
        <p>Exit code: <?php echo $retcode ?></p>

        <pre><?php processGitDiffOutput($output); ?></pre>
    </body>
</html>