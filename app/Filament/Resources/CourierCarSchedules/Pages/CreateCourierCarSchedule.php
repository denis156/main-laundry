<?php

declare(strict_types=1);

namespace App\Filament\Resources\CourierCarSchedules\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CourierCarSchedules\CourierCarScheduleResource;

class CreateCourierCarSchedule extends CreateRecord
{
    protected static string $resource = CourierCarScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('solar-reply-2-linear')
                ->tooltip('Kembali ke daftar jadwal')
                ->color('gray')
                ->url($this->getResource()::getUrl('index'))
                ->extraAttributes([
                    'class' => 'order-first'
                ]),
        ];
    }
}
