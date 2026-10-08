<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationLabel = 'Layanan';
    protected static ?string $pluralModelLabel = 'Layanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Layanan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('icon')
                    ->label('Icon (Font Awesome Class)')
                    ->placeholder('Contoh: fas fa-ring'),
                Forms\Components\FileUpload::make('image')
                    ->label('Gambar')
                    ->image()
                    ->disk('public')
                    ->directory('services')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16:9'),
                Forms\Components\RichEditor::make('description')
                    ->label('Deskripsi')
                    ->required(),
                Forms\Components\TagsInput::make('features')
                    ->label('Fitur-fitur')
                    ->separator(',')
                    ->placeholder('Masukkan fitur dan tekan Enter')
                    ->hint('Pisahkan setiap fitur dengan koma atau Enter'),
                Forms\Components\TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->minValue(1)
                    ->prefix('Rp')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('features')
                    ->label('Fitur')
                    ->formatStateUsing(function ($state) {
                        if (is_array($state)) {
                            return implode(', ', $state);
                        }
                        return $state;
                    })
                    ->wrap(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}