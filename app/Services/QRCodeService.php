<?php

namespace App\Services;

use Endroid\QrCode\QrCode as EndroidQrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\Storage;

class QRCodeService
{
    protected PngWriter $pngWriter;

    protected SvgWriter $svgWriter;

    public function __construct()
    {
        $this->pngWriter = new PngWriter();
        $this->svgWriter = new SvgWriter();
    }

    /**
     * Generate QR Code as base64 data URI for direct display in browser
     */
    public function generateAsBase64(string $content, int $size = 300): string
    {
        $qrCode = new EndroidQrCode($content);
        $result = $this->pngWriter->write($qrCode);

        return 'data:image/png;base64,'.base64_encode($result->getString());
    }

    /**
     * Generate QR Code as SVG
     */
    public function generateAsSvg(string $content, int $size = 300): string
    {
        $qrCode = new EndroidQrCode($content);
        $result = $this->svgWriter->write($qrCode);

        return $result->getString();
    }

    /**
     * Generate QR Code and save to storage
     */
    public function generateAndSave(string $content, string $filename = null): array
    {
        $qrCode = new EndroidQrCode($content);
        $result = $this->pngWriter->write($qrCode);

        if (! $filename) {
            $filename = 'qrcode-'.md5($content).'.png';
        }

        $path = 'qrcodes/'.$filename;
        Storage::disk('public')->put($path, $result->getString());

        return [
            'path' => $path,
            'url' => Storage::url($path),
        ];
    }

    /**
     * Generate QR Code as raw PNG data (for direct response)
     */
    public function generateAsRawPng(string $content, int $size = 300): string
    {
        $qrCode = new EndroidQrCode($content);
        $result = $this->pngWriter->write($qrCode);

        return $result->getString();
    }
}
