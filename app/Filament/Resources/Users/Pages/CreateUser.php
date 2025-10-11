<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Users\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
               ->icon('solar-reply-2-linear')
                ->tooltip('Kembali ke daftar pengguna')
                ->color('gray')
                ->url($this->getResource()::getUrl('index'))
                ->extraAttributes([
                    'class' => 'order-first'
                ]),
        ];
    }
}
