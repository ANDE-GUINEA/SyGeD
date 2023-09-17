<?php

namespace App\Filament\Resources\ArreteResource\Pages;

use App\Models\Inbox;
use Filament\Actions;
use App\Models\Worker;
use App\Models\Message;
use App\Models\Validation;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use App\Filament\Resources\ArreteResource;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Components\MarkdownEditor;

class ViewArrete extends ViewRecord
{
    protected static string $resource = ArreteResource::class;
    protected static string $view = 'filament.resources.arrete-resource.pages.show-arrete';
    protected static ?string $title = 'DETAILS DE L\'ARRETE';



    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            // ->visible(
            //     auth()->user()->can('update', $this->record)
            // ),

            Action::make('soumettre')
                ->requiresConfirmation()
                ->label('SOUMETTRE')
                ->color('success')
                ->hidden(!auth()->user()->can('soumettre', $this->record))
                // ->hidden($this->soumission_possible())
                ->action(function () {
                    // dd(request());
                    $workDepartement = Worker::where('name', 'departement')->first();
                    if ($this->record->validations->count() >= 1) {
                        $comments = 'Prière de recevoir à nouveau pour examen et avis le projet d\'arrete de code ' . $this->record->code . '   et de libelle ' . $this->record->objet;
                    } else {
                        $comments = 'Prière de recevoir pour examen et avis le projet d\'arrete de code ' . $this->record->code . '   et de libelle ' . $this->record->objet;
                    }
                    //validation SGG prière de recevoir pour examen et avis le projet d\'arrete de code xxxx et de libelle ….
                    if (auth()->user()->worker->id === $workDepartement->id) {
                        $inbox = Inbox::where('name', 'SGG')->first();
                        // dd($inbox->user->id);
                        Validation::create([
                            'arrete_id' => $this->record->id,
                            'comments' => $comments,
                            'color' => '#1B5F8C',
                            'type' => 'soumis',

                            // 'comments' => $data['comments'],
                            // 'document' => $data['document'],
                        ]);
                        $messageTitle = 'Réception du projet de arrete ' . $this->record->code;
                        $message = 'Prière de recevoir le projet de arrete ' . $this->record->code . ' pour examen et avis. ';
                        Message::create([
                            'arrete_id' => $this->record->id,
                            'inbox_id' => $inbox->id,
                            'title' => $messageTitle,
                            'contenu' => $message,
                        ]);

                        $this->record->update([
                            'inbox_id' => $inbox->id,
                            'status' => 'Examen SGG',
                            'submit_at' => now(),
                        ]);
                        // $this->archive();
                        // $recipient = $this->record->user();
                        $recipient = auth()->user();
                        $titleM = 'Le projet de arrete ' . $this->record->code . ' a été transmis avec succès ';
                        $recipient->notify(
                            Notification::make()
                                ->title("$titleM")
                                ->toDatabase(),
                        );
                        Notification::make()
                            ->title('Projet transmis avec succès.')
                            ->success()
                            ->send();
                        return redirect()->route('filament.admin.resources.arretes.index');
                    }
                }),


            Action::make('valide')
                ->label('VALIDER')
                ->color('success')
                ->hidden(!auth()->user()->can('valide', $this->record))
                // ->modalWidth('3xl')
                ->modalSubmitActionLabel('OUI, JE CONFIRME')
                ->requiresConfirmation()
                ->successRedirectUrl(route('filament.admin.resources.arretes.index'))

