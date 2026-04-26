<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BumpProductResource\Pages;
use App\Models\BumpProduct;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BumpProductResource extends Resource
{
    protected static ?string $model = BumpProduct::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'การเงิน';
    protected static ?string $navigationLabel = 'Order Bumps';
    protected static ?string $modelLabel = 'Order Bump';
    protected static ?string $pluralModelLabel = 'Order Bumps';
    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ข้อมูลสินค้า')
                    ->schema([
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(80)
                            ->unique(ignoreRecord: true)
                            ->regex('/^[a-z0-9-]+$/')
                            ->helperText('ตัวพิมพ์เล็ก a-z, 0-9, ขีดกลางเท่านั้น เช่น press-method-playbook')
                            ->disabled(fn ($record) => filled($record))
                            ->dehydrated(fn ($record) => blank($record)),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('ราคา')
                    ->schema([
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step('0.01')
                            ->prefix('฿'),

                        Forms\Components\TextInput::make('original_price')
                            ->label('Original Price (ขีดฆ่าโชว์)')
                            ->numeric()
                            ->minValue(0)
                            ->step('0.01')
                            ->prefix('฿')
                            ->helperText('แสดงเป็นราคาขีดฆ่าใน UI — เว้นว่างได้'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('การส่งมอบสินค้า')
                    ->description('เมื่อชำระเงินสำเร็จ ระบบจะส่งมอบ bump ตามประเภทนี้ (Phase 3 จะ wire เข้ากับ payment flow จริง)')
                    ->schema([
                        Forms\Components\Select::make('deliverable_type')
                            ->options([
                                BumpProduct::TYPE_PLAYBOOK_COURSE => 'Playbook / Course (สร้าง enrollment ให้ผู้ซื้อ)',
                                BumpProduct::TYPE_GROUP_MEMBERSHIP => 'Group Membership (เพิ่มเข้ากลุ่ม)',
                                BumpProduct::TYPE_MANUAL => 'Manual (ทีมงานติดต่อเอง)',
                            ])
                            ->required()
                            ->default(BumpProduct::TYPE_MANUAL)
                            ->reactive(),

                        Forms\Components\TextInput::make('deliverable_ref_id')
                            ->label('Deliverable Reference ID')
                            ->helperText(fn ($get) => match ($get('deliverable_type')) {
                                BumpProduct::TYPE_PLAYBOOK_COURSE => 'UUID ของ Course (content_type=playbook) ที่จะปลดล็อก',
                                BumpProduct::TYPE_GROUP_MEMBERSHIP => 'UUID ของ Group ที่จะเพิ่มผู้ซื้อเข้าไป (Phase 2)',
                                default => 'ไม่จำเป็นสำหรับ Manual — ปล่อยว่างได้',
                            })
                            ->maxLength(36)
                            ->visible(fn ($get) => $get('deliverable_type') !== BumpProduct::TYPE_MANUAL),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('การแสดงผล')
                    ->schema([
                        Forms\Components\Select::make('course_id')
                            ->label('แสดงเฉพาะตอน checkout คอร์สนี้')
                            ->relationship('course', 'title')
                            ->searchable()
                            ->preload()
                            ->placeholder('— ทุกคอร์ส —')
                            ->helperText('เว้นว่าง = bump นี้สามารถใช้กับคอร์สใดก็ได้ ที่ frontend อ้างถึง slug ของ bump นี้'),

                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('น้อย = แสดงก่อน'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('เปิดขาย')
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(60),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->size('xs'),

                Tables\Columns\TextColumn::make('price')
                    ->money('THB')
                    ->sortable(),

                Tables\Columns\TextColumn::make('original_price')
                    ->money('THB')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('deliverable_type')
                    ->label('ส่งมอบ')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        BumpProduct::TYPE_PLAYBOOK_COURSE => 'Playbook',
                        BumpProduct::TYPE_GROUP_MEMBERSHIP => 'Group',
                        default => 'Manual',
                    })
                    ->color(fn (string $state) => match ($state) {
                        BumpProduct::TYPE_PLAYBOOK_COURSE => 'success',
                        BumpProduct::TYPE_GROUP_MEMBERSHIP => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('course.title')
                    ->label('คอร์ส')
                    ->placeholder('— ทุกคอร์ส —')
                    ->limit(30)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('เปิดขาย')
                    ->boolean(),

                Tables\Columns\TextColumn::make('order_items_count')
                    ->label('ขายแล้ว')
                    ->counts('orderItems')
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('เปิดขาย'),
                Tables\Filters\SelectFilter::make('deliverable_type')
                    ->label('ประเภทส่งมอบ')
                    ->options([
                        BumpProduct::TYPE_PLAYBOOK_COURSE => 'Playbook',
                        BumpProduct::TYPE_GROUP_MEMBERSHIP => 'Group',
                        BumpProduct::TYPE_MANUAL => 'Manual',
                    ]),
                Tables\Filters\SelectFilter::make('course_id')
                    ->label('คอร์ส')
                    ->relationship('course', 'title'),
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
            'index' => Pages\ListBumpProducts::route('/'),
            'create' => Pages\CreateBumpProduct::route('/create'),
            'edit' => Pages\EditBumpProduct::route('/{record}/edit'),
        ];
    }
}
