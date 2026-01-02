<?php

namespace App\Filament\Admin\Widgets;

use App\Core\Users\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class ActiveUsersWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Online Users';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->with('tenant')
                    ->join('sessions', 'users.id', '=', 'sessions.user_id')
                    ->select('users.*', 'sessions.last_activity', 'sessions.ip_address', 'sessions.user_agent')
                    ->where('sessions.last_activity', '>=', now()->subMinutes(config('session.lifetime'))->timestamp)
                    ->orderBy('sessions.last_activity', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_activity')
                    ->label('Last Active')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::createFromTimestamp($state)->diffForHumans())
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address'),
            ])
            ->paginated(false);
    }
}
