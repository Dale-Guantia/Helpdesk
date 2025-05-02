<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('office_id')
                    ->label('Office of concern')
                    ->required()
                    ->relationship('office', 'office_name'),
                Forms\Components\Select::make('problem_category_id')
                    ->label('Problem Category')
                    ->required()
                    ->relationship('problemCategory', 'category_name'),
                Forms\Components\Select::make('priority_id')
                    ->label('Priority Level')
                    ->required()
                    ->relationship('priority', 'priority_name'),
                Forms\Components\Select::make('status_id')
                    ->label('Status')
                    ->required()
                    ->relationship('status', 'status_name'),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->maxLength(65535),
                Forms\Components\FileUpload::make('attachment')
                    ->multiple()
                    ->reorderable()
                    ->enableOpen()
                    ->enableDownload()
                    ->directory('uploads')
                    ->maxSize(25000)
                    ->columnSpanFull()
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                        'application/zip',
                        'text/plain',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Ticket ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->html()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('office.office_name')
                    ->label('Office of concern')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('problemCategory.category_name')
                    ->label('Problem category')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority.priority_name')
                    ->label('Priority Level')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status.status_name')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),
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
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
