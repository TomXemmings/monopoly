<?php

namespace App\Enums;

enum ColumnStateEnum: string
{
    case Unknown              = 'Unknown';
    case Free                 = 'Free';
    case TakenRefuelingNozzle = 'TakenRefuelingNozzle';
    case Fueling              = 'Fueling';
    case FuelingCompleted     = 'FuelingCompleted';
}
