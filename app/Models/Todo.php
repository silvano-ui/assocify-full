<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Users\User;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'is_completed',
        'due_date',
        'attachments',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date' => 'datetime',
        'attachments' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
