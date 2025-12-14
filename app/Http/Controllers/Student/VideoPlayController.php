<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VideoPlayHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VideoPlayController extends Controller
{
    /**
     * Track video play.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function track(Request $request)
    {
        Log::info('Video tracking request received', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        try {
            $request->validate([
                'resource_id' => 'required|exists:resources,id',
                'watch_duration' => 'required|integer|min:0',
                'completed' => 'required|boolean'
            ]);

            $history = VideoPlayHistory::create([
                'user_id' => Auth::id(),
                'resource_id' => $request->resource_id,
                'played_at' => now(),
                'watch_duration' => $request->watch_duration,
                'completed' => $request->completed
            ]);

            Log::info('Video tracking record created', [
                'history_id' => $history->id,
                'user_id' => $history->user_id,
                'resource_id' => $history->resource_id,
                'watch_duration' => $history->watch_duration,
                'completed' => $history->completed
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Video play tracked successfully',
                'data' => $history
            ]);
        } catch (\Exception $e) {
            Log::error('Error tracking video', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error tracking video: ' . $e->getMessage()
            ], 500);
        }
    }
}
