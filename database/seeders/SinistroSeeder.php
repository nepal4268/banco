<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sinistro;
use App\Models\Apolice;
use App\Models\StatusSinistro;

class SinistroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $apolices = Apolice::all();
        $statusAberto = StatusSinistro::where('nome', 'Aberto')->first() ?? StatusSinistro::first();
        $statusPago = StatusSinistro::where('nome', 'Pago')->first() ?? $statusAberto;

        foreach ($apolices as $apolice) {
            if (fake()->boolean(40)) { // ~40% das apólices possuem sinistros
                $qtd = fake()->numberBetween(1, 3);
                for ($i = 0; $i < $qtd; $i++) {
                    $valorReivindicado = fake()->randomFloat(2, 10000, 200000);
                    $pago = fake()->boolean(50);
                    Sinistro::create([
                        'apolice_id' => $apolice->id,
                        'descricao' => fake()->sentence(8),
                        'valor_reivindicado' => $valorReivindicado,
                        'valor_pago' => $pago ? fake()->randomFloat(2, 1000, $valorReivindicado) : 0,
                        'data_sinistro' => fake()->dateTimeBetween('-5 months', 'now')->format('Y-m-d'),
                        'status_sinistro_id' => $pago ? $statusPago->id : $statusAberto->id,
                    ]);
                }
            }
        }

        $this->command->info('✅ Sinistros criados com sucesso! (' . Sinistro::count() . ' total)');
    }
}

