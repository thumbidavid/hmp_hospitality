<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RfpSubmission;
use App\Models\ContactSubmission;
use App\Models\NewsletterSubscriber;
use App\Models\Property;
use App\Models\Destination;
use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the Sales & Representation Command Center.
     */
    public function index(): Response
    {
        // 1. Capture the optional destination ID from the request query string
        $destinationId = request()->input('destination_id');

        // 2. Define a reusable scoping constraint for RFP queries
        $scopeByDestination = function ($query) use ($destinationId) {
            return $query->when($destinationId, function ($q, $destId) {
                return $q->where(function ($innerQuery) use ($destId) {
                    $innerQuery->where('destination_id', $destId)
                        ->orWhereHas('properties', function ($propertyQuery) use ($destId) {
                            $propertyQuery->where('destination_id', $destId);
                        });
                });
            });
        };

        // =====================================================================
        // 1. SALES PIPELINE METRICS (SCOPED)
        // =====================================================================

        $pipelineQuery = RfpSubmission::whereIn('status', ['new', 'contacted', 'quoted']);
        $pipelineQuery = $scopeByDestination($pipelineQuery);

        $pipelineValues = $pipelineQuery->selectRaw('currency, SUM(budget_amount) as total_budget')
            ->groupBy('currency')
            ->get()
            ->map(function ($item) {
                return [
                    'currency' => $item->currency,
                    'total' => number_format($item->total_budget, 2)
                ];
            })->toArray();

        // Lead Conversion Funnel Count (SCOPED)
        $funnelQuery = RfpSubmission::query();
        $funnelQuery = $scopeByDestination($funnelQuery);

        $funnelRaw = $funnelQuery->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $funnel = [
            'new' => $funnelRaw['new'] ?? 0,
            'contacted' => $funnelRaw['contacted'] ?? 0,
            'quoted' => $funnelRaw['quoted'] ?? 0,
            'won' => $funnelRaw['won'] ?? 0,
            'lost' => $funnelRaw['lost'] ?? 0,
            'total' => array_sum($funnelRaw)
        ];

        // Lead Volume Velocity (SCOPED)
        $velocityQuery = RfpSubmission::where('created_at', '>=', Carbon::now()->subDays(30));
        $velocityQuery = $scopeByDestination($velocityQuery);

        $velocity = $velocityQuery->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => Carbon::parse($item->date)->format('M d'),
                    'count' => $item->count
                ];
            })->toArray();

        // =====================================================================
        // 2. OPERATIONAL HEALTH (THE TO-DO LIST - SCOPED WHERE APPLICABLE)
        // =====================================================================

        $newRfpsQuery = RfpSubmission::where('status', 'new');
        $newRfpsQuery = $scopeByDestination($newRfpsQuery);
        $newRfpsCount = $newRfpsQuery->count();

        // These are global operational metrics and do not tie directly to property destinations
        $pendingContactsCount = ContactSubmission::where('status', 'new')->count();
        $newsletterCount = NewsletterSubscriber::where('is_active', true)->count();

        // =====================================================================
        // 3. REPRESENTATION POPULARITY & INVENTORY (SCOPED)
        // =====================================================================

        // Top 5 Shortlisted Properties (SCOPED)
        $topPropertiesQuery = DB::table('rfp_properties')
            ->join('properties', 'rfp_properties.property_id', '=', 'properties.id')
            ->select('properties.name', 'properties.city', DB::raw('COUNT(*) as shortlist_count'))
            ->groupBy('properties.id', 'properties.name', 'properties.city')
            ->orderByDesc('shortlist_count')
            ->take(5);

        if ($destinationId) {
            $topPropertiesQuery->where('properties.destination_id', $destinationId);
        }
        $topProperties = $topPropertiesQuery->get()->toArray();

        // Top 5 Inquired Parent Destinations (Left unfiltered to show overall trends)
        $topDestinations = DB::table('rfp_properties')
            ->join('properties', 'rfp_properties.property_id', '=', 'properties.id')
            ->join('destinations', 'properties.destination_id', '=', 'destinations.id')
            ->select('destinations.name', DB::raw('COUNT(*) as inquiry_count'))
            ->groupBy('destinations.id', 'destinations.name')
            ->orderByDesc('inquiry_count')
            ->take(5)
            ->get()
            ->toArray();

        // Network Inventory Counts
        $totalProperties = Property::where('is_active', true)->count();
        $totalDestinations = Destination::where('is_active', true)->count();

        // =====================================================================
        // 4. SEGMENT ANALYTICS (WHO IS BUYING - SCOPED)
        // =====================================================================

        // Leads by B2B Buyer Type (SCOPED)
        $leadsByBuyerQuery = DB::table('rfp_submissions')
            ->leftJoin('buyer_types', 'rfp_submissions.buyer_type_id', '=', 'buyer_types.id')
            ->select(DB::raw('COALESCE(buyer_types.name, "Unassigned") as buyer_name'), DB::raw('COUNT(*) as count'))
            ->groupBy('rfp_submissions.buyer_type_id', 'buyer_types.name');

        if ($destinationId) {
            $leadsByBuyerQuery->where(function ($q) use ($destinationId) {
                $q->where('rfp_submissions.destination_id', $destinationId)
                    ->orWhereExists(function ($pq) use ($destinationId) {
                        $pq->select(DB::raw(1))
                            ->from('rfp_properties')
                            ->join('properties', 'rfp_properties.property_id', '=', 'properties.id')
                            ->whereColumn('rfp_properties.rfp_id', 'rfp_submissions.id')
                            ->where('properties.destination_id', $destinationId);
                    });
            });
        }
        $leadsByBuyer = $leadsByBuyerQuery->get()->toArray();

        // Leads by Event Requirement Type (SCOPED)
        $leadsByReqQuery = RfpSubmission::query();
        $leadsByReqQuery = $scopeByDestination($leadsByReqQuery);
        $leadsByRequirement = $leadsByReqQuery->selectRaw('requirement_type, COUNT(*) as count')
            ->groupBy('requirement_type')
            ->get()
            ->map(function ($item) {
                return [
                    'label' => ucwords(str_replace('_', ' ', $item->requirement_type)),
                    'count' => $item->count
                ];
            })->toArray();

        // Recent RFP Submissions List (SCOPED)
        $recentRfpsQuery = RfpSubmission::orderBy('created_at', 'desc')->take(5);
        $recentRfpsQuery = $scopeByDestination($recentRfpsQuery);
        $recentRfps = $recentRfpsQuery->get()
            ->map(function ($rfp) {
                return [
                    'reference' => $rfp->reference_number,
                    'close_date' => $rfp->created_at->format('M d, Y'),
                    'user' => $rfp->email,
                    'amount' => number_format($rfp->budget_amount, 2) . ' ' . $rfp->currency,
                    'status' => ucfirst($rfp->status)
                ];
            })->toArray();

        // 5. Render to Admin Dashboard View
        return Inertia::render('Dashboard', [
            'metrics' => [
                'pipelineValues' => $pipelineValues,
                'funnel' => $funnel,
                'velocity' => $velocity,
                'operational' => [
                    'newRfps' => $newRfpsCount,
                    'pendingContacts' => $pendingContactsCount,
                    'audienceCount' => $newsletterCount
                ],
                'popularity' => [
                    'properties' => $topProperties,
                    'destinations' => $topDestinations,
                    'inventory' => [
                        'properties' => $totalProperties,
                        'destinations' => $totalDestinations
                    ]
                ],
                'segments' => [
                    'buyerTypes' => $leadsByBuyer,
                    'requirementTypes' => $leadsByRequirement
                ],
                'recent_rfps' => $recentRfps
            ],
            // Drive the dropdown filter with the active database destinations
            'ticketed_events' => Destination::orderBy('name')->get()
        ]);
    }
}
