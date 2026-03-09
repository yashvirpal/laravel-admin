<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

use App\Models\Address;
use App\Models\Order;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ProfileController extends Controller
{
    use AuthorizesRequests; // 👈 ADD THIS

    /**
     * Display the user's profile form.
     */
    public function __construct()
    {
        //   $this->middleware('auth');
    }

    public function show()
    {
        dd('order success');
    }
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }



    // Dashboard
    public function dashboard()
    {
        $user = Auth::user();

        $stats = [
            'total_orders' => $user->getTotalOrders(),
            'total_spent' => $user->getTotalSpent(),
            'pending_orders' => $user->getPendingOrders(),
        ];

        $recentOrders = $user->orders()->take(5)->get();

        return view('profile.dashboard', compact('user', 'stats', 'recentOrders'));
    }
    //Wishlist
    public function wishlist()
    {
        $wishlists = Wishlist::with('wishlistable')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        // Used only for UI (heart color)
        $wishlistedProductIds = $wishlists->pluck('wishlistable_id')->toArray();

        return view('profile.wishlist', compact(
            'wishlists',
            'wishlistedProductIds'
        ));
    }

    // Profile
    public function profile()
    {
        $user = Auth::user();
        return view('profile.profile-edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'country_code' => 'nullable|string|max:5',
                'phone' => 'nullable|string|max:15|unique:users,phone,' . $user->id,
                'profile_image' => 'nullable|image|max:2048',
            ]);

            if ($request->hasFile('profile_image')) {
                // Delete old image
                if ($user->profile_image) {
                    Storage::disk('public')->delete($user->profile_image);
                }

                $validated['profile_image'] = $request->file('profile_image')
                    ->store('profile-images', 'public');
            }

            $user->update($validated);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully!',
                    'redirect_url' => route('profile.edit')
                ]);
            }
            return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }

            throw $e;
        }

    }

    public function editPassword()
    {
        $user = Auth::user();
        return view('profile.password-edit', compact('user'));
    }
    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'password' => 'required|min:8|confirmed',
            ]);

            $user = Auth::user();

            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect']);
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password updated successfully!',
                ]);
            }
            return back()->with('success', 'Password updated successfully!');
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }

            throw $e;
        }

    }

    // Addresses
    public function addresses()
    {
        $user = Auth::user();
        $addresses = $user->addresses()->get();

        return view('profile.addresses.index', compact('addresses'));
    }

    public function createAddress()
    {
        return view('profile.addresses.form');
    }

    public function storeAddress(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|in:billing,shipping',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'company' => 'nullable|string|max:255',
                'address_line1' => 'required|string|max:255',
                'address_line2' => 'nullable|string|max:255',
                'phone' => 'required|string|max:20',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'zip' => 'required|string|max:10',
                'is_default' => 'nullable|boolean',
            ]);
            $validated['is_default'] = $request->boolean('is_default');

            $address = Auth::user()->addresses()->create($validated);

            if ($request->is_default) {
                $address->makeDefault();
            }
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Address added successfully!',
                    'redirect_url' => route('profile.addresses')
                ]);
            }
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }

            throw $e;
        }
        return redirect()->route('profile.addresses')
            ->with('success', 'Address added successfully!');
    }

    public function editAddress(Address $address)
    {
        $this->authorize('update', $address);
        return view('profile.addresses.form', compact('address'));
    }

    public function updateAddress(Request $request, Address $address)
    {
        try {
            $this->authorize('update', $address);

            $validated = $request->validate([
                'type' => 'required|in:billing,shipping',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'company' => 'nullable|string|max:255',
                'address_line1' => 'required|string|max:255',
                'address_line2' => 'nullable|string|max:255',
                'phone' => 'required|string|max:20',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'zip' => 'required|string|max:10',
                'is_default' => 'nullable|boolean',
            ]);

            $validated['is_default'] = $request->boolean('is_default');

            $address->update($validated);

            if ($validated['is_default']) {
                $address->makeDefault();
            }
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Address updated successfully!',
                    'redirect_url' => route('profile.addresses')
                ]);
            }

            return redirect()->route('profile.addresses')
                ->with('success', 'Address updated successfully!');
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors()
                ], 422);
            }

            throw $e;
        }
    }

    public function deleteAddress(Request $request, Address $address)
    {
        try {
            $this->authorize('delete', $address);
            $address->delete();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Address deleted successfully!',
                    'redirect_url' => route('profile.addresses')
                ]);
            }
            return redirect()->route('profile.addresses')->with('success', 'Address deleted successfully!');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong. Please try again.',
                    'errors' => $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function makeDefaultAddress(Address $address)
    {
        $this->authorize('update', $address);

        $address->makeDefault();

        return back()->with('success', 'Default address updated!');
    }

    // Orders
    public function orders()
    {
        $user = Auth::user();
        $orders = $user->orders()->paginate(10);

        return view('profile.orders.index', compact('orders'));
    }

    public function orderDetail(Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['items.product', 'items.variant', 'transactions', 'coupons']);

        return view('profile.orders.show', compact('order'));
    }

    public function cancelOrder(Order $order)
    {
        $this->authorize('update', $order);

        if (!$order->canCancel()) {
            return back()->with('error', 'This order cannot be cancelled.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order cancelled successfully!');
    }

    // Transactions
    public function transactions()
    {
        $user = Auth::user();
        $transactions = $user->transactions()->with('order')->paginate(15);

        return view('profile.transactions.index', compact('transactions'));
    }

    public function transactionDetail($id)
    {
        $transaction = Auth::user()->transactions()->findOrFail($id);
        $transaction->load('order.items');

        return view('profile.transactions.show', compact('transaction'));
    }

}
