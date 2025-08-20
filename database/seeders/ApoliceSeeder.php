<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Apolice;
use App\Models\Cliente;
use App\Models\TipoSeguro;
use App\Models\StatusApolice;

class ApoliceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientes = Cliente::all();
        $tipoSeguro = TipoSeguro::inRandomOrder()->first() ?? TipoSeguro::first();
        $statusAtiva = StatusApolice::where('nome', 'Ativa')->first() ?? StatusApolice::first();

        foreach ($clientes as $cliente) {
            if (fake()->boolean(30)) { // ~30% dos clientes têm apólice
                Apolice::create([
                    'cliente_id' => $cliente->id,
                    'tipo_seguro_id' => $tipoSeguro->id,
                    'numero_apolice' => strtoupper(fake()->bothify('AP-########')),
                    'inicio_vigencia' => fake()->dateTimeBetween('-6 months', '-1 month')->format('Y-m-d'),
                    'fim_vigencia' => fake()->dateTimeBetween('+6 months', '+18 months')->format('Y-m-d'),
                    'status_apolice_id' => $statusAtiva->id,
                    'premio_mensal' => fake()->randomFloat(2, 1000, 20000),
                ]);
            }
        }

        $this->command->info('✅ Apólices criadas com sucesso! (' . Apolice::count() . ' total)');
    }
}

