http://127.0.0.1:8000/admin/cloudflare/domain-associations
http://127.0.0.1:8000/admin/cloudflare/domain-associations


http://127.0.0.1:8000/admin/cloudflare/domain-associations/1/3



JokerLab Admin

    Ver Site
    Administrador

    Dashboard
    Usuários
    Instituições Bancárias
    APIs Externas
    Registros DNS
    Logs
    API Pública

Detalhes da Associação de Domínio Cloudflare
Informações da Associação
Domínio:
exemplo1.com
Usuário:
Cliente 2 (cliente2@example.com)
Status:
Ativo
Data de criação:
08/06/2025 08:12:53
Última atualização:
08/06/2025 08:12:53
Observações:
Associação automática via seeder
Configurações:

"{\"auto_create_dns\":true}"

Registros DNS (13)
Nenhum registro DNS encontrado para este domínio.
Ações
Informações de Acesso

Status atual: Ativo
O usuário pode visualizar e editar registros DNS.

Os registros DNS serão sincronizados automaticamente conforme as configurações do sistema.


http://127.0.0.1:8000/admin/cloudflare/domain-associations/1/3/edit

C:\Projetos\jokerlab_cpanel\app\Http\Controllers\Admin\CloudflareDomainAssociationController.php
  20,39:         return view('admin.cloudflare.domain-associations.index', compact('domains'));
  31,39:         return view('admin.cloudflare.domain-associations.create', compact('domains', 'usuarios'));
  58,60:                 return redirect()->route('admin.cloudflare.domain-associations.index')
  82,39:         return view('admin.cloudflare.domain-associations.show', compact('domain', 'usuario'));
  94,39:         return view('admin.cloudflare.domain-associations.edit', compact('domain', 'usuario', 'association'));
  116,56:             return redirect()->route('admin.cloudflare.domain-associations.index')
  136,56:             return redirect()->route('admin.cloudflare.domain-associations.index')

C:\Projetos\jokerlab_cpanel\resources\views\admin\cloudflare\domain-associations\create.blade.php
  10,49:             <a href="{{ route('admin.cloudflare.domain-associations.index') }}" class="btn btn-sm btn-outline-secondary">
  27,62:                     <form action="{{ route('admin.cloudflare.domain-associations.store') }}" method="POST">

C:\Projetos\jokerlab_cpanel\resources\views\admin\cloudflare\domain-associations\edit.blade.php
  10,49:             <a href="{{ route('admin.cloudflare.domain-associations.index') }}" class="btn btn-sm btn-outline-secondary">
  27,62:                     <form action="{{ route('admin.cloudflare.domain-associations.update', [$domain->id, $usuario->id]) }}" method="POST">
  102,62:                     <form action="{{ route('admin.cloudflare.domain-associations.destroy', [$domain->id, $usuario->id]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta associação?')">

C:\Projetos\jokerlab_cpanel\resources\views\admin\cloudflare\domain-associations\index.blade.php
  11,53:                 <a href="{{ route('admin.cloudflare.domain-associations.create') }}" class="btn btn-sm btn-outline-primary">
  68,77:                                         <a href="{{ route('admin.cloudflare.domain-associations.show', [$domain->id, $usuario->id]) }}" class="btn btn-sm btn-info" title="Detalhes">
  71,77:                                         <a href="{{ route('admin.cloudflare.domain-associations.edit', [$domain->id, $usuario->id]) }}" class="btn btn-sm btn-primary" title="Editar">
  74,82:                                         <form action="{{ route('admin.cloudflare.domain-associations.destroy', [$domain->id, $usuario->id]) }}" method="POST" class="d-inline">

C:\Projetos\jokerlab_cpanel\resources\views\admin\cloudflare\domain-associations\show.blade.php
  11,53:                 <a href="{{ route('admin.cloudflare.domain-associations.edit', [$domain->id, $usuario->id]) }}" class="btn btn-sm btn-primary">
  14,53:                 <a href="{{ route('admin.cloudflare.domain-associations.index') }}" class="btn btn-sm btn-outline-secondary">
  132,61:                         <a href="{{ route('admin.cloudflare.domain-associations.edit', [$domain->id, $usuario->id]) }}" class="btn btn-primary">
  135,66:                         <form action="{{ route('admin.cloudflare.domain-associations.destroy', [$domain->id, $usuario->id]) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta associação?')">

C:\Projetos\jokerlab_cpanel\resources\views\admin\user-details.blade.php
  204,77:                                         <a href="{{ route('admin.cloudflare.domain-associations.show', [$domain->id, $user->id]) }}" class="btn btn-sm btn-info">
  227,81:                                             <a href="{{ route('admin.cloudflare.domain-associations.show', [$domain->id, $user->id]) }}" class="btn btn-sm btn-info">
  264,77:                                         <a href="{{ route('admin.cloudflare.domain-associations.show', [$domain->id, $user->id]) }}" class="btn btn-sm btn-info">

