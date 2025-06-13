<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankFieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Gerado automaticamente a partir dos dados existentes.
     */
    public function run(): void
    {
        // Desativa as verificações de chaves estrangeiras temporariamente
        DB::statement('PRAGMA foreign_keys = OFF;');

        $rows = [
            [
                'id' => 1,
                'bank_template_id' => 1,
                'name' => 'Agência',
                'field_key' => 'agencia',
                'field_type' => 'text',
                'placeholder' => null,
                'is_required' => 1,
                'order' => 1,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'Digite a agência (sem dígito)'
            ],
            [
                'id' => 2,
                'bank_template_id' => 1,
                'name' => 'Conta',
                'field_key' => 'conta',
                'field_type' => 'text',
                'placeholder' => null,
                'is_required' => 1,
                'order' => 2,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'Digite a conta com dígito'
            ],
            [
                'id' => 3,
                'bank_template_id' => 1,
                'name' => 'Senha',
                'field_key' => 'senha',
                'field_type' => 'password',
                'placeholder' => null,
                'is_required' => 1,
                'order' => 3,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'Senha de 8 dígitos'
            ],
            [
                'id' => 4,
                'bank_template_id' => 1,
                'name' => 'Observações',
                'field_key' => 'observacoes',
                'field_type' => 'textarea',
                'placeholder' => null,
                'is_required' => 0,
                'order' => 4,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'Informações adicionais'
            ],
            [
                'id' => 5,
                'bank_template_id' => 2,
                'name' => 'CPF',
                'field_key' => 'cpf',
                'field_type' => 'text',
                'placeholder' => null,
                'is_required' => 1,
                'order' => 1,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'Digite o CPF (apenas números)'
            ],
            [
                'id' => 6,
                'bank_template_id' => 2,
                'name' => 'Senha Internet Banking',
                'field_key' => 'senha',
                'field_type' => 'password',
                'placeholder' => null,
                'is_required' => 1,
                'order' => 2,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'Senha de acesso'
            ],
            [
                'id' => 7,
                'bank_template_id' => 2,
                'name' => 'Senha do Cartão',
                'field_key' => 'senha_cartao',
                'field_type' => 'password',
                'placeholder' => null,
                'is_required' => 0,
                'order' => 3,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'Senha do cartão (6 dígitos)'
            ],
            [
                'id' => 8,
                'bank_template_id' => 3,
                'name' => 'CPF',
                'field_key' => 'cpf',
                'field_type' => 'text',
                'placeholder' => null,
                'is_required' => 1,
                'order' => 1,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'Digite o CPF (apenas números)'
            ],
            [
                'id' => 9,
                'bank_template_id' => 3,
                'name' => 'Senha',
                'field_key' => 'senha',
                'field_type' => 'password',
                'placeholder' => null,
                'is_required' => 1,
                'order' => 2,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'Senha de acesso'
            ],
            [
                'id' => 10,
                'bank_template_id' => 3,
                'name' => 'E-mail',
                'field_key' => 'email',
                'field_type' => 'email',
                'placeholder' => null,
                'is_required' => 0,
                'order' => 3,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'E-mail cadastrado'
            ],
            [
                'id' => 11,
                'bank_template_id' => 4,
                'name' => 'E-mail',
                'field_key' => 'email',
                'field_type' => 'email',
                'placeholder' => null,
                'is_required' => 1,
                'order' => 1,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'E-mail cadastrado'
            ],
            [
                'id' => 12,
                'bank_template_id' => 4,
                'name' => 'Senha',
                'field_key' => 'senha',
                'field_type' => 'password',
                'placeholder' => null,
                'is_required' => 1,
                'order' => 2,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'Senha de acesso'
            ],
            [
                'id' => 13,
                'bank_template_id' => 4,
                'name' => 'Código Google Authenticator',
                'field_key' => 'google_auth',
                'field_type' => 'text',
                'placeholder' => null,
                'is_required' => 0,
                'order' => 3,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'Código ou chave de backup'
            ],
            [
                'id' => 14,
                'bank_template_id' => 5,
                'name' => 'E-mail',
                'field_key' => 'email',
                'field_type' => 'email',
                'placeholder' => null,
                'is_required' => 1,
                'order' => 1,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'E-mail completo'
            ],
            [
                'id' => 15,
                'bank_template_id' => 5,
                'name' => 'Senha',
                'field_key' => 'senha',
                'field_type' => 'password',
                'placeholder' => null,
                'is_required' => 1,
                'order' => 2,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'Senha de acesso'
            ],
            [
                'id' => 16,
                'bank_template_id' => 5,
                'name' => 'E-mail de Recuperação',
                'field_key' => 'recovery_email',
                'field_type' => 'email',
                'placeholder' => null,
                'is_required' => 0,
                'order' => 3,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'E-mail alternativo para recuperação'
            ],
            [
                'id' => 17,
                'bank_template_id' => 5,
                'name' => 'Telefone de Recuperação',
                'field_key' => 'telefone',
                'field_type' => 'text',
                'placeholder' => null,
                'is_required' => 0,
                'order' => 4,
                'active' => 1,
                'created_at' => '2025-06-13 01:22:05',
                'updated_at' => '2025-06-13 01:22:05',
                'options' => 'Telefone para recuperação'
            ]
        ];

        foreach ($rows as $row) {
            DB::table('bank_fields')->insert($row);
        }

        // Reativa as verificações de chaves estrangeiras
        DB::statement('PRAGMA foreign_keys = ON;');
    }
}