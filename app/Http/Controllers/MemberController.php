<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $quary = Member::with('activeborrowings');

        if ($request->has('search')) {
            $search = $request->search;
            $quary = $quary->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        if ($request->has('status')) {
            $quary->where('status', $request->status);
        }

        $member = $quary->paginate(10);

        return MemberResource::collection($member);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request)
    {
        $member = Member::create($request->validated());

        return new MemberResource($member);
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        $member->load(['activeborrowings', 'borrowings']);

        return new MemberResource($member);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMemberRequest $request, Member $member)
    {
        $member->update($request->validated());

        return new MemberResource($member);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        if ($member->activeborrowings()->count() > 0) {
            return response()->json(['status' => false, 'message' => 'Cannot delete member with active borrowings'], 422);
        } else {
            $member->delete();

            return response()->json(['status' => true, 'message' => 'Member deleted successfully'], 200);
        }
    }
}
