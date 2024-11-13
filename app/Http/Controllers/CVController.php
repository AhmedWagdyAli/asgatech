<?php

namespace App\Http\Controllers;

use App\Jobs\BatchCvParserJob;
use Illuminate\Http\Request;
use App\Services\ParseCv;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CVController extends Controller
{
    protected $parseCvService;

    public function __construct(ParseCv $parseCvService)
    {
        $this->parseCvService = $parseCvService;
    }

    public function uploadCv(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'cv' => 'required|file|mimes:pdf,docx,png|max:2048', // Adjust max size as needed
            'keywords' => 'array',
            'keywords.*' => 'required_if:keywords'
        ]);

        $file = $request->file('cv');
        $filePath = $file->getPathname();
        $name = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        // Parse the file based on its type
        if ($extension == 'pdf') {
            $text = $this->parseCvService->parsePDF($filePath);
        } elseif ($extension == 'docx') {
            $text = $this->parseCvService->parseDocs($filePath);
        } elseif ($extension == ('png' || 'jpeg' || 'PNG')) {
            $text = $this->parseCvService->parsePhotos($filePath);
        } else {
            return response()->json(['error' => 'Unsupported file format'], 400);
        }
        $contactInfo = $this->parseCvService->extractContactInfo($text, $name);

        $keywords = implode(',', $request->input('keywords'));
        $keywords_array = explode(',', $keywords);
        $foundKeywords = $this->parseCvService->extractKeyWords($text, $keywords_array);
        $match = $this->parseCvService->MatchKeywords($foundKeywords, $keywords_array);
        return response()->json(['contact' => $contactInfo, 'match' => $match . '%']);
    }

    public function cvForm()
    {
        return view('cvform');
    }

    public function batchCVForm()
    {
        return view('batch_cvs');
    }

    public function dispatchBatchCvJob(Request $request): JsonResponse
    {
        $jobName = $request->input('job_title');
        $files = $request->file('cvs');
        $cvPaths = [];
        foreach ($files as $file) {
            $path = $file->store('cvs');
            $cvPaths[] = $path;
        }
        $file = Storage::url($cvPaths[0]);
        //$url = Storage::url('file.jpg');


        dispatch(new BatchCvParserJob($cvPaths, $jobName, $request->input('keywords')))->onQueue('default');

        return response()->json(['message' => 'Job dispatched successfully']);
    }
}
