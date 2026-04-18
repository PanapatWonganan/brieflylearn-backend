<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnrollmentResource\Pages;
use App\Models\Enrollment;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EnrollmentResource extends Resource
{
    protected static ?string $model = Enrollment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'การเงิน';

    protected static ?string $navigationLabel = 'คำสั่งซื้อ / Enrollment';

    protected static ?string $modelLabel = 'คำสั่งซื้อ';

    protected static ?string $pluralModelLabel = 'คำสั่งซื้อ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('ผู้ใช้')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options(fn () => User::query()->pluck('full_name', 'id')->toArray()),
                Forms\Components\Select::make('course_id')
                    ->label('คอร์ส')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship('course', 'title'),
                Forms\Components\TextInput::make('order_no')
                    ->label('หมายเลขคำสั่งซื้อ')
                    ->maxLength(32)
                    ->disabled(fn (?Enrollment $record) => $record !== null),
                Forms\Components\Select::make('status')
                    ->label('สถานะการเรียน')
                    ->options([
                        'pending' => 'รอดำเนินการ',
                        'active' => 'กำลังเรียน',
                        'completed' => 'เรียนจบ',
                        'cancelled' => 'ยกเลิก',
                    ])
                    ->default('pending'),
                Forms\Components\Select::make('payment_status')
                    ->label('สถานะการชำระเงิน')
                    ->required()
                    ->options([
                        'pending' => 'รอชำระเงิน',
                        'completed' => 'ชำระแล้ว',
                        'failed' => 'ไม่สำเร็จ',
                        'refunded' => 'คืนเงินแล้ว',
                    ])
                    ->default('pending'),
                Forms\Components\Select::make('payment_gateway')
                    ->label('ช่องทางชำระเงิน')
                    ->options([
                        'paysolutions' => 'Pay Solutions',
                        'manual' => 'Manual / โอนเงิน',
                        'free' => 'ฟรี',
                    ]),
                Forms\Components\TextInput::make('amount_paid')
                    ->label('ยอดที่ชำระ')
                    ->numeric()
                    ->prefix('฿'),
                Forms\Components\DateTimePicker::make('payment_date')
                    ->label('วันที่ชำระ'),
                Forms\Components\TextInput::make('transaction_id')
                    ->label('รหัสอ้างอิง (Txn)')
                    ->maxLength(100),
                Forms\Components\DateTimePicker::make('enrolled_at')
                    ->label('วันที่ลงทะเบียน'),
                Forms\Components\DateTimePicker::make('completed_at')
                    ->label('วันที่เรียนจบ'),
                Forms\Components\KeyValue::make('gateway_response')
                    ->label('Gateway Response')
                    ->keyLabel('Key')
                    ->valueLabel('Value')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('enrolled_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_no')
                    ->label('Order #')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('ผู้ใช้')
                    ->searchable(),
                Tables\Columns\TextColumn::make('course.title')
                    ->label('คอร์ส')
                    ->searchable()
                    ->limit(32),
                Tables\Columns\TextColumn::make('amount_paid')
                    ->label('ยอด')
                    ->money('THB')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('ชำระ')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'secondary' => 'refunded',
                    ]),
                Tables\Columns\TextColumn::make('payment_gateway')
                    ->label('ช่องทาง')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('ชำระเมื่อ')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('enrolled_at')
                    ->label('สร้างเมื่อ')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('สถานะการชำระเงิน')
                    ->options([
                        'pending' => 'รอชำระเงิน',
                        'completed' => 'ชำระแล้ว',
                        'failed' => 'ไม่สำเร็จ',
                        'refunded' => 'คืนเงินแล้ว',
                    ]),
                Tables\Filters\SelectFilter::make('payment_gateway')
                    ->label('ช่องทาง')
                    ->options([
                        'paysolutions' => 'Pay Solutions',
                        'manual' => 'Manual',
                        'free' => 'ฟรี',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('markPaid')
                    ->label('ยืนยันชำระเงิน')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Enrollment $record) => $record->payment_status !== 'completed')
                    ->requiresConfirmation()
                    ->action(function (Enrollment $record) {
                        $record->fill([
                            'payment_status' => 'completed',
                            'status' => 'active',
                            'payment_date' => now(),
                            'amount_paid' => $record->amount_paid ?: ($record->course?->price ?? 0),
                            'enrolled_at' => $record->enrolled_at ?: now(),
                            'payment_method' => $record->payment_method ?: 'manual',
                            'payment_gateway' => $record->payment_gateway ?: 'manual',
                        ])->save();

                        Notification::make()
                            ->success()
                            ->title('ยืนยันการชำระเงินแล้ว')
                            ->send();
                    }),
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
            'index' => Pages\ListEnrollments::route('/'),
            'create' => Pages\CreateEnrollment::route('/create'),
            'edit' => Pages\EditEnrollment::route('/{record}/edit'),
        ];
    }
}
