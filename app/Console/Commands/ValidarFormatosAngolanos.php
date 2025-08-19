<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cliente;
use App\Models\Cartao;
use App\Models\Agencia;

class ValidarFormatosAngolanos extends Command
{
    protected $signature = 'test:formatos-angolanos';
    protected $description = 'Valida especificamente os formatos angolanos (BI, Cartão, Telefones)';

    public function handle(): int
    {
        $this->info('🇦🇴 Testando formatos específicos angolanos...');
        $this->newLine();

        $this->testarFormatoBI();
        $this->testarFormatoCartao();
        $this->testarTelefonesJSON();
        $this->testarGeracaoAutomatica();

        $this->newLine();
        $this->info('✅ Validação de formatos angolanos concluída!');
        
        return self::SUCCESS;
    }

    private function testarFormatoBI(): void
    {
        $this->info('🆔 Testando formato de BI angolano...');
        
        // Testes de formato válido
        $bisValidos = [
            '123456789AB123',
            '987654321XY999',
            '555666777CD456',
            '111222333EF789',
            '999888777GH111'
        ];

        $this->line('   ✅ Testando BIs válidos:');
        foreach ($bisValidos as $bi) {
            $valido = preg_match('/^\d{9}[A-Z]{2}\d{3}$/', $bi) === 1;
            $status = $valido ? '✅' : '❌';
            $this->line("      {$status} {$bi}");
        }

        // Testes de formato inválido
        $bisInvalidos = [
            '12345678AB123',     // 8 dígitos em vez de 9
            '123456789ab123',    // letras minúsculas
            '123456789ABC123',   // 3 letras em vez de 2
            '123456789AB12',     // 2 dígitos finais em vez de 3
            '123456789AB1234',   // 4 dígitos finais
            'ABC456789AB123',    // começa com letras
        ];

        $this->line('   ❌ Testando BIs inválidos (devem falhar):');
        foreach ($bisInvalidos as $bi) {
            $valido = preg_match('/^\d{9}[A-Z]{2}\d{3}$/', $bi) === 1;
            $status = !$valido ? '✅' : '❌';
            $this->line("      {$status} {$bi} " . ($valido ? '(ERRO: foi aceito!)' : '(rejeitado corretamente)'));
        }

        // Testar geração automática
        $this->line('   🔄 Testando geração automática:');
        for ($i = 1; $i <= 5; $i++) {
            $bi = $this->gerarBI();
            $valido = preg_match('/^\d{9}[A-Z]{2}\d{3}$/', $bi) === 1;
            $status = $valido ? '✅' : '❌';
            $this->line("      {$status} Gerado: {$bi}");
        }
    }

    private function testarFormatoCartao(): void
    {
        $this->info('💳 Testando formato de cartão angolano...');
        
        // Testes de formato válido (4042 + 12 dígitos)
        $cartoesValidos = [
            '4042000100010001',
            '4042000200020002',
            '4042000300030003',
            '4042001000100010',
            '4042002000200020'
        ];

        $this->line('   ✅ Testando cartões válidos:');
        foreach ($cartoesValidos as $cartao) {
            $valido = preg_match('/^4042\d{12}$/', $cartao) === 1;
            $status = $valido ? '✅' : '❌';
            $this->line("      {$status} {$cartao}");
        }

        // Testes de formato inválido
        $cartoesInvalidos = [
            '4041000100010001',  // não começa com 4042
            '4043000100010001',  // não começa com 4042
            '40420001000100',    // 14 dígitos em vez de 16
            '40420001000100012', // 17 dígitos
            '4042ABCD00010001',  // contém letras
            '5042000100010001',  // começa com 5042
        ];

        $this->line('   ❌ Testando cartões inválidos (devem falhar):');
        foreach ($cartoesInvalidos as $cartao) {
            $valido = preg_match('/^4042\d{12}$/', $cartao) === 1;
            $status = !$valido ? '✅' : '❌';
            $this->line("      {$status} {$cartao} " . ($valido ? '(ERRO: foi aceito!)' : '(rejeitado corretamente)'));
        }

        // Testar geração baseada em conta
        $this->line('   🔄 Testando geração automática:');
        for ($i = 1; $i <= 5; $i++) {
            $cartao = $this->gerarNumeroCartao($i, '0001');
            $valido = preg_match('/^4042\d{12}$/', $cartao) === 1;
            $status = $valido ? '✅' : '❌';
            $this->line("      {$status} Gerado: {$cartao}");
        }
    }

