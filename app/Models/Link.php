<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Link extends BaseModel
{
    use HasFactory;

    /**
     * @var mixed
     */
    protected $fillable = [
        'link',
        'short_link',
    ];

    protected $filters= [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
