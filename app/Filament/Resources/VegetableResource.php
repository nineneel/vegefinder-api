<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VegetableResource\Pages;
use App\Filament\Resources\VegetableResource\RelationManagers;
use App\Filament\Resources\VegetableResource\Widgets\VegetableStatsOverview;
use App\Models\Type;
use App\Models\Vegetable;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use stdClass;

class VegetableResource extends Resource
{
    protected static ?string $model = Vegetable::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-clip';

    protected static ?string $navigationGroup = 'Vegetable';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'sm' => 1,
                    'xl' => 5,
                ])
                    ->schema([
                        Card::make()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('name')->placeholder("Vegetable Name")->required(),
                                        TextInput::make('other_name')->placeholder("Latin Name"),
                                    ]),
                                FileUpload::make('thumbnail')
                                    ->preserveFilenames()
                                    ->image()
                                    ->disk('gcs')
                                    ->directory("vegetable-thumbnail")
                                    ->required(),
                                Fieldset::make('Description Field')
                                    ->schema([
                                        Textarea::make('description')
                                            ->rows(10)->required(),
                                        TextInput::make('description_source')
                                            ->label('Source (URL)')
                                            ->placeholder('https://vegefinder.com')
                                            ->url()
                                            ->required(),
                                    ])
                                    ->columns(1),
                                Fieldset::make('How To Plant Field')
                                    ->schema([
                                        TextArea::make('how_to_plant')
                                            ->rows(10)->required(),
                                        TextInput::make('how_to_plant_source')
                                            ->label('Source (URL)')
                                            ->placeholder('https://vegefinder.com')
                                            ->url()
                                            ->required(),
                                    ])
                                    ->columns(1),
                                Fieldset::make('Plant Care Field')
                                    ->schema([
                                        TextArea::make('plant_care')
                                            ->rows(10)->required(),
                                        TextInput::make('plant_care_source')
                                            ->label('Source (URL)')
                                            ->placeholder('https://vegefinder.com')
                                            ->url()
                                            ->required(),
                                    ])
                                    ->columns(1),
                                Fieldset::make('Plant Disease Field')
                                    ->schema([
                                        TextArea::make('plant_disease')
                                            ->rows(10)->required(),
                                        TextInput::make('plant_disease_source')
                                            ->label('Source (URL)')
                                            ->placeholder('https://vegefinder.com')
                                            ->url()
                                            ->required(),
                                    ])
                                    ->columns(1),
                            ])->columnSpan(3),
                        Grid::make(1)
                            ->schema([
                                Card::make()
                                    ->schema([
                                        CheckboxList::make('type_id')
                                            ->label('Type')
                                            ->relationship('types', 'name')
                                            ->options(Type::all()->pluck('name', 'id'))
                                            ->required()
                                            ->columns(2),
                                    ]),
                                Card::make()
                                    ->schema([
                                        FileUpload::make('images')
                                            ->label('Images')
                                            ->preserveFilenames()
                                            ->multiple()
                                            ->image()
                                            ->disk('gcs')
                                            ->directory("vegetable-images")
                                            ->minFiles(1)
                                            ->maxFiles(3)
                                            ->enableReordering()
                                            ->required()
                                    ])
                            ])->columnSpan(2),
                    ])

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('No')->getStateUsing(
                    static function (stdClass $rowLoop, HasTable $livewire): string {
                        return (string) ($rowLoop->iteration +
                            ($livewire->tableRecordsPerPage * ($livewire->page - 1
                            ))
                        );
                    }
                ),
                TextColumn::make('name')->searchable()->sortable(),
                TagsColumn::make('types.name'),
                TextColumn::make('description')
                    ->words(25)
                    ->size('sm')
                    ->wrap(),
            ])
            ->filters([])
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

    public static function getWidgets(): array
    {
        return [
            VegetableStatsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVegetables::route('/'),
            'create' => Pages\CreateVegetable::route('/create'),
            'edit' => Pages\EditVegetable::route('/{record}/edit'),
        ];
    }
}
