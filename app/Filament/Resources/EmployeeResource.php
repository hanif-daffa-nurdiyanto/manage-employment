<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Filament\Resources\EmployeeResource\Widgets\EmployeeStatsOverview;
use App\Models\City;
use App\Models\Country;
use App\Models\Employee;
use App\Models\State;
use Carbon\Callback;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('country_id')
                    ->label('Country')
                    ->options(Country::all()->pluck('name', 'id')->toArray())
                    ->reactive()
                    ->afterStateUpdated(fn (callable $set) => $set('state_id', null)),
                Select::make('state_id')
                    ->label('State')
                    ->options(function (callable $get) {
                        $country = Country::with('states')->find($get('country_id'));
                        if (!$country || !$country->states) {
                            return State::all()->pluck('name', 'id');
                        }
                        return $country->states->pluck('name', 'id');
                    }),                
                Select::make('city_id')
                    ->options(function (callable $get) {
                        $state = State::with('cities')->find($get('state_id'));
                        if (!$state || !$state->cities) {
                            return City::all()->pluck('name', 'id');
                        }
                        return $state->cities->pluck('name', 'id');
                    }),  
                Select::make('department_id')
                    ->relationship('department', 'name'),
                TextInput::make('firstname')
                    ->label('First Name')
                    ->placeholder('Enter the first name')
                    ->required(),
                TextInput::make('lastname')
                    ->label('Last Name')
                    ->placeholder('Enter the last name')
                    ->required(),
                Textarea::make('address')
                    ->label('Address')
                    ->placeholder('Enter the address')
                    ->required(),
                TextInput::make('zip_code')
                    ->label('Zip Code')
                    ->placeholder('Enter the zip code')
                    ->required(),
                DatePicker::make('birthdate')
                    ->label('Birthdate')
                    ->placeholder('Enter the birthdate')
                    ->required(),
                DatePicker::make('date_hired')
                    ->label('Date Hired')
                    ->placeholder('Enter the Date Hired')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('firstname')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('lastname')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_hired')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
            ])
            ->filters([
                SelectFilter::make('department_id')
                    ->relationship('department', 'name')
                    ->label('Department')
                    ->options(fn () => \App\Models\Department::all()->pluck('name', 'id')->toArray())
                    ->default(null),
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
            //
        ];
    }

    public static function getWidgets(): array
    {
        return [
            EmployeeStatsOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
