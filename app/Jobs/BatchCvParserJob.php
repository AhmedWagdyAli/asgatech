<?php

namespace App\Jobs;

use App\Models\parsedCV;
use App\Services\ParseCv;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class BatchCvParserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $files;
    public $jobName;
    public $keywords;

    public function __construct(array $files, string $jobName, string $keywords)
    {
        $this->files = is_array($files) ? $files : $files->toArray();
        $this->keywords = $keywords;
        $this->jobName = $jobName;
    }
    /**
     * Execute the job.
     *
     * @param \App\Services\ParseCv $parseCv
     * @return void
     */
    public function handle()
    {


        foreach ($this->files as $file) {
            $filePath = Storage::url($file);

            $parseCv =  new ParseCv();
            $text = $parseCv->parsePDF($filePath);
            $contactInfo = $parseCv->extractContactInfo($text);

            $keywords_array = explode(',', $this->keywords);
            $foundKeywords = $parseCv->extractKeyWords($text, $keywords_array);
            $match = $parseCv->MatchKeywords($foundKeywords, $keywords_array);
            // Parse the file based on its type
            /*             if ($extension == 'pdf') {
            } elseif ($extension == 'docx') {
                $text = $parseCv->parseDocs($filePath);
            } elseif ($extension == ('png' || 'jpeg' || 'PNG')) {
                $text = $parseCv->parsePhotos($filePath);
            } */


            parsedCV::create([
                'job_title' => $this->jobName,
                'match_rating' => $match,
                'name' => $contactInfo['names'],
                'email' => $contactInfo['email'][0],
                'phone_number' => $contactInfo['phone_numbers'][0],
            ]);
        }
    }
}
