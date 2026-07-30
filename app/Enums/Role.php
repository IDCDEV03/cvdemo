<?php

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Agency = 'agency';
    case Manager = 'manager';
    case User = 'user';
    case Company = 'company';
    case Supply = 'supply';
    case Staff = 'staff';
    case Inspector = 'inspector';
}