                ->action(function (array $data) {
                    // dd('ok');
                    $workSgg = Worker::where('name', 'sgg')->first();
                    $workPm = Worker::where('name', 'primature')->first();

                    //validation SGG
                    if (auth()->user()->worker->id === $workSgg->id) {
                        Validation::create([
                            'arrete_id' => $this->record->id,
                            'comments' => 'Nous sommes ravis de vous informer que le projet d\'arrete portant le code ' . $this->record->code . ' a été officiellement validé par notre département.',
                            'color' => '#43A047',
                            'type' => 'valider',
                        ]);
                        $inbox = Inbox::where('name', 'PRIMATURE')->first();
                        $inbox1 = Inbox::where('name', $this->record->init)->first();
                        $this->record->update([
                            'inbox_id' => $inbox->id,
                            'status' => 'Examen Primature',
                            'okSGG' => true,

                        ]);
                        $messageTitle = 'Notification de Validation du Projet de Décret (' . $this->record->code . ')';
                        $message = 'Nous avons le plaisir de vous informer que le projet d\'arrete portant le code (' . $this->record->code . ') a été validé à notre niveau.';
                        Message::create([
                            'arrete_id' => $this->record->id,
                            'inbox_id' => $inbox->id,
                            'title' => $messageTitle,
                            'contenu' => $message,
                        ]);
                        Message::create([
                            'arrete_id' => $this->record->id,
                            'inbox_id' => $inbox1->id,
                            'title' => $messageTitle,
                            'contenu' => $message,
                        ]);

                        $recipient = auth()->user();
                        $recipient->notify(
                            Notification::make()
                                ->title('Decret retourner avec succès!')
                                ->toDatabase(),
                        );
                        Notification::make()
                            ->title('Validé avec succès')
                            ->success()
                            ->send();
                        return redirect()->route('filament.admin.resources.arretes.index');
                    }
                    //Validation de la PRIMATURE
                    if (auth()->user()->worker->id === $workPm->id) {
                        # code...
                        Validation::create([
                            'arrete_id' => $this->record->id,
                            'comments' => 'Nous sommes ravis de vous informer que le projet d\'arrete portant le code ' . $this->record->code . ' a été officiellement validé par notre département.',
                            // 'comments' => $data['comments'],
                            'color' => '#43A047',
                            'type' => 'valider',
                        ]);
                        $inbox = Inbox::where('name', $this->record->init)->first();
                        $inbox1 = Inbox::where('name', 'SGG')->first();
                        $inbox2 = Inbox::where('name', $this->record->init)->first();
                        $this->record->update([
                            'inbox_id' => $inbox->id,
                            'status' => 'En Attente Signature',
                            'okPRIMATURE' => true,
                        ]);
                        $messageTitle = 'Notification de Validation du Projet de Décret (' . $this->record->code . ')';
                        $message = 'Nous avons le plaisir de vous informer que le projet d\'arrete portant le code (' . $this->record->code . ') a été validé à notre niveau.';
                        Message::create([
                            'arrete_id' => $this->record->id,
                            'inbox_id' => $inbox1->id,
                            'title' => $messageTitle,
                            'contenu' => $message,
                        ]);
                        Message::create([
                            'arrete_id' => $this->record->id,
                            'inbox_id' => $inbox2->id,
                            'title' => $messageTitle,
                            'contenu' => $message,
                        ]);

                        $recipient = auth()->user();
                        $recipient->notify(
                            Notification::make()
                                ->title('Decret validé avec succès!')
                                ->toDatabase(),
                        );
                        Notification::make()
                            ->title('Validé avec succès')
                            ->success()
                            ->send();
                        return redirect()->route('filament.admin.resources.arretes.index');
                    }
                    //Validation de la PRG
                }),
            // ->stickyModalHeader()
            // ->stickyModalFooter()
            // ->closeModalByClickingAway(false)
            // ->slideOver(),

