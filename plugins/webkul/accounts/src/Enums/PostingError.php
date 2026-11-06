<?php

namespace Webkul\Account\Enums;

enum PostingError: string
{
    case INVALID_ENTRY = 'invalid_entry';

    case UNBALANCED = 'unbalanced';

    case REJECTED_BY_VALIDATOR = 'rejected_by_validator';

    case MOVE_NOT_POSTABLE = 'move_not_postable';

    case MOVE_NOT_REVERSIBLE = 'move_not_reversible';

    case DUPLICATE_SOURCE = 'duplicate_source';
}
