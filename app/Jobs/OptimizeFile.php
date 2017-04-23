<?php

namespace App\Jobs;

use App\File;
use App\Helpers\StatisticsUpdater;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OptimizeFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $file;

    /**
     * Create a new job instance.
     *
     * @param \App\File $file File to optimize
     * @return void
     */
    public function __construct(File $file) {
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {

        Log::info('Optimizing file "'.$this->file->code.'", '.$this->file->size.' bytes');

        $imgPath = storage_path('app/data/'.$this->file->location);
        //Log::debug('Path: ' . $imgPath);

        switch ($this->file->mimetype) {

            // PNG image
            case 'image/png':
                if (config('upload.optimize.png.optipng_path', '') == '') {
                    // No optipng path set
                    Log::error('No optipng path set! Check the configuration file');
                    return;
                }
                // Run optimization
                $command = config('upload.optimize.png.optipng_path') . ' ' . config('upload.optimize.png.optipng_flags') . ' ' . $imgPath;
                //Log::debug('Command: "'.$command.'"');
                exec($command);
                break;

            // JPEG image
            case 'image/jpeg':
                if (config('upload.optimize.jpeg.jpegoptim_path', '') == '') {
                    // No jpegoptim path set
                    Log::error('No jpegoptim path set! Check the configuration file');
                    return;
                }
                // Run optimization
                $command = config('upload.optimize.jpeg.jpegoptim_path') . ' ' . config('upload.optimize.jpeg.jpegoptim_flags') . ' ' . $imgPath;
                //Log::debug('Command: "'.$command.'"');
                exec($command);
                break;

            default:
                Log::warning('Unoptimizable filetype "'.$this->file->mimetype.'"');
                return;
        }

        // Update CRC
        $crc = hash_file('crc32b', $imgPath);
        $this->file->crc = $crc;

        // Get file size after optimization
        $sizeNow = Storage::disk('upload')->size($this->file->location);
        $this->file->size_optimized = $sizeNow;
        $this->file->save();

        $savings = $this->file->size - $sizeNow;
        $savingPercent = round(($savings / $this->file->size) * 100, 2);

        Log::info('File "'.$this->file->code.'" optimized, '.$savings.' bytes saved ('.$savingPercent.'%)');

        StatisticsUpdater::addTotalOptimizedFiles();
    }
}
