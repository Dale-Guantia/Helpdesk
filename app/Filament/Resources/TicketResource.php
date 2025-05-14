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
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Infolists\Components\ViewEntry;

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
                    ->label('Message')
                    ->maxLength(65535),
                Forms\Components\FileUpload::make('attachment')
                    ->multiple()
                    ->preserveFilenames()
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
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'High' => 'danger',
                        'Medium' => 'warning',
                        'Low' => 'info',
                    })
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status.status_name')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Resolved' => 'success',
                        'Unassigned' => 'gray',
                    })
                    ->searchable()
                    ->sortable()
            ])
            ->filters([
                SelectFilter::make('status')
                    ->multiple()
                    ->options([
                        'Pending' => 'Pending',
                        'Resolved' => 'Resolved',
                        'Unassigned' => 'Unassigned',
                    ])
                    ->query(function ($query, array $data) {
                        $values = $data['values'] ?? [];
                        if (count($values)) {
                            $query->whereHas('status', function ($q) use ($values) {
                                $q->whereIn('status_name', $values);
                            });
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(''),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->hidden(fn ($record) => !auth()->user()->isSuperAdmin() && auth()->id() !== $record->user_id),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->hidden(fn ($record) => !auth()->user()->isSuperAdmin() && auth()->id() !== $record->user_id),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Grid::make()
                    ->schema([
                        Components\Section::make('Ticket Details')
                            ->schema([
                                Components\TextEntry::make('id')->label('Ticket ID'),
                                Components\TextEntry::make('title')->label('Title'),
                                Components\TextEntry::make('description')->label('Message'),
                                ViewEntry::make('attachment')
                                    ->label('Attachments:')
                                    ->view('filament.components.attachment-list')
                                    ->visible(fn (Ticket $record) => is_array($record->attachment) && count($record->attachment) > 0),
                            ])->columnSpan(9), // 8/12 columns = ~66%
                        Components\Section::make()
                            ->schema([
                                Components\TextEntry::make('office.office_name')->label('Office of concern'),
                                Components\TextEntry::make('problemCategory.category_name')->label('Problem category'),
                                Components\TextEntry::make('priority.priority_name')
                                    ->label('Priority Level')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'High' => 'danger',
                                        'Medium' => 'warning',
                                        'Low' => 'info',
                                    }),
                                Components\TextEntry::make('status.status_name')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Pending' => 'warning',
                                        'Resolved' => 'success',
                                        'Unassigned' => 'gray',
                                    }),
                                Components\TextEntry::make('created_at')->dateTime('F j, Y g:i A')->label('Created at'),
                                Components\TextEntry::make('updated_at')->dateTime('F j, Y g:i A')->label('Updated at'),

                            ])->columnSpan(3), // 4/12 columns = ~33%
                    ])
                    ->columns(12), // total columns for grid system
            ]);
    }




    protected static function getTableQuery(): Builder
    {
        $user = auth()->user();
        // If admin, return all records
        if ($user->isSuperAdmin()) {
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
            'view' => Pages\ViewTicket::route('/{record}'),
        ];
    }
}
