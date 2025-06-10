<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankTemplate;
use App\Models\BankField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:bank_templates,slug',
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
            $logoPath = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $logoPath;
        }

        // Status ativo padrão e multipágina
        $validated['active'] = $request->has('active') ? 1 : 0;
        $validated['is_multipage'] = $request->has('is_multipage') ? 1 : 0;

        BankTemplate::create($validated);

        return redirect()->route('admin.templates.index')
            ->with('success', 'Instituição bancária criada com sucesso!');
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