C:\Projetos\jokerlab_cpanel\routes\web.php
  107,21:         Route::get('domain-associations', [\App\Http\Controllers\Admin\CloudflareDomainAssociationController::class, 'index'])
  108,21:             ->name('domain-associations.index');
  109,21:         Route::get('domain-associations/create', [\App\Http\Controllers\Admin\CloudflareDomainAssociationController::class, 'create'])
  110,21:             ->name('domain-associations.create');
  111,22:         Route::post('domain-associations', [\App\Http\Controllers\Admin\CloudflareDomainAssociationController::class, 'store'])
  112,21:             ->name('domain-associations.store');
  113,21:         Route::get('domain-associations/{domainId}/{usuarioId}', [\App\Http\Controllers\Admin\CloudflareDomainAssociationController::class, 'show'])
  114,21:             ->name('domain-associations.show');
  115,21:         Route::get('domain-associations/{domainId}/{usuarioId}/edit', [\App\Http\Controllers\Admin\CloudflareDomainAssociationController::class, 'edit'])
  116,21:             ->name('domain-associations.edit');
  117,21:         Route::put('domain-associations/{domainId}/{usuarioId}', [\App\Http\Controllers\Admin\CloudflareDomainAssociationController::class, 'update'])
  118,21:             ->name('domain-associations.update');
  119,24:         Route::delete('domain-associations/{domainId}/{usuarioId}', [\App\Http\Controllers\Admin\CloudflareDomainAssociationController::class, 'destroy'])
  120,21:             ->name('domain-associations.destroy');

C:\Projetos\jokerlab_cpanel\.phpstorm.meta.php
  2482,19: 'admin.cloudflare.domain-associations.index','admin.cloudflare.domain-associations.create','admin.cloudflare.domain-associations.store','admin.cloudflare.domain-associations.show','admin.cloudflare.domain-associations.edit',
  2482,64: 'admin.cloudflare.domain-associations.index','admin.cloudflare.domain-associations.create','admin.cloudflare.domain-associations.store','admin.cloudflare.domain-associations.show','admin.cloudflare.domain-associations.edit',
  2482,110: 'admin.cloudflare.domain-associations.index','admin.cloudflare.domain-associations.create','admin.cloudflare.domain-associations.store','admin.cloudflare.domain-associations.show','admin.cloudflare.domain-associations.edit',
  2482,155: 'admin.cloudflare.domain-associations.index','admin.cloudflare.domain-associations.create','admin.cloudflare.domain-associations.store','admin.cloudflare.domain-associations.show','admin.cloudflare.domain-associations.edit',
  2482,199: 'admin.cloudflare.domain-associations.index','admin.cloudflare.domain-associations.create','admin.cloudflare.domain-associations.store','admin.cloudflare.domain-associations.show','admin.cloudflare.domain-associations.edit',
  2483,19: 'admin.cloudflare.domain-associations.update','admin.cloudflare.domain-associations.destroy','admin.logs','admin.reports.deprecated-api','admin.templates.index',
  2483,65: 'admin.cloudflare.domain-associations.update','admin.cloudflare.domain-associations.destroy','admin.logs','admin.reports.deprecated-api','admin.templates.index',
  2494,19: 'admin.cloudflare.domain-associations.create','admin.cloudflare.domain-associations.edit','admin.cloudflare.domain-associations.index','admin.cloudflare.domain-associations.show','admin.create-bank',
  2494,65: 'admin.cloudflare.domain-associations.create','admin.cloudflare.domain-associations.edit','admin.cloudflare.domain-associations.index','admin.cloudflare.domain-associations.show','admin.create-bank',
  2494,109: 'admin.cloudflare.domain-associations.create','admin.cloudflare.domain-associations.edit','admin.cloudflare.domain-associations.index','admin.cloudflare.domain-associations.show','admin.create-bank',
  2494,154: 'admin.cloudflare.domain-associations.create','admin.cloudflare.domain-associations.edit','admin.cloudflare.domain-associations.index','admin.cloudflare.domain-associations.show','admin.create-bank',

Untitled-1
  1,40: http://127.0.0.1:8000/admin/cloudflare/domain-associations
  2,40: http://127.0.0.1:8000/admin/cloudflare/domain-associations
  5,40: http://127.0.0.1:8000/admin/cloudflare/domain-associations/1/3
  51,40: http://127.0.0.1:8000/admin/cloudflare/domain-associations/1/3/edit