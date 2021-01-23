<?php

class Request
{

    public static function getParam($name, $default = null, $empty_msg = null)
    {
        if (isset($_REQUEST[$name])) {
            return $_REQUEST[$name];
        }

        if (is_null($empty_msg)) {
            return $default;
        } else {
            die($empty_msg);
        }
    }

}

class GitDiffFormatter
{

    private const STYLES = [
        'linerem' => 'background-color: #ffdddd;',
        'lineadd' => 'background-color: #ddffdd;',
        'meta' => 'background-color: #effaff; font-style: italic;',
        'command' => 'background-color: #555555; color: white; display: block; margin-top: 3em;',
        'notes' => 'font-style: italic;',
        'default' => 'color: #777777;',
    ];
    private const RULES = [
        'diff ' => 'command',
        '@' => 'meta',
        'index' => 'meta',
        '---' => 'meta',
        '+++' => 'meta',
        '-' => 'linerem',
        '+' => 'lineadd',
        '\ ' => 'meta',
    ];

    protected static function getStyleForLine(string $line): string
    {
        $style = self::STYLES['default'];

        foreach (self::RULES as $key => $rule) {
            if (strpos($line, $key) === 0) {
                $style = self::STYLES[$rule];
                break;
            }
        }
        return 'font-family: monospace; white-space: pre;' . $style;
    }

    public static function formattedOutput(array $lines): void
    {
        foreach ($lines as $line) {
            $style = self::getStyleForLine($line);
            echo '<span style="' . $style . '">' . htmlentities($line) . "</span>\r\n";
        }
    }

}

class GitDiffCommand
{

    public static function run($path): array
    {
        $output = [];
        $retcode = [];

        chdir($path);
        exec('git diff', $output, $retcode);

        return ['path' => $path, 'output' => $output, 'retcode' => $retcode];
    }

}

class Application
{

    static function execute(): array
    {
        $path = \Request::getParam('path', null, 'Path not set. Try with <a href="?path=my/local/path/to/git/repo">?path=my/local/path/to/git/repo</a>');
        return GitDiffCommand::run($path);
    }

}

$diff_result = Application::execute();
?>

<!doctype html>
<html>
    <head>
        <title>Git diff renderer</title>
    </head>
    <body>
        <p>Path: <?php echo $diff_result['path'] ?></p>
        <p>Exit code: <?php echo $diff_result['retcode'] ?></p>
        <pre><?php \GitDiffFormatter::formattedOutput($diff_result['output']); ?></pre>
    </body>
</html>