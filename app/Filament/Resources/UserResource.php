<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Avatar;
use App\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Resources\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Card::make()
                            ->schema([
                                TextInput::make('name')
                                    ->required()->columnSpan(2),
                                TextInput::make('email')
                                    ->email()
                                    ->unique(fn (Page $livewire): string => $livewire instanceof CreateRecord ? 'users' : false)
                                    ->required()->columnSpan(2),
                                TextInput::make('password')
                                    ->password()
                                    ->minLength(6)
                                    ->same('password_confirmation')
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->required(fn (Page $livewire): bool => $livewire instanceof CreateRecord),
                                TextInput::make('password_confirmation')
                                    ->password()
                                    ->label("Password Confirmation")
                                    ->dehydrated(false)
                                    ->required(fn (Page $livewire): bool => $livewire instanceof CreateRecord),
                            ])->columns(2)->columnSpan(2),
                        Card::make()
                            ->schema([
                                CheckboxList::make('role_id')
                                    ->label('Role')
                                    ->relationship('roles', 'name')
                                    ->options(Role::all()->pluck('name', 'id'))
                                    ->required()
                                    ->columns(2),
                                Select::make('avatar_id')
                                    ->label('Avatar')
                                    ->options(Avatar::all()->pluck('name', 'id'))
                                    ->required()
                            ])->columnSpan(1)
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                TagsColumn::make('roles.name')
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
