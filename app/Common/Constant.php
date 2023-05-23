<?php

namespace App\Common;

class Constant
{
    // Flags
    const PRODUCTION_FLAG = 'production';
    const MAIL_X_RAPIDAPI_HOST = 'rapidprod-sendgrid-v1.p.rapidapi.com';
    const MAIL_EXPIRED_TIME = 300;
    const OTP_LENGTH = 10;
    // Common fields
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const IS_ACTIVE = 'is_active';
}
