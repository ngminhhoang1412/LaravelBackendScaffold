<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostController extends Controller
{
    public $model = Post::class;
}
