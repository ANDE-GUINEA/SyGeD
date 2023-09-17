<?php

namespace App\Filament\Resources\DecretResource\Pages;

use App\Models\Inbox;
use Filament\Actions;
use App\Models\Decret;
use App\Models\Worker;
use App\Models\Dossier;
use App\Models\Message;
use App\Models\Validation;
use Filament\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use App\Filament\Resources\DecretResource;
use App\Models\Archive;
use App\Models\Publie;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class ViewDecret extends ViewRecord implements HasShieldPermissions
{
    protected static string $resource = DecretResource::class;
    protected static string $view = 'filament.resources.decret-resource.pages.show-decret';
    protected static ?string $title = 'DETAILS DU DECRET';
    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any', 'retourner', 'valider', 'soumettre'];
    }

    protected function archive()
    {
        return Archive::create([
            'decret_id' => $this->record->id,
            'type_id' => $this->record->type_id,
            'code' => $this->record->code,
            'init' => $this->record->init,
            'objet' => $this->record->objet,
            'content' => $this->record->content,
            'motif' => $this->record->motif,
            'visa' => $this->record->visa,
            'autres' => $this->record->autres,
            'references' => $this->record->references,
            'confidential' => $this->record->confidential,
        ]);
    }

    protected function retour_possible()
    {
        if (auth()->user()->worker) {
            if (auth()->user()->worker->name == 'PRIMATURE' || auth()->user()->worker->name == 'SGG' || auth()->user()->worker->name == 'PRG') {
                if (!$this->record->okPrimature || !$this->record->okPRG || !$this->record->okSGG) {
                    return auth()
                        ->user()
                        ->can('valider', $this->record);
                } else {
                    return !auth()
                        ->user()
                        ->can('valider', $this->record);
                }
                return auth()
                    ->user()
                    ->can('valider', $this->record);
            } else {
                return !auth()
                    ->user()
                    ->can('valider', $this->record);
            }
        } else {
            return auth()
                ->user()
                ->can('valider', $this->record);
        }
    }

    protected function validate_possible()
    {
        if (auth()->user()->worker) {
            if (auth()->user()->worker->name == 'PRIMATURE' || auth()->user()->worker->name == 'SGG') {
                if (!$this->record->okPrimature || !$this->record->okPRG || !$this->record->okSGG) {
                    return auth()
                        ->user()
                        ->can('valider', $this->record);
                } else {
                    return !auth()
                        ->user()
                        ->can('valider', $this->record);
                }
                return auth()
                    ->user()
                    ->can('valider', $this->record);
            } else {
                return !auth()
                    ->user()
                    ->can('valider', $this->record);
            }
        } else {
            return auth()
                ->user()
                ->can('valider', $this->record);
        }
    }

    protected function confirm_possible()
    {
        if (auth()->user()->worker) {
            if (auth()->user()->worker->name === 'PRG') {
                return !auth()
                    ->user()
                    ->can('retourne', $this->record);
            } else {
                return auth()
                    ->user()
                    ->can('retourne', $this->record);
            }
        } else {
            return auth()
                ->user()
                ->can('retourne', $this->record);
        }
    }

    protected function soumission_possible()
    {
        if (auth()->user()->worker) {
            if (auth()->user()->worker->name == 'DEPARTEMENT' && auth()->user()->departement->inbox->id === $this->record->inbox->id) {
                return auth()
                    ->user()
                    ->can('soumission', $this->record);
            } else {
                return !auth()
                    ->user()
                    ->can('soumission', $this->record);
            }
        } else {
            return auth()
                ->user()
                ->can('soumission', $this->record);
        }
    }

    protected function private_possible()
    {
        $myP = Decret::where('init', $this->record->init)->first();
        if (auth()->user()->worker) {
            // || auth()->user()->departement->name == $myP->init
            if (auth()->user()->worker->name == 'PRG' && auth()->user()->departement->inbox->id === $this->record->inbox->id) {
                return auth()
                    ->user()
                    ->can('retourne', $this->record);
            } else {
                return !auth()
                    ->user()
                    ->can('retourne', $this->record);
            }
        } else {
            return auth()
                ->user()
                ->can('retourne', $this->record);
        }
    }

    protected function publie_possible()
    {
        $myP = Decret::where('init', $this->record->init)->first();
        if (auth()->user()->worker) {
            if (auth()->user()->worker->name == 'SGG' && auth()->user()->departement->inbox->id === $this->record->inbox->id && $this->record->status === 'Signé') {
                return auth()
                    ->user()
                    ->can('retourne', $this->record);
            } else {
                return !auth()
                    ->user()
                    ->can('retourne', $this->record);
            }
        } else {
            return auth()
                ->user()
                ->can('retourne', $this->record);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Action::make('pulbish')
                ->label('SAUVEGARDER')
                ->color('success')
                // ->requiresConfirmation()
                // ->hidden($this->publie_possible())
                ->hidden(!auth()->user()->can('pulbish', $this->record))
                ->form([
                    FileUpload::make('document')
                        ->required()
                        ->label('DOCUMENT')
                        // ->multiple()
                        ->enableOpen()
                        // ->maxSize(1024)
                        ->directory('publish_files')
                        ->preserveFilenames()
                        ->enableDownload(),

                ])
                ->action(function (array $data) {
                    $workSgg = Worker::where('name', 'SGG')->first();
                    if (auth()->user()->worker->id === $workSgg->id) {
                        # code...
                        Validation::create([
                            'decret_id' => $this->record->id,
                            'comments' => 'Le projet de décret portant le code ' . $this->record->code . ' a été Publié',
                            'color' => '#43A047',
                            'type' => 'valider',
                        ]);
                        $inbox = Inbox::where('name', 'SGG')->first();
                        $inbox1 = Inbox::where('name', 'PRIMATURE')->first();
                        $inbox2 = Inbox::where('name', $this->record->init)->first();
                        $this->record->update([
                            'inbox_id' => $inbox->id,
                            'status' => 'Signé',
                            'okPRG' => true,
                            'Publié' => true,
                            'publie' => $data['document'],
                        ]);
                        $messageTitle = 'Notification de Validation du Projet de Décret (' . $this->record->code . ')';
                        $message = 'Nous avons le plaisir de vous informer que le projet de décret portant le code (' . $this->record->code . ') a été Publié.';
                        Message::create([
                            'decret_id' => $this->record->id,
                            'inbox_id' => $inbox->id,
                            'title' => $messageTitle,
                            'contenu' => $message,
                        ]);
                        Message::create([
                            'decret_id' => $this->record->id,
                            'inbox_id' => $inbox1->id,
                            'title' => $messageTitle,
                            'contenu' => $message,
                        ]);
                        Message::create([
                            'decret_id' => $this->record->id,
                            'inbox_id' => $inbox2->id,
                            'title' => $messageTitle,
                            'contenu' => $message,
                        ]);

                        $recipient = auth()->user();
                        $recipient->notify(
                            Notification::make()
                                ->title('Decret publié avec succès!')
                                ->toDatabase(),
                        );
                        Notification::make()
                            ->title('Publié avec succès')
                            ->success()
                            ->send();
                        return redirect()->route('filament.admin.resources.decrets.index');
                    }
                })
                ->stickyModalHeader()
                ->modalHeading('Enregistrement des documents signé')
                ->stickyModalFooter()
                ->closeModalByClickingAway(false)
                ->slideOver(),

            Action::make('signe')
                ->label('VALIDER LE DECRET')
                ->color('success')
                // ->requiresConfirmation()
                // ->hidden($this->private_possible())
                ->hidden(!auth()->user()->can('signe', $this->record))
                ->form([
                    // TextInput::make('title')
                    //     ->required()
                    //     ->label('TITRE DU DOCUMMENT'),
                    FileUpload::make('document')
                        ->required()
                        ->label('DOCUMENT')
                        // ->multiple()
                        ->enableOpen()
                        // ->maxSize(1024)
                        ->directory('private_files')
                        ->preserveFilenames()
                        ->enableDownload(),

                    // Toggle::make('private')
                    //     ->onIcon('heroicon-m-lock-closed')
                    //     ->offIcon('heroicon-m-lock-open')
                    //     ->onColor('danger')
                    //     ->offColor('success')
                    //     ->label('CET DOCUMENT EST CONFIDENTIEL ?')
                ])
                // ->disabledForm()
                ->action(function (array $data) {
                    // Dossier::create([
                    //     'decret_id' => $this->record->id,
                    //     'title' => 'Les documents signé du decret' . $this->record->code . '',
                    //     // 'private' => $data['private'],
                    //     'document' => $data['document'],
                    // ]);
                    $workPrg = Worker::where('name', 'prg')->first();
                    if (auth()->user()->worker->id === $workPrg->id) {
                        # code...
                        Validation::create([
                            'decret_id' => $this->record->id,
                            'comments' => 'Le projet de décret portant le code ' . $this->record->code . ' a été officiellement validé et signé par SEM le President de la République.',
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
                            'okPRG' => true,
                            'Signé' => true,
                            'signe' => $data['document'],

                        ]);
                        $messageTitle = 'Notification de Validation du Projet de Décret (' . $this->record->code . ')';
                        $message = 'Nous avons le plaisir de vous informer que le projet de décret portant le code (' . $this->record->code . ') a été signé par SEM le President de la République.';
                        Message::create([
                            'decret_id' => $this->record->id,
                            'inbox_id' => $inbox->id,
                            'title' => $messageTitle,
                            'contenu' => $message,
                        ]);
                        Message::create([
                            'decret_id' => $this->record->id,
                            'inbox_id' => $inbox1->id,
                            'title' => $messageTitle,
                            'contenu' => $message,
                        ]);
                        Message::create([
                            'decret_id' => $this->record->id,
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
                        return redirect()->route('filament.admin.resources.decrets.index');
                    }
                })
                ->stickyModalHeader()
                ->modalHeading('Enregistrement des documents signé')
                ->stickyModalFooter()
                ->closeModalByClickingAway(false)
                ->slideOver(),

            Action::make('soumettre')
                ->requiresConfirmation()
                ->label('SOUMETTRE')
                ->color('success')
                // ->hidden($this->soumission_possible())
                ->hidden(!auth()->user()->can('soumettre', $this->record))
                ->action(function () {
                    $workDepartement = Worker::where('name', 'departement')->first();
                    if ($this->record->validations->count() >= 1) {

                        $comments = 'Prière de recevoir à nouveau pour examen et avis le projet de décret de code ' . $this->record->code . '   et de libelle ' . $this->record->objet;
                    } else {
                        $comments = 'Prière de recevoir pour examen et avis le projet de décret de code ' . $this->record->code . '   et de libelle ' . $this->record->objet;
                    }
                    //validation SGG prière de recevoir pour examen et avis le projet de décret de code xxxx et de libelle ….
                    if (auth()->user()->worker->id === $workDepartement->id) {
                        $inbox = Inbox::where('name', 'SGG')->first();
                        // dd($inbox->user->id);
                        Validation::create([
                            'decret_id' => $this->record->id,
                            'comments' => $comments,
                            'color' => '#1B5F8C',
                            'type' => 'soumis',

                            // 'comments' => $data['comments'],
                            // 'document' => $data['document'],
                        ]);
                        $messageTitle = 'Réception du projet de decret ' . $this->record->code;
                        $message = 'Prière de recevoir le projet de decret ' . $this->record->code . ' pour examen et avis. ';
                        Message::create([
                            'decret_id' => $this->record->id,
                            'inbox_id' => $inbox->id,
                            'title' => $messageTitle,
                            'contenu' => $message,
                        ]);

                        $this->record->update([
                            'inbox_id' => $inbox->id,
                            'status' => 'Examen SGG',
                            'submit_at' => now(),
                            'Submit' => true,
                        ]);
                        $this->archive();
                        // $recipient = $this->record->user();
                        $recipient = auth()->user();
                        $titleM = 'Le projet de decret ' . $this->record->code . ' a été transmis avec succès ';
                        $recipient->notify(
                            Notification::make()
                                ->title("$titleM")
                                ->toDatabase(),
                        );
                        Notification::make()
                            ->title('Projet transmis avec succès.')
                            ->success()
                            ->send();
                        return redirect()->route('filament.admin.resources.decrets.index');
                    }
                }),

            Action::make('retourne')
                ->label('RETOURNER')
                ->color('warning')
                ->modalWidth('2xl')
                // ->hidden($this->retour_possible())
                ->hidden(!auth()->user()->can('retourne', $this->record))
                // ->requiresConfirmation()
                ->successRedirectUrl(route('filament.admin.resources.decrets.index'))

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
                    $workDep = Worker::where('name', 'Departement')->first();
                    $workSgg = Worker::where('name', 'sgg')->first();
                    $workPm = Worker::where('name', 'primature')->first();
                    $workPrg = Worker::where('name', 'prg')->first();

                    if (auth()->user()->IsWorker) {
                        //Action du SGG
                        if (auth()->user()->worker->id === $workSgg->id) {
                            # code...
                            Validation::create([
                                'decret_id' => $this->record->id,
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
                            $message = 'Prière de recevoir le projet de decret ' . $this->record->code . 'pour correction. ';
                            Message::create([
                                'decret_id' => $this->record->id,
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
                            return redirect()->route('filament.admin.resources.decrets.index');
                        }

                        //Action de la PRIMATURE
                        if (auth()->user()->worker->id === $workPm->id) {
                            Validation::create([
                                'decret_id' => $this->record->id,
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
                            $message = 'Prière de recevoir le projet de decret ' . $this->record->code . ' correction. ';
                            Message::create([
                                'decret_id' => $this->record->id,
                                'inbox_id' => $inbox1->id,
                                'title' => $messageTitle,
                                'contenu' => $message,
                            ]);
                            Message::create([
                                'decret_id' => $this->record->id,
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
                            return redirect()->route('filament.admin.resources.decrets.index');
                        }

                        //Action de la PRESIDENCE
                        if (auth()->user()->worker->id === $workPrg->id) {
                            # code...
                            Validation::create([
                                'decret_id' => $this->record->id,
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
                                'Submit' => false,
                            ]);
                            $messageTitle = 'Notification de Retour du Projet de Décret (' . $this->record->code . ')';
                            $messageTitle = 'Notification de Retour du Projet de Décret (' . $this->record->code . ')';
                            $message = 'Nous tenons à vous informer que nous avons décidé de retourner le projet de décret portant le code ' . $this->record->code;
                            Message::create([
                                'decret_id' => $this->record->id,
                                'inbox_id' => $inbox->id,
                                'title' => $messageTitle,
                                'contenu' => $message,
                            ]);
                            Message::create([
                                'decret_id' => $this->record->id,
                                'inbox_id' => $inbox1->id,
                                'title' => $messageTitle,
                                'contenu' => $message,
                            ]);
                            Message::create([
                                'decret_id' => $this->record->id,
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
                            return redirect()->route('filament.admin.resources.decrets.index');
                        }
                    }
                })
                ->stickyModalHeader()
                ->stickyModalFooter()
                ->closeModalByClickingAway(false)
                ->slideOver(),

            Action::make('valide')
                ->label('VALIDER')
                ->color('success')
                // ->hidden($this->validate_possible())
                // ->modalWidth('3xl')
                ->hidden(!auth()->user()->can('valide', $this->record))
                ->modalSubmitActionLabel('OUI, JE CONFIRME')
                ->requiresConfirmation()
                ->successRedirectUrl(route('filament.admin.resources.decrets.index'))

                ->action(function (array $data) {
                    // dd('ok');
                    $workSgg = Worker::where('name', 'sgg')->first();
                    $workPm = Worker::where('name', 'primature')->first();

                    //validation SGG
                    if (auth()->user()->worker->id === $workSgg->id) {
                        Validation::create([
                            'decret_id' => $this->record->id,
                            'comments' => 'Nous sommes ravis de vous informer que le projet de décret portant le code ' . $this->record->code . ' a été officiellement validé par notre département.',
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
                        $message = 'Nous avons le plaisir de vous informer que le projet de décret portant le code (' . $this->record->code . ') a été validé à notre niveau.';
                        Message::create([
                            'decret_id' => $this->record->id,
                            'inbox_id' => $inbox->id,
                            'title' => $messageTitle,
                            'contenu' => $message,
                        ]);
                        Message::create([
                            'decret_id' => $this->record->id,
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
                        return redirect()->route('filament.admin.resources.decrets.index');
                    }
                    //Validation de la PRIMATURE
                    if (auth()->user()->worker->id === $workPm->id) {
                        # code...
                        Validation::create([
                            'decret_id' => $this->record->id,
                            'comments' => 'Nous sommes ravis de vous informer que le projet de décret portant le code ' . $this->record->code . ' a été officiellement validé par notre département.',
                            // 'comments' => $data['comments'],
                            'color' => '#43A047',
                            'type' => 'valider',
                        ]);
                        $inbox = Inbox::where('name', 'PRG')->first();
                        $inbox1 = Inbox::where('name', 'SGG')->first();
                        $inbox2 = Inbox::where('name', $this->record->init)->first();
                        $this->record->update([
                            'inbox_id' => $inbox->id,
                            'status' => 'Examen Presidence',
                            'okPRIMATURE' => true,
                        ]);
                        $messageTitle = 'Notification de Validation du Projet de Décret (' . $this->record->code . ')';
                        $message = 'Nous avons le plaisir de vous informer que le projet de décret portant le code (' . $this->record->code . ') a été validé à notre niveau.';
                        Message::create([
                            'decret_id' => $this->record->id,
                            'inbox_id' => $inbox->id,
                            'title' => $messageTitle,
                            'contenu' => $message,
                        ]);
                        Message::create([
                            'decret_id' => $this->record->id,
                            'inbox_id' => $inbox1->id,
                            'title' => $messageTitle,
                            'contenu' => $message,
                        ]);
                        Message::create([
                            'decret_id' => $this->record->id,
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
                        return redirect()->route('filament.admin.resources.decrets.index');
                    }
                    //Validation de la PRG
                }),
            // ->stickyModalHeader()
            // ->stickyModalFooter()
            // ->closeModalByClickingAway(false)
            // ->slideOver(),
        ];
    }

    public function publishAction(): Action
    {
        return Action::make('publish')
            // ->requiresConfirmation()
            ->form([RichEditor::make('comments')->label('VOTRE COMMENTAIRE')])
            ->action(function (array $arguments) {
                dd('ok');
            });
    }
    // public function approve()
    // {
    //     dd('ok');
    // }
}
