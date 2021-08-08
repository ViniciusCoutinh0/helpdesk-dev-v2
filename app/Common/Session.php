<?php

namespace App\Common;

class Session
{
    public function __construct()
    {
        if (!session_id()) {
            $pathOs = str_replace('/', DIRECTORY_SEPARATOR, env('CONFIG_PATH_SESSION'));
            session_save_path(__DIR__ . $pathOs);
            session_start();
        }
    }

    public function __get($name)
    {
        if (!empty($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return null;
    }

    public function __isset($name): bool
    {
        return $this->has($name);
    }

    public function all(): ?object
    {
        return (object) $_SESSION;
    }

    public function set(string $key, $value): Session
    {
        $_SESSION[$key] = (is_array($value) ? (object) $value : $value);
        return $this;
    }

    public function unset(string $key): Session
    {
        unset($_SESSION[$key]);
        return $this;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function regenerate(): Session
    {
        session_regenerate_id(true);
        return $this;
    }

    public function destroy(): Session
    {
        session_destroy();
        return $this;
    }
}
