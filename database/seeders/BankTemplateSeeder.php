<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BankTemplate;
use App\Models\BankField;

class BankTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Templates comuns de bancos brasileiros
        $templates = [
            [
                'name' => 'Banco do Brasil',
                'description' => 'Template para acessos ao Banco do Brasil (BB)',
                'slug' => 'banco-do-brasil',
                'active' => true,
                'fields' => [
                    [
                        'field_key' => 'agencia',
                        'name' => 'Agência',
                        'field_type' => 'text',
                        'options' => 'Digite a agência (sem dígito)',
                        'is_required' => true,
                        'active' => true,
                        'order' => 1
                    ],
                    [
                        'field_key' => 'conta',
                        'name' => 'Conta',
                        'field_type' => 'text',
                        'options' => 'Digite a conta com dígito',
                        'is_required' => true,
                        'active' => true,
                        'order' => 2
                    ],
                    [
                        'field_key' => 'senha',
                        'name' => 'Senha',
                        'field_type' => 'password',
                        'options' => 'Senha de 8 dígitos',
                        'is_required' => true,
                        'active' => true,
                        'order' => 3
                    ],
                    [
                        'field_key' => 'observacoes',
                        'name' => 'Observações',
                        'field_type' => 'textarea',
                        'options' => 'Informações adicionais',
                        'is_required' => false,
                        'active' => true,
                        'order' => 4
                    ],
                ]
            ],
            [
                'name' => 'Caixa Econômica Federal',
                'description' => 'Template para acessos à Caixa Econômica Federal',
                'slug' => 'caixa-economica-federal',
                'active' => true,
                'fields' => [
                    [
                        'field_key' => 'cpf',
                        'name' => 'CPF',
                        'field_type' => 'text',
                        'options' => 'Digite o CPF (apenas números)',
                        'is_required' => true,
                        'active' => true,
                        'order' => 1
                    ],
                    [
                        'field_key' => 'senha',
                        'name' => 'Senha Internet Banking',
                        'field_type' => 'password',
                        'options' => 'Senha de acesso',
                        'is_required' => true,
                        'active' => true,
                        'order' => 2
                    ],
                    [
                        'field_key' => 'senha_cartao',
                        'name' => 'Senha do Cartão',
                        'field_type' => 'password',
                        'options' => 'Senha do cartão (6 dígitos)',
                        'is_required' => false,
                        'active' => true,
                        'order' => 3
                    ],
                ]
            ],
            [
                'name' => 'Nubank',
                'description' => 'Template para acessos ao Nubank',
                'slug' => 'nubank',
                'active' => true,
                'fields' => [
                    [
                        'field_key' => 'cpf',
                        'name' => 'CPF',
                        'field_type' => 'text',
                        'options' => 'Digite o CPF (apenas números)',
                        'is_required' => true,
                        'active' => true,
                        'order' => 1
                    ],
                    [
                        'field_key' => 'senha',
                        'name' => 'Senha',
                        'field_type' => 'password',
                        'options' => 'Senha de acesso',
                        'is_required' => true,
                        'active' => true,
                        'order' => 2
                    ],
                    [
                        'field_key' => 'email',
                        'name' => 'E-mail',
                        'field_type' => 'email',
                        'options' => 'E-mail cadastrado',
                        'is_required' => false,
                        'active' => true,
                        'order' => 3
                    ],
                ]
            ],
            [
                'name' => 'Binance',
                'description' => 'Template para acessos à exchange Binance',
                'slug' => 'binance',
                'active' => true,
                'fields' => [
                    [
                        'field_key' => 'email',
                        'name' => 'E-mail',
                        'field_type' => 'email',
                        'options' => 'E-mail cadastrado',
                        'is_required' => true,
                        'active' => true,
                        'order' => 1
                    ],
                    [
                        'field_key' => 'senha',
                        'name' => 'Senha',
                        'field_type' => 'password',
                        'options' => 'Senha de acesso',
                        'is_required' => true,
                        'active' => true,
                        'order' => 2
                    ],
                    [
                        'field_key' => 'google_auth',
                        'name' => 'Código Google Authenticator',
                        'field_type' => 'text',
                        'options' => 'Código ou chave de backup',
                        'is_required' => false,
                        'active' => true,
                        'order' => 3
                    ],
                ]
            ],
            [
                'name' => 'E-mail Genérico',
                'description' => 'Template para acessos a serviços de e-mail (Gmail, Outlook, etc)',
                'slug' => 'email',
                'active' => true,
                'fields' => [
                    [
                        'field_key' => 'email',
                        'name' => 'E-mail',
                        'field_type' => 'email',
                        'options' => 'E-mail completo',
                        'is_required' => true,
                        'active' => true,
                        'order' => 1
                    ],
                    [
                        'field_key' => 'senha',
                        'name' => 'Senha',
                        'field_type' => 'password',
                        'options' => 'Senha de acesso',
                        'is_required' => true,
                        'active' => true,
                        'order' => 2
                    ],
                    [
                        'field_key' => 'recovery_email',
                        'name' => 'E-mail de Recuperação',
                        'field_type' => 'email',
                        'options' => 'E-mail alternativo para recuperação',
                        'is_required' => false,
                        'active' => true,
                        'order' => 3
                    ],
                    [
                        'field_key' => 'telefone',
                        'name' => 'Telefone de Recuperação',
                        'field_type' => 'text',
                        'options' => 'Telefone para recuperação',
                        'is_required' => false,
                        'active' => true,
                        'order' => 4
                    ],
                ]
            ],
        ];

        foreach ($templates as $templateData) {
            $fields = $templateData['fields'];
            unset($templateData['fields']);
            
            $template = BankTemplate::create($templateData);
            
            foreach ($fields as $fieldData) {
                $fieldData['bank_template_id'] = $template->id;
                BankField::create($fieldData);
            }
        }

        $this->command->info('Templates de bancos criados com sucesso!');
    }
}