            Action::make('signe')
                ->label('SIGNER')
                ->color('success')
                ->modalWidth('lg')
                // ->requiresConfirmation()
                ->hidden(!auth()->user()->can('signe', $this->record))
                ->form([
                    // TextInput::make('title')
                    //     ->required()
                    //     ->label('TITRE DU DOCUMMENT'),
                    FileUpload::make('document')
                        ->required()
                        ->label('DOCUMENT SIGNE')
                        // ->multiple()
                        ->enableOpen()
                        // ->maxSize(1024)
                        ->directory('singned_files')
                        ->preserveFilenames()
                        ->enableDownload(),

                ])
                // ->disabledForm()
                ->action(function (array $data) {
                    // Dossier::create([
                    //     'arrete_id' => $this->record->id,
                    //     'title' => 'Les documents signé du arrete' . $this->record->code . '',
                    //     // 'private' => $data['private'],
                    //     'document' => $data['document'],
                    // ]);
                    Validation::create([
                        'arrete_id' => $this->record->id,
                        'comments' => 'L\'arreté portant le code ' . $this->record->code . ' a été officiellement validé et signé.',
                        // 'comments' => $data['comments'],
                        // 'document' => $data['document'],
                        'color' => '#43A047',
                        'type' => 'valider',
                    ]);
                    $inbox = Inbox::where('name', 'SGG')->first();
                    $inbox1 = Inbox::where('name', 'PRIMATURE')->first();
                    $inbox2 = Inbox::where('name', $this->record->init)->first();
                    $this->record->update([
                        'inbox_id' => $inbox->id,
                        'status' => 'Signé',
                        'signe' => $data['document'],
                        // 'okPRG' => true,
                        'Signé' => true,
                    ]);
                    $messageTitle = 'Notification de Signature l\'arreté portant le code (' . $this->record->code . ')';
                    $message = 'Nous avons le plaisir de vous informer l\'arreté portant le code ' . $this->record->code . ' a été signé et de libellé.';
                    Message::create([
                        'arrete_id' => $this->record->id,
                        'inbox_id' => $inbox->id,
                        'title' => $messageTitle,
                        'contenu' => $message,
                    ]);
                    Message::create([
                        'arrete_id' => $this->record->id,
                        'inbox_id' => $inbox1->id,
                        'title' => $messageTitle,
                        'contenu' => $message,
                    ]);
                    Message::create([
                        'arrete_id' => $this->record->id,
                        'inbox_id' => $inbox2->id,
                        'title' => $messageTitle,
                        'contenu' => $message,
                    ]);

                    $recipient = auth()->user();
                    $recipient->notify(
                        Notification::make()
                            ->title('Decret validé avec succès!')
                            ->toDatabase(),
                    );
                    Notification::make()
                        ->title('Validé avec succès')
                        ->success()
                        ->send();
                    return redirect()->route('filament.admin.resources.arretes.index');
                })
                ->stickyModalHeader()
                ->modalHeading('Enregistrement des documents signé')
                ->stickyModalFooter()
                ->closeModalByClickingAway(false)
                ->slideOver(),
            Action::make('pulbish')
                ->label('SAUVEGARDER')
                ->color('success')
                ->modalWidth('lg')
                // ->requiresConfirmation()
                ->hidden(!auth()->user()->can('pulbish', $this->record))
                ->form([
                    // TextInput::make('title')
                    //     ->required()
                    //     ->label('TITRE DU DOCUMMENT'),
                    FileUpload::make('document')
                        ->required()
                        ->label('DOCUMENT')
                        ->multiple()
                        ->enableOpen()
                        // ->maxSize(1024)
                        ->directory('singned_files')
                        ->preserveFilenames()
                        ->enableDownload(),

                ])
                // ->disabledForm()
                ->action(function (array $data) {

                    Validation::create([
                        'arrete_id' => $this->record->id,
                        'comments' => 'L\'arreté portant le code ' . $this->record->code . ' a été publié.',
                        'color' => '#43A047',
                        'type' => 'valider',
                    ]);
                    $inbox = Inbox::where('name', 'SGG')->first();
                    $inbox1 = Inbox::where('name', 'PRIMATURE')->first();
                    $inbox2 = Inbox::where('name', $this->record->init)->first();
                    $this->record->update([
                        'inbox_id' => $inbox->id,
                        'status' => 'Signé',
                        'publie' => $data['document'],
                        // 'okPRG' => true,
                        'Publié' => true,
                    ]);
                    $messageTitle = 'Notification de Publication l\'arreté portant le code (' . $this->record->code . ')';
                    $message = 'Nous avons le plaisir de vous informer l\'arreté portant le code ' . $this->record->code . ' a été publié.';
                    Message::create([
                        'arrete_id' => $this->record->id,
                        'inbox_id' => $inbox->id,
                        'title' => $messageTitle,
                        'contenu' => $message,
                    ]);
                    Message::create([
                        'arrete_id' => $this->record->id,
                        'inbox_id' => $inbox1->id,
                        'title' => $messageTitle,
                        'contenu' => $message,
                    ]);
                    Message::create([
                        'arrete_id' => $this->record->id,
                        'inbox_id' => $inbox2->id,
                        'title' => $messageTitle,
                        'contenu' => $message,
                    ]);

                    $recipient = auth()->user();
                    $recipient->notify(
                        Notification::make()
                            ->title('Arreté validé avec succès!')
                            ->toDatabase(),
                    );
                    Notification::make()
                        ->title('Publié avec succès')
                        ->success()
                        ->send();
                    return redirect()->route('filament.admin.resources.arretes.index');
                })
                ->stickyModalHeader()
                ->modalHeading('Enregistrement des documents signé')
                ->stickyModalFooter()
                ->closeModalByClickingAway(false)
                ->slideOver(),

