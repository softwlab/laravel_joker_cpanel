<?php

namespace App\Http\Controllers;

use App\Models\BankTemplate;
use App\Models\BankField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BankTemplateController extends Controller
{
    /**
     * Display a listing of the bank templates.
     */
    public function index()
    {
        $templates = BankTemplate::withCount('fields')->paginate(15);
        return view('admin.bank-templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new bank template.
     */
    public function create()
    {
        return view('admin.bank-templates.create');
    }

    /**
     * Store a newly created bank template in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_url' => 'nullable|string|url',
            'logo' => 'nullable|string',
            'active' => 'boolean',
        ]);
        
        $validated['slug'] = Str::slug($validated['name']);
        $validated['active'] = $request->has('active');
        
        $template = BankTemplate::create($validated);
        
        return redirect()->route('admin.bank-templates.edit', $template->id)
            ->with('success', 'Template de banco criado com sucesso. Agora adicione os campos necessários.');
    }

    /**
     * Display the specified bank template.
     */
    public function show($id)
    {
        $template = BankTemplate::with('fields')->findOrFail($id);
        return view('admin.bank-templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified bank template.
     */
    public function edit($id)
    {
        $template = BankTemplate::with(['fields' => function($query) {
            $query->orderBy('order', 'asc');
        }])->findOrFail($id);
        
        return view('admin.bank-templates.edit', compact('template'));
    }

    /**
     * Update the specified bank template in storage.
     */
    public function update(Request $request, $id)
    {
        $template = BankTemplate::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_url' => 'nullable|string|url',
            'logo' => 'nullable|string',
            'active' => 'boolean',
        ]);
        
        $validated['slug'] = Str::slug($validated['name']);
        $validated['active'] = $request->has('active');
        
        $template->update($validated);
        
        return redirect()->route('admin.bank-templates.index')
            ->with('success', 'Template de banco atualizado com sucesso.');
    }

    /**
     * Remove the specified bank template from storage.
     */
    public function destroy($id)
    {
        $template = BankTemplate::findOrFail($id);
        
        // Verificar se o template está em uso
        if ($template->banks()->exists()) {
            return redirect()->route('admin.bank-templates.index')
                ->with('error', 'Este template não pode ser excluído pois está sendo usado por bancos.');
        }
        
        $template->fields()->delete();
        $template->delete();
        
        return redirect()->route('admin.bank-templates.index')
            ->with('success', 'Template de banco excluído com sucesso.');
    }
    
    /**
     * Add a field to the bank template
     */
    public function addField(Request $request, $id)
    {
        $template = BankTemplate::findOrFail($id);
        
        $validated = $request->validate([
            'field_name' => 'required|string|max:50',
            'field_label' => 'required|string|max:100',
            'field_type' => 'required|in:text,email,number,password,date,tel',
            'placeholder' => 'nullable|string|max:100',
            'required' => 'boolean',
            'order' => 'integer|min:0',
        ]);
        
        $validated['required'] = $request->has('required');
        $validated['bank_template_id'] = $template->id;
        $validated['active'] = true;
        
        BankField::create($validated);
        
        return redirect()->route('admin.bank-templates.edit', $template->id)
            ->with('success', 'Campo adicionado com sucesso.');
    }
    
    /**
     * Update a field of the bank template
     */
    public function updateField(Request $request, $id, $fieldId)
    {
        $field = BankField::where('bank_template_id', $id)->findOrFail($fieldId);
        
        $validated = $request->validate([
            'field_name' => 'required|string|max:50',
            'field_label' => 'required|string|max:100',
            'field_type' => 'required|in:text,email,number,password,date,tel',
            'placeholder' => 'nullable|string|max:100',
            'required' => 'boolean',
            'order' => 'integer|min:0',
            'active' => 'boolean',
        ]);
        
        $validated['required'] = $request->has('required');
        $validated['active'] = $request->has('active');
        
        $field->update($validated);
        
        return redirect()->route('admin.bank-templates.edit', $id)
            ->with('success', 'Campo atualizado com sucesso.');
    }
    
    /**
     * Remove a field from the bank template
     */
    public function deleteField($id, $fieldId)
    {
        $field = BankField::where('bank_template_id', $id)->findOrFail($fieldId);
        $field->delete();
        
        return redirect()->route('admin.bank-templates.edit', $id)
            ->with('success', 'Campo excluído com sucesso.');
    }
}
