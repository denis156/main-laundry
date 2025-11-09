<?php

declare(strict_types=1);

namespace App\Filament\Resources\Customers\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Customers\CustomerResource;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('solar-reply-2-linear')
                ->tooltip('Kembali ke daftar pelanggan')
                ->color('gray')
                ->url($this->getResource()::getUrl('index'))
                ->extraAttributes([
                    'class' => 'order-first',
                ]),
        ];
    }
}
