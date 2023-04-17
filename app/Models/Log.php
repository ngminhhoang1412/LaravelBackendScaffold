<?php

namespace App\Models;

class Log extends BaseModel
{
    const LOG_CATEGORY_ENUM = ['PortalLog', 'WorkerLog', 'SeederLog', 'CronLog'];
}