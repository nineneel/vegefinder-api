<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AvatarResource\Pages;
use App\Filament\Resources\AvatarResource\RelationManagers;
use App\Models\Avatar;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AvatarResource extends Resource
{
    protected static ?string $model = Avatar::class;

    protected static ?string $navigationIcon = 'heroicon-o-emoji-happy';

    protected static ?string $navigationGroup = 'Resources';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make("name")->required(),
                    FileUpload::make('file_name')
                        ->preserveFilenames()
                        ->image()
                        ->disk('gcs')
                        ->directory("avatar")
                        ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                ImageColumn::make('file_name')->label('Thumbnail')->disk('gcs'),
                TextColumn::make('created_at')->date(),
                TextColumn::make('updated_at')->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAvatars::route('/'),
            'create' => Pages\CreateAvatar::route('/create'),
            'edit' => Pages\EditAvatar::route('/{record}/edit'),
        ];
    }
}
