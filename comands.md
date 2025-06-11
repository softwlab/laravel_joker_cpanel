php artisan tinker --execute="App\Models\Usuario::create(['nome' => 'Cliente Teste', 'email' => 'cliente@teste.com', 'senha' => Hash::make('123456'), 'nivel' => 'cliente', 'ativo' => true]);"




veja agora precisamos criar uma parte importante, lembra-se que   o registro de visitantes externos e registro de informações são baseados nos lniks que são baseados no user_id correto?

então vamos supor um visitante externo foi inserido através da api externa que iremos configurar e a partir do link foi encontrado o user_id devemos adicionar o visitante e deve ter um menu Visitantes npara os clientes e um menu Informações (são informaçoes bancarias data|agencia|conta|cpf|nome completo|telefone)

lembrando que ao ser inserido um visitante deve ser gerado com uuidv4 ou superior um id unico que será utilizado também parece inserir uma informaçao bancaria para termos rastreabilidade 

 taskkill /F /IM php.exe
ÊXITO: o processo "php.exe" com PID 9332 foi finalizado.
ÊXITO: o processo "php.exe" com PID 20024 foi finalizad