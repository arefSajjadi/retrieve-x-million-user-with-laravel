<?php

namespace App\Helpers;

use Exception;

final class jobFechted
{
    private static jobFechted $instance = null;

    /**
     * gets the instance via lazy initialization
     */
    public static function getInstance($instance): jobFechted
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * prevent from being unserialized (which would create a second instance of it)
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
