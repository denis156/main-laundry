<?php

declare(strict_types=1);

namespace App\Filament\Resources\Members;

use UnitEnum;
use BackedEnum;
use App\Models\Member;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\Members\Pages\EditMember;
use App\Filament\Resources\Members\Pages\ListMembers;
use App\Filament\Resources\Members\Pages\CreateMember;
use App\Filament\Resources\Members\Schemas\MemberForm;
use App\Filament\Resources\Members\Tables\MembersTable;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?int $navigationSort = 3;
    protected static ?string $slug = 'member';
    protected static ?string $modelLabel = 'Member';
    protected static int $globalSearchResultsLimit = 5;
    protected static ?string $navigationLabel = 'Member';
    protected static ?string $recordTitleAttribute = 'member_number';
    protected static string|UnitEnum|null $navigationGroup = 'Menu Master Data';
    protected static string |BackedEnum | null $navigationIcon = 'solar-medal-star-linear';
    protected static string |BackedEnum | null $activeNavigationIcon = 'solar-medal-star-bold';

    public static function form(Schema $schema): Schema
    {
        return MemberForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MembersTable::configure($table);
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
            'index' => ListMembers::route('/'),
            'create' => CreateMember::route('/create'),
            'edit' => EditMember::route('/{record}/edit'),
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
