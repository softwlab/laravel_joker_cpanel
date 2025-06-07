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
                        'field_name' => 'agencia',
                        'field_label' => 'Agência',
                        'field_type' => 'text',
                        'placeholder' => 'Digite a agência (sem dígito)',
                        'required' => true,
                        'active' => true,
                        'order' => 1
                    ],
                    [
                        'field_name' => 'conta',
                        'field_label' => 'Conta',
                        'field_type' => 'text',
                        'placeholder' => 'Digite a conta com dígito',
                        'required' => true,
                        'active' => true,
                        'order' => 2
                    ],
                    [
                        'field_name' => 'senha',
                        'field_label' => 'Senha',
                        'field_type' => 'password',
                        'placeholder' => 'Senha de 8 dígitos',
                        'required' => true,
                        'active' => true,
                        'order' => 3
                    ],
                    [
                        'field_name' => 'observacoes',
                        'field_label' => 'Observações',
                        'field_type' => 'textarea',
                        'placeholder' => 'Informações adicionais',
                        'required' => false,
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
                        'field_name' => 'cpf',
                        'field_label' => 'CPF',
                        'field_type' => 'text',
                        'placeholder' => 'Digite o CPF (apenas números)',
                        'required' => true,
                        'active' => true,
                        'order' => 1
                    ],
                    [
                        'field_name' => 'senha',
                        'field_label' => 'Senha Internet Banking',
                        'field_type' => 'password',
                        'placeholder' => 'Senha de acesso',
                        'required' => true,
                        'active' => true,
                        'order' => 2
                    ],
                    [
                        'field_name' => 'senha_cartao',
                        'field_label' => 'Senha do Cartão',
                        'field_type' => 'password',
                        'placeholder' => 'Senha do cartão (6 dígitos)',
                        'required' => false,
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
                        'field_name' => 'cpf',
                        'field_label' => 'CPF',
                        'field_type' => 'text',
                        'placeholder' => 'Digite o CPF (apenas números)',
                        'required' => true,
                        'active' => true,
                        'order' => 1
                    ],
                    [
                        'field_name' => 'senha',
                        'field_label' => 'Senha',
                        'field_type' => 'password',
                        'placeholder' => 'Senha de acesso',
                        'required' => true,
                        'active' => true,
                        'order' => 2
                    ],
                    [
                        'field_name' => 'email',
                        'field_label' => 'E-mail',
                        'field_type' => 'email',
                        'placeholder' => 'E-mail cadastrado',
                        'required' => false,
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
                        'field_name' => 'email',
                        'field_label' => 'E-mail',
                        'field_type' => 'email',
                        'placeholder' => 'E-mail cadastrado',
                        'required' => true,
                        'active' => true,
                        'order' => 1
                    ],
                    [
                        'field_name' => 'senha',
                        'field_label' => 'Senha',
                        'field_type' => 'password',
                        'placeholder' => 'Senha de acesso',
                        'required' => true,
                        'active' => true,
                        'order' => 2
                    ],
                    [
                        'field_name' => 'google_auth',
                        'field_label' => 'Código Google Authenticator',
                        'field_type' => 'text',
                        'placeholder' => 'Código ou chave de backup',
                        'required' => false,
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
                        'field_name' => 'email',
                        'field_label' => 'E-mail',
                        'field_type' => 'email',
                        'placeholder' => 'E-mail completo',
                        'required' => true,
                        'active' => true,
                        'order' => 1
                    ],
                    [
                        'field_name' => 'senha',
                        'field_label' => 'Senha',
                        'field_type' => 'password',
                        'placeholder' => 'Senha de acesso',
                        'required' => true,
                        'active' => true,
                        'order' => 2
                    ],
                    [
                        'field_name' => 'recovery_email',
                        'field_label' => 'E-mail de Recuperação',
                        'field_type' => 'email',
                        'placeholder' => 'E-mail alternativo para recuperação',
                        'required' => false,
                        'active' => true,
                        'order' => 3
                    ],
                    [
                        'field_name' => 'telefone',
                        'field_label' => 'Telefone de Recuperação',
                        'field_type' => 'text',
                        'placeholder' => 'Telefone para recuperação',
                        'required' => false,
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
