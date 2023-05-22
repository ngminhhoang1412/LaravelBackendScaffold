<?php

namespace App\Common;

class Constant
{
    // Flags
    const PRODUCTION_FLAG = 'production';
    const Mail_X_RapidAPI_Host = 'rapidprod-sendgrid-v1.p.rapidapi.com';
    const Expired_Mail_Time = 300;
    // Common fields
    const CREATED_BY = 'created_by';
    const UPDATED_BY = 'updated_by';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const IS_ACTIVE = 'is_active';
}
