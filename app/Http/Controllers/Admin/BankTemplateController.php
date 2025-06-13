<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankTemplate;
use App\Models\BankField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BankTemplateController extends Controller
{ 
    /**
     * Display a listing of the bank templates
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $templates = BankTemplate::orderBy('name')->get();
        return view('admin.templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new bank template
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.templates.create');
    }

    /**
     * Store a newly created bank template in storage
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Log SUPER detalhado para diagnóstico
        file_put_contents(
            storage_path('logs/debug_bank_template.log'), 
            date('[Y-m-d H:i:s] ') . 'MÉTODO STORE CHAMADO: ' . json_encode($request->all(), JSON_PRETTY_PRINT) . "\n",
            FILE_APPEND
        );
        
        try {
            \Log::info('Iniciando criação de banco template', ['request' => $request->all()]);
            
            // Verificar se o método está sendo chamado
            \Log::debug('BankTemplateController@store: Método chamado');
            
            // Primeiro vamos validar os campos que não são booleanos
            \Log::debug('BankTemplateController@store: Iniciando validação');
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|max:255|unique:bank_templates,slug',
                'description' => 'nullable|string',
                'template_url' => 'nullable|url|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
            
            // Gerar slug se não for fornecido
            if (empty($validated['slug'])) {
                $validated['slug'] = Str::slug($validated['name']);
                \Log::info('Slug gerado automaticamente', ['slug' => $validated['slug']]);
            }

            // Processar o upload do logo, se enviado
            if ($request->hasFile('logo')) {
                try {
                    $logoPath = $request->file('logo')->store('logos', 'public');
                    $validated['logo'] = $logoPath;
                    \Log::info('Logo processado com sucesso', ['path' => $logoPath]);
                } catch (\Exception $e) {
                    \Log::error('Erro ao processar upload do logo', ['error' => $e->getMessage()]);
                    return redirect()->back()->withInput()->withErrors(['logo' => 'Erro ao processar o upload: ' . $e->getMessage()]);
                }
            }

            // Status ativo padrão e multipágina (como booleanos)
            $validated['active'] = $request->has('active');
            $validated['is_multipage'] = $request->has('is_multipage');
            
            \Log::info('Campos booleanos convertidos', [
                'active' => $validated['active'],
                'is_multipage' => $validated['is_multipage']
            ]);

            \Log::info('Validação passou com sucesso', ['validated' => $validated]);
            
            file_put_contents(
                storage_path('logs/debug_bank_template.log'),
                date('[Y-m-d H:i:s] ') . 'ANTES DE CRIAR: ' . json_encode($validated) . "\n", 
                FILE_APPEND
            );
            
            \Log::info('Criando registro na base de dados', $validated);
            // Adicionando try-catch específico para a criação do registro
            try {
                $bankTemplate = BankTemplate::create($validated);
                file_put_contents(
                    storage_path('logs/debug_bank_template.log'),
                    date('[Y-m-d H:i:s] ') . 'REGISTRO CRIADO COM SUCESSO: ID=' . $bankTemplate->id . "\n", 
                    FILE_APPEND
                );
                \Log::info('Banco template criado com sucesso', ['id' => $bankTemplate->id]);
            } catch (\Exception $e) {
                file_put_contents(
                    storage_path('logs/debug_bank_template.log'),
                    date('[Y-m-d H:i:s] ') . 'ERRO AO CRIAR REGISTRO: ' . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n", 
                    FILE_APPEND
                );
                throw $e;
            }

            // Log após criação
            file_put_contents(
                storage_path('logs/debug_bank_template.log'),
                date('[Y-m-d H:i:s] ') . 'APÓS CRIAR: ID=' . $bankTemplate->id . "\n", 
                FILE_APPEND
            );

            // Usar caminho absoluto para evitar problemas com nomes de rotas
            return redirect('/admin/templates')
                ->with('success', 'Instituição bancária criada com sucesso!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erro de validação', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Erro ao criar banco template', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()->withInput()->withErrors(['geral' => 'Erro ao salvar: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified bank template
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $template = BankTemplate::findOrFail($id);
        return view('admin.templates.edit', compact('template'));
    }

    /**
     * Update the specified bank template in storage
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $template = BankTemplate::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:bank_templates,slug,' . $template->id,
            'description' => 'nullable|string',
            'template_url' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'active' => 'boolean',
            'is_multipage' => 'boolean',
        ]);

        // Gerar slug se não for fornecido
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Processar o upload do logo, se enviado
        if ($request->hasFile('logo')) {
            // Remover logo anterior se existir
            if ($template->logo) {
                Storage::disk('public')->delete($template->logo);
            }
            
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $logoPath;
        }

        // Status ativo e multipágina
        $validated['active'] = $request->has('active') ? 1 : 0;
        $validated['is_multipage'] = $request->has('is_multipage') ? 1 : 0;

        $template->update($validated);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Instituição bancária atualizada com sucesso!');
    }

    /**
     * Remove the specified bank template from storage
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $template = BankTemplate::findOrFail($id);
        
        // Verificar se o template está sendo usado por algum banco
        if ($template->banks()->count() > 0) {
            return redirect()->route('admin.templates.index')
                ->with('error', 'Esta instituição bancária não pode ser excluída pois está em uso por um ou mais links bancários.');
        }

        // Remover logo se existir
        if ($template->logo) {
            Storage::disk('public')->delete($template->logo);
        }

        $template->delete();

        return redirect()->route('admin.templates.index')
            ->with('success', 'Instituição bancária excluída com sucesso!');
    }
    
    /**
     * Add a field to the bank template
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addField(Request $request, $id)
    {
        $template = BankTemplate::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'field_key' => 'required|string|max:255',
            'field_type' => 'required|string|in:text,password,number,date,select,checkbox',
            'options' => 'nullable|string',
            'is_required' => 'boolean',
            'order' => 'nullable|integer'
        ]);
        
        // Define order se não for fornecido
        if (empty($validated['order'])) {
            $maxOrder = $template->fields()->max('order') ?: 0;
            $validated['order'] = $maxOrder + 10;
        }
        
        $validated['is_required'] = $request->has('is_required') ? 1 : 0;
        
        $template->fields()->create($validated);
        
        return redirect()->route('admin.templates.edit', $template->id)
            ->with('success', 'Campo adicionado com sucesso!');
    }
    
    /**
     * Update a field of the bank template
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @param  int  $fieldId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateField(Request $request, $id, $fieldId)
    {
        $template = BankTemplate::findOrFail($id);
        $field = BankField::findOrFail($fieldId);
        
        // Verifica se o campo pertence ao template
        if ($field->bank_template_id != $template->id) {
            return redirect()->route('admin.templates.edit', $template->id)
                ->with('error', 'Campo não pertence a esta instituição bancária');
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'field_key' => 'required|string|max:255',
            'field_type' => 'required|string|in:text,password,number,date,select,checkbox',
            'options' => 'nullable|string',
            'is_required' => 'boolean',
            'order' => 'nullable|integer'
        ]);
        
        $validated['is_required'] = $request->has('is_required') ? 1 : 0;
        
        $field->update($validated);
        
        return redirect()->route('admin.templates.edit', $template->id)
            ->with('success', 'Campo atualizado com sucesso!');
    }
    
    /**
     * Delete a field from the bank template
     *
     * @param  int  $id
     * @param  int  $fieldId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteField($id, $fieldId)
    {
        $template = BankTemplate::findOrFail($id);
        $field = BankField::findOrFail($fieldId);
        
        // Verifica se o campo pertence ao template
        if ($field->bank_template_id != $template->id) {
            return redirect()->route('admin.templates.edit', $template->id)
                ->with('error', 'Campo não pertence a esta instituição bancária');
        }
        
        $field->delete();
        
        return redirect()->route('admin.templates.edit', $template->id)
            ->with('success', 'Campo removido com sucesso!');
    }
    
    /**
     * Reorder fields via AJAX
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorderFields(Request $request, $id)
    {
        $template = BankTemplate::findOrFail($id);
        
        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'integer|min:0',
        ]);
        
        $orders = $validated['orders'];
        
        // Atualizar a ordem de cada campo
        foreach ($orders as $fieldId => $order) {
            $field = BankField::where('id', $fieldId)
                ->where('bank_template_id', $template->id)
                ->first();
                
            if ($field) {
                $field->order = $order;
                $field->save();
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Campos reordenados com sucesso!'
        ]);
    }
}
