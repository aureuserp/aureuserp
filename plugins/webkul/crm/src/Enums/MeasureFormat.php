<?php

namespace Webkul\Crm\Enums;

enum MeasureFormat: string
{
    case INTEGER = 'integer';

    case CURRENCY = 'currency';

    case DAYS = 'days';

    case PERCENT = 'percent';
}
