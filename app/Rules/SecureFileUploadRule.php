<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class SecureFileUploadRule implements Rule
{
    protected $maxFileSize;

    protected $mimes;

    protected $errorMessage = ':attribute tidak valid atau mengandung konten berbahaya.';

    public function __construct($mimes = [], $maxFileSize = 5)
    {
        $this->maxFileSize = $maxFileSize;
        $this->mimes = $mimes;
    }

    public function passes($attribute, $value)
    {
        if (! ($value instanceof \Illuminate\Http\UploadedFile)) {
            // Bisa dianggap valid, karena bukan file baru yang diupload
            return true;
        }

        if (! $value->isValid()) {
            $this->errorMessage = ':attribute gagal diupload.';

            return false;
        }

        // ✅ 1. Cek ekstensi
        $extension = strtolower($value->getClientOriginalExtension());
        if (! in_array($extension, $this->mimes) && count($this->mimes) > 0) {
            $this->errorMessage = ':attribute harus berupa file dengan format '.implode(', ', $this->mimes).'.';

            return false;
        }

        // ✅ 2. Cek ukuran (dalam MB)
        $maxBytes = $this->maxFileSize * 1024 * 1024;
        if ($value->getSize() > $maxBytes) {
            $this->errorMessage = ':attribute tidak boleh lebih dari '.$this->maxFileSize.' MB.';

            return false;
        }

        // ✅ 3. Validasi keamanan konten
        $mimeType = strtolower($value->getMimeType());
        $allowedExtensions = [
            // 📄 Dokumen
            'application/pdf' => ['pdf'],
            'application/msword' => ['doc'],
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
            'application/vnd.ms-excel' => ['xls'],
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => ['xlsx'],
            'application/vnd.ms-powerpoint' => ['ppt'],
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => ['pptx'],
            'text/plain' => ['txt', 'log', 'text'],
            'text/csv' => ['csv'],
            'application/rtf' => ['rtf'],
            'application/vnd.oasis.opendocument.text' => ['odt'],
            'application/vnd.oasis.opendocument.spreadsheet' => ['ods'],
            'application/vnd.oasis.opendocument.presentation' => ['odp'],

            // 🖼️ Gambar
            'image/jpeg' => ['jpg', 'jpeg', 'jfif'],
            'image/png' => ['png'],
            'image/gif' => ['gif'],
            'image/bmp' => ['bmp'],
            'image/webp' => ['webp'],
            'image/svg+xml' => ['svg'],
            'image/tiff' => ['tif', 'tiff'],
            'image/x-icon' => ['ico'],

            // 🎵 Audio
            'audio/mpeg' => ['mp3'],
            'audio/wav' => ['wav'],
            'audio/ogg' => ['ogg'],
            'audio/x-aac' => ['aac'],
            'audio/flac' => ['flac'],
            'audio/mp4' => ['m4a'],
            'audio/webm' => ['weba'],

            // 🎬 Video
            'video/mp4' => ['mp4'],
            'video/x-msvideo' => ['avi'],
            'video/x-ms-wmv' => ['wmv'],
            'video/mpeg' => ['mpg', 'mpeg'],
            'video/webm' => ['webm'],
            'video/3gpp' => ['3gp'],
            'video/quicktime' => ['mov'],

            // 📦 Arsip & Kompresi
            'application/zip' => ['zip'],
            'application/x-7z-compressed' => ['7z'],
            'application/x-rar-compressed' => ['rar'],
            'application/gzip' => ['gz'],
            'application/x-tar' => ['tar'],

            // 💻 Kode / JSON / XML
            'application/json' => ['json'],
            'application/xml' => ['xml'],
            'text/xml' => ['xml'],
            'text/html' => ['html', 'htm'],
            'text/css' => ['css'],
            'text/javascript' => ['js'],
        ];

        if (! isset($allowedExtensions[$mimeType]) || ! in_array($extension, $allowedExtensions[$mimeType])) {
            $this->errorMessage = ':attribute tidak valid atau tidak sesuai tipe MIME.';

            return false;
        }

        $content = file_get_contents($value->getRealPath());
        $commonPatterns = [
            '/<\s*(script|iframe|embed|object|svg|link|style|meta)\b[^>]*>/i',
            '/on\w+\s*=\s*(["\']).*?\1/i',
            '/javascript\s*:/i',
            '/data\s*:[^;]+;base64/i',
            '/<\?php/i',
            '/\b(eval|base64_decode|shell_exec|system|passthru|exec)\s*\(/i',
            '/\$_(GET|POST|REQUEST|SERVER|COOKIE|FILES)\b/i',
        ];

        foreach ($commonPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                $this->errorMessage = ':attribute mengandung skrip berbahaya.';

                return false;
            }
        }

        if ($extension === 'pdf' && strpos($content, '%PDF-') === 0) {
            $pdfPatterns = [
                '/\/(JS|JavaScript|AA|OpenAction|RichMedia)\s/',
            ];

            foreach ($pdfPatterns as $pattern) {
                if (preg_match($pattern, $content)) {
                    $this->errorMessage = ':attribute (PDF) mengandung elemen JavaScript berbahaya.';

                    return false;
                }
            }
        }

        return true;
    }

    public function message()
    {
        return $this->errorMessage;
    }
}
