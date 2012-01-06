<?php

namespace ezr_keymedia;

class Autoloader
{
    public static $base = null;
    public static $registered = false;
    /**
     * Register ezote/Autoloader::autoload as autoloader
     */
    static public function register($base = false)
    {
        if (!static::$registered)
        {
            static::$base = $base ?: __DIR__;
            static::$registered = spl_autoload_register(array(new self, 'autoload'));
        }
    }

    /**
     * Autoloads namespaced  classes
     *
     * @param string $class A class name.
     * @return boolean Returns true if the class has been loaded
     */
    static public function autoload($class)
    {
        $file = static::$base . '/' . str_replace('\\', '/', $class) .'.php';
        if (file_exists($file))
            return require $file;
        return false;
    }
}
