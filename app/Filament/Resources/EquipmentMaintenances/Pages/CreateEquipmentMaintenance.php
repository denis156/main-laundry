<?php

declare(strict_types=1);

namespace App\Filament\Resources\EquipmentMaintenances\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\EquipmentMaintenances\EquipmentMaintenanceResource;

class CreateEquipmentMaintenance extends CreateRecord
{
    protected static string $resource = EquipmentMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('solar-reply-2-linear')
                ->tooltip('Kembali ke daftar perawatan')
                ->color('gray')
                ->url($this->getResource()::getUrl('index'))
                ->extraAttributes([
                    'class' => 'order-first'
                ]),
        ];
    }
}
