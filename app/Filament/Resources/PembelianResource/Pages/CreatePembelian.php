<?php

namespace App\Filament\Resources\PembelianResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PembelianResource;

class CreatePembelian extends CreateRecord
{
    protected static string $resource = PembelianResource::class;

    // atur form
    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label(__('selanjutnya'))
                ->submit('create')
                ->keyBindings(['mod+s']),
        ];
    }

    protected function getRedirectUrl(): string
    {
        // untuk mendapatkan id yang disimpan
        $id = $this->record->id;
        return route(
            'filament.admin.resources.pembelian-items.create',
            ['pembelian_id' => $id]
        );
    }
}
