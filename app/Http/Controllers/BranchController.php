<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;

class BranchController extends Controller
{
    public function index() {
        $branches = Branch::all();
        return view('branches.index', compact('branches'));
    }

    public function create() {
        return view('branches.create');
    }

    public function store(Request $request) {
        $request->validate(['branch_name' => 'required']);
        Branch::create($request->all());
        return redirect()->route('branches.index');
    }

    public function edit(Branch $branch) {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch) {
        $request->validate(['branch_name' => 'required']);
        $branch->update($request->all());
        return redirect()->route('branches.index');
    }

    public function destroy(Branch $branch) {
        $branch->delete();
        return redirect()->route('branches.index');
    }
}
