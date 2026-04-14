<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerManagementController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('customer-management.index', compact('customers'));
    }

    public function createBlob()
    {
        return view('customer-management.create-blob');
    }

    public function storeBlob(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'photo_data' => 'required|string',
            'photo_mime_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Decode base64 photo data
        $photoData = $request->input('photo_data');
        $photoData = preg_replace('/^data:image\/\w+;base64,/', '', $photoData);
        $photoBlob = base64_decode($photoData);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'customer',
            'photo_blob' => $photoBlob,
            'photo_mime_type' => $request->photo_mime_type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil ditambahkan!',
            'redirect' => route('customer-management.index'),
        ]);
    }

    public function createFile()
    {
        return view('customer-management.create-file');
    }

    public function storeFile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'photo_data' => 'required|string',
            'photo_mime_type' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Decode base64 photo data
        $photoData = $request->input('photo_data');
        $photoData = preg_replace('/^data:image\/\w+;base64,/', '', $photoData);
        $imageData = base64_decode($photoData);

        // Get mime type and extension
        $mimeType = $request->photo_mime_type;
        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        // Generate filename and save
        $filename = 'customer-'.Str::random(32).'.'.$extension;
        $path = 'customer-photos/'.$filename;

        Storage::disk('public')->put($path, $imageData);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'customer',
            'photo_path' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil ditambahkan!',
            'redirect' => route('customer-management.index'),
        ]);
    }

    public function getPhoto($id)
    {
        $user = User::findOrFail($id);

        if (! $user->photo_blob) {
            abort(404);
        }

        return response($user->photo_blob, 200)
            ->header('Content-Type', $user->photo_mime_type ?? 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
