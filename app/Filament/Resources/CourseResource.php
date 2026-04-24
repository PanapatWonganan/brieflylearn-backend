<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'คอร์สเรียน';
    
    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'คอร์ส';

    protected static ?string $pluralModelLabel = 'คอร์สเรียน';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('instructor_id')
                    ->label('ผู้สอน')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options(fn () => User::query()
                        ->where('role', 'admin')
                        ->orWhere('role', 'instructor')
                        ->pluck('full_name', 'id')
                        ->toArray()),
                Forms\Components\Select::make('category_id')
                    ->label('หมวดหมู่')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->relationship('category', 'name'),
                Forms\Components\Select::make('content_type')
                    ->label('ประเภทเนื้อหา')
                    ->required()
                    ->options([
                        'video' => 'Video Course (คอร์สวิดีโอ)',
                        'playbook' => 'Playbook (เนื้อหาอ่าน HTML)',
                    ])
                    ->default('video')
                    ->helperText('Video = มีบทเรียนหลายบท + วิดีโอ / Playbook = 1 บทเรียนเดียว เนื้อหาอ่าน HTML')
                    ->live(),
                Forms\Components\Select::make('level')
                    ->label('ระดับ')
                    ->required()
                    ->options([
                        'beginner' => 'Beginner',
                        'intermediate' => 'Intermediate',
                        'advanced' => 'Advanced',
                    ])
                    ->default('beginner'),
                Forms\Components\TextInput::make('duration_weeks')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('original_price')
                    ->numeric(),
                Forms\Components\TextInput::make('thumbnail_url')
                    ->maxLength(500),
                Forms\Components\TextInput::make('trailer_video_url')
                    ->maxLength(500),
                Forms\Components\Toggle::make('is_published'),
                Forms\Components\TextInput::make('rating')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_students')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_lessons')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('content_type')
                    ->label('ประเภท')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'playbook' => 'warning',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'playbook' => 'Playbook',
                        'video' => 'Video',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('instructor_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('level'),
                Tables\Columns\TextColumn::make('duration_weeks')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('original_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('thumbnail_url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('trailer_video_url')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('rating')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_students')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_lessons')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            RelationManagers\LessonsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}
