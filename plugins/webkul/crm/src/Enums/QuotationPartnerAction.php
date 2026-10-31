<?php

namespace Webkul\Crm\Enums;

enum QuotationPartnerAction: string
{
    case CREATE = 'create';

    case LINK = 'link';

    case NONE = 'none';

    case EXISTING = 'existing';
}
