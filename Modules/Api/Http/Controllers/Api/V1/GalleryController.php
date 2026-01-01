<?php

namespace Modules\Api\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Modules\Gallery\Entities\Album;
use Modules\Gallery\Entities\Media;
use Illuminate\Support\Facades\Storage;

class GalleryController extends BaseApiController
{
    // Albums
    public function index()
    {
        return $this->paginate(Album::query());
    }

    public function show($id)
    {
        $album = Album::with('media')->findOrFail($id);
        return $this->success($album);
    }

    // Media
    public function mediaIndex()
    {
        return $this->paginate(Media::query());
    }

    public function mediaShow($id)
    {
        $media = Media::findOrFail($id);
        return $this->success($media);
    }

    public function mediaStore(Request $request)
    {
        $request->validate([
            'file' => 'required|file|image|max:10240',
            'album_id' => 'nullable|exists:albums,id',
        ]);

        $path = $request->file('file')->store('gallery', 'public');

        $media = Media::create([
            'tenant_id' => auth()->user()->tenant_id ?? 1,
            'album_id' => $request->album_id,
            'file_path' => $path,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_type' => $request->file('file')->getClientMimeType(),
            'file_size' => $request->file('file')->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        return $this->success($media, 'Media uploaded successfully', 201);
    }

    public function mediaDestroy($id)
    {
        $media = Media::findOrFail($id);
        
        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }

        $media->delete();
        return $this->success([], 'Media deleted successfully');
    }
}
