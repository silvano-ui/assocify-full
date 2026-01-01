<?php

namespace Modules\Api\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Modules\Newsletter\Entities\NewsletterList;
use Modules\Newsletter\Entities\NewsletterSubscriber;

class NewsletterController extends BaseApiController
{
    public function lists()
    {
        return $this->paginate(NewsletterList::query());
    }

    public function subscribers()
    {
        return $this->paginate(NewsletterSubscriber::query());
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'newsletter_list_id' => 'required|exists:newsletter_lists,id',
            'first_name' => 'nullable|string',
            'last_name' => 'nullable|string',
        ]);

        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => $request->email, 'tenant_id' => auth()->user()->tenant_id ?? 1],
            [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'status' => 'subscribed',
            ]
        );

        // Attach to list if not already
        if (!$subscriber->lists()->where('newsletter_list_id', $request->newsletter_list_id)->exists()) {
            $subscriber->lists()->attach($request->newsletter_list_id, ['status' => 'subscribed', 'created_at' => now()]);
        }

        return $this->success($subscriber, 'Subscribed successfully');
    }

    public function unsubscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'newsletter_list_id' => 'nullable|exists:newsletter_lists,id',
        ]);

        $subscriber = NewsletterSubscriber::where('email', $request->email)->first();

        if (!$subscriber) {
            return $this->error('Subscriber not found', 404);
        }

        if ($request->newsletter_list_id) {
            $subscriber->lists()->updateExistingPivot($request->newsletter_list_id, ['status' => 'unsubscribed', 'unsubscribed_at' => now()]);
        } else {
            // Unsubscribe from all
            $subscriber->update(['status' => 'unsubscribed']);
            $subscriber->lists()->update(['status' => 'unsubscribed', 'unsubscribed_at' => now()]); // Logic depends on pivot or direct update
        }

        return $this->success([], 'Unsubscribed successfully');
    }
}
