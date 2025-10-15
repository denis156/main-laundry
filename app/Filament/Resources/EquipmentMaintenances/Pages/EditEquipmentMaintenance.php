<?php

declare(strict_types=1);

namespace App\Filament\Resources\EquipmentMaintenances\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\EquipmentMaintenances\EquipmentMaintenanceResource;

class EditEquipmentMaintenance extends EditRecord
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
            DeleteAction::make()
                ->color('danger')
                ->icon('solar-trash-bin-minimalistic-linear')
                ->modalIcon('solar-trash-bin-minimalistic-linear')
                ->tooltip('Hapus perawatan ini'),
        ];
    }
}
