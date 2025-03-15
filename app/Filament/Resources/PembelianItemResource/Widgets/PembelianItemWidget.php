<?php

namespace App\Filament\Resources\PembelianItemResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\PembelianItem;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\Summarizers\Summarizer;

class PembelianItemWidget extends BaseWidget
{
    public $pembelianId;

    public function mount($record)
    {
        // mengambil dari create pembelian item
        $this->pembelianId = $record;
    }



    public function table(Table $table): Table
    {
        return $table
            ->query(
                // mengambil dari yang atas
                PembelianItem::query()->where('pembelian_id', $this->pembelianId),
            )
            ->columns([
                TextColumn::make('barang.nama')->label('Barang'),
                TextColumn::make('jumlah')->label('Jumlah Barang'),
                TextColumn::make('harga')->label('Harga Barang')->money('IDR')->alignEnd(),
                TextColumn::make('Total Harga')->label('Total Harga Barang')
                    ->getStateUsing(function ($record) {
                        return $record->jumlah * $record->harga;
                    })->money('IDR')->alignEnd()

                    ->summarize(
                        Summarizer::make()->label('Grand Total')
                            ->using(function ($query) {
                                return $query->sum(DB::raw('jumlah * harga'));
                            })->money('IDR')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->form([
                        TextInput::make('jumlah')->required(),
                    ]),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
