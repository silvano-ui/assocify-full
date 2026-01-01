<?php

namespace Modules\Api\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Modules\Documents\Entities\Document;
use Modules\Documents\Entities\DocumentCategory;
use Illuminate\Support\Facades\Storage;

class DocumentsController extends BaseApiController
{
    public function index()
    {
        return $this->paginate(Document::query());
    }

    public function show($id)
    {
        $document = Document::findOrFail($id);
        return $this->success($document);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:10240', // 10MB max
            'document_category_id' => 'nullable|exists:document_categories,id',
        ]);

        $path = $request->file('file')->store('documents', 'public');

        $document = Document::create([
            'tenant_id' => auth()->user()->tenant_id ?? 1,
            'title' => $request->title,
            'file_path' => $path,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_type' => $request->file('file')->getClientMimeType(),
            'file_size' => $request->file('file')->getSize(),
            'document_category_id' => $request->document_category_id,
            'uploaded_by' => auth()->id(),
        ]);

        return $this->success($document, 'Document uploaded successfully', 201);
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        
        // Optional: Delete file from storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();
        return $this->success([], 'Document deleted successfully');
    }

    public function download($id)
    {
        $document = Document::findOrFail($id);
        
        if (!Storage::disk('public')->exists($document->file_path)) {
            return $this->error('File not found', 404);
        }

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function categories()
    {
        $categories = DocumentCategory::all();
        return $this->success($categories);
    }
}
