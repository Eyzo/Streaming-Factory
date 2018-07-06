<?php
namespace Framework\Session;

class PHPSession implements Sessioninterface, \ArrayAccess
{

    /**
    *Assure que la session est démarrée
    */
    public function ensureStarted()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
    *Récupére une information en session
    */
    public function get(string $key, $default = null)
    {

        $this->ensureStarted();

        if (array_key_exists($key, $_SESSION)) {
            return $_SESSION[$key];
        }
        return $default;
    }

    /**
    *Rentre une clée plus la valeur en session
    */
    public function set(string $key, $value):void
    {

        $this->ensureStarted();

        $_SESSION[$key] = $value;
    }

    /**
    *Supprime une information stockée en session
    */
    public function delete(string $key):void
    {
        
        $this->ensureStarted();

        unset($_SESSION[$key]);
    }

    public function offsetExists($offset)
    {
        $this->ensureStarted();
        return array_key_exists($offset, $_SESSION);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }
}
