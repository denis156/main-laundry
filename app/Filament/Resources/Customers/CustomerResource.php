<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers;

use BackedEnum;
use App\Models\Customer;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Customers\Pages\EditCustomer;
use App\Filament\Resources\Customers\Pages\ListCustomers;
use App\Filament\Resources\Customers\Pages\CreateCustomer;
use App\Filament\Resources\Customers\Schemas\CustomerForm;
use App\Filament\Resources\Customers\Tables\CustomersTable;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'pelanggan';
    protected static ?string $modelLabel = 'Pelanggan';
    protected static int $globalSearchResultsLimit = 5;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = 'Manajemen Pelanggan';
    protected static string |BackedEnum | null $navigationIcon = 'solar-users-group-rounded-linear';

    public static function form(Schema $schema): Schema
    {
        return CustomerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CustomersTable::configure($table);
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
            'index' => ListCustomers::route('/'),
            'create' => CreateCustomer::route('/create'),
            'edit' => EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
