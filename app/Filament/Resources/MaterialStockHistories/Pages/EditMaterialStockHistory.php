<?php

declare(strict_types=1);

namespace App\Filament\Resources\MaterialStockHistories\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\MaterialStockHistories\MaterialStockHistoryResource;

class EditMaterialStockHistory extends EditRecord
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
            DeleteAction::make()
                ->label('Hapus')
                ->color('danger')
                ->icon('solar-trash-bin-minimalistic-linear')
                ->modalIcon('solar-trash-bin-minimalistic-bold')
                ->tooltip('Hapus riwayat ini'),
        ];
    }
}
