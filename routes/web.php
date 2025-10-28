<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\MonthlyTargetController;
use App\Http\Controllers\VAController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\CommentController;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    } else {
        return redirect()->route('login');
    }
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // Video Streaming Routes
    Route::resource('videos', VideoController::class);
    Route::post('/videos/{video}/like', [VideoController::class, 'toggleLike'])->name('videos.like');
    Route::get('/videos/category/{category}', [VideoController::class, 'category'])->name('videos.category');
    Route::get('/videos/history', [VideoController::class, 'history'])->name('videos.history');
    Route::get('/videos/recommended', [VideoController::class, 'recommended'])->name('videos.recommended');

    // Playlists
    Route::resource('playlists', PlaylistController::class);
    Route::post('/playlists/{playlist}/add-video', [PlaylistController::class, 'addVideo'])->name('playlists.add-video');
    Route::delete('/playlists/{playlist}/remove-video/{video}', [PlaylistController::class, 'removeVideo'])->name('playlists.remove-video');

    // Comments
    Route::resource('comments', CommentController::class)->only(['store', 'update', 'destroy']);
    Route::get('/videos/{video}/comments', [CommentController::class, 'getComments'])->name('videos.comments');

    // Schedules
    Route::resource('schedules', ScheduleController::class);
    Route::get('/schedules-calendar', [ScheduleController::class, 'calendar'])->name('schedules.calendar');
    Route::get('/schedules/{id}/ajax', [ScheduleController::class, 'showAjax'])->name('schedules.ajax');

    // Financial (Transactions)
    Route::resource('financial', TransactionController::class);
    Route::get('/financial/export/{format}', [TransactionController::class, 'export'])->name('financial.export');
    Route::get('/transactions/{id}/ajax', [TransactionController::class, 'showAjax'])->name('transactions.ajax');

    // Monthly Targets
    Route::resource('targets', MonthlyTargetController::class);
    Route::get('/targets/{id}/ajax', [MonthlyTargetController::class, 'showAjax'])->name('targets.ajax');

    // Virtual Assistant
    Route::get('/va/recommendations', [VAController::class, 'recommendations'])->name('va.recommendations');
    Route::post('/va/generate-recommendations', [VAController::class, 'generateRecommendations'])->name('va.generate');
    Route::get('/va/recommendations/ajax', [VAController::class, 'getRecommendationsAjax'])->name('va.recommendations.ajax');

    // Chatbot
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/history', [ChatController::class, 'getHistory'])->name('chat.history');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/recent', [NotificationController::class, 'getRecent'])->name('notifications.recent');

    // Reports (to be implemented)
    Route::get('/reports/monthly/{year}/{month}', [TransactionController::class, 'monthlyReport'])->name('reports.monthly');
    Route::get('/reports/export/pdf', [TransactionController::class, 'exportPdf'])->name('reports.export.pdf');
    Route::get('/reports/export/csv', [TransactionController::class, 'exportCsv'])->name('reports.export.csv');
});

require __DIR__.'/auth.php';

