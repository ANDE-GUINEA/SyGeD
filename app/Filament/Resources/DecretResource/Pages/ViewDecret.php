<?php

namespace App\Filament\Resources\DecretResource\Pages;

use App\Models\Inbox;
use Filament\Actions;
use App\Models\Worker;
use App\Models\Validation;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use App\Filament\Resources\DecretResource;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class ViewDecret extends ViewRecord implements HasShieldPermissions
{
    protected static string $resource = DecretResource::class;
    protected static string $view = 'filament.resources.decret-resource.pages.show-decret';
    public static function getPermissionPrefixes(): array
    {
        return ['view', 'view_any', 'create', 'update', 'delete', 'delete_any', 'retourner', 'valider', 'soumettre'];
    }

    protected function retour_possible()
    {
        if (auth()->user()->worker) {
            # code...
            if (auth()->user()->worker->name = ['primature', 'prg', 'sgg']) {
                if ($this->record->okPRIMATURE == true && $this->record->okSGG == true && $this->record->okPRG == true) {
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
        } else {
            return auth()
                ->user()
                ->can('retourne', $this->record);
        }
    }

    protected function vadate_possible()
    {
        if (auth()->user()->worker) {
            if (auth()->user()->worker->name = ['primature', 'sgg']) {
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

    protected function confirm_possible()
    {
        if (auth()->user()->worker) {
            if (auth()->user()->worker->name == 'prg') {
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),

            Action::make('ok')
                ->requiresConfirmation()
                ->hidden($this->confirm_possible())
                ->action(function () {
                    // ...

                    $this->replaceMountedAction('publish');
                }),

            Action::make('retourne')
                ->label('RETOURNER')
                ->color('warning')
                ->modalWidth('2xl')
                ->hidden($this->retour_possible())
                // ->requiresConfirmation()
                ->successRedirectUrl(route('filament.admin.resources.decrets.index'))
                ->successNotificationTitle('Transmit avec succè')
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
                        //Action du departement
                        if (auth()->user()->worker->id === $workDep->id) {
                            # code...
                            Validation::create([
                                'decret_id' => $this->record->id,
                                'comments' => $data['comments'],
                                'document' => $data['document'],
                            ]);
                            $inbox = Inbox::where('name', 'SGG')->first();
                            $this->record->update([
                                'inbox_id' => $inbox->id,
                                'status' => 'En cours',
                            ]);

                            return redirect()->route('filament.admin.resources.decrets.index');
                        }
                        //Action du SGG
                        if (auth()->user()->worker->id === $workSgg->id) {
                            # code...
                            Validation::create([
                                'decret_id' => $this->record->id,
                                'comments' => $data['comments'],
                                'document' => $data['document'],
                            ]);
                            $inbox = Inbox::where('name', $this->record->init)->first();
                            $this->record->update([
                                'inbox_id' => $inbox->id,
                                'status' => 'En attente',
                            ]);
                            return redirect()->route('filament.admin.resources.decrets.index');
                        }
                        //Action de la PRIMATURE
                        if (auth()->user()->worker->id === $workPm->id) {
                            # code...
                            // $this->replaceMountedAction('publish');
                            Validation::create([
                                'decret_id' => $this->record->id,
                                'comments' => $data['comments'],
                                'document' => $data['document'],
                            ]);
                            $inbox = Inbox::where('name', 'SGG')->first();
                            $this->record->update([
                                'inbox_id' => $inbox->id,
                                'status' => 'En cours',
                                'okSGG' => false,
                            ]);
                            return redirect()->route('filament.admin.resources.decrets.index');
                        }
                        //Action de la PRESIDENCE
                        if (auth()->user()->worker->id === $workPrg->id) {
                            # code...
                            Validation::create([
                                'decret_id' => $this->record->id,
                                'comments' => $data['comments'],
                                'document' => $data['document'],
                            ]);
                            $inbox = Inbox::where('name', 'PRIMATURE')->first();
                            $this->record->update([
                                'inbox_id' => $inbox->id,
                                'status' => 'En cours',
                                'okPRIMATURE' => false,
                            ]);
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
                ->hidden($this->vadate_possible())
                // ->modalWidth('3xl')
                ->modalSubmitActionLabel('OUI, JE COMFIRME')
                ->requiresConfirmation()
                ->successRedirectUrl(route('filament.admin.resources.decrets.index'))
                // ->form([
                //     RichEditor::make('comments')->label('VOTRE COMMENTAIRE'),
                //     FileUpload::make('document')
                //         // ->required()
                //         ->label('DOCUMENT')
                //         ->enableOpen()
                //         // ->maxSize(1024)
                //         ->directory('decrets_files')
                //         ->preserveFilenames()
                //         ->enableDownload(),
                // ])
                // ->fillForm([
                //     'title' => $this->record->objet,
                //     'content' => $this->record->content,
                // ])
                // ->disabledForm()
                ->action(function (array $data) {
                    // dd('ok');
                    $workSgg = Worker::where('name', 'sgg')->first();
                    $workPm = Worker::where('name', 'primature')->first();
                    $workPrg = Worker::where('name', 'prg')->first();

                    //validation SGG
                    if (auth()->user()->worker->id === $workSgg->id) {
                        Validation::create([
                            'decret_id' => $this->record->id,
                            'comments' => 'Nous sommes ravis de vous informer que le projet de décret portant le code ' . $this->record->code . ' a été officiellement validé par notre département. Cette démarche de validation témoigne de la conformité du projet de décret aux réglementations en vigueur ainsi qu\'aux exigences juridiques et normatives qui lui sont applicables.',
                            // 'comments' => $data['comments'],
                            // 'document' => $data['document'],
                        ]);
                        $inbox = Inbox::where('name', 'PRIMATURE')->first();
                        $this->record->update([
                            'inbox_id' => $inbox->id,
                            'status' => 'En cours',
                            'okSGG' => true,
                        ]);
                        return redirect()->route('filament.admin.resources.decrets.index');
                    }
                    //Validation de la PRIMATURE
                    if (auth()->user()->worker->id === $workPm->id) {
                        # code...
                        Validation::create([
                            'decret_id' => $this->record->id,
                            'comments' => 'Nous sommes ravis de vous informer que le projet de décret portant le code ' . $this->record->code . ' a été officiellement validé par notre département. Cette démarche de validation témoigne de la conformité du projet de décret aux réglementations en vigueur ainsi qu\'aux exigences juridiques et normatives qui lui sont applicables.',
                            // 'comments' => $data['comments'],
                            // 'document' => $data['document'],
                        ]);
                        $inbox = Inbox::where('name', 'PRG')->first();
                        $this->record->update([
                            'inbox_id' => $inbox->id,
                            'status' => 'En cours',
                            'okPRIMATURE' => true,
                        ]);
                        return redirect()->route('filament.admin.resources.decrets.index');
                    }
                    //Validation de la PRG
                    if (auth()->user()->worker->id === $workPrg->id) {
                        # code...
                        Validation::create([
                            'decret_id' => $this->record->id,
                            'comments' => 'Nous sommes ravis de vous informer que le projet de décret portant le code ' . $this->record->code . ' a été officiellement validé par notre département. Cette démarche de validation témoigne de la conformité du projet de décret aux réglementations en vigueur ainsi qu\'aux exigences juridiques et normatives qui lui sont applicables.',
                            // 'comments' => $data['comments'],
                            // 'document' => $data['document'],
                        ]);
                        $inbox = Inbox::where('name', 'PRG')->first();
                        $this->record->update([
                            'inbox_id' => $inbox->id,
                            'status' => 'Approuvé',
                            'okPRG' => true,
                        ]);
                        return redirect()->route('filament.admin.resources.decrets.index');
                    }
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
