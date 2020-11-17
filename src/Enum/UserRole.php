<?php

namespace Enum;

use MyCLabs\Enum\Enum;

class UserRole extends Enum
{
    // Socio.
    const ADMIN = -1;
    const ALUMNO = 1;
    const PROFESOR = 2;

    public static function IsValidArea($area)
    {
        switch ($area) {
            case UserRole::ADMIN:
                return true;
            case UserRole::ALUMNO:
                return true;
            case UserRole::PROFESOR:
                return true;
            default:
                return false;
        }
    }

    public static function GetDescription($role)
    {
        switch ($role) {
            case UserRole::ADMIN:
                return "ADMIN";
            case UserRole::ALUMNO:
                return "ALUMNO";
            case UserRole::PROFESOR:
                return "PROFESOR";
        }
    }

    public static function getVal($string)
    {
        switch ($string) {
            case "admin":
                return UserRole::ADMIN;
            case "alumno":
                return UserRole::ALUMNO;
            case "profesor":
                return UserRole::PROFESOR;
            default:
                return "";
        }
    }
}
