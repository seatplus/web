<?php

declare(strict_types=1);

namespace Seatplus\Web\Enums;

enum EmploymentStatus: string
{
    case Active = 'active';
    case Suspended = 'suspended';
    case Alumni = 'alumni';
}
