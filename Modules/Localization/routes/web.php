<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Modules\Localization\Entities\Language;
use Modules\Localization\Entities\Translation;
use Modules\Localization\Services\AutoTranslateService;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'localization', 'middleware' => ['web', 'auth']], function () {
    Route::post('/switch', function (Request $request) {
        $request->validate(['locale' => 'required|string|size:2']); // or exists:languages,code
        
        $locale = $request->input('locale');
        
        // Update user preference
        $user = $request->user();
        if ($user) {
            $user->locale = $locale;
            $user->save();
        }
        
        // Update session
        session()->put('locale', $locale);
        
        return back();
    })->name('localization.switch');

    Route::get('/export/{locale}', function ($locale) {
        // Simple download route wrapper around logic similar to Export command
        $translations = Translation::where('locale', $locale)->get()->groupBy('group');
        $output = [];
        foreach ($translations as $group => $items) {
            foreach ($items as $item) {
                $output[$group][$item->key] = $item->value;
            }
        }
        
        $filename = "translations_{$locale}.json";
        return response()->streamDownload(function () use ($output) {
            echo json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }, $filename);
    })->name('localization.export');
});

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'api/localization', 'middleware' => ['api', 'auth:sanctum']], function () {
    
    Route::get('/languages', function () {
        return Language::where('is_active', true)->get();
    });

    Route::get('/translations/{locale}', function ($locale) {
        // Return translations for frontend consumption
        // Maybe we want nested structure: group.key
        $translations = Translation::where('locale', $locale)->get();
        $output = [];
        foreach ($translations as $t) {
            if (!isset($output[$t->group])) {
                $output[$t->group] = [];
            }
            $output[$t->group][$t->key] = $t->value;
        }
        return response()->json($output);
    });

    Route::post('/translate', function (Request $request) {
        $request->validate([
            'text' => 'required|string',
            'target_locale' => 'required|string',
            'source_locale' => 'nullable|string',
        ]);
        
        $service = app(AutoTranslateService::class);
        $result = $service->translate(
            $request->text,
            $request->target_locale,
            $request->source_locale ?? 'it'
        );
        
        return response()->json(['translation' => $result]);
    });
});

Route::put('/api/user/locale', function (Request $request) {
    $request->validate(['locale' => 'required|string|exists:languages,code']);
    
    $user = $request->user();
    $user->locale = $request->locale;
    $user->save();
    
    return response()->json(['message' => 'Locale updated']);
})->middleware(['api', 'auth:sanctum']);
