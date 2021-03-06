<?php

abstract class Request
{

    abstract function getParam($name, $default = null, $empty_msg = null);
}

class ConsoleRequest extends Request
{

    private $arguments;

    function __construct()
    {
        $this->arguments = [];

        $args = $_SERVER['argv'];

        unset($args[0]);

        foreach ($args as $arg) {
            list($name, $value) = explode('=', $arg);
            $this->arguments["$name"] = $value;
        }
    }

    public function getParam($name, $default = null, $empty_msg = null)
    {
        if (isset($this->arguments[$name])) {
            return $this->arguments[$name];
        }

        if (is_null($empty_msg)) {
            return $default;
        } else {
            die($empty_msg);
        }
    }

}

class BrowserRequest extends Request
{

    public function getParam($name, $default = null, $empty_msg = null)
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

class Environment
{

    private $request;

    function __construct()
    {
        if (defined('STDIN')) {
            $this->request = new \ConsoleRequest();
        } else {
            $this->request = new \BrowserRequest();
        }
    }

    function getRequest()
    {
        return $this->request;
    }

}

class StringUtils
{

    public static function startsWith(string $haystack, string $needle): bool
    {
        return (strpos($haystack, $needle) === 0);
    }

    public static function getEnclosedString(string $input, string $opentag, string $closetag): string
    {
        $match = [];
        if (preg_match('/' . $opentag . '(.*?)' . $closetag . '/', $input, $match) === 1) {
            return $match[1];
        }
        return false;
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
    private const NO_COUNT = ['diff ', 'index', '---', '+++', '\ ', '-'];
    private const BASIC_STYLE = 'font-family: monospace; white-space: pre;';

    private static $line_number;

    protected static function getLineNumber(string $line): string
    {
        $empty_line = str_repeat(' ', strlen((string) (self::$line_number))) . ' ';

        foreach (self::NO_COUNT as $value) {
            if (StringUtils::startsWith($line, $value)) {
                return $empty_line;
            }
        }

        if (StringUtils::startsWith($line, '@@')) {
            $chunk = StringUtils::getEnclosedString($line, '@@ ', ' @@');
            $chunk_parts = explode(' ', $chunk);
            $chunk_def_last = explode(',', $chunk_parts[count($chunk_parts) - 1]);
            $chunk_line = abs(intval($chunk_def_last[0]));

            self::$line_number = $chunk_line - 1;
            return $empty_line;
        } else {
            self::$line_number++;
            return self::$line_number . ' ';
        }
    }

    protected static function getStyleForLine(string $line): string
    {
        $style = self::STYLES['default'];

        foreach (self::RULES as $key => $rule) {
            if (StringUtils::startsWith($line, $key)) {
                $style = self::STYLES[$rule];
                break;
            }
        }
        return $style;
    }

    public static function formattedOutput(array $lines): void
    {
        self::$line_number = 0;
        foreach ($lines as $line) {
            $style = self::getStyleForLine($line);
            $line_number = self::getLineNumber($line);
            echo '<span style="' . self::BASIC_STYLE . 'color: #aaa; display: inline-block; width: 5em;">' . $line_number . '</span>';
            echo '<span style="' . self::BASIC_STYLE . $style . '">' . htmlentities($line) . "</span>\r\n";
        }
    }

}

class GitDiffCommandReader
{

    public static function run($path): array
    {
        $output = [];
        $retcode = null;

        chdir($path);
        exec('git diff', $output, $retcode);

        return ['path' => $path, 'output' => $output, 'retcode' => $retcode];
    }

}

class FileReader
{

    public static function run($path): array
    {
        return ['path' => $path, 'output' => file($path), 'retcode' => 0];
    }

}

class GitDiffRenderer
{

    private const PATH_NOT_SET_MSG = 'Path not set. Try with <a href="?path=my/local/path/to/git/repo">?path=my/local/path/to/git/repo</a>';

    static function execute(\Request $request): array
    {
        $path = $request->getParam('path', null, self::PATH_NOT_SET_MSG);

        if (is_file($path)) {
            return FileReader::run($path);
        } elseif (is_dir($path)) {
            return GitDiffCommandReader::run($path);
        } else {
            die('Specified path is not a directory of a file.');
        }
    }

}

$environment = new \Environment();
$diff_result = GitDiffRenderer::execute($environment->getRequest());
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