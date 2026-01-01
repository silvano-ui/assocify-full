<?php

namespace Modules\Api\Http\Controllers\Api\V1;

use Modules\Members\Entities\MemberProfile;
use Modules\Members\Entities\MemberCategory;
use Modules\Payments\Entities\Transaction;
use Modules\Api\Http\Requests\V1\StoreMemberRequest;
use Modules\Api\Http\Requests\V1\UpdateMemberRequest;
use Modules\Api\Http\Resources\V1\MemberResource;
use Modules\Api\Http\Resources\V1\TransactionResource;

class MembersController extends BaseApiController
{
    public function index()
    {
        return $this->paginate(MemberProfile::query(), 15, MemberResource::class);
    }

    public function store(StoreMemberRequest $request)
    {
        $member = MemberProfile::create($request->validated());
        return $this->success(new MemberResource($member), 'Member created successfully', 201);
    }

    public function show($id)
    {
        $member = MemberProfile::with('user')->findOrFail($id);
        return $this->success(new MemberResource($member));
    }

    public function update(UpdateMemberRequest $request, $id)
    {
        $member = MemberProfile::findOrFail($id);
        $member->update($request->validated());
        return $this->success(new MemberResource($member), 'Member updated successfully');
    }

    public function destroy($id)
    {
        $member = MemberProfile::findOrFail($id);
        $member->delete();
        return $this->success([], 'Member deleted successfully');
    }

    public function payments($id)
    {
        $member = MemberProfile::findOrFail($id);
        
        if (!$member->user_id) {
            return $this->success([]);
        }

        $query = Transaction::where('user_id', $member->user_id)->latest();
        return $this->paginate($query, 15, TransactionResource::class);
    }

    public function categories()
    {
        $categories = MemberCategory::all();
        return $this->success($categories);
    }
}
