<?php

namespace Voerro\Laravel\VisitorTracker\Controllers;

use Voerro\Laravel\VisitorTracker\Models\Visit;
use Carbon\Carbon;
use Voerro\Laravel\VisitorTracker\Facades\VisitStats;

class StatisticsController
{
    protected function viewSettings()
    {
        return [
            'visitortrackerLayout' => config('visitortracker.layout'),
            'visitortrackerSectionContent' => config('visitortracker.section_content'),
            'datetimeFormat' => config('visitortracker.datetime_format'),
        ];
    }

    public function summary()
    {
        $visits24h = VisitStats::query()->visits()->where('created_at', '>=', Carbon::now()->subHours(24));
        $visits1w = VisitStats::query()->visits()->where('created_at', '>=', Carbon::now()->subDays(7));
        $visits1m = VisitStats::query()->visits()->where('created_at', '>=', Carbon::now()->subMonth(1));
        $visits1y = VisitStats::query()->visits()->where('created_at', '>=', Carbon::now()->subYears(1));

        return view('visitstats::summary', array_merge([
            'lastVisits' => VisitStats::query()
                ->visits()
                ->withUsers()
                ->latest()
                ->paginate(10),

            'visits24h' => $visits24h->count(),
            'unique24h' => $visits24h->unique()->count(),

            'visits1w' => $visits1w->count(),
            'unique1w' => $visits1w->unique()->count(),

            'visits1m' => $visits1m->count(),
            'unique1m' => $visits1m->unique()->count(),

            'visits1y' => $visits1y->count(),
            'unique1y' => $visits1y->unique()->count(),

            'visitsTotal' => Visit::count(),
            'uniqueTotal' => VisitStats::query()->visits()->unique()->count(),
        ], $this->viewSettings()));
    }
}