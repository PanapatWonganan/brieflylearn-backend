<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailSequenceSubscriptionResource\Pages;
use App\Models\EmailSequence;
use App\Models\EmailSequenceSubscription;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailSequenceSubscriptionResource extends Resource
{
    protected static ?string $model = EmailSequenceSubscription::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'อีเมลมาร์เก็ตติ้ง';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'สมาชิกซีรีส์';

    protected static ?string $pluralModelLabel = 'สมาชิกซีรีส์อีเมล';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('ผู้ใช้')
                    ->relationship('user', 'full_name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('sequence_id')
                    ->label('ซีรีส์')
                    ->relationship('sequence', 'name')
                    ->required(),
                Forms\Components\TextInput::make('current_step')
                    ->label('Step ปัจจุบัน')
                    ->numeric()
                    ->default(0),
                Forms\Components\Select::make('status')
                    ->label('สถานะ')
                    ->options([
                        'active' => 'กำลังส่ง',
                        'paused' => 'หยุดชั่วคราว',
                        'completed' => 'ส่งครบแล้ว',
                        'unsubscribed' => 'ยกเลิกแล้ว',
                    ])
                    ->default('active')
                    ->required(),
                Forms\Components\DateTimePicker::make('next_send_at')
                    ->label('ส่งฉบับถัดไป'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('ผู้ใช้')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('อีเมล')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sequence.name')
                    ->label('ซีรีส์')
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_step')
                    ->label('Step')
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('สถานะ')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'paused' => 'warning',
                        'completed' => 'info',
                        'unsubscribed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'กำลังส่ง',
                        'paused' => 'หยุดชั่วคราว',
                        'completed' => 'ส่งครบแล้ว',
                        'unsubscribed' => 'ยกเลิก',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('next_send_at')
                    ->label('ส่งถัดไป')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('started_at')
                    ->label('เริ่มเมื่อ')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('จบเมื่อ')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('next_send_at', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('สถานะ')
                    ->options([
                        'active' => 'กำลังส่ง',
                        'paused' => 'หยุดชั่วคราว',
                        'completed' => 'ส่งครบแล้ว',
                        'unsubscribed' => 'ยกเลิก',
                    ]),
                Tables\Filters\SelectFilter::make('sequence_id')
                    ->label('ซีรีส์')
                    ->options(EmailSequence::pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailSequenceSubscriptions::route('/'),
            'create' => Pages\CreateEmailSequenceSubscription::route('/create'),
            'edit' => Pages\EditEmailSequenceSubscription::route('/{record}/edit'),
        ];
    }
}
