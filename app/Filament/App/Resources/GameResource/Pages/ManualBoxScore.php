<?php

namespace App\Filament\App\Resources\GameResource\Pages;

use App\Filament\App\Resources\GameResource;
use App\Models\Game;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class ManualBoxScore extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = GameResource::class;

    protected string $view = 'filament.app.resources.game-resource.pages.manual-box-score';

    public Game $record;

    public ?array $data = [];

    public function mount(int|string $record): void
    {
        $this->record = Game::findOrFail($record);

        $this->form->fill([
            'home_score' => $this->record->home_score,
            'visitor_score' => $this->record->visitor_score,
            'status' => 'finished',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Carga de Totales Finales')
                    ->description('Use esta opción si no pudo anotar el juego en vivo. Esto actualizará los resultados y los standings.')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('home_score')
                                    ->label("Carreras {$this->record->homeTeam->name}")
                                    ->numeric()
                                    ->required(),
                                Forms\Components\TextInput::make('visitor_score')
                                    ->label("Carreras {$this->record->visitorTeam->name}")
                                    ->numeric()
                                    ->required(),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'finished' => 'Finalizado',
                                        'suspended' => 'Suspendido',
                                    ])
                                    ->required(),
                            ]),
                    ]),

                Forms\Components\Section::make('Nota Estadística')
                    ->description('Al guardar, el sistema marcará el juego como finalizado. Para registrar estadísticas individuales de jugadores en diferido, use la pestaña de "Estadísticas de Jugadores" en el modo edición.')
                    ->schema([
                        // In a more complex version, we could put a Repeater here for players
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->record->update($data);

        Notification::make()
            ->title('Juego actualizado exitosamente')
            ->success()
            ->send();

        $this->redirect(GameResource::getUrl('index'));
    }

    public function getTitle(): string
    {
        return "Carga en Diferido: " . $this->record->homeTeam->name . " vs " . $this->record->visitorTeam->name;
    }
}
