<?php


class Helper
{
    public static function isBanned($unbantime, $time = 0) {
        $unbantime = intval($unbantime);
        $time = $time == 0 ? time() : intval($time);

        // Забанен навсегда
        if ($unbantime == 0) {
            return true;
        }

        // Разбанен
        if ($unbantime == -1) {
            return false;
        }

        // Время еще не прошло
        if ($time < $unbantime) {
            return true;
        }

        return false;
    }
    public static function getPagintaionLimit() {
        $page = $_GET['page'] ? abs((int)$_GET['page']) : 1;
        $pageCount = Configuration::$pagination['p_main'];

        return [ ($page*$pageCount-$pageCount), $pageCount];
    }

    public static function searchFromAdminArray(array $admins, string $name, string $steamID = '') {
        foreach ($admins as $admin) {
            if ($name && $admin['nick'] == $name) {
                return $admin;
            }

            if ($steamID && $admin['steamid'] == $steamID) {
                return $admin;
            }
        }

        return [];
    }
}