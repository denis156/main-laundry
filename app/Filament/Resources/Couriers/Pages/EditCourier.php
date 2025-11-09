<?php

declare(strict_types=1);

namespace App\Filament\Resources\Couriers\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\Couriers\CourierResource;

class EditCourier extends EditRecord
{
    protected static string $resource = CourierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('solar-reply-2-linear')
                ->tooltip('Kembali ke daftar kurir')
                ->color('gray')
                ->url($this->getResource()::getUrl('index'))
                ->extraAttributes([
                    'class' => 'order-first',
                ]),
            DeleteAction::make()
                ->color('warning')
                ->icon('solar-trash-bin-minimalistic-linear')
                ->modalIcon('solar-trash-bin-minimalistic-linear')
                ->tooltip('Hapus kurir ini'),
            ForceDeleteAction::make()
                ->color('danger')
                ->icon('solar-trash-bin-2-linear')
                ->modalIcon('solar-trash-bin-2-linear')
                ->tooltip('Hapus selamanya kurir ini'),
            RestoreAction::make()
                ->color('gray')
                ->icon('solar-refresh-linear')
                ->modalIcon('solar-refresh-linear')
                ->tooltip('Pulihkan kurir ini'),
        ];
    }
}
