<?php

declare(strict_types=1);

namespace App\Filament\Resources\MaterialStockHistories\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\MaterialStockHistories\MaterialStockHistoryResource;

class CreateMaterialStockHistory extends CreateRecord
{
    protected static string $resource = MaterialStockHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('solar-reply-2-linear')
                ->tooltip('Kembali ke daftar riwayat stock')
                ->color('gray')
                ->url($this->getResource()::getUrl('index'))
                ->extraAttributes(['class' => 'order-first']),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}
