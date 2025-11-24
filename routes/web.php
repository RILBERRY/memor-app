<?php

use App\Http\Controllers\PostGenerateDataController;
use App\Http\Middleware\IsUsersPost;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Models\Category;
use App\Models\PostGenerateData;


    Route::get('/', [PostGenerateDataController::class, 'home'])->name('home');

    Route::post('/create-post-new', [PostGenerateDataController::class, 'storeAndCreateNew'])->name('create-post-new');



Route::middleware(['auth', IsUsersPost::class])->group(function () {
    Route::get('/generate-post/{id}', [PostGenerateDataController::class, 'generateFromSearchImg'])->name('create-post');
    Route::get('/dashboard', [PostGenerateDataController::class, 'dashboard'])->name('dashboard');
});

Route::middleware(['auth'])->group(function () {

    Route::post('/create-post', [PostGenerateDataController::class, 'storeAndCreate'])->name('create-post');

    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});


Route::post('/webhook/receiver', function (Request $request) {

    // Make sure the folder exists
    $folder = public_path('zooarea');
    logger()->info("file");
    if (!file_exists($folder)) {
        mkdir($folder, 0777, true);
    }

    // Check if image/video exists in the request
    if ($request->hasFile('file')) {
        
logger()->info("have file");
        $file = $request->file('file');

        // Generate unique name
        $filename = time() . '_' . $file->getClientOriginalName();

        // Save to /public/zooarea/
        $file->move($folder, $filename);

        $params = [
            'celebration_title' => 'test',
            'message' => null,
            'url_slug' => null,
            'post_path' =>  'zooarea/' . $filename,
            'published_at' => null,
        ];

       $post = PostGenerateData::create($params);
      logger()->info("file save". $post->id);

        $token = Category::where('name', 'token')->first();
        $chid = $token->custom_img_path['chid'];
        $botToken = $token->custom_img_path['test'];

         Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => "lock added",
        ]);

        return response()->json([
            'status' => 'received'
        ]);
        

    }

    return response()->json([
        'status' => 'no file received'
    ], 400);
});




require __DIR__.'/auth.php';
