<?php

namespace Modules\Chat\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Core\Users\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'reply_to_id',
        'forward_from_id',
        'body',
        'attachments',
        'mentions',
        'is_system',
        'is_pinned',
        'is_scheduled',
        'scheduled_at',
        'edited_at',
        'deleted_at',
    ];

    protected $casts = [
        'attachments' => 'json',
        'mentions' => 'json',
        'is_system' => 'boolean',
        'is_pinned' => 'boolean',
        'is_scheduled' => 'boolean',
        'scheduled_at' => 'datetime',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'reply_to_id');
    }

    public function forwardFrom(): BelongsTo
    {
        return $this->belongsTo(Message::class, 'forward_from_id');
    }

    public function chatAttachments(): HasMany
    {
        return $this->hasMany(ChatAttachment::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(MessageReaction::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(MessageRead::class);
    }
    
    public function hashtags(): BelongsToMany
    {
        return $this->belongsToMany(ChatHashtag::class, 'message_hashtags', 'message_id', 'hashtag_id');
    }
}
