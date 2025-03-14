<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Subcategory;

class AdminController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('admin.dashboard', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create($request->all());

        return redirect()->route('admin.dashboard')->with('success', 'Category created successfully.');
    }

    public function storeSubcategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
        ]);

        Subcategory::create($request->all());

        return redirect()->route('admin.dashboard')->with('success', 'Subcategory created successfully.');
    }
}