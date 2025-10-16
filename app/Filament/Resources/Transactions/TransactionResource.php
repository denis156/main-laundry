<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions;

use UnitEnum;
use BackedEnum;
use App\Models\Transaction;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Transactions\Pages\EditTransaction;
use App\Filament\Resources\Transactions\Pages\ListTransactions;
use App\Filament\Resources\Transactions\Pages\CreateTransaction;
use App\Filament\Resources\Transactions\Schemas\TransactionForm;
use App\Filament\Resources\Transactions\Tables\TransactionsTable;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'transaksi';
    protected static ?string $modelLabel = 'Transaksi';
    protected static int $globalSearchResultsLimit = 10;
    protected static ?string $recordTitleAttribute = 'invoice_number';
    protected static ?string $navigationLabel = 'Transaksi';
    protected static string|UnitEnum|null $navigationGroup = 'Pelanggan & Layanan';
    protected static string |BackedEnum | null $navigationIcon = 'solar-bill-list-linear';
    protected static string |BackedEnum | null $activeNavigationIcon = 'solar-bill-list-bold';

    public static function form(Schema $schema): Schema
    {
        return TransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TransactionsTable::configure($table);
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
            'index' => ListTransactions::route('/'),
            'edit' => EditTransaction::route('/{record}/edit'),
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
