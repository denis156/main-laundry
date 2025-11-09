<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Transactions\TransactionResource;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('solar-reply-2-linear')
                ->tooltip('Kembali ke daftar transaksi')
                ->color('gray')
                ->url($this->getResource()::getUrl('index'))
                ->extraAttributes([
                    'class' => 'order-first',
                ]),
        ];
    }
}
