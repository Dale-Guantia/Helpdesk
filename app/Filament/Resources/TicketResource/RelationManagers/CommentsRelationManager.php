<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;


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
                    Split::make([
                        TextColumn::make('user.name')
                            ->translateLabel()
                            ->weight('bold')
                            ->grow(false),
                        TextColumn::make('created_at')
                            ->translateLabel()
                            ->dateTime()
                            ->color('secondary'),
                    ]),
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
                })
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
                    ->hidden(fn ($record) => !auth()->user()->isAdmin() && auth()->id() !== $record->user_id),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->hidden(fn ($record) => !auth()->user()->isAdmin() && auth()->id() !== $record->user_id),
            ])
            ->bulkActions([]);
    }
}
