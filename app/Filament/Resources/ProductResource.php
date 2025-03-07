<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Doctrine\DBAL\Schema\Column;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Relationship;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use illuminate\Support\Str;
use function Laravel\Prompts\select;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-s-newspaper';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Product Information')->schema([
                        TextInput::make('name')
                        ->required()
                        ->live(onBlur:true)
                        ->afterStateUpdated(function(string $operation,$state ,Set $set){
                            if($operation !=='create'){
                                return; 
                            }
                            $set('slug',Str::slug($state));
                        })
                        ->maxLength(255),

                        TextInput::make('slug')
                        ->maxLength(255)
                        ->disabled()
                        ->dehydrated()
                        ->unique(Product::class,'slug',ignoreRecord:true),

                        MarkdownEditor::make('description')
                        ->columnSpanFull()
                        ->fileAttachmentsDirectory('products')
                    ])->columns(2),

                    Section::make('Images')->schema([
                        FileUpload::make('images')
                        ->multiple()
                        ->directory('products')
                        ->maxFiles(5)
                        ->reorderable()
                    ])
                ])->columnSpan(2),

                Group::make()->schema([
                    Section::make('price')->schema([
                            TextInput::make('price')
                            ->numeric()
                            ->required()
                            ->prefix('$')
                    ]),
                    
                    Section::make('Association')->schema([
                        Select::make('catagory_id')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->relationship('catagory','name'),

                        Select::make('brand_id')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->relationship('brand','name')
                    ]),

                    Section::make('Status')->schema([
                        Toggle::make('in_stock')
                        ->required()
                        ->default(true),

                        Toggle::make('is_activate')
                        ->required()
                        ->default(true),

                        Toggle::make('is_featured')
                        ->required()
                        ->default(true),

                        Toggle::make('on_sale')
                        ->required()
                        ->default(true)
                    ])

                ])->columnSpan(1)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                ->searchable(),

                TextColumn::make('catagory.name')
                ->sortable(),

                TextColumn::make('brand.name')
                ->sortable(),
                
                TextColumn::make('price')
                ->sortable()
                ->prefix('$'),
                
                IconColumn::make('is_featured')
                ->boolean(),
                IconColumn::make('is_active')
                ->boolean(),
                IconColumn::make('on_sale')
                ->boolean(),
                IconColumn::make('in_stock')
                ->boolean(),
                IconColumn::make('created_at')
                ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('updated_at')
                ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                SelectFilter::make('catagory')
                ->relationship('catagory','name')
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
