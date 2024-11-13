<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;
use TheIconic\NameParser\Parser as NameParserParser;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ParseCv
{

    // Function to parse PDF files
    public function parsePDF($filePath)
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();
        //return $this->cleanText($text);
        return $text;
    }

    // Function to parse DOCX files
    public function parseDocs($filePath)
    {
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
        $text = '';
        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                    // Handle simple text elements
                    $text .= $element->getText() . ' ';
                } elseif ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                    // Handle TextRun elements
                    foreach ($element->getElements() as $childElement) {
                        if (method_exists($childElement, 'getText')) {
                            $text .= $childElement->getText() . ' ';
                        }
                    }
                }
            }
        }

        // Clean up the extracted text (optional)
        // return $this->cleanText($text);
        return $text;
    }


    public function parsePhotos($filePath)
    {


        $text = (new TesseractOCR($filePath))
            ->lang('eng')  // Use 'ara' for Arabic
            ->run();

        return $text;
    }

    // Helper function to clean text
    private function cleanText($text)
    {
        $text = preg_replace('/[^\\p{L}\\p{N}\\s]/u', '', $text); // Remove non-alphanumeric characters
        $text = strtolower($text); // Convert to lowercase
        return trim($text);
    }

    // Function to extract keywords from parsed text
    public function extractKeyWords($text, $keywords)
    {
        $foundKeywords = [];
        foreach ($keywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                $foundKeywords[] = $keyword;
            }
        }
        return $foundKeywords;
    }

    // Function to extract keywords from a job post (or similar text)
    public function extractKeywordFromJobPost($jobPostText, $keywords)
    {
        return $this->extractKeyWords($this->cleanText($jobPostText), $keywords);
    }

    // Function to match keywords between CV and job post
    public function MatchKeywords($cvKeywords, $jobPostKeywords)
    {
        return count(array_intersect($cvKeywords, $jobPostKeywords)) / count($jobPostKeywords) * 100;
    }

    // Function to extract contact information from text
    public function extractContactInfo($text)
    {
        $contactInfo = [];
        $text = preg_replace('/\\s+/', ' ', $text);
        $text = trim($text);
        //dd($text);
        preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/', $text, $emails);
        $contactInfo['emails'] = $emails[0] ?? [];

        preg_match_all('/\+?\d{1,3}?[-.\s]?\(?\d{2,4}\)?[-.\s]?\d{3,4}[-.\s]?\d{4,6}/', $text, $phoneNumbers);

        // Filter and clean up phone numbers
        $contactInfo['phone_numbers'] = array_map(function ($number) {
            $number = preg_replace('/\)\s*$/', '', $number);
            $number = preg_replace('/^\(+|[^()\d+\-\s.]+/', '', $number);
            $number = $this->cleanText($number);
            return trim($number);
        }, array_filter($phoneNumbers[0] ?? [], function ($number) {
            return !preg_match('/^\d{4}$/', $number);
        }));
        preg_match_all('/\b[A-Z][a-z]+(?:\s[A-Z][a-z]+)*\b/', $text, $names);
        $contactInfo['names'] = $names[0][0] ?? [];


        $namePart = implode('@', $emails[0]);
        // Replace common separators with spaces
        $namePart = str_replace(['.', '_', '-', '+', 'gmail', 'yahoo', 'com', '@', 'email'], ' ', $namePart);

        // Remove any numbers from the name part
        $namePart = preg_replace('/\d+/', '', $namePart);

        // Capitalize each word to make it look like a name
        $namePart = ucwords(trim($namePart));

        $contactInfo['names'] = $namePart ?? [];

        //$this->cleanText($phoneNumbers[0]);
        return $contactInfo;
    }
}
