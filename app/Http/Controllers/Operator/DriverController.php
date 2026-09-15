<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Operator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
    private function getOperator()
    {
        $operator = Auth::guard('operator')->user();
        if ($operator) {
            session(['operator_id' => $operator->id]);
            return $operator;
        }
        if (session('operator_id')) {
            $operator = Operator::find(session('operator_id'));
            if ($operator) return $operator;
        }
        abort(403, 'Operator session expired. Please log in again.');
    }

    public function index()
    {
        $operator = $this->getOperator();
        $drivers = Driver::where('operator_id', $operator->id)->orderBy('full_name')->paginate(20);
        return view('operator.drivers', compact('operator', 'drivers'));
    }

    public function store(Request $request)
    {
        $operator = $this->getOperator();
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'license_number' => ['required', 'string', 'max:50', Rule::unique('drivers', 'license_number')],
            'license_expiry_date' => 'nullable|date|after:today',
            'address' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);
        $validated['operator_id'] = $operator->id;
        Driver::create($validated);
        return back()->with('status', 'Driver added successfully.');
    }

    public function update(Request $request, $id)
    {
        $operator = $this->getOperator();
        $driver = Driver::where('operator_id', $operator->id)->findOrFail($id);
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'license_number' => ['required', 'string', 'max:50', Rule::unique('drivers', 'license_number')->ignore($driver->id)],
            'license_expiry_date' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);
        $driver->update($validated);
        return back()->with('status', 'Driver updated successfully.');
    }

    public function json($id)
    {
        $operator = $this->getOperator();
        $driver = Driver::where('operator_id', $operator->id)->findOrFail($id);
        return response()->json(['success' => true, 'driver' => $driver]);
    }

    public function destroy($id)
    {
        $operator = $this->getOperator();
        $driver = Driver::where('operator_id', $operator->id)->findOrFail($id);
        if ($driver->routes()->where('is_active', true)->exists()) {
            return back()->withErrors(['error' => 'Cannot delete driver assigned to active trips.']);
        }
        $driver->delete();
        return back()->with('status', 'Driver removed successfully.');
    }
}