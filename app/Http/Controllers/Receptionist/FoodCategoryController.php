<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Http\Requests\Receptionist\FoodCategoryRequest;
use App\Models\FoodCategory;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// Mengelola kategori makanan dan minuman.
class FoodCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $categories = FoodCategory::query()->withCount('menuItems')
            ->when($request->filled('search'), fn ($query) => $query->where('name', 'like', '%'.$request->string('search').'%'))
            ->orderBy('sort_order')->orderBy('name')->paginate(12)->withQueryString();

        return view('receptionist.food.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('receptionist.food.categories.create');
    }

    public function store(FoodCategoryRequest $request): RedirectResponse
    {
        $category = FoodCategory::create($request->validated());
        AuditLogger::record($request, 'create', 'food_categories', $category, 'Membuat kategori F&B '.$category->name.'.', null, $category->toArray());

        return redirect()->route('receptionist.food-categories.index')->with('success', 'Kategori F&B berhasil ditambahkan.');
    }

    public function edit(FoodCategory $foodCategory): View
    {
        return view('receptionist.food.categories.edit', compact('foodCategory'));
    }

    public function update(FoodCategoryRequest $request, FoodCategory $foodCategory): RedirectResponse
    {
        $old = $foodCategory->toArray();
        $foodCategory->update($request->validated());
        AuditLogger::record($request, 'update', 'food_categories', $foodCategory, 'Memperbarui kategori F&B '.$foodCategory->name.'.', $old, $foodCategory->fresh()->toArray());

        return redirect()->route('receptionist.food-categories.index')->with('success', 'Kategori F&B berhasil diperbarui.');
    }

    public function destroy(Request $request, FoodCategory $foodCategory): RedirectResponse
    {
        if ($foodCategory->menuItems()->exists()) {
            $foodCategory->update(['is_active' => false]);
            AuditLogger::record($request, 'deactivate', 'food_categories', $foodCategory, 'Menonaktifkan kategori F&B '.$foodCategory->name.' karena masih memiliki menu.');

            return back()->with('success', 'Kategori masih memiliki menu sehingga dinonaktifkan, bukan dihapus.');
        }

        $foodCategory->delete();
        AuditLogger::record($request, 'delete', 'food_categories', $foodCategory, 'Menghapus kategori F&B '.$foodCategory->name.'.');

        return back()->with('success', 'Kategori F&B berhasil dihapus.');
    }
}
