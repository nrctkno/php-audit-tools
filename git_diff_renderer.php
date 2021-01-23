<?php

function getParam($name, $default = null)
{
    return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
}

function getStyleForLine(string $line): string
{
    $styles = [
        '-' => 'background-color: #ffdddd;',
        '+' => 'background-color: #ddffdd;',
        '@' => 'background-color: #ffffdd;',
        'diff ' => 'background-color: #555555; color: white; display: block; margin-top: 3em;',
        '\ No newline at end of file' => 'font-style: italic;',
    ];

    $style = 'font-family: monospace;';

    foreach ($styles as $key => $details) {
        if (strpos($line, $key) === 0) {
            $style .= $details;
            break;
        }
    }

    return $style;
}

function processGitStatus(array $lines)
{

    foreach ($lines as $line) {
        $style = getStyleForLine($line);
        echo '<span style="' . $style . '">' . htmlentities($line) . "</span>\r\n";
    }
}

$path = getParam('path');

if (is_null($path)) {
    die('path not set. Try with ?path=my/local/path/to/git/repo/');
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

        <pre><code><?php processGitStatus($output); ?></code></pre>

    </body>
</html>