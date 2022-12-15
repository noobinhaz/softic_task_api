<?php

namespace App\Enums;

final class Role
{
    const Super_admin       = "1";
    const Affiliate      = "2";
    const General_user      = "3";

    const getRoles = [
        'Super_admin' => '1',
        'Affiliate' => '2',
        'General_user' => '3',
    ];
}