<?php
// app/Http/Controllers/UserProfileController.php
namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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

    // Profile
    public function profile()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

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

        return redirect()->route('profile.edit')
            ->with('success', 'Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
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

        return back()->with('success', 'Password updated successfully!');
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
        return view('profile.addresses.create');
    }

    public function storeAddress(Request $request)
    {
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
            'postal_code' => 'required|string|max:10',
            'is_default' => 'boolean',
        ]);

        $address = Auth::user()->addresses()->create($validated);

        if ($request->is_default) {
            $address->makeDefault();
        }

        return redirect()->route('profile.addresses')
            ->with('success', 'Address added successfully!');
    }

    public function editAddress(Address $address)
    {
        $this->authorize('update', $address);
        return view('profile.addresses.edit', compact('address'));
    }

    public function updateAddress(Request $request, Address $address)
    {
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
            'postal_code' => 'required|string|max:10',
            'is_default' => 'boolean',
        ]);

        $address->update($validated);

        if ($request->is_default) {
            $address->makeDefault();
        }

        return redirect()->route('profile.addresses')
            ->with('success', 'Address updated successfully!');
    }

    public function deleteAddress(Address $address)
    {
        $this->authorize('delete', $address);

        $address->delete();

        return redirect()->route('profile.addresses')
            ->with('success', 'Address deleted successfully!');
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