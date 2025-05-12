<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\ProblemCategory;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->maxLength(65535),
                Forms\Components\FileUpload::make('attachment')
                    ->multiple()

                    ->reorderable()
                    ->openable()
                    ->downloadable()
                    ->directory('attachments/' . date('m-y'))
                    ->maxSize(25000)
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
                ])->columnSpan(2),

                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('office_id')
                        ->label('Office of concern')
                        ->required()
                        ->reactive()
                        ->relationship('office', 'office_name'),
                    Forms\Components\Select::make('problem_category_id')
                        ->label('Problem Category')
                        ->required()
                        ->options(function (callable $get) {
                            $office_id = $get('office_id');

                            if (!$office_id) {
                                return [];
                            }

                            return ProblemCategory::where('office_id', $office_id)
                                ->pluck('category_name', 'id')
                                ->toArray();
                        })
                        ->disabled(fn (callable $get) => !$get('office_id')), // Disable if office_id is not selected
                    Forms\Components\Select::make('priority_id')
                        ->label('Priority Level')
                        ->required()
                        ->relationship('priority', 'priority_name'),
                    Forms\Components\Select::make('status_id')
                        ->label('Status')
                        ->required()
                        ->relationship('status', 'status_name'),
                ])->columnSpan(1)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Ticket ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->title)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('office.office_name')
                    ->label('Office of concern')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('problemCategory.category_name')
                    ->label('Problem category')
                    ->searchable()
                    ->sortable()
                    ->limit(20)
                    ->tooltip(fn ($record) => $record->problemCategory?->category_name ?? '-'),
                Tables\Columns\TextColumn::make('priority.priority_name')
                    ->label('Priority Level')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status.status_name')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->hidden(fn ($record) => !auth()->user()->isAdmin() && auth()->id() !== $record->user_id),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->hidden(fn ($record) => !auth()->user()->isAdmin() && auth()->id() !== $record->user_id),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected static function getTableQuery(): Builder
    {
        $user = auth()->user();

        // If admin, return all records
        if ($user->isAdmin()) {
            return static::getModel()::query();
        }

        // Otherwise filter by office_id
        return static::getModel()::where('office_id', $user->office_id);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CommentsRelationManager::class,
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
