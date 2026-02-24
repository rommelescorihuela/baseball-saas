<?php

namespace App\Console\Commands;

use App\Models\Season;
use App\Services\SeasonStatsAggregator;
use Illuminate\Console\Command;

class AggregateSeasonStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stats:aggregate-season {season_id? : ID de la temporada} {--all : Agregar todas las temporadas activas}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agrega las estadísticas de juegos a estadísticas de temporada';

    /**
     * Execute the console command.
     */
    public function handle(SeasonStatsAggregator $aggregator): int
    {
        if ($this->option('all')) {
            $seasons = Season::where('is_active', true)->get();
            $this->info("Agregando estadísticas para {$seasons->count()} temporadas activas...");

            $bar = $this->output->createProgressBar($seasons->count());

            foreach ($seasons as $season) {
                $aggregator->aggregateSeason($season);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info('Estadísticas agregadas exitosamente.');

            return Command::SUCCESS;
        }

        $seasonId = $this->argument('season_id');

        if (! $seasonId) {
            $this->error('Debes especificar un ID de temporada o usar --all');

            return Command::FAILURE;
        }

        $season = Season::find($seasonId);

        if (! $season) {
            $this->error("No se encontró la temporada con ID {$seasonId}");

            return Command::FAILURE;
        }

        $this->info("Agregando estadísticas para la temporada: {$season->name}");

        $aggregator->aggregateSeason($season);

        $this->info('Estadísticas agregadas exitosamente.');

        return Command::SUCCESS;
    }
}
