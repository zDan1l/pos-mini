<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // For PostgreSQL BYTEA, we need to use hex encoding
        if (DB::getConnection()->getDriverName() === 'pgsql') {
            // Use hex encode for PostgreSQL BYTEA
            $hexBlob = bin2hex($photoBlob);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 'customer',
                'photo_blob' => DB::raw("DECODE('" . $hexBlob . "', 'hex')"),
                'photo_mime_type' => $request->photo_mime_type,
            ]);
        } else {
            // For MySQL/MariaDB
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => 'customer',
                'photo_blob' => $photoBlob,
                'photo_mime_type' => $request->photo_mime_type,
            ]);
        }

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

        // For PostgreSQL BYTEA, get raw binary data
        $photoData = $user->photo_blob;

        // If using PostgreSQL and data is in hex format, decode it
        if (DB::getConnection()->getDriverName() === 'pgsql' && is_string($photoData) && strlen($photoData) > 0) {
            // PostgreSQL BYTEA data should already be decoded by PDO
            // But if we have issues, we can use raw query
            if (strpos($photoData, '\x') === 0) {
                // Data is in hex format from PostgreSQL
                $photoData = hexdec(substr($photoData, 2));
            }
        }

        return response($photoData, 200)
            ->header('Content-Type', $user->photo_mime_type ?? 'image/jpeg')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    public function edit($id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);
        return view('customer-management.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$customer->id,
            'password' => 'nullable|min:6',
            'photo_data' => 'nullable|string',
            'photo_mime_type' => 'nullable|string',
            'remove_photo' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update basic info
        $customer->name = $request->name;
        $customer->email = $request->email;

        if ($request->filled('password')) {
            $customer->password = bcrypt($request->password);
        }

        // Handle photo removal
        if ($request->boolean('remove_photo')) {
            // Delete file if exists
            if ($customer->photo_path) {
                Storage::disk('public')->delete($customer->photo_path);
            }
            $customer->photo_path = null;
            $customer->photo_blob = null;
            $customer->photo_mime_type = null;
        }
        // Handle new photo
        elseif ($request->filled('photo_data')) {
            $photoData = $request->input('photo_data');
            $photoData = preg_replace('/^data:image\/\w+;base64,/', '', $photoData);
            $imageData = base64_decode($photoData);

            // Delete old photo file if exists
            if ($customer->photo_path) {
                Storage::disk('public')->delete($customer->photo_path);
            }

            // Determine storage type based on existing data or default to file
            if ($customer->photo_blob !== null) {
                // Update as BLOB (customer was using BLOB storage)
                if (DB::getConnection()->getDriverName() === 'pgsql') {
                    $hexBlob = bin2hex($imageData);
                    $customer->photo_blob = DB::raw("DECODE('" . $hexBlob . "', 'hex')");
                } else {
                    $customer->photo_blob = $imageData;
                }
                $customer->photo_mime_type = $request->photo_mime_type;
                $customer->photo_path = null;
            } else {
                // Update as file (default or customer was using file storage)
                $mimeType = $request->photo_mime_type;
                $extension = match ($mimeType) {
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp',
                    default => 'jpg',
                };

                $filename = 'customer-'.Str::random(32).'.'.$extension;
                $path = 'customer-photos/'.$filename;

                Storage::disk('public')->put($path, $imageData);

                $customer->photo_path = $path;
                $customer->photo_blob = null;
                $customer->photo_mime_type = null;
            }
        }

        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil diperbarui!',
            'redirect' => route('customer-management.index'),
        ]);
    }

    public function destroy($id)
    {
        $customer = User::where('role', 'customer')->findOrFail($id);

        // Delete photo file if exists
        if ($customer->photo_path) {
            Storage::disk('public')->delete($customer->photo_path);
        }

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer berhasil dihapus!',
        ]);
    }
}
