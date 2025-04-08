<?php

namespace App\Console\Commands;

use App\Models\DailyRate;
use Illuminate\Console\Command;

class FixDailyRateFeedingValue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-daily-rate-feeding-value';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commando criado para corrigir o valor de alimentação da diária.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;

        $dailyRates = DailyRate::all();

        // Inicia um contador para acompanhar o progresso
        $progressBar = $this->output->createProgressBar($dailyRates->count());
        foreach ($dailyRates as $dailyRate) {

            if ($dailyRate->collaborator->is_leader) {
                $hourlyRate = $dailyRate->companySection->leaderPay;
            } elseif ($dailyRate->collaborator->is_extra) {
                $hourlyRate = $dailyRate->companySection->extra;
            } else {
                $hourlyRate = $dailyRate->companySection->employeePay;
            }


            // Avança a barra de progresso
            $progressBar->advance();
            
            // Verifica se o valor de alimentação é nulo ou zero
            if (is_null($dailyRate->feeding) || $dailyRate->feeding != 10) {

                if ($dailyRate->collaborator->is_leader) {
                    $hourlyRate = $dailyRate->companySection->leaderPay;
                } elseif ($dailyRate->collaborator->is_extra) {
                    $hourlyRate = $dailyRate->companySection->extra;
                } else {
                    $hourlyRate = $dailyRate->companySection->employeePay;
                }
                
                $workedHours = self::timeToDecimal($dailyRate->total_time);
                $feeding = ceil(round($dailyRate->pay_amount - ($hourlyRate * $workedHours), 2));                          

                if ($feeding != 0 && $feeding <= 11) {
                    
                    $dailyRate->feeding = 10;
                    $count++;
                }
            }

            $dailyRate->hourly_rate = $hourlyRate;
            $dailyRate->save();
        }

        // Finaliza a barra de progresso
        $progressBar->finish();
        $this->line('');

        // Exibe uma mensagem de conclusão
        $this->info('Todos os valores de alimentação foram corrigidos com sucesso!');

        $this->info($count . ' valores de alimentação corrigidos!');
        
        return 0;
    }

    public function timeToDecimal($totalTime)
    {
        if (str_contains($totalTime, ':')) {
            [$horas, $minutos] = explode(':', $totalTime);
            if ($horas == 0 && $minutos == 0) {
                return 1;
            }
            return (int)$horas + ((int)$minutos / 60);
        }

        return (float)$totalTime;
    }
}
