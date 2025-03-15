<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Barang;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Pembelian;
use Filament\Tables\Table;
use App\Models\PembelianItem;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PembelianItemResource\Pages;
use App\Filament\Resources\PembelianItemResource\RelationManagers;

class PembelianItemResource extends Resource
{
    protected static ?string $model = PembelianItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $pembelian = new Pembelian();
        if (request()->filled('pembelian_id')) {
            $pembelian = Pembelian::find(request('pembelian_id'));
        }
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        DatePicker::make('tanggal')
                            ->label('Tanggal Pembelian')
                            ->required()
                            ->default($pembelian->tanggal)
                            ->disabled(),
                        TextInput::make('supplier_id')
                            ->label('Supplier')
                            ->required()
                            ->disabled()
                            ->default($pembelian->supplier?->nama),
                        TextInput::make('email')
                            ->label('Email Supplier')
                            ->required()
                            ->disabled()
                            ->default($pembelian->supplier?->email),
                    ])->columns(3),

                Hidden::make('pembelian_id')->default(request('pembelian_id')),

                Grid::make()->schema([
                    Select::make('barang_id')
                        ->label('Barang')
                        ->required()
                        ->options(Barang::all()->pluck('nama', 'id'))
                        ->reactive()->afterStateUpdated(function ($state, Set $set, Get $get) {
                            $barang = Barang::find($state);
                            $set('harga', $barang->harga ?? null);
                            $jumlah = $get('jumlah');
                            $total = $jumlah * $barang->harga;
                            $set('total', $total);
                        }),
                    TextInput::make('jumlah')
                        ->reactive()
                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                            $jumlah = $state;
                            $harga = $get('harga');
                            $total = $jumlah * $harga;
                            $set('total', $total);
                        })
                        ->label('Jumlah Barang')->required()->default(1),
                    TextInput::make('harga')
                        ->label('Harga Barang'),
                    TextInput::make('total')
                        ->disabled()
                        ->label('Total Harga Barang'),
                ])->columns(4)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembelianItems::route('/'),
            'create' => Pages\CreatePembelianItem::route('/create'),
            'edit' => Pages\EditPembelianItem::route('/{record}/edit'),
        ];
    }
}
