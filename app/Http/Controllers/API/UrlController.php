<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\UrlResource;
use App\Models\Url;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UrlController extends Controller
{
    public function insertLongUrl(Request $request)
    {
        return $this->checkLongUrlAlreadyExists($request->url);
    }

    public function checkLongUrlAlreadyExists($longUrl): JsonResponse
    {
        $foundLongUrl = Url::where('long_url', $longUrl)->first();

        if (!$foundLongUrl) { // if no long url found
            $shortCode = $this->generateShortCode();

            while($this->checkShortCodeExists($shortCode)) {
                $shortCode = $this->generateShortCode();
            }

            $shortUrl = "https://yourdomain.com/" . $shortCode;

            $data = Url::create([
                'user_id' => Auth::user()->id,
                'long_url' => $longUrl,
                'short_url' => $shortCode
            ]);

            if ($data) {
                return response()->json([
                    'success' => true,
                    'message' => 'Got the short url successfully',
                    'short_url' => $shortUrl,
                    'long_url' => $longUrl
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong!'
                ], 400);
            }
        } else { // long url already exists in DB
            $data = Url::where('long_url', $longUrl)->first();

            if ($data) {
                return response()->json([
                    'success' => true,
                    'message' => 'Got the short url successfully',
                    'short_url' => "https://yourdomain.com/" . $data['short_url'],
                    'long_url' => $longUrl
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong!'
                ], 400);
            }
        }
    }

    // Function to generate a short code
    public function generateShortCode($length = 6): string
    {
        return substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $length);
    }

    public function checkShortCodeExists($shortCode): bool
    {
        $shortUrl = Url::where('short_url', $shortCode)->first();
        if ($shortUrl) {
            return true;
        } else {
            return false;
        }
    }

    public function getUrlsByUser(User $user)
    {
        if (Auth::user()->id !== $user->id ) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized!'
            ], 403);
        }

        $urlsByUser = Url::where('user_id', $user->id)->get();
        if (count($urlsByUser) !== 0) {
            return response()->json([
                'success' => true,
                'message' => 'Fetched the urls successfully',
                'data' => UrlResource::collection($urlsByUser)
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'No data found!'
            ]);
        }
    }
}
