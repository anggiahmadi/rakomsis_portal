<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePromotionRequest;
use App\Http\Requests\UpdatePromotionRequest;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PromotionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Promotion::class, 'promotion');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Promotion::class);

        $query = Promotion::query();

        if ($request->filled('q')) {
            $query->where('name', 'like', '%'.$request->q.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $promotions = $query->orderBy('starts_at', 'desc')->paginate(20);

        return view('admin.promotions.index', compact('promotions'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Promotion $promotion)
    {
        $this->authorize('view', $promotion);
        return view('admin.promotions.show', compact('promotion'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Promotion::class);
        return view('admin.promotions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePromotionRequest $request)
    {
        $this->authorize('create', Promotion::class);

        Promotion::create($request->validated());

        return Redirect::route('admin.promotions.index')->with('success', 'Promotion created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promotion)
    {
        $this->authorize('update', $promotion);
        return view('admin.promotions.edit', compact('promotion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePromotionRequest $request, Promotion $promotion)
    {
        $this->authorize('update', $promotion);

        $promotion->update($request->validated());

        return Redirect::route('admin.promotions.index')->with('success', 'Promotion updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        $this->authorize('delete', $promotion);

        $promotion->delete();

        return Redirect::route('admin.promotions.index')->with('success', 'Promotion deleted successfully.');
    }
}
