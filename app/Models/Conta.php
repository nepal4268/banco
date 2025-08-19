<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contas';

    protected $fillable = [
        'cliente_id',
        'agencia_id',
        'numero_conta',
        'tipo_conta_id',
        'moeda_id',
        'saldo',
        'iban',
        'status_conta_id',
    ];

    protected $casts = [
        'saldo' => 'decimal:2',
    ];

    // Relacionamentos
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function agencia(): BelongsTo
    {
        return $this->belongsTo(Agencia::class, 'agencia_id');
    }

    public function tipoConta(): BelongsTo
    {
        return $this->belongsTo(TipoConta::class, 'tipo_conta_id');
    }

    public function moeda(): BelongsTo
    {
        return $this->belongsTo(Moeda::class, 'moeda_id');
    }

    public function statusConta(): BelongsTo
    {
        return $this->belongsTo(StatusConta::class, 'status_conta_id');
    }

    public function cartoes(): HasMany
    {
        return $this->hasMany(Cartao::class, 'conta_id');
    }

    public function transacoesOrigem(): HasMany
    {
        return $this->hasMany(Transacao::class, 'conta_origem_id');
    }

    public function transacoesDestino(): HasMany
    {
        return $this->hasMany(Transacao::class, 'conta_destino_id');
    }

    public function pagamentos(): HasMany
    {
        return $this->hasMany(Pagamento::class, 'conta_id');
    }

    public function operacoesCambioOrigem(): HasMany
    {
        return $this->hasMany(OperacaoCambio::class, 'conta_origem_id');
    }

    public function operacoesCambioDestino(): HasMany
    {
        return $this->hasMany(OperacaoCambio::class, 'conta_destino_id');
    }

    // Métodos auxiliares para IBAN e número da conta
    public static function gerarNumeroConta(Agencia $agencia): string
    {
        // Formato: AAAACCCCCCCC (4 dígitos agência + 8 dígitos sequencial)
        $ultimaConta = self::where('agencia_id', $agencia->id)
            ->orderBy('id', 'desc')
            ->first();
        
        $proximoSequencial = $ultimaConta ? 
            intval(substr($ultimaConta->numero_conta, -8)) + 1 : 1;
        
        return $agencia->codigo_agencia . str_pad($proximoSequencial, 8, '0', STR_PAD_LEFT);
    }

    public function gerarIban(): string
    {
        // Formato IBAN Angola: AO06 0042 0001 12345678 (AO + 2 dígitos verificação + código banco + agência + conta)
        $codigoPais = 'AO';
        $codigoBanco = $this->agencia->codigo_banco;
        $codigoAgencia = $this->agencia->codigo_agencia;
        $numeroConta = $this->numero_conta;
        
        // Cálculo simplificado dos dígitos de verificação (normalmente seria mod-97)
        $semVerificacao = $codigoBanco . $codigoAgencia . substr($numeroConta, -8);
        $digitosVerificacao = str_pad((98 - (intval($semVerificacao) % 97)), 2, '0', STR_PAD_LEFT);
        
        return $codigoPais . $digitosVerificacao . $codigoBanco . $codigoAgencia . substr($numeroConta, -8);
    }

    // Boot method para auto-gerar número da conta e IBAN
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($conta) {
            if (empty($conta->numero_conta)) {
                $conta->numero_conta = self::gerarNumeroConta($conta->agencia);
            }
            
            if (empty($conta->iban)) {
                $conta->iban = $conta->gerarIban();
            }
        });
        
        static::updating(function ($conta) {
            if ($conta->isDirty('agencia_id') || $conta->isDirty('numero_conta')) {
                $conta->iban = $conta->gerarIban();
            }
        });
    }
}
