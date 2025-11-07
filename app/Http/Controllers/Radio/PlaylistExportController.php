<?php
// app/Http/Controllers/Radio/PlaylistExportController.php

namespace App\Http\Controllers\Radio;

use App\Http\Controllers\Controller;
use App\Models\Playlist;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PlaylistExportController extends Controller
{
    /** Write one row as UTF-8 (no BOM), ; delimiter, CRLF line ending. */
    private function fputcsvUtf8($handle, array $fields, string $delimiter = ';'): void
    {
        // Keep data EXACTLY as in DB — do not normalize or transliterate
        fputcsv($handle, $fields, $delimiter, '', "\\"); // Removed quotes
        fwrite($handle, "\r\n"); // force CRLF
    }

    /** CSV: UTF-8 (no BOM), ; delimiter, CRLF */
    public function csv(Playlist $playlist)
    {
        $playlist->load(['items.track' => fn($q) => $q->select('*')]);

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$this->sanitizeFilename($this->safeName($playlist, 'csv')).'"',
        ];

        return response()->stream(function () use ($playlist) {
            $out = fopen('php://output', 'wb');

            // DO NOT write BOM for this app (BOM can confuse some CSV parsers)
            $this->fputcsvUtf8($out, ['MEDIANAME','TITLE']);

            foreach ($playlist->items as $it) {
                $t = $it->track;
                $media = str_replace(["\r","\n"], ' ', (string)$t->filename);
                $title = str_replace(["\r","\n"], ' ', (string)($t->title ?? ''));
                $this->fputcsvUtf8($out, [$media, $title]);
            }

            fclose($out);
        }, 200, $headers);
    }

    /** TXT: UTF-8 (no BOM), tab-delimited, CRLF */
    public function txt(Playlist $playlist)
    {
        $playlist->load(['items.track' => fn($q) => $q->select('*')]);

        $headers = [
            'Content-Type'        => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$this->sanitizeFilename($this->safeName($playlist, 'txt')).'"',
        ];

        return response()->stream(function () use ($playlist) {
            $out = fopen('php://output', 'wb');

            fwrite($out, "MEDIANAME\tTITLE\r\n");
            foreach ($playlist->items as $it) {
                $t = $it->track;
                $media = str_replace(["\r","\n"], ' ', (string)$t->filename);
                $title = str_replace(["\r","\n"], ' ', (string)($t->title ?? ''));
                fwrite($out, "{$media}\t{$title}\r\n");
            }
            fclose($out);
        }, 200, $headers);
    }

    /** XLSX is already Unicode; no change needed */
    public function xlsx(Playlist $playlist)
    {
        $playlist->load(['items.track' => fn($q) => $q->select('*')]);

        $sheet = (new Spreadsheet())->getActiveSheet();
        $sheet->setCellValue('A1', 'MEDIANAME');
        $sheet->setCellValue('B1', 'TITLE');

        $r = 2;
        foreach ($playlist->items as $it) {
            $t = $it->track;
            $sheet->setCellValue("A{$r}", (string)$t->filename);
            $sheet->setCellValue("B{$r}", (string)($t->title ?? ''));
            $r++;
        }

        $writer = new Xlsx($sheet->getParent());
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $this->sanitizeFilename($this->safeName($playlist, 'xlsx')), [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /** Send to XPlayout as CSV: UTF-8 (no BOM), ; delimiter, CRLF */
    public function sendToXPlayout(Playlist $playlist, Request $request, string $format = 'csv')
    {
        $request->validate(['when' => ['required','date']]);

        $dt   = \Carbon\Carbon::parse($request->input('when'), config('app.timezone'));
        $hhmm = $dt->format('Hi');
        $ymd  = $dt->format('Y_m_d');

        $base = rtrim((string) config('radio.xplayout_auto_path'), "\\/");
        // Basic path hardening: require a real, absolute path, and disallow traversal
        if ($base === '' || strpos($base, '..') !== false) {
            return back()->with('error', 'Invalid export base path.');
        }
        $realBase = realpath($base);
        if ($realBase === false || !is_dir($realBase)) {
            return back()->with('error', 'Export base path not available.');
        }
        $dir  = $realBase . DIRECTORY_SEPARATOR . $ymd;
        if (!is_dir($dir) && !@mkdir($dir, 0777, true)) {
            return back()->with('error', "Cannot create folder: {$dir}");
        }

        $ext     = ($format === 'xlsx') ? 'xlsx' : 'csv';
        $outFile = $dir . DIRECTORY_SEPARATOR . "{$hhmm}.xsc.{$ext}";

        $playlist->load(['items.track' => fn($q) => $q->select('*')]);

        if ($ext === 'xlsx') {
            $sheet = (new Spreadsheet())->getActiveSheet();
            $sheet->setCellValue('A1', 'MEDIANAME');
            $sheet->setCellValue('B1', 'TITLE');
            $r = 2;
            foreach ($playlist->items as $it) {
                $t = $it->track;
                $sheet->setCellValue("A{$r}", (string)$t->filename);
                $sheet->setCellValue("B{$r}", (string)($t->title ?? ''));
                $r++;
            }
            (new Xlsx($sheet->getParent()))->save($outFile);
        } else {
            $fh = @fopen($outFile, 'wb');
            if (!$fh) return back()->with('error', "Cannot write file: {$outFile}");

            $this->fputcsvUtf8($fh, ['MEDIANAME','TITLE']);
            foreach ($playlist->items as $it) {
                $t = $it->track;
                $media = str_replace(["\r","\n"], ' ', (string)$t->filename);
                $title = str_replace(["\r","\n"], ' ', (string)($t->title ?? ''));
                $this->fputcsvUtf8($fh, [$media, $title]);
            }
            fclose($fh);
        }

        return back()->with('success', "Sent to XPlayout: {$outFile}");
    }

    private function safeName(Playlist $playlist, string $ext): string
    {
        return ($playlist->name ?: 'playlist').'.'.$ext;
    }

    private function sanitizeFilename(string $name): string
    {
        // Remove CR/LF, quotes, control chars that can break headers or filesystems
        $name = preg_replace('/[\r\n\x00-\x1F\x7F"\\\/]+/u', ' ', (string)$name);
        $name = trim($name);
        if ($name === '') $name = 'download';
        return $name;
    }
}
