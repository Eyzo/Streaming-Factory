<?php
namespace Framework\Session;

interface Sessioninterface
{


    public function get(string $key, $default = null);

    public function set(string $key, $value):void;

    public function delete(string $key):void;
}
