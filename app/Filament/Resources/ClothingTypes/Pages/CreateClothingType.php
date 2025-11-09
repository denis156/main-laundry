<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClothingTypes\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ClothingTypes\ClothingTypeResource;

class CreateClothingType extends CreateRecord
{
    protected static string $resource = ClothingTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('solar-reply-2-linear')
                ->tooltip('Kembali ke daftar jenis pakaian')
                ->color('gray')
                ->url($this->getResource()::getUrl('index'))
                ->extraAttributes([
                    'class' => 'order-first',
                ]),
        ];
    }
}
