<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
    /**
     * Get users with filters
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsers(Request $request)
    {
        $query = User::query();

        // Apply filters
        if ($request->has('name') && $request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        
        if ($request->has('email') && $request->email) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        
        if ($request->has('phone') && $request->phone) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }
        
        if ($request->has('user_type') && $request->user_type) {
            $query->where('user_type', $request->user_type);
        }
        
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }
        
        if ($request->has('is_verified') && $request->is_verified !== '') {
            $query->where('is_verified', $request->is_verified);
        }
        
        if ($request->has('created_at_from') && $request->created_at_from) {
            $query->whereDate('created_at', '>=', $request->created_at_from);
        }
        
        if ($request->has('created_at_to') && $request->created_at_to) {
            $query->whereDate('created_at', '<=', $request->created_at_to);
        }

        // Get results
        $users = $query->orderBy('id', 'desc')->get();

        // Format results
        $formattedUsers = $users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'user_type' => $user->user_type,
                'user_type_label' => ucfirst(str_replace('_', ' ', $user->user_type)),
                'status' => $user->status,
                'status_badge' => $this->getStatusBadge($user->status),
                'is_verified' => $user->is_verified,
                'verification_badge' => $this->getVerificationBadge($user->is_verified),
                'created_at' => $user->created_at->format('M d, Y H:i'),
                'actions' => $this->getActionButtons($user)
            ];
        });

        return response()->json([
            'data' => $formattedUsers
        ]);
    }

    /**
     * Get status badge HTML
     *
     * @param int $status
     * @return string
     */
    private function getStatusBadge($status)
    {
        if ($status == 1) {
            return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-success/10 text-success ring-success/20">Active</span>';
        } else {
            return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-destructive/10 text-destructive ring-destructive/20">Inactive</span>';
        }
    }

    /**
     * Get verification badge HTML
     *
     * @param int $isVerified
     * @return string
     */
    private function getVerificationBadge($isVerified)
    {
        if ($isVerified == 1) {
            return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-success/10 text-success ring-success/20">Verified</span>';
        } else {
            return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-warning/10 text-warning ring-warning/20">Unverified</span>';
        }
    }

    /**
     * Get action buttons HTML
     *
     * @param User $user
     * @return string
     */
    private function getActionButtons($user)
    {
        $action = '<div class="flex items-center space-x-2">';
        
        // View button
        $action .= '<a href="' . route('user.show', $user->id) . '" class="action-button" data-tooltip="View">
            <i class="fas fa-eye text-primary"></i>
        </a>';
        
        // Edit button
        $action .= '<a href="' . route('user.edit', $user->id) . '" class="action-button" data-tooltip="Edit">
            <i class="fas fa-edit text-secondary"></i>
        </a>';
        
        // Delete button (don't allow deleting self)
        if ($user->id != auth()->id()) {
            $action .= '<a href="javascript:void(0)" class="action-button delete-user" data-id="' . $user->id . '" data-tooltip="Delete">
                <i class="fas fa-trash text-destructive"></i>
            </a>';
        }
        
        $action .= '</div>';
        
        return $action;
    }
}
