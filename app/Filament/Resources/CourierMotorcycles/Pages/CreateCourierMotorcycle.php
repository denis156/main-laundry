<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourierMotorcycles\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CourierMotorcycles\CourierMotorcycleResource;

class CreateCourierMotorcycle extends CreateRecord
{
    protected static string $resource = CourierMotorcycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('solar-reply-2-linear')
                ->tooltip('Kembali ke daftar kurir motor')
                ->color('gray')
                ->url($this->getResource()::getUrl('index'))
                ->extraAttributes([
                    'class' => 'order-first'
                ]),
        ];
    }
}
