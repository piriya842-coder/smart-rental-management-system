<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class RoomBrowseController extends Controller
{
    public function index(Request $request)
    {
        // ---------------------------
        // Inputs (student filters)
        // ---------------------------
        $location = trim((string) $request->get('location', ''));
        $type     = trim((string) $request->get('type', ''));
        $budget   = $request->get('budget', null);
        $gender   = strtolower(trim((string) $request->get('gender', '')));
        $sort     = strtolower(trim((string) $request->get('sort', 'latest')));

        // normalize gender
        if ($gender === 'any') $gender = '';
        if (!in_array($gender, ['', 'male', 'female'], true)) $gender = '';

        // normalize type
        $typeNorm = strtolower($type);

        $hasFilters = (
            $location !== '' ||
            $type !== '' ||
            ($budget !== null && $budget !== '') ||
            $gender !== '' ||
            ($sort !== '' && $sort !== 'latest')
        );

        // ---------------------------
        // Room model exists?
        // ---------------------------
        if (!class_exists(\App\Models\Room::class)) {
            return view('student.rooms.index', [
                'rooms' => collect(),
                'meta'  => $this->meta($request, $hasFilters, 0, 'Room model not found.'),
            ]);
        }

        $Room = \App\Models\Room::class;

        // ---------------------------
        // Base query
        // Show only published / active rooms
        // ---------------------------
        $base = $Room::query();

        if (Schema::hasColumn('rooms', 'status')) {
            $base->where('status', 'active');
        } elseif (Schema::hasColumn('rooms', 'is_published')) {
            $base->where('is_published', 1);
        }

        // Eager load safely
        try { if (method_exists($Room, 'images'))   $base->with('images'); } catch (\Throwable $e) {}
        try { if (method_exists($Room, 'landlord')) $base->with('landlord'); } catch (\Throwable $e) {}

        // Bookmark state for ❤️ icon
        $userId = auth()->id();
        if ($userId && method_exists($Room, 'savedByUsers')) {
            try {
                $base->withCount([
                    'savedByUsers as is_saved' => function ($q) use ($userId) {
                        $q->where('users.id', $userId);
                    }
                ]);
            } catch (\Throwable $e) {
                // ignore if relation/pivot missing
            }
        }

        $allRooms = $base->latest()->get()->values();

        // ---------------------------
        // Helpers (safe getters)
        // ---------------------------
        $getPrice = function ($r) {
            foreach (['price_monthly', 'price', 'monthly_rent', 'rent', 'amount'] as $c) {
                if (isset($r->$c) && $r->$c !== null && is_numeric($r->$c)) return (float) $r->$c;
            }
            return null;
        };

        $getType = function ($r) {
            foreach (['type', 'room_type'] as $c) {
                if (isset($r->$c) && $r->$c) return strtolower(trim((string) $r->$c));
            }
            return '';
        };

        $getLocationText = function ($r) {
            $parts = [];
            foreach (['address', 'city', 'area', 'state', 'postcode'] as $c) {
                if (isset($r->$c) && $r->$c) {
                    $parts[] = strtolower(trim((string) $r->$c));
                }
            }
            return trim(implode(' ', $parts));
        };

        $getGender = function ($r) {
            foreach (['gender_preference', 'gender', 'preferred_gender'] as $c) {
                if (isset($r->$c) && $r->$c) {
                    $g = strtolower(trim((string) $r->$c));
                    if (in_array($g, ['male', 'female', 'any'], true)) return $g;
                    return 'any';
                }
            }
            return 'any';
        };

        $getFacilities = function ($r) {
            $facilities = $r->facilities ?? [];
            if (is_string($facilities)) {
                $decoded = json_decode($facilities, true);
                $facilities = is_array($decoded) ? $decoded : [];
            }
            if (!is_array($facilities)) return [];

            return collect($facilities)
                ->map(fn($f) => strtolower(trim((string) $f)))
                ->filter()
                ->unique()
                ->values()
                ->all();
        };

        // Distance (optional)
        $msuLat = 3.0738;
        $msuLng = 101.4952;

        $getDistanceKm = function ($r) use ($msuLat, $msuLng) {
            foreach (['distance_km', 'distance_to_msu_km', 'msu_distance_km'] as $c) {
                if (isset($r->$c) && $r->$c !== null && is_numeric($r->$c)) return (float) $r->$c;
            }

            $lat = null; $lng = null;
            foreach (['latitude', 'lat'] as $c) if (isset($r->$c) && is_numeric($r->$c)) $lat = (float) $r->$c;
            foreach (['longitude', 'lng', 'lon'] as $c) if (isset($r->$c) && is_numeric($r->$c)) $lng = (float) $r->$c;

            if ($lat === null || $lng === null) return null;

            $toRad = fn($deg) => $deg * (M_PI / 180);
            $R = 6371;
            $dLat = $toRad($msuLat - $lat);
            $dLng = $toRad($msuLng - $lng);

            $a = sin($dLat / 2) * sin($dLat / 2) +
                cos($toRad($lat)) * cos($toRad($msuLat)) *
                sin($dLng / 2) * sin($dLng / 2);

            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            return $R * $c;
        };

        // ---------------------------
        // Build saved-room preference profile (V2)
        // ---------------------------
        $savedPrefs = [
            'has_data' => false,
            'avg_price' => null,
            'preferred_types' => [],
            'preferred_facilities' => [],
            'preferred_location_tokens' => [],
        ];

        if ($userId && auth()->user() && method_exists(auth()->user(), 'savedRooms')) {
            try {
                $savedRooms = auth()->user()->savedRooms()->get();

                if ($savedRooms->isNotEmpty()) {
                    $savedPrefs['has_data'] = true;

                    $savedPrices = $savedRooms
                        ->map(fn($r) => $getPrice($r))
                        ->filter(fn($v) => $v !== null)
                        ->values();

                    $savedPrefs['avg_price'] = $savedPrices->isNotEmpty()
                        ? round($savedPrices->avg(), 2)
                        : null;

                    $savedPrefs['preferred_types'] = $savedRooms
                        ->map(fn($r) => $getType($r))
                        ->filter()
                        ->countBy()
                        ->sortDesc()
                        ->keys()
                        ->take(3)
                        ->values()
                        ->all();

                    $savedPrefs['preferred_facilities'] = $savedRooms
                        ->flatMap(fn($r) => $getFacilities($r))
                        ->filter()
                        ->countBy()
                        ->sortDesc()
                        ->keys()
                        ->take(8)
                        ->values()
                        ->all();

                    $stopWords = ['jalan', 'sek', 'section', 'area', 'shah', 'alam', 'near', 'msu', 'room', 'unit', 'house', 'apartment', 'residence'];

                    $savedPrefs['preferred_location_tokens'] = $savedRooms
                        ->map(fn($r) => $getLocationText($r))
                        ->flatMap(function ($text) use ($stopWords) {
                            return collect(preg_split('/\s+/', strtolower($text)))
                                ->map(fn($t) => trim($t))
                                ->filter(function ($t) use ($stopWords) {
                                    return $t !== '' &&
                                        strlen($t) >= 3 &&
                                        !in_array($t, $stopWords, true);
                                });
                        })
                        ->countBy()
                        ->sortDesc()
                        ->keys()
                        ->take(6)
                        ->values()
                        ->all();
                }
            } catch (\Throwable $e) {
                // ignore saved-room preference extraction errors
            }
        }

        // ---------------------------
        // HARD RULE: Gender filter
        // female => allow female OR any
        // male   => allow male OR any
        // ---------------------------
        $genderHardFilter = function ($rooms) use ($gender, $getGender) {
            if ($gender === '') return $rooms;

            return $rooms->filter(function ($r) use ($gender, $getGender) {
                $rg = $getGender($r);
                return ($rg === 'any' || $rg === $gender);
            })->values();
        };

        // ---------------------------
        // STRICT filters
        // ---------------------------
        $budgetNum = null;
        if ($budget !== null && $budget !== '' && is_numeric($budget)) {
            $budgetNum = (float) $budget;
        }

        $applyStrict = function ($rooms, $useLocation, $useType, $budgetLimit) use ($location, $typeNorm, $getLocationText, $getType, $getPrice) {
            $out = $rooms;

            if ($useLocation && $location !== '') {
                $tokens = preg_split('/\s+/', strtolower($location));
                $out = $out->filter(function ($r) use ($tokens, $getLocationText) {
                    $hay = $getLocationText($r);
                    if ($hay === '') return false;

                    $hits = 0;
                    foreach ($tokens as $t) {
                        $t = trim($t);
                        if ($t !== '' && str_contains($hay, $t)) $hits++;
                    }
                    return $hits > 0;
                })->values();
            }

            if ($useType && $typeNorm !== '') {
                $out = $out->filter(function ($r) use ($typeNorm, $getType) {
                    return $getType($r) === $typeNorm;
                })->values();
            }

            if ($budgetLimit !== null) {
                $out = $out->filter(function ($r) use ($budgetLimit, $getPrice) {
                    $p = $getPrice($r);
                    if ($p === null) return false;
                    return $p <= $budgetLimit;
                })->values();
            }

            return $out;
        };

        // Start from all rooms, apply gender hard always
        $candidates = $genderHardFilter($allRooms);
        $note = null;

        // Phase 1: strict filter
        $final = $applyStrict($candidates, true, true, $budgetNum);

        // Relax rules step-by-step
        if ($final->isEmpty()) {
            $stepA = $applyStrict($candidates, false, true, $budgetNum);
            if ($stepA->isNotEmpty()) {
                $final = $stepA;
                $note = 'Showing best matches near MSU (gender, budget, and type respected).';
            } else {
                $stepB = $applyStrict($candidates, false, false, $budgetNum);
                if ($stepB->isNotEmpty()) {
                    $final = $stepB;
                    $note = 'Showing rooms within your budget (gender respected).';
                } else {
                    if ($budgetNum !== null) {
                        $stepC = $applyStrict($candidates, false, false, $budgetNum + 50);
                        if ($stepC->isNotEmpty()) {
                            $final = $stepC;
                            $note = 'Showing slightly above budget (+RM50).';
                        } else {
                            $stepD = $applyStrict($candidates, false, false, $budgetNum + 200);
                            if ($stepD->isNotEmpty()) {
                                $final = $stepD;
                                $note = 'Limited matches — showing wider budget range (+RM200).';
                            } else {
                                $final = collect();
                                $note = 'No matches found for your selected gender and budget range.';
                            }
                        }
                    } else {
                        $final = collect();
                        $note = 'No matches found. Try selecting a different room type or adding a budget.';
                    }
                }
            }
        }

        // ---------------------------
        // Recommendation V2: similarity-based scoring
        // ---------------------------
        $scoreRoom = function ($room) use (
            $location,
            $typeNorm,
            $budgetNum,
            $gender,
            $savedPrefs,
            $getPrice,
            $getType,
            $getLocationText,
            $getGender,
            $getDistanceKm,
            $getFacilities
        ) {
            $score = 0;

            $roomPrice = $getPrice($room);
            $roomType  = $getType($room);
            $roomLoc   = $getLocationText($room);
            $roomGen   = $getGender($room);
            $dist      = $getDistanceKm($room);
            $roomFacilities = $getFacilities($room);
            $isSaved = (int) ($room->is_saved ?? 0) > 0;

            // 1. Gender rule / preference similarity
            if ($gender !== '') {
                if ($roomGen === $gender) $score += 10;
                elseif ($roomGen === 'any') $score += 6;
                else $score -= 20;
            } else {
                $score += 1;
            }

            // 2. Budget similarity
            if ($budgetNum !== null && $roomPrice !== null) {
                if ($roomPrice <= $budgetNum) {
                    $score += 10;
                    $difference = $budgetNum - $roomPrice;
                    $score += max(0, 4 - ($difference / 150));
                } else {
                    $difference = $roomPrice - $budgetNum;
                    $score -= min(10, $difference / 50);
                }
            } else {
                $score += 1;
            }

            // 3. Type similarity
            if ($typeNorm !== '') {
                if ($roomType === $typeNorm) {
                    $score += 9;
                } else {
                    $score -= 2;
                }
            }

            // 4. Explicit location similarity
            if ($location !== '') {
                $tokens = preg_split('/\s+/', strtolower($location));
                $hits = 0;
                foreach ($tokens as $t) {
                    $t = trim($t);
                    if ($t !== '' && str_contains($roomLoc, $t)) $hits++;
                }
                if ($hits > 0) $score += min(6, 2 + $hits);
                else $score -= 2;
            }

            // 5. Distance similarity
            if ($dist !== null) {
                if ($dist <= 1.5) $score += 6;
                elseif ($dist <= 3) $score += 4;
                elseif ($dist <= 6) $score += 2;
                else $score -= 1;
            }

            // 6. Saved-room average price similarity
            if ($savedPrefs['avg_price'] !== null && $roomPrice !== null) {
                $diff = abs($roomPrice - $savedPrefs['avg_price']);
                if ($diff <= 50) $score += 6;
                elseif ($diff <= 150) $score += 4;
                elseif ($diff <= 300) $score += 2;
            }

            // 7. Saved-room preferred type similarity
            if (!empty($savedPrefs['preferred_types']) && in_array($roomType, $savedPrefs['preferred_types'], true)) {
                $score += 6;
            }

            // 8. Facilities similarity from saved rooms
            if (!empty($savedPrefs['preferred_facilities']) && !empty($roomFacilities)) {
                $matches = count(array_intersect($savedPrefs['preferred_facilities'], $roomFacilities));
                $score += min(8, $matches * 2);
            }

            // 9. Location pattern similarity from saved rooms
            if (!empty($savedPrefs['preferred_location_tokens'])) {
                $locMatches = 0;
                foreach ($savedPrefs['preferred_location_tokens'] as $token) {
                    if ($token !== '' && str_contains($roomLoc, $token)) {
                        $locMatches++;
                    }
                }
                $score += min(6, $locMatches * 2);
            }

            // 10. Already saved boost
            if ($isSaved) {
                $score += 5;
            }

            return round($score, 2);
        };

        $final = $final->map(function ($r) use ($scoreRoom) {
            $r->_score = $scoreRoom($r);
            return $r;
        });

        // ---------------------------
        // Sorting
        // ---------------------------
        if ($sort === 'recommend') {
            $final = $final->sortByDesc('_score')->values();

            if ($note === null) {
                $note = !empty($savedPrefs['has_data'])
                    ? 'Recommended for you based on your preferences and saved rooms.'
                    : 'Recommended for you based on your selected preferences.';
            }
        } elseif ($sort === 'price_low') {
            $final = $final->sortBy(function ($r) use ($getPrice) {
                $p = $getPrice($r);
                return $p === null ? 999999 : $p;
            })->values();
        } elseif ($sort === 'price_high') {
            $final = $final->sortByDesc(function ($r) use ($getPrice) {
                $p = $getPrice($r);
                return $p === null ? 0 : $p;
            })->values();
        } elseif ($sort === 'nearest') {
            $final = $final->sortBy(function ($r) use ($getDistanceKm) {
                $d = $getDistanceKm($r);
                return $d === null ? 999999 : $d;
            })->values();
        } else {
            $final = $final->sortByDesc('id')->values();
        }

        return view('student.rooms.index', [
            'rooms' => $final,
            'meta'  => $this->meta($request, $hasFilters, $final->count(), $note),
        ]);
    }

    public function show($room)
    {
        if (!class_exists(\App\Models\Room::class)) abort(404);

        $Room = \App\Models\Room::class;

        $room = $Room::query()
            ->with(['landlord', 'images'])
            ->findOrFail($room);

        return view('student.rooms.show', compact('room'));
    }

    private function meta(Request $request, bool $hasFilters, int $count, ?string $note)
    {
        return [
            'hasFilters' => $hasFilters,
            'count'      => $count,
            'note'       => $note,
            'params'     => [
                'location' => (string) $request->get('location', ''),
                'type'     => (string) $request->get('type', ''),
                'budget'   => (string) $request->get('budget', ''),
                'gender'   => (string) $request->get('gender', ''),
                'sort'     => (string) $request->get('sort', 'latest'),
            ],
        ];
    }
}