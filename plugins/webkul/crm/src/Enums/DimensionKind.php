<?php

namespace Webkul\Crm\Enums;

enum DimensionKind: string
{
    case RELATION = 'relation';

    case ATTRIBUTE = 'attribute';

    case OPTIONS = 'options';

    case MONTH = 'month';
}
