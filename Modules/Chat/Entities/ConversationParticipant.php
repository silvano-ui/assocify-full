<?php

namespace Modules\Chat\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Core\Users\User;

class ConversationParticipant extends Model
{
    protected $table = 'conversation_participants';
    
    protected $fillable = [
        'conversation_id',
        'user_id',
        'role',
        'nickname',
        'notifications',
        'is_muted',
        'muted_until',
        'joined_at',
        'last_read_at',
        'last_typing_at',
        'is_online',
        'last_seen_at',
    ];

    protected $casts = [
        'notifications' => 'boolean',
        'is_muted' => 'boolean',
        'is_online' => 'boolean',
        'muted_until' => 'datetime',
        'joined_at' => 'datetime',
        'last_read_at' => 'datetime',
        'last_typing_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
