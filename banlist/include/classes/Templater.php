<?php


class Templater
{
    /** Return content of template
     * @param $fileName
     * @param array $vars
     * @return false|string
     */
    public static function template($fileName, $vars = array())
    {
        foreach ($vars as $k => $v) $$k = $v;

        ob_start();
        include $fileName;
        return ob_get_clean();
    }
}