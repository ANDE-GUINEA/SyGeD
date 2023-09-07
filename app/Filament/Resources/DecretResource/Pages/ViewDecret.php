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

    protected function retour_possible()
    {
        if (auth()->user()->worker) {
            # code...
            if (auth()->user()->worker->name == 'PRIMATURE' || auth()->user()->worker->name == 'PRG' || auth()->user()->worker->name == 'SGG') {
                if ($this->record->okPRIMATURE == false || $this->record->okSGG == false || $this->record->okPRG == false) {
                    return auth()
                        ->user()
                        ->can('retourne', $this->record);
                } else {
                    return auth()
                        ->user()
                        ->can('retourne', $this->record);
                }
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

    protected function validate_possible()
    {
        if (auth()->user()->worker) {
            if (auth()->user()->worker->name == 'PRIMATURE' || auth()->user()->worker->name == 'SGG' || auth()->user()->worker->name == 'PRG') {
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Action::make('validerPgr')
                ->label('VALIDER LE DECRET')
                ->color('success')
                // ->requiresConfirmation()
                ->hidden($this->private_possible())
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
                    Dossier::create([
                        'decret_id' => $this->record->id,
                        'title' => 'Les documents signé du decret' . $this->record->code . '',
                        // 'private' => $data['private'],
                        'document' => $data['document'],
                    ]);
                    $workPrg = Worker::where('name', 'prg')->first();
                    if (auth()->user()->worker->id === $workPrg->id) {
                        # code...
                        Validation::create([
                            'decret_id' => $this->record->id,
                            'comments' => 'Nous sommes ravis de vous informer que le projet de décret portant le code ' . $this->record->code . ' a été officiellement validé et signé par le President de la République.',
                            // 'comments' => $data['comments'],
                            // 'document' => $data['document'],
                            'color' => '#1B5E20',
                            'type' => 'valider',
                        ]);
                        $inbox = Inbox::where('name', 'SGG')->first();
                        $inbox1 = Inbox::where('name', 'PRIMATURE')->first();
                        $inbox2 = Inbox::where('name', $this->record->init)->first();
                        $this->record->update([
                            'inbox_id' => $inbox->id,
                            'status' => 'Signé par la Presidence',
                            'okPRG' => true,
                        ]);
                        $messageTitle = 'Notification de Validation du Projet de Décret (' . $this->record->code . ')';
                        $message = 'Nous avons le plaisir de vous informer que le projet de décret portant le code (' . $this->record->code . ') a été signé et validé par le President de la République.';
                        Message::create([
                            'decret_id' => $this->record->id,
                            'inbox_id' => $inbox->id,
                            'title' => $messageTitle,
                            'content' => $message,
                        ]);
                        Message::create([
                            'decret_id' => $this->record->id,
                            'inbox_id' => $inbox1->id,
                            'title' => $messageTitle,
                            'content' => $message,
                        ]);
                        Message::create([
                            'decret_id' => $this->record->id,
                            'inbox_id' => $inbox2->id,
                            'title' => $messageTitle,
                            'content' => $message,
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

            Action::make('soumission')
                ->requiresConfirmation()
                ->label('SOUMISSION')
                ->color('danger')
                ->hidden($this->soumission_possible())
                ->action(function () {
                    $workDepartement = Worker::where('name', 'departement')->first();
                    if ($this->record->validations->count() >= 1) {
                        $comments = 'Prière de recevoir à nouveau le projet de decret ' . $this->record->code . 'pour examen et avis. ';
                    } else {
                        $comments = 'Merci de recevoir le projet de decret ' . $this->record->code . ' pour examen et avis. ';
                    }
                    //validation SGG
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
                        $message = 'Merci de recevoir le projet de decret ' . $this->record->code . ' pour examen et avis. ';
                        Message::create([
                            'decret_id' => $this->record->id,
                            'inbox_id' => $inbox->id,
                            'title' => $messageTitle,
                            'content' => $message,
                        ]);

                        $this->record->update([
                            'inbox_id' => $inbox->id,
                            'status' => 'Examen SGG',
                            'submit_at' => now(),
                        ]);
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
                ->hidden($this->retour_possible())
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
                    $workPrg = Worker::where('name', 'primature')->first();

                    if (auth()->user()->IsWorker) {
                        //Action du SGG
                        if (auth()->user()->worker->id === $workSgg->id) {
                            # code...
                            Validation::create([
                                'decret_id' => $this->record->id,
                                'comments' => $data['comments'],
                                'document' => $data['document'],
                                'color' => '#FF5722',
                                'type' => 'retourner',
                            ]);
                            $inbox = Inbox::where('name', $this->record->init)->first();
                            $this->record->update([
                                'inbox_id' => $inbox->id,
                                'status' => 'Retour SGG',
                            ]);

                            $messageTitle = 'Notification de Retour du Projet de Décret (' . $this->record->code . ')';
                            $message = 'Merci de recevoir le projet de decret ' . $this->record->code . ' correction. ';
                            Message::create([
                                'decret_id' => $this->record->id,
                                'inbox_id' => $inbox->id,
                                'title' => $messageTitle,
                                'content' => $message,
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
                            $inbox1 = Inbox::where('name', "SGG")->first();
                            $inbox2 = Inbox::where('name', $this->record->init)->first();
                            $this->record->update([
                                'inbox_id' => $inbox->id,
                                'status' => 'Retour Primature',
                                'okSGG' => false,
                            ]);
                            $messageTitle =
                                'Notification de Retour du Projet de Décret (' . $this->record->code . ')';
                            $message = 'Merci de recevoir le projet de decret ' . $this->record->code . ' correction. ';
                            Message::create([
                                'decret_id' => $this->record->id,
                                'inbox_id' => $inbox1->id,
                                'title' => $messageTitle,
                                'content' => $message,
                            ]);
                            Message::create([
                                'decret_id' => $this->record->id,
                                'inbox_id' => $inbox2->id,
                                'title' => $messageTitle,
                                'content' => $message,
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
                            Notification::make()
                                ->title('Envoyé avec succès')
                                ->success()
                                ->send();
                            return redirect()->route('filament.admin.resources.decrets.index');
                        }

                        //Action de la PRESIDENCE
                        if (auth()->user()->worker->id === $workPrg->id) {
                            # code...
                            Validation::create([
                                'decret_id' => $this->record->id,
                                'comments' => $data['comments'],
                                'document' => $data['document'],
                                'color' => '#DD2C00',
                                'type' => 'retourner',
                            ]);
                            $inbox = Inbox::where('name', $this->record->init)->first();
                            $inbox1 = Inbox::where('name', "SGG")->first();
                            $inbox2 = Inbox::where('name', "PRIMATURE")->first();
                            $this->record->update([
                                'inbox_id' => $inbox->id,
                                'status' => 'Retour Presidence',
                                'okPRIMATURE' => false,
                                'okSGG' => false,
                            ]);
                            $messageTitle = 'Notification de Retour du Projet de Décret (' . $this->record->code . ')';
                            $messageTitle
                                = 'Notification de Retour du Projet de Décret (' . $this->record->code . ')';
                            $message = 'Nous tenons à vous informer que nous avons décidé de retourner le projet de décret portant le code ' . $this->record->code;
                            Message::create([
                                'decret_id' => $this->record->id,
                                'inbox_id' => $inbox->id,
                                'title' => $messageTitle,
                                'content' => $message,
                            ]);
                            Message::create([
                                'decret_id' => $this->record->id,
                                'inbox_id' => $inbox1->id,
                                'title' => $messageTitle,
                                'content' => $message,
                            ]);
                            Message::create([
                                'decret_id' => $this->record->id,
                                'inbox_id' => $inbox2->id,
                                'title' => $messageTitle,
                                'content' => $message,
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

            Action::make('valider')
                ->label('VALIDER')
                ->color('success')
                ->hidden($this->validate_possible())
                // ->modalWidth('3xl')
                ->modalSubmitActionLabel('OUI, JE COMFIRME')
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
                            'content' => $message,
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
                            'color' => '#388E3C',
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
                            'content' => $message,
                        ]);
                        Message::create([
                            'decret_id' => $this->record->id,
                            'inbox_id' => $inbox1->id,
                            'title' => $messageTitle,
                            'content' => $message,
                        ]);
                        Message::create([
                            'decret_id' => $this->record->id,
                            'inbox_id' => $inbox2->id,
                            'title' => $messageTitle,
                            'content' => $message,
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
