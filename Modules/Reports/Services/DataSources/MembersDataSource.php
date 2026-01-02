<?php

namespace Modules\Reports\Services\DataSources;

use Illuminate\Database\Eloquent\Builder;
use Modules\Members\Entities\MemberProfile;
use Illuminate\Support\Facades\Schema;

class MembersDataSource implements DataSourceInterface
{
    public function getQuery(): Builder
    {
        return MemberProfile::query()->with('user');
    }

    public function getLabel(): string
    {
        return 'Members';
    }

    public function getAvailableColumns(): array
    {
        return [
            'id',
            'member_number',
            'first_name', // Assuming these exist on MemberProfile or we need to access via user
            'last_name',
            'birth_date',
            'city',
            'country',
            'created_at',
            'updated_at',
        ];
    }

    public function getFilterableColumns(): array
    {
        return [
            'city',
            'country',
            'birth_date',
            'created_at',
        ];
    }
}