    private function testarTelefonesJSON(): void
    {
        $this->info('📞 Testando telefones em formato JSON...');
        
        // Testes de arrays válidos
        $telefonesValidos = [
            ['930202034'],
            ['930202034', '222123456'],
            ['930202034', '222123456', '244123456'],
            ['923456789'],
            ['944556677', '933445566']
        ];

        $this->line('   ✅ Testando arrays de telefones válidos:');
        foreach ($telefonesValidos as $telefones) {
            $json = json_encode($telefones);
            $decodificado = json_decode($json, true);
            $valido = is_array($decodificado) && !empty($decodificado);
            $status = $valido ? '✅' : '❌';
            $this->line("      {$status} " . implode(', ', $telefones) . " → {$json}");
        }

        // Testar validação de números angolanos
        $numerosAngolanos = [
            '930202034',  // Unitel
            '923456789',  // Unitel
            '944556677',  // Africell
            '933445566',  // Movicel
            '222123456',  // Fixo Luanda
            '272345678',  // Fixo Benguela
        ];

        $this->line('   📱 Testando números angolanos:');
        foreach ($numerosAngolanos as $numero) {
            $valido = preg_match('/^(9[2-4]\d{7}|2[2-7]\d{7})$/', $numero) === 1;
            $status = $valido ? '✅' : '❌';
            $tipo = $this->identificarTipoTelefone($numero);
            $this->line("      {$status} {$numero} ({$tipo})");
        }
    }

    private function testarGeracaoAutomatica(): void
    {
        $this->info('🤖 Testando geração automática de dados...');
        
        $this->line('   🆔 Gerando 10 BIs únicos:');
        $bisGerados = [];
        for ($i = 1; $i <= 10; $i++) {
            $bi = $this->gerarBI();
            $unico = !in_array($bi, $bisGerados);
            $bisGerados[] = $bi;
            $status = $unico ? '✅' : '❌';
            $this->line("      {$status} {$bi} " . ($unico ? '' : '(DUPLICADO!)'));
        }

        $this->line('   💳 Gerando 10 cartões únicos:');
        $cartoesGerados = [];
        for ($i = 1; $i <= 10; $i++) {
            $cartao = $this->gerarNumeroCartao($i, str_pad($i, 4, '0', STR_PAD_LEFT));
            $unico = !in_array($cartao, $cartoesGerados);
            $cartoesGerados[] = $cartao;
            $status = $unico ? '✅' : '❌';
            $this->line("      {$status} {$cartao} " . ($unico ? '' : '(DUPLICADO!)'));
        }

        $this->line('   📞 Gerando arrays de telefones:');
        for ($i = 1; $i <= 5; $i++) {
            $telefones = $this->gerarTelefones();
            $json = json_encode($telefones);
            $this->line("      ✅ " . implode(', ', $telefones) . " → {$json}");
        }
    }

    private function gerarBI(): string
    {
        $noveDigitos = str_pad(random_int(100000000, 999999999), 9, '0', STR_PAD_LEFT);
        $duasLetras = chr(random_int(65, 90)) . chr(random_int(65, 90)); // A-Z
        $tresDigitos = str_pad(random_int(100, 999), 3, '0', STR_PAD_LEFT);
        
        return $noveDigitos . $duasLetras . $tresDigitos;
    }

    private function gerarNumeroCartao(int $contaId, string $agenciaCode): string
    {
        $prefixoBanco = '4042';
        $agenciaFormatted = str_pad($agenciaCode, 4, '0', STR_PAD_LEFT);
        $contaFormatted = str_pad($contaId, 4, '0', STR_PAD_LEFT);
        $random = str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefixoBanco . $agenciaFormatted . $contaFormatted . $random;
    }

    private function gerarTelefones(): array
    {
        $prefixos = ['930', '923', '944', '933', '222', '272', '241'];
        $quantidade = random_int(1, 3);
        $telefones = [];
        
        for ($i = 0; $i < $quantidade; $i++) {
            $prefixo = $prefixos[array_rand($prefixos)];
            $sufixo = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
            $telefones[] = $prefixo . $sufixo;
        }
        
        return array_unique($telefones);
    }

    private function identificarTipoTelefone(string $numero): string
    {
        if (preg_match('/^9[23]\d{7}$/', $numero)) {
            return 'Unitel';
        } elseif (preg_match('/^94\d{7}$/', $numero)) {
            return 'Africell';
        } elseif (preg_match('/^93\d{7}$/', $numero)) {
            return 'Movicel';
        } elseif (preg_match('/^22\d{7}$/', $numero)) {
            return 'Fixo Luanda';
        } elseif (preg_match('/^2[3-7]\d{7}$/', $numero)) {
            return 'Fixo Provincial';
        } else {
            return 'Formato desconhecido';
        }
    }
}