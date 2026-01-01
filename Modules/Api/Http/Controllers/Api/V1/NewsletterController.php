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

        $subscriber = NewsletterSubscriber::updateOrCreate(
            [
                'email' => $request->email, 
                'list_id' => $request->newsletter_list_id,
                'tenant_id' => auth()->user()->tenant_id ?? 1
            ],
            [
                'name' => trim($request->first_name . ' ' . $request->last_name),
                'status' => 'subscribed',
                'subscribed_at' => now(),
                'source' => 'api',
            ]
        );

        return $this->success($subscriber, 'Subscribed successfully');
    }

    public function unsubscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'newsletter_list_id' => 'nullable|exists:newsletter_lists,id',
        ]);

        $query = NewsletterSubscriber::where('email', $request->email)
            ->where('tenant_id', auth()->user()->tenant_id ?? 1);

        if ($request->newsletter_list_id) {
            $query->where('list_id', $request->newsletter_list_id);
        }

        $updatedCount = $query->update([
            'status' => 'unsubscribed', 
            'unsubscribed_at' => now()
        ]);

        if ($updatedCount === 0) {
            return $this->error('Subscriber not found or already unsubscribed', 404);
        }

        return $this->success([], 'Unsubscribed successfully');
    }
}
