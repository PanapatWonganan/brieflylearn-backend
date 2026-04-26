<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GroupResource\Pages;
use App\Filament\Resources\GroupResource\RelationManagers;
use App\Models\Group;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'คอร์สเรียน';
    protected static ?string $navigationLabel = 'Groups (Coaching/Community)';
    protected static ?string $modelLabel = 'Group';
    protected static ?string $pluralModelLabel = 'Groups';
    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ข้อมูลกลุ่ม')
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(80)
                            ->unique(ignoreRecord: true)
                            ->regex('/^[a-z0-9-]+$/')
                            ->helperText('a-z, 0-9, ขีดกลาง เช่น ai-100m-dwy')
                            ->disabled(fn ($record) => filled($record))
                            ->dehydrated(fn ($record) => blank($record)),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Select::make('type')
                            ->options([
                                Group::TYPE_COACHING => 'Coaching (กลุ่มสอนสด เก็บเงิน)',
                                Group::TYPE_COMMUNITY => 'Community (เปิดทั่วไป)',
                                Group::TYPE_COHORT => 'Cohort (รุ่น/แบทช์)',
                            ])
                            ->required()
                            ->default(Group::TYPE_COACHING),

                        Forms\Components\TextInput::make('max_members')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('เว้นว่าง = ไม่จำกัด')
                            ->helperText('DWY = 12 คน/รุ่น'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('การประชุม / Zoom')
                    ->description('ผู้ที่ซื้อเข้ากลุ่มจะเห็นข้อมูลนี้ในหน้า /groups/{slug}')
                    ->schema([
                        Forms\Components\TextInput::make('zoom_link')
                            ->label('Zoom Link')
                            ->url()
                            ->placeholder('https://zoom.us/j/...')
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('meeting_schedule')
                            ->label('ตารางประชุม')
                            ->rows(3)
                            ->placeholder('เช่น ทุกวันอังคาร 20:00-21:30 (รวม 6 ครั้ง)')
                            ->columnSpanFull(),

                        Forms\Components\Repeater::make('resources')
                            ->label('Resources / Replay (Array)')
                            ->schema([
                                Forms\Components\TextInput::make('label')->required(),
                                Forms\Components\TextInput::make('url')->url()->required(),
                            ])
                            ->columns(2)
                            ->addActionLabel('เพิ่ม resource')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('สถานะ')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('เปิดใช้งาน')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->size('xs'),

                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        Group::TYPE_COACHING => 'warning',
                        Group::TYPE_COMMUNITY => 'success',
                        Group::TYPE_COHORT => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('active_members_count')
                    ->label('สมาชิก')
                    ->counts('activeMembers'),

                Tables\Columns\TextColumn::make('max_members')
                    ->label('เพดาน')
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('เปิด')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('เปิดใช้งาน'),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        Group::TYPE_COACHING => 'Coaching',
                        Group::TYPE_COMMUNITY => 'Community',
                        Group::TYPE_COHORT => 'Cohort',
                    ]),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\MembersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGroups::route('/'),
            'create' => Pages\CreateGroup::route('/create'),
            'edit' => Pages\EditGroup::route('/{record}/edit'),
        ];
    }
}
