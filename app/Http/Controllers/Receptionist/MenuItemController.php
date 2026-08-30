<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\MenuItemRequest;
use App\Models\FoodCategory;
use App\Models\MenuItem;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function index(Request $request): View
    {
        $menuItems = MenuItem::query()->with('category')->withCount('orderItems')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('category'), fn ($query) => $query->where('food_category_id', $request->integer('category')))
            ->orderBy('sort_order')->orderBy('name')->paginate(12)->withQueryString();
        $categories = FoodCategory::query()->orderBy('sort_order')->orderBy('name')->get();

        return view('receptionist.food.menus.index', compact('menuItems', 'categories'));
    }

    public function create(): View
    {
        return view('receptionist.food.menus.create', ['categories' => $this->categories()]);
    }

    public function store(MenuItemRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('menu-items', 'public');
        }
        $menuItem = MenuItem::create($data);
        AuditLogger::record($request, 'create', 'menu_items', $menuItem, 'Membuat menu '.$menuItem->name.'.', null, $menuItem->toArray());

        return redirect()->route('receptionist.menu-items.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(MenuItem $menuItem): View
    {
        return view('receptionist.food.menus.edit', ['menuItem' => $menuItem, 'categories' => $this->categories()]);
    }

    public function update(MenuItemRequest $request, MenuItem $menuItem): RedirectResponse
    {
        $old = $menuItem->toArray();
        $data = $request->safe()->except('image');
        if ($request->hasFile('image')) {
            $oldImage = $menuItem->image_path;
            $data['image_path'] = $request->file('image')->store('menu-items', 'public');
            if ($oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
        }
        $menuItem->update($data);
        AuditLogger::record($request, 'update', 'menu_items', $menuItem, 'Memperbarui menu '.$menuItem->name.'.', $old, $menuItem->fresh()->toArray());

        return redirect()->route('receptionist.menu-items.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Request $request, MenuItem $menuItem): RedirectResponse
    {
        if ($menuItem->orderItems()->exists()) {
            $menuItem->update(['is_active' => false, 'is_available' => false]);
            AuditLogger::record($request, 'deactivate', 'menu_items', $menuItem, 'Menonaktifkan menu '.$menuItem->name.' karena memiliki histori pesanan.');

            return back()->with('success', 'Menu sudah pernah dipesan sehingga dinonaktifkan, bukan dihapus.');
        }

        $image = $menuItem->image_path;
        $menuItem->delete();
        if ($image) {
            Storage::disk('public')->delete($image);
        }
        AuditLogger::record($request, 'delete', 'menu_items', $menuItem, 'Menghapus menu '.$menuItem->name.'.');

        return back()->with('success', 'Menu berhasil dihapus.');
    }

    private function categories()
    {
        return FoodCategory::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
    }
}
