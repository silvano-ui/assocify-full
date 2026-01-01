<?php

namespace Modules\Api\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Modules\Members\Entities\MemberProfile;
use Modules\Members\Entities\MemberCategory;
use Modules\Payments\Entities\Transaction;

class MembersController extends BaseApiController
{
    public function index()
    {
        return $this->paginate(MemberProfile::query());
    }

    public function store(Request $request)
    {
        $member = MemberProfile::create($request->all());
        return $this->success($member, 'Member created successfully', 201);
    }

    public function show($id)
    {
        $member = MemberProfile::findOrFail($id);
        return $this->success($member);
    }

    public function update(Request $request, $id)
    {
        $member = MemberProfile::findOrFail($id);
        $member->update($request->all());
        return $this->success($member, 'Member updated successfully');
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
        return $this->paginate($query);
    }

    public function categories()
    {
        $categories = MemberCategory::all();
        return $this->success($categories);
    }
}
