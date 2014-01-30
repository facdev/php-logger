<?php
/**
 * Log class  - Log.php file
 *
 * @author     Tyurin D. <fobia3d@gmail.com>
 * @copyright  (c) 2013, AC
 */



/**
 * Log class
 *
 * @package		AC
 */
class Log
{

    private static $_messages;
    public static $enable = false;

    public static function trace($message, $category = 'Log', $level = 'trace')
    {
        static $time_start = null;
        if ( ! self::$enable) {
            return;
        }
        if ($time_start === null) {
            if (defined('TIME_START')) {
                $time_start = TIME_START;
            } else {
                $time_start = microtime(true);
            }
        }

        if (is_object($category)) {
            $category = get_class($category);
        }

        self::$_messages[] = array(
            'msg'   => $message,
            'ctg'   => $category,
            'level' => $level,
            'time'  => sprintf(" %01.6f", microtime(true) - $time_start)
        );
    }

    public static function dump($object, $name = null)
    {
        if ( ! self::$enable) {
            return;
        }

        ob_start();
        var_dump($object);
        $message = '<b>' . $name . '::</b>' . ob_get_contents();
        ob_end_clean();
        self::trace($message, 'dump');
    }

    public static function error($message, $category = 'Log')
    {
        self::trace($message, $category, 'error');
    }

    public static function enable($check = null)
    {
        if (func_num_args() == 0) {
            return self::$enable;
        }
        self::$enable = (bool) $check;
    }

    public static function getLogs()
    {
        return self::$_messages;
    }

    public static function render($print = true)
    {
        if ( ! self::$enable) {
            return;
        }

        $Logs = self::$_messages;


        ob_start();
        $i = 0;
        if ( ! isset($_SERVER['HTTP_HOST'])) {
            foreach ($Logs as $row) {
                printf("%'02d", ++ $i);
                printf("%-9s", $row['time']);
                printf("%-9s", '[' . $row['level'] . ']');
                printf("%-9s", $row['ctg']);
                echo $row['msg'] . "\n";
            }
        } else {

            include 'view/view.php';
        }

        $content = ob_get_contents();
        ob_end_clean();

        if ($print) {
            echo $content;
        } else {
            return $content;
        }
    }
}