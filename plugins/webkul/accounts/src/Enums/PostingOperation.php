<?php

namespace Webkul\Account\Enums;

enum PostingOperation: string
{
    case POST = 'post';

    case REVERSE = 'reverse';

    case RESET_TO_DRAFT = 'reset_to_draft';

    case CANCEL = 'cancel';
}