            Action::make('retourne')
                ->hidden(!auth()->user()->can('retourne', $this->record))
                ->color('warning')
                ->label('RETOURNER')
                ->modalWidth('2xl')
                // ->requiresConfirmation()
                ->successRedirectUrl(route('filament.admin.resources.arretes.index'))
                ->form([
                    RichEditor::make('comments')
                        ->required()
                        ->label('VOTRE COMMENTAIRE'),
                    FileUpload::make('document')
                        // ->required()
                        ->label('DOCUMENT DU DECRET')
                        ->enableOpen()
                        // ->maxSize(1024)
                        ->directory('validation_files')
                        ->preserveFilenames()
                        ->enableDownload(),
                ])
                ->action(function (array $data) {
                    $workSgg = Worker::where('name', 'sgg')->first();
                    $workPm = Worker::where('name', 'primature')->first();
                    $workPrg = Worker::where('name', 'prg')->first();

                    if (auth()->user()->IsWorker) {
                        //Action du SGG
                        if (auth()->user()->worker->id === $workSgg->id) {
                            # code...
                            Validation::create([
                                'arrete_id' => $this->record->id,
                                'comments' => $data['comments'],
                                'document' => $data['document'],
                                'color' => '#E24A68',
                                'type' => 'retourner',
                            ]);
                            $inbox = Inbox::where('name', $this->record->init)->first();
                            $this->record->update([
                                'inbox_id' => $inbox->id,
                                'status' => 'Retour SGG',
                                'Submit' => false,
                            ]);

                            $messageTitle = 'Notification de retour du Projet de Décret (' . $this->record->code . ')';
                            $message = 'Prière de recevoir le projet de arrete ' . $this->record->code . 'pour correction. ';
                            Message::create([
                                'arrete_id' => $this->record->id,
                                'inbox_id' => $inbox->id,
                                'title' => $messageTitle,
                                'contenu' => $message,
                            ]);
                            Notification::make()
                                ->title('Rétourner avec succès!')
                                ->success()
                                ->send();

                            $recipient = auth()->user();

                            $recipient->notify(
                                Notification::make()
                                    ->title('Decret retourner avec succès!')
                                    ->toDatabase(),
                            );
                            return redirect()->route('filament.admin.resources.arretes.index');
                        }

                        //Action de la PRIMATURE
                        if (auth()->user()->worker->id === $workPm->id) {
                            Validation::create([
                                'arrete_id' => $this->record->id,
                                'comments' => $data['comments'],
                                'document' => $data['document'],
                                'color' => '#E24A68',
                                'type' => 'retourner',
                            ]);
                            $inbox = Inbox::where('name', $this->record->init)->first();
                            $inbox1 = Inbox::where('name', 'SGG')->first();
                            $inbox2 = Inbox::where('name', $this->record->init)->first();
                            $this->record->update([
                                'inbox_id' => $inbox->id,
                                'status' => 'Retour Primature',
                                'okSGG' => false,
                                'Submit' => false,
                            ]);
                            $messageTitle = 'Notification de retour du Projet de Décret (' . $this->record->code . ')';
                            $message = 'Prière de recevoir le projet de arrete ' . $this->record->code . ' correction. ';
                            Message::create([
                                'arrete_id' => $this->record->id,
                                'inbox_id' => $inbox1->id,
                                'title' => $messageTitle,
                                'contenu' => $message,
                            ]);
                            Message::create([
                                'arrete_id' => $this->record->id,
                                'inbox_id' => $inbox2->id,
                                'title' => $messageTitle,
                                'contenu' => $message,
                            ]);
                            Notification::make()
                                ->title('Rétourner avec succès!')
                                ->success()
                                ->send();

                            $recipient = auth()->user();
                            $recipient->notify(
                                Notification::make()
                                    ->title('Decret retourner avec succès!')
                                    ->toDatabase(),
                            );
                            return redirect()->route('filament.admin.resources.arretes.index');
                        }

                        //Action de la PRESIDENCE
                        if (auth()->user()->worker->id === $workPrg->id) {
                            # code...
                            Validation::create([
                                'arrete_id' => $this->record->id,
                                'comments' => $data['comments'],
                                'document' => $data['document'],
                                'color' => '#E24A68',
                                'type' => 'retourner',
                            ]);
                            $inbox = Inbox::where('name', $this->record->init)->first();
                            $inbox1 = Inbox::where('name', 'SGG')->first();
                            $inbox2 = Inbox::where('name', 'PRIMATURE')->first();
                            $this->record->update([
                                'inbox_id' => $inbox->id,
                                'status' => 'Retour Presidence',
                                'okPRIMATURE' => false,
                                'okSGG' => false,
                            ]);
                            $messageTitle = 'Notification de Retour du Projet de Décret (' . $this->record->code . ')';
                            $messageTitle = 'Notification de Retour du Projet de Décret (' . $this->record->code . ')';
                            $message = 'Nous tenons à vous informer que nous avons décidé de retourner le projet d\'arrete portant le code ' . $this->record->code;
                            Message::create([
                                'arrete_id' => $this->record->id,
                                'inbox_id' => $inbox->id,
                                'title' => $messageTitle,
                                'contenu' => $message,
                            ]);
                            Message::create([
                                'arrete_id' => $this->record->id,
                                'inbox_id' => $inbox1->id,
                                'title' => $messageTitle,
                                'contenu' => $message,
                            ]);
                            Message::create([
                                'arrete_id' => $this->record->id,
                                'inbox_id' => $inbox2->id,
                                'title' => $messageTitle,
                                'contenu' => $message,
                            ]);
                            Notification::make()
                                ->title('Rétourner avec succès!')
                                ->success()
                                ->send();

                            $recipient = auth()->user();
                            $recipient->notify(
                                Notification::make()
                                    ->title('Decret retourner avec succès!')
                                    ->toDatabase(),
                            );
                            return redirect()->route('filament.admin.resources.arretes.index');
                        }
                    }
                })
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->closeModalByClickingAway(false)
                ->slideOver(),

            Action::make('publish')
                ->hidden(!auth()->user()->can('publish', $this->record))
                ->steps([
                    Step::make('Name')
                        ->description('Give the category a unique name')
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->disabled()
                                ->required()
                                ->unique(Category::class, 'slug'),
                        ])
                        ->columns(2),
                    Step::make('Description')
                        ->description('Add some extra details')
                        ->schema([
                            MarkdownEditor::make('description'),
                        ]),
                    Step::make('Visibility')
                        ->description('Control who can view it')
                        ->schema([
                            Toggle::make('is_visible')
                                ->label('Visible to customers.')
                                ->default(true),
                        ]),
                ]),
        ];
    }
}
