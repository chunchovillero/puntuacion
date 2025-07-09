<?php
// Test to check if clubs exist in database and the API issue
require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Initialize the application
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Club;

echo "=== Database Club Test ===\n";

// Test 1: Direct count of all clubs
$totalClubs = Club::count();
echo "Total clubs in database: $totalClubs\n";

// Test 2: Direct query without any relationships
$clubs = Club::all();
echo "Clubs found via direct query: " . $clubs->count() . "\n";

// Test 3: Query with pilots count
$clubsWithPilots = Club::withCount('pilots')->get();
echo "Clubs with pilots count: " . $clubsWithPilots->count() . "\n";

// Test 4: Query with active pilots count
try {
    $clubsWithActivePilots = Club::withCount('activePilots')->get();
    echo "Clubs with active pilots count: " . $clubsWithActivePilots->count() . "\n";
} catch (Exception $e) {
    echo "Error with activePilots: " . $e->getMessage() . "\n";
}

// Test 5: Test the exact same query as in apiIndex
try {
    $query = Club::withCount(['pilots', 'activePilots']);
    $clubs = $query->orderBy('name')->paginate(10);
    echo "API query result - total: " . $clubs->total() . "\n";
    echo "API query result - count: " . $clubs->count() . "\n";
} catch (Exception $e) {
    echo "Error with API query: " . $e->getMessage() . "\n";
}

// Test 6: Show first club details if any
if ($totalClubs > 0) {
    $firstClub = Club::first();
    echo "\nFirst club details:\n";
    echo "ID: " . $firstClub->id . "\n";
    echo "Name: " . $firstClub->name . "\n";
    echo "Status: " . $firstClub->status . "\n";
    echo "Created: " . $firstClub->created_at . "\n";
}

echo "\n=== End Test ===\n";
