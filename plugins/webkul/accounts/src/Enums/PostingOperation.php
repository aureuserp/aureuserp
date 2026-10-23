<?php

namespace Webkul\Account\Enums;

enum PostingOperation: string
{
    case POST = 'post';

    case REVERSE = 'reverse';
}
