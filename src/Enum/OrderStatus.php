<?php

namespace App\Enum;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Success = 'success';
    case Failure = 'failure';
}
