<?php

namespace App\Http\Controllers;

use App\Models\LinkGroup;
use App\Models\LinkGroupItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LinkGroupController extends Controller
{
    /**
     * Display a listing of the user's link groups.
     */
    public function index()
    {
        $user = Auth::user();
        $linkGroups = LinkGroup::where('usuario_id', $user->id)
            ->with('items')
            ->get();
            
        return view('linkgroups.index', compact('linkGroups'));
    }

    /**
     * Show the form for creating a new link group.
     */
    public function create()
    {
        return view('linkgroups.create');
    }

    /**
     * Store a newly created link group.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $linkGroup = new LinkGroup();
        $linkGroup->title = $request->input('title');
        $linkGroup->description = $request->input('description');
        $linkGroup->active = $request->input('active', true);
        $linkGroup->usuario_id = Auth::id();
        $linkGroup->save();

        return redirect()->route('cliente.linkgroups.show', $linkGroup->id)
            ->with('success', 'Grupo de links criado com sucesso!');
    }

    /**
     * Display the specified link group.
     */
    public function show($id)
    {
        $user = Auth::user();
        $linkGroup = LinkGroup::where('id', $id)
            ->where('usuario_id', $user->id)
            ->with(['items' => function($query) {
                $query->orderBy('order', 'asc');
            }, 'banks' => function($query) {
                $query->with('template');
            }])
            ->firstOrFail();

        return view('cliente.linkgroups.show', compact('linkGroup'));
    }

    /**
     * Show the form for editing the specified link group.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $linkGroup = LinkGroup::where('id', $id)
            ->where('usuario_id', $user->id)
            ->firstOrFail();

        return view('linkgroups.edit', compact('linkGroup'));
    }

    /**
     * Update the specified link group.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $linkGroup = LinkGroup::where('id', $id)
            ->where('usuario_id', $user->id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $linkGroup->title = $request->input('title');
        $linkGroup->description = $request->input('description');
        $linkGroup->active = $request->has('active');
        $linkGroup->save();

        return redirect()->route('cliente.linkgroups.show', $linkGroup->id)
            ->with('success', 'Grupo de links atualizado com sucesso!');
    }

    /**
     * Remove the specified link group.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $linkGroup = LinkGroup::where('id', $id)
            ->where('usuario_id', $user->id)
            ->firstOrFail();

        // Delete related items first
        LinkGroupItem::where('group_id', $linkGroup->id)->delete();
        
        // Now delete the group
        $linkGroup->delete();

        return redirect()->route('cliente.linkgroups.index')
            ->with('success', 'Grupo de links removido com sucesso!');
    }

    /**
     * Add a new link item to a group.
     */
    public function addItem(Request $request, $groupId)
    {
        $user = Auth::user();
        $linkGroup = LinkGroup::where('id', $groupId)
            ->where('usuario_id', $user->id)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get the last order value if not provided
        $order = $request->input('order');
        if (!$order) {
            $lastItem = LinkGroupItem::where('group_id', $groupId)
                ->orderBy('order', 'desc')
                ->first();
            $order = $lastItem ? $lastItem->order + 1 : 1;
        }

        $item = new LinkGroupItem();
        $item->group_id = $groupId;
        $item->title = $request->input('title');
        $item->url = $request->input('url');
        $item->icon = $request->input('icon');
        $item->order = $order;
        $item->active = $request->input('active', true);
        $item->save();

        return redirect()->route('cliente.linkgroups.show', $groupId)
            ->with('success', 'Item adicionado com sucesso!');
    }

    /**
     * Update a link item.
     */
    public function updateItem(Request $request, $groupId, $itemId)
    {
        $user = Auth::user();
        $linkGroup = LinkGroup::where('id', $groupId)
            ->where('usuario_id', $user->id)
            ->firstOrFail();

        $item = LinkGroupItem::where('id', $itemId)
            ->where('group_id', $groupId)
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:1000',
            'icon' => 'nullable|string|max:100',
            'order' => 'nullable|integer',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $item->title = $request->input('title');
        $item->url = $request->input('url');
        $item->icon = $request->input('icon');
        $item->order = $request->input('order', $item->order);
        $item->active = $request->has('active');
        $item->save();

        return redirect()->route('cliente.linkgroups.show', $groupId)
            ->with('success', 'Item atualizado com sucesso!');
    }

    /**
     * Remove a link item.
     */
    public function removeItem($groupId, $itemId)
    {
        $user = Auth::user();
        $linkGroup = LinkGroup::where('id', $groupId)
            ->where('usuario_id', $user->id)
            ->firstOrFail();

        $item = LinkGroupItem::where('id', $itemId)
            ->where('group_id', $groupId)
            ->firstOrFail();

        $item->delete();

        return redirect()->route('cliente.linkgroups.show', $groupId)
            ->with('success', 'Item removido com sucesso!');
    }
    
    /**
     * Reorder link items.
     */
    public function reorderItems(Request $request, $groupId)
    {
        $user = Auth::user();
        $linkGroup = LinkGroup::where('id', $groupId)
            ->where('usuario_id', $user->id)
            ->firstOrFail();
            
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*' => 'integer|exists:link_group_items,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $items = $request->input('items');
        foreach ($items as $index => $itemId) {
            LinkGroupItem::where('id', $itemId)
                ->where('group_id', $groupId)
                ->update(['order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
