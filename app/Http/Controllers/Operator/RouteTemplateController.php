<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\Operator;
use App\Models\Route;
use App\Models\RouteTemplate;
use App\Services\OperatorAuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RouteTemplateController extends Controller
{
    private function getOperator()
    {
        $operator = Auth::guard('operator')->user();
        if ($operator) {
            session(['operator_id' => $operator->id]);
            return $operator;
        }
        return Operator::find(session('operator_id'));
    }

    /**
     * Display route templates management page.
     */
    public function index()
    {
        $operator = $this->getOperator();
        if (!$operator) {
            return redirect()->route('operator.login')->with('error', 'Please log in.');
        }

        $templates = RouteTemplate::where('operator_id', $operator->id)
            ->withCount('trips')
            ->orderBy('name')
            ->get();

        // Get trip counts from templates
        $templates->each(function ($template) {
            $template->recent_trips = $template->trips()
                ->where('travel_date', '>=', Carbon::today())
                ->count();
        });

        return view('operator.route_templates', compact('operator', 'templates'));
    }

    /**
     * Store a new route template.
     */
    public function store(Request $request)
    {
        $operator = $this->getOperator();
        if (!$operator) {
            return redirect()->route('operator.login')->with('error', 'Please log in.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255|different:origin',
            'distance_km' => 'nullable|numeric|min:0',
            'base_fare' => 'required|numeric|min:0',
        ]);

        $template = RouteTemplate::create([
            'operator_id' => $operator->id,
            'name' => $validated['name'],
            'origin' => $validated['origin'],
            'destination' => $validated['destination'],
            'distance_km' => $validated['distance_km'] ?? 0,
            'base_fare' => $validated['base_fare'],
            'is_active' => true,
        ]);

        OperatorAuditService::created($template, "Created route template: {$template->name} ({$template->origin} → {$template->destination})");

        return redirect()->route('operator.route-templates.index')
            ->with('success', "Route template '{$template->name}' created successfully.");
    }

    /**
     * Update a route template.
     */
    public function update(Request $request, $id)
    {
        $operator = $this->getOperator();
        $template = RouteTemplate::where('operator_id', $operator->id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255|different:origin',
            'distance_km' => 'nullable|numeric|min:0',
            'base_fare' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $template->update($validated);

        OperatorAuditService::updated($template, $template->getOriginal(), "Updated route template: {$template->name}");

        return redirect()->route('operator.route-templates.index')
            ->with('success', "Route template '{$template->name}' updated successfully.");
    }

    /**
     * Delete a route template.
     */
    public function destroy($id)
    {
        $operator = $this->getOperator();
        $template = RouteTemplate::where('operator_id', $operator->id)->findOrFail($id);
        $name = $template->name;
        $template->delete();

        OperatorAuditService::deleted($template, "Deleted route template: {$name}");

        return redirect()->route('operator.route-templates.index')
            ->with('success', "Route template '{$name}' deleted.");
    }

    /**
     * Create a trip from a template.
     */
    public function createTrip(Request $request, $id)
    {
        $operator = $this->getOperator();
        $template = RouteTemplate::where('operator_id', $operator->id)->findOrFail($id);

        $validated = $request->validate([
            'travel_date' => 'required|date|after_or_equal:today',
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'nullable|date_format:H:i',
            'bus_id' => 'required|exists:buses,id',
            'fare' => 'nullable|numeric|min:0',
        ]);

        // Verify bus belongs to operator
        $bus = \App\Models\Bus::where('id', $validated['bus_id'])
            ->where('operator_id', $operator->id)
            ->first();

        if (!$bus) {
            return back()->withErrors(['bus_id' => 'The selected bus does not belong to your fleet.']);
        }

        // Check for scheduling conflicts
        $conflict = Route::where('bus_id', $bus->id)
            ->where('travel_date', $validated['travel_date'])
            ->where('departure_time', $validated['departure_time'])
            ->where('is_active', true)
            ->exists();

        if ($conflict) {
            return back()->withErrors(['bus_id' => 'This bus is already scheduled for the selected date and time.']);
        }

        $fare = $validated['fare'] ?? $template->base_fare;

        $route = Route::create([
            'operator_id' => $operator->id,
            'bus_id' => $validated['bus_id'],
            'route_template_id' => $template->id,
            'origin' => $template->origin,
            'destination' => $template->destination,
            'distance_km' => $template->distance_km,
            'departure_time' => $validated['departure_time'],
            'arrival_time' => $validated['arrival_time'] ?? null,
            'fare' => $fare,
            'travel_date' => $validated['travel_date'],
            'is_active' => true,
        ]);

        $tripId = 'TRP-' . str_pad($route->id, 4, '0', STR_PAD_LEFT);
        OperatorAuditService::created($route, "Created trip {$tripId} from template {$template->name}: {$route->origin} → {$route->destination}");

        return redirect()->route('operator.trips.index')
            ->with('success', "Trip {$tripId} created from template '{$template->name}' successfully!");
    }

    /**
     * Create multiple trips from a template (bulk/recurring).
     */
    public function createBulkTrips(Request $request, $id)
    {
        $operator = $this->getOperator();
        $template = RouteTemplate::where('operator_id', $operator->id)->findOrFail($id);

        $validated = $request->validate([
            'departure_time' => 'required|date_format:H:i',
            'arrival_time' => 'nullable|date_format:H:i',
            'bus_id' => 'required|exists:buses,id',
            'fare' => 'nullable|numeric|min:0',
            'days_of_week' => 'required|array',
            'days_of_week.*' => 'integer|between:0,6',
            'weeks' => 'required|integer|min:1|max:12',
            'start_date' => 'required|date|after_or_equal:today',
        ]);

        $bus = \App\Models\Bus::where('id', $validated['bus_id'])
            ->where('operator_id', $operator->id)
            ->first();

        if (!$bus) {
            return back()->withErrors(['bus_id' => 'The selected bus does not belong to your fleet.']);
        }

        $fare = $validated['fare'] ?? $template->base_fare;
        $created = 0;
        $errors = [];
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = $startDate->copy()->addWeeks($validated['weeks'])->subDay();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dayOfWeek = $date->dayOfWeek;
            // Convert Sunday from 0 to 7 for comparison
            $dayOfWeek = $dayOfWeek === 0 ? 7 : $dayOfWeek;
            $selectedDays = array_map(function($d) { return $d == 0 ? 7 : $d; }, $validated['days_of_week']);

            if (!in_array($dayOfWeek, $selectedDays)) {
                continue;
            }

            // Check for conflicts
            $conflict = Route::where('bus_id', $bus->id)
                ->where('travel_date', $date->toDateString())
                ->where('departure_time', $validated['departure_time'])
                ->where('is_active', true)
                ->exists();

            if ($conflict) {
                $errors[] = "Conflict on {$date->format('D d M')} - bus already scheduled.";
                continue;
            }

            try {
                Route::create([
                    'operator_id' => $operator->id,
                    'bus_id' => $validated['bus_id'],
                    'route_template_id' => $template->id,
                    'origin' => $template->origin,
                    'destination' => $template->destination,
                    'distance_km' => $template->distance_km,
                    'departure_time' => $validated['departure_time'],
                    'arrival_time' => $validated['arrival_time'] ?? null,
                    'fare' => $fare,
                    'travel_date' => $date->toDateString(),
                    'is_active' => true,
                ]);
                $created++;
            } catch (\Exception $e) {
                $errors[] = "Failed on {$date->format('D d M')}: {$e->getMessage()}";
            }
        }

        $message = "Successfully created {$created} trip(s) from template '{$template->name}'.";
        if (!empty($errors)) {
            $message .= " " . count($errors) . " error(s) occurred.";
        }

        OperatorAuditService::created($template, "Bulk created {$created} trips from template: {$template->name}");

        if (!empty($errors)) {
            return back()->with('success', $message)->with('bulk_errors', $errors);
        }

        return redirect()->route('operator.trips.index')->with('success', $message);
    }

    /**
     * Get templates as JSON (for AJAX).
     */
    public function getTemplatesJson()
    {
        $operator = $this->getOperator();
        if (!$operator) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $templates = RouteTemplate::where('operator_id', $operator->id)
            ->active()
            ->get()
            ->map(function ($t) {
                return [
                    'id' => $t->id,
                    'name' => $t->name,
                    'origin' => $t->origin,
                    'destination' => $t->destination,
                    'base_fare' => (float) $t->base_fare,
                    'distance_km' => (float) $t->distance_km,
                    'display' => $t->name . ' (' . $t->origin . ' → ' . $t->destination . ')',
                ];
            });

        return response()->json($templates);
    }
}