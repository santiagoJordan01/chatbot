<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatLog extends Model
{
    protected $table = 'chat_logs';

    protected $fillable = [
        'user_id',
        'message',
        'response',
        'used_rag',
        'model',
    ];

    protected $casts = [
        'response' => 'array',
        'used_rag' => 'boolean',
    ];
}
