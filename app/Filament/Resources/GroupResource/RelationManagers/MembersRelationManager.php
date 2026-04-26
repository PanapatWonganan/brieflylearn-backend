<?php

namespace App\Filament\Resources\GroupResource\RelationManagers;

use App\Models\GroupMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'สมาชิก';
    protected static ?string $modelLabel = 'สมาชิก';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'email')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Select::make('role')
                    ->options([
                        GroupMember::ROLE_MEMBER => 'Member',
                        GroupMember::ROLE_ADMIN => 'Admin',
                    ])
                    ->default(GroupMember::ROLE_MEMBER)
                    ->required(),

                Forms\Components\Select::make('status')
                    ->options([
                        GroupMember::STATUS_ACTIVE => 'Active',
                        GroupMember::STATUS_PENDING => 'Pending',
                        GroupMember::STATUS_REMOVED => 'Removed',
                    ])
                    ->default(GroupMember::STATUS_ACTIVE)
                    ->required(),

                Forms\Components\DateTimePicker::make('joined_at')
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('ชื่อ')
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.email')
                    ->label('อีเมล')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state) => $state === 'admin' ? 'warning' : 'gray'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'removed' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('joined_at')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('enrollment_id')
                    ->label('Enrollment')
                    ->formatStateUsing(fn ($state) => $state ? substr($state, 0, 8) : '—')
                    ->fontFamily('mono')
                    ->size('xs')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'pending' => 'Pending',
                        'removed' => 'Removed',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
