<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Carbon;


class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'Comments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Forms\Components\Textarea::make('comment'),
                    Forms\Components\FileUpload::make('attachment')
                    ->multiple()
                    ->preserveFilenames()
                    ->openable()
                    ->downloadable()
                    ->directory('attachments/' . date('m-y'))
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
                ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('comment')
            ->columns([
                Stack::make([
                        TextColumn::make('user.name')
                            ->translateLabel()
                            ->weight('bold')
                            ->grow(false)
                            ->formatStateUsing(function ($state, $record) {
                                return $record->user_id === auth()->id() ? 'You' : $state;
                            }),
                        TextColumn::make('created_at')
                            ->html()
                            ->formatStateUsing(fn (string $state) =>'<span class="text-xs">' . e(Carbon::parse($state)->format('h:i A - m/d/y')) . '</span>')
                            ->color('warning'),
                    TextColumn::make('comment')
                        ->wrap()
                        ->html(),
                ]),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->mutateFormDataUsing(function (array $data): array {
                    $data['user_id'] = auth()->id();
                    return $data;
                })->disableCreateAnother(),
            ])
            ->actions([
                Tables\Actions\Action::make('attachment')
                    ->label('')
                    ->icon('heroicon-o-paper-clip')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false)
                    ->modalHeading('Attachments')
                    ->modalDescription('Click on an attachment to open it in a new tab.')
                    ->modalContent(function ($record) {
                        $attachments = $record['attachment'] ?? [];
                        return view('filament.components.attachment-modal', [
                            'attachments' => $attachments,
                        ]);
                    }),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->hidden(fn ($record) => !auth()->user()->isSuperAdmin() && auth()->id() !== $record->user_id),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->hidden(fn ($record) => !auth()->user()->isSuperAdmin() && auth()->id() !== $record->user_id),
            ])
            ->bulkActions([]);
    }
}
