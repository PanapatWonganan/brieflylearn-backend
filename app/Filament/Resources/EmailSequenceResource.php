<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailSequenceResource\Pages;
use App\Models\EmailSequence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailSequenceResource extends Resource
{
    protected static ?string $model = EmailSequence::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'อีเมลมาร์เก็ตติ้ง';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'ซีรีส์อีเมล';

    protected static ?string $pluralModelLabel = 'ซีรีส์อีเมล';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('ข้อมูลซีรีส์')
                    ->description('ตั้งค่าซีรีส์อีเมลสำหรับส่งเป็น drip campaign')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('ชื่อซีรีส์')
                            ->placeholder('เช่น Welcome Story Series')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->placeholder('จะสร้างอัตโนมัติจากชื่อ')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('คำอธิบาย')
                            ->placeholder('อธิบายว่าซีรีส์นี้เกี่ยวกับอะไร เพื่อบันทึกไว้ดูเอง')
                            ->rows(2)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('เปิดใช้งาน')
                            ->helperText('ปิดเพื่อหยุดส่งอีเมลในซีรีส์นี้ชั่วคราว')
                            ->default(true),
                        Forms\Components\Toggle::make('is_default')
                            ->label('ซีรีส์หลัก (auto-subscribe สมาชิกใหม่)')
                            ->helperText('เปิดเพื่อให้สมาชิกใหม่ทุกคนเข้าซีรีส์นี้อัตโนมัติ (ควรมีแค่ 1 ซีรีส์)')
                            ->default(false),
                    ])->columns(2),

                Forms\Components\Section::make('ฉบับอีเมล (Steps)')
                    ->description('เพิ่มอีเมลแต่ละฉบับในซีรีส์ — เรียงตามลำดับ step ที่ต้องการส่ง')
                    ->schema([
                        Forms\Components\Repeater::make('steps')
                            ->label('')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('step_number')
                                    ->label('ลำดับ')
                                    ->numeric()
                                    ->required()
                                    ->minValue(1)
                                    ->default(1),
                                Forms\Components\TextInput::make('subject')
                                    ->label('หัวข้ออีเมล')
                                    ->placeholder('ใช้ {first_name} สำหรับชื่อผู้รับ')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Forms\Components\RichEditor::make('body_html')
                                    ->label('เนื้อหา')
                                    ->placeholder('เขียนเรื่องเล่าของคุณที่นี่... ใช้ {first_name}, {name}, {email}, {app_url} เป็น placeholder ได้')
                                    ->required()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'strike',
                                        'link',
                                        'orderedList',
                                        'bulletList',
                                        'blockquote',
                                        'h2',
                                        'h3',
                                        'redo',
                                        'undo',
                                    ])
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('delay_days')
                                    ->label('ส่งหลังจาก step ก่อนหน้า (วัน)')
                                    ->helperText('ฉบับแรก = วันหลังสมัคร, ฉบับถัดไป = วันหลังฉบับก่อน')
                                    ->numeric()
                                    ->required()
                                    ->default(3)
                                    ->minValue(1),
                                Forms\Components\Toggle::make('is_active')
                                    ->label('เปิดใช้งาน')
                                    ->default(true),
                            ])
                            ->columns(2)
                            ->orderColumn('step_number')
                            ->defaultItems(0)
                            ->addActionLabel('เพิ่มฉบับใหม่')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                isset($state['step_number'], $state['subject'])
                                    ? "ฉบับ {$state['step_number']}: {$state['subject']}"
                                    : null
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('ชื่อซีรีส์')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('steps_count')
                    ->label('จำนวนฉบับ')
                    ->counts('steps')
                    ->sortable(),
                Tables\Columns\TextColumn::make('active_subscriptions_count')
                    ->label('กำลังส่ง')
                    ->counts('activeSubscriptions')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('subscriptions_count')
                    ->label('สมาชิกทั้งหมด')
                    ->counts('subscriptions')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('สถานะ')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_default')
                    ->label('ซีรีส์หลัก')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('สร้างเมื่อ')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('สถานะ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('viewSubscribers')
                    ->label('ดูสมาชิก')
                    ->icon('heroicon-o-users')
                    ->url(fn (EmailSequence $record): string =>
                        EmailSequenceSubscriptionResource::getUrl('index', ['tableFilters[sequence_id][value]' => $record->id])
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailSequences::route('/'),
            'create' => Pages\CreateEmailSequence::route('/create'),
            'edit' => Pages\EditEmailSequence::route('/{record}/edit'),
        ];
    }
}
