<?php

namespace App\Filament\Resources;

use App\Enums\EventStatus;
use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Event';
    protected static ?string $pluralModelLabel = 'Events';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Event')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'engagement' => 'Engagement',
                                'gathering' => 'Gathering',
                                'birthday' => 'Birthday Party'
                            ])
                            ->required(),
                        Forms\Components\FileUpload::make('image')
                            ->label('Gambar')
                            ->image()
                            ->disk('public')
                            ->directory('events')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('16:9'),
                        Forms\Components\DatePicker::make('event_date')
                            ->label('Tanggal Event')
                            ->required(),
                        Forms\Components\TextInput::make('location')
                            ->label('Lokasi')
                            ->required(),
                        Forms\Components\RichEditor::make('description')
                            ->label('Deskripsi')
                            ->required(),
                        Forms\Components\TagsInput::make('features')
                            ->label('Fitur-fitur')
                            ->separator(','),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(EventStatus::options())
                            ->default(EventStatus::Upcoming->value)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('public')
                    ->square()
                    ->size(100),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'engagement' => 'primary',
                        'gathering'  => 'success',
                        'birthday'   => 'warning',
                        default      => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'engagement' => 'Engagement',
                        'gathering'  => 'Family Gathering',
                        'birthday'   => 'Birthday Party',
                        default      => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('event_date')
                    ->label('Tanggal')
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => EventStatus::from($state)->label()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'engagement' => 'Engagement Event',
                        'gathering'  => 'Family Gathering',
                        'birthday'   => 'Birthday Party',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(EventStatus::options()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'view' => Pages\ViewEvent::route('/{record}'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}


