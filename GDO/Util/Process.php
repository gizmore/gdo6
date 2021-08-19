<?php
namespace GDO\Util;

/**
 * Process utilities.
 * Check if Windows is used.
 * Check if a command is in PATH env.
 * Turn pathes to OS DIR_SEPARATOR path.
 *
 * @author gizmore
 * @version 6.10
 * @since 6.10
 */
final class Process
{
    /**
     * Check if the operating system is Windows.
     * @return boolean
     */
    public static function isWindows()
    {
        return PHP_OS === 'WINNT';
    }

    /**
     * Convert DIR separator for operating System.
     * On Windows we use backslash.
     * On Linux we keep forward slash, which is default in gdo6.
     * @param string $path
     * @return string
     */
    public static function osPath($path)
    {
        if (self::isWindows())
        {
            return str_replace('/', '\\', $path);
        }
        return $path;
    }


    /**
     * Determines if a command exists on the current environment
     * @param string $command The command to check
     * @return bool True if the command has been found; otherwise, false.
     * @author https://stackoverflow.com/a/18540185/13599483
     */
    public static function commandPath($command, $windowsSuffix='.*')
    {
        $whereIsCommand = self::isWindows() ? 'where' : 'which';
        $command = self::isWindows() ? "$command$windowsSuffix" : $command;

        $pipes = [];
        $process = proc_open(
            "$whereIsCommand $command",
            array(
                0 => array("pipe", "r"), //STDIN
                1 => array("pipe", "w"), //STDOUT
                2 => array("pipe", "w"), //STDERR
            ),
            $pipes
        );

        if ($process !== false)
        {
            $stdout = stream_get_contents($pipes[1]);
//             $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);

            if ($stdout !== '')
            {
                return trim($stdout, "\r\n\t ");
            }
        }
        return null;
    }

}
