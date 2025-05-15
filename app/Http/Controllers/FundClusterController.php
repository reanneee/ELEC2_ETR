<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FundCluster;

class FundClusterController extends Controller
{
    public function index()
    {
        $fundClusters = FundCluster::all();
        return view('fund_clusters.index', compact('fundClusters'));
    }

    public function create()
    {
        return view('fund_clusters.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        FundCluster::create($request->only('name'));
        return redirect()->route('fund_clusters.index')->with('success', 'Fund Cluster created successfully.');
    }

    public function edit(FundCluster $fund_cluster)
    {
        return view('fund_clusters.edit', compact('fund_cluster'));
    }

    public function update(Request $request, FundCluster $fund_cluster)
    {
        $request->validate(['name' => 'required']);
        $fund_cluster->update($request->only('name'));
        return redirect()->route('fund_clusters.index')->with('success', 'Fund Cluster updated successfully.');
    }

    public function destroy(FundCluster $fund_cluster)
    {
        $fund_cluster->delete();
        return redirect()->route('fund_clusters.index')->with('success', 'Fund Cluster deleted.');
    }
}
