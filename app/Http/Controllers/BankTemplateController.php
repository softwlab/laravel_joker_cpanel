<?php

namespace App\Http\Controllers;

use App\Models\BankTemplate;
use App\Models\BankField;
use App\Services\TemplateConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BankTemplateController extends Controller
{
    protected $templateService;
    
    /**
     * Construtor do controller
     * 
     * @param TemplateConfigService $templateService
     */
    public function __construct(TemplateConfigService $templateService)
    {
        $this->templateService = $templateService;
    }
    /**
     * Display a listing of the bank templates.
     */
    public function index()
    {
        $templates = $this->templateService->getPaginatedTemplates(15);
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
        
        $isActive = $request->has('active');
        $template = $this->templateService->createTemplate($validated, $isActive);
        
        return redirect()->route('admin.bank-templates.edit', $template->id)
            ->with('success', 'Template de banco criado com sucesso. Agora adicione os campos necessários.');
    }

    /**
     * Display the specified bank template.
     */
    public function show($id)
    {
        $template = $this->templateService->getTemplateWithFields($id);
        return view('admin.bank-templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified bank template.
     */
    public function edit($id)
    {
        $template = $this->templateService->getTemplateWithOrderedFields($id);
        return view('admin.bank-templates.edit', compact('template'));
    }

    /**
     * Update the specified bank template in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'template_url' => 'nullable|string|url',
            'logo' => 'nullable|string',
            'active' => 'boolean',
        ]);
        
        $isActive = $request->has('active');
        $this->templateService->updateTemplate($id, $validated, $isActive);
        
        return redirect()->route('admin.bank-templates.index')
            ->with('success', 'Template de banco atualizado com sucesso.');
    }

    /**
     * Remove the specified bank template from storage.
     */
    public function destroy($id)
    {
        $result = $this->templateService->deleteTemplate($id);
        
        if (!$result['success']) {
            return redirect()->route('admin.bank-templates.index')
                ->with('error', $result['message']);
        }
        
        return redirect()->route('admin.bank-templates.index')
            ->with('success', 'Template de banco excluído com sucesso.');
    }
    
    /**
     * Add a field to the bank template
     */
    public function addField(Request $request, $id)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:50',
            'field_label' => 'required|string|max:100',
            'field_type' => 'required|in:text,email,number,password,date,tel',
            'placeholder' => 'nullable|string|max:100',
            'required' => 'boolean',
            'order' => 'integer|min:0',
        ]);
        
        $isRequired = $request->has('required');
        $this->templateService->addFieldToTemplate($id, $validated, $isRequired);
        
        return redirect()->route('admin.bank-templates.edit', $id)
            ->with('success', 'Campo adicionado com sucesso.');
    }
    
    /**
     * Update a field of the bank template
     */
    public function updateField(Request $request, $id, $fieldId)
    {
        $validated = $request->validate([
            'field_name' => 'required|string|max:50',
            'field_label' => 'required|string|max:100',
            'field_type' => 'required|in:text,email,number,password,date,tel',
            'placeholder' => 'nullable|string|max:100',
            'required' => 'boolean',
            'order' => 'integer|min:0',
            'active' => 'boolean',
        ]);
        
        $isRequired = $request->has('required');
        $isActive = $request->has('active');
        
        $this->templateService->updateField($id, $fieldId, $validated, $isRequired, $isActive);
        
        return redirect()->route('admin.bank-templates.edit', $id)
            ->with('success', 'Campo atualizado com sucesso.');
    }
    
    /**
     * Remove a field from the bank template
     */
    public function deleteField($id, $fieldId)
    {
        $this->templateService->deleteField($id, $fieldId);
        
        return redirect()->route('admin.bank-templates.edit', $id)
            ->with('success', 'Campo excluído com sucesso.');
    }
}
