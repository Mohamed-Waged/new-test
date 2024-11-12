<?php

namespace App\Constants;

class GlobalConstants
{
    const UNREGISTERED_APP_ID_MESSAGE = 'Unregistered appId!';

    const UNDEFINED_APP_ID_MESSAGE = 'Undefined appId, please add appId in the header!';

    const UNREGISTERED_DOMAIN_MESSAGE = 'Unregistered domain!';

    const REGISTERED_DOMAIN_NAME = [
        'http://localhost:8000',
        'http://localhost:8001'
    ];

    const SUPPORTED_LOCALES = ['en', 'ar'];

    const DEFAULT_CONTENT_TYPE = 'application/json';

    const DEFAULT_GUARD = 'api';

    // Matches letters, numbers, whitespace, ., _, - but does not allow / or '
    const TITLE_REGEX = '/^[A-Za-z0-9\s._-]+$/';

    const MAX_PRICE = 99999.99;

    const EXCEL_DIRECTORY_PATH = 'uploads/excel';

    const DEFAULT_PAGINATE = 10;

    const DEFAULT_IMAGE_FORMAT = 'jpeg';

    const DEFAULT_CURRENCY = 'USD';

    const DEFAULT_DATE_FORMAT = 'd-m-Y';

    const DEFAULT_TIME_FORMAT = 'h:i A';

    const DEFAULT_TIMEZONE = 'Asia/Kuwait';
}
