<?php

namespace Modules\Api\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Modules\Chat\Entities\Conversation;
use Modules\Chat\Entities\Message;
use Illuminate\Support\Facades\DB;

class ChatController extends BaseApiController
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        
        // Assuming relationship or simple query for user's conversations
        // This depends on how ConversationParticipant is set up
        $conversations = Conversation::whereHas('participants', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->with('lastMessage')->latest('updated_at');

        return $this->paginate($conversations);
    }

    public function messages($id)
    {
        $conversation = Conversation::findOrFail($id);
        
        // Check participation
        if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
             return $this->error('Unauthorized', 403);
        }

        $messages = $conversation->messages()->latest()->paginate(20);
        
        return $this->success([
            'data' => $messages->items(),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
            ]
        ]);
    }

    public function sendMessage(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);
        
        if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
            return $this->error('Unauthorized', 403);
        }

        $request->validate([
            'body' => 'required|string',
        ]);

        $message = $conversation->messages()->create([
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);
        
        $conversation->touch(); // Update updated_at

        return $this->success($message, 'Message sent', 201);
    }

    public function store(Request $request)
    {
        $request->validate([
            'participant_ids' => 'required|array',
            'participant_ids.*' => 'exists:users,id',
            'subject' => 'nullable|string',
        ]);

        $userId = auth()->id();
        $participants = array_unique(array_merge([$userId], $request->participant_ids));

        DB::beginTransaction();
        try {
            $conversation = Conversation::create([
                'tenant_id' => auth()->user()->tenant_id ?? 1,
                'subject' => $request->subject,
                'type' => count($participants) > 2 ? 'group' : 'private',
            ]);

            foreach ($participants as $participantId) {
                $conversation->participants()->create([
                    'user_id' => $participantId,
                ]);
            }
            
            DB::commit();
            return $this->success($conversation, 'Conversation started', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Failed to create conversation: ' . $e->getMessage(), 500);
        }
    }
}
