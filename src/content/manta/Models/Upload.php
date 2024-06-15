<?php

namespace Manta\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use setasign\Fpdi\Fpdi;
use Manta\Services\PdfToImage;
use Intervention\Image\ImageManager;

class Upload extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'created_by',
        'updated_by',
        'deleted_by',
        'user_id',
        'company_id',
        'locale',
        'sort',
        'main',
        'host',
        'model',
        'model_id',
        'disk',
        'url',
        'location',
        'filename',
        'filenameOriginal',
        'mime',
        'size',
        'extension',
        'title',
        'content',
        'seo_title',
        'private',
        'identifier',
        'image',
        'pdfLock',
        'error',
        'pages',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [];

    public function upload($file, $model, $model_id, array $options = [])
    {
        if (isset($options['replace']) && !isset($options['upload_id'])) {
            return false;
        }
        if (isset($options['upload_id'])) {
            $upload = Upload::find($options['upload_id']);
            if ($upload) {
                $extension = $upload->extension;
                $mime = $upload->mime;
                $size = $upload->size;
            }
        } else {
            $upload = null;
        }

        // Default disk en directory locatie instellen
        $disk = $options['disk'] ?? config('manta.upload.disk');

        $location = $options['location'] ?? 'uploads/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';

        // Map maken met specifieke rechten
        Storage::disk($disk)->makeDirectory($location, 0755, true, true); // Recursief en publiek

        if (is_string($file) && !is_object($file)) {
            // Controleer of de bestandsnaam in de opties is opgegeven, anders gebruik de oorspronkelijke bestandsnaam
            $filenameOriginal = isset($options['filename']) ? $options['filename'] : null;

            // Als vervangen is ingeschakeld en er een upload is, gebruik de bestaande gegevens
            if (isset($options['replace']) && $options['replace'] == 1 && $upload) {
                $disk = $upload->disk;
                $location = $upload->location;
                $filename = $upload->filename;
            } else {
                // Anders genereer een unieke bestandsnaam
                $filename = $this->uniqueFileName($filenameOriginal, $disk, $location, false);
            }

            // Controleer of het bestand met succes is opgeslagen
            if (isset($disk, $location, $filename) && Storage::disk($disk)->put($location . $filename, $file)) {
                if (Storage::disk($disk)->exists($location . $filename)) {
                    // Als het bestand bestaat, haal de MIME-type en grootte op
                    $extension = pathinfo($filenameOriginal, PATHINFO_EXTENSION);
                    $mime = Storage::disk($disk)->mimeType($location . $filename);
                    $size = Storage::disk($disk)->size($location . $filename);
                }
            }
        } elseif (is_object($file)) {
            $filenameOriginal = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $mime = $file->getMimeType();
            $size = $file->getSize();
            // Unieke bestandsnaam genereren
            $filename = $this->uniqueFileName($file->getClientOriginalName(), $disk, $location, true);
            // Bestand opslaan met specifieke rechten
            if (Storage::disk($disk)->putFileAs($location, $file, $filename, ['visibility' => 'public'])) {
                // Stel bestandsrechten in na het uploaden (leesbaar door anderen)
                Storage::disk($disk)->setVisibility($location . $filename, 'public');
            }
        }

        $values = [
            'model' => $model,
            'model_id' => $model_id,
            'disk' => $disk,
            'url' => env('APP_URL'),
            'location' => $location,
            'filename' => $filename,
            'filenameOriginal' => $filenameOriginal,
            'mime' => $mime,
            'size' => $size,
            'extension' => $extension,
            'image' => in_array($extension, ['jpg', 'jpeg', 'png']) ? 1 : 0,
        ];

        if (isset($options['replace']) && $options['replace'] == 1) {
            $values['updated_by'] = auth('staff')->user()->name;
            $upload->update($values);
        } else {
            $values['title'] = isset($options['main']) ? $options['main'] : $filenameOriginal;
            $values['seo_title'] = isset($options['main']) ? $options['main'] : $filenameOriginal;
            $values['content'] = isset($options['content']) ? $options['content'] : null;
            $values['private'] = isset($options['private']) ? $options['private'] : 0;
            $values['identifier'] = isset($options['identifier']) ? $options['identifier'] : null;
            $values['company_id'] = isset($options['company_id']) ? $options['company_id'] : 1;
            $values['locale'] = isset($options['locale']) ? $options['locale'] : config('manta.locale');
            $values['sort'] = isset($options['sort']) ? $options['sort'] : 0;
            $values['main'] = isset($options['main']) ? $options['main'] : 0;
            $values['host'] = isset($options['host']) ? $options['host'] : Request::getHost();
            $values['created_by'] = auth('staff')->user()->name;
            $values['user_id'] = auth('staff')->user()->id;
            $upload = upload::create($values);
        }

        if (in_array($upload->extension, ['jpg', 'jpeg', 'png'])) {
            foreach (config('manta.thumbnails') as $size) {
                $upload->resize($size);
            }
        }
        if (in_array($upload->extension, ['pdf'])) {
            $upload->pdfToPages();
        }
    }

    public function uniqueFileName($filename, string $disk, string $location, bool $timename = true): ?string
    {
        try {
            // Extract base name and extension
            $basename = pathinfo($filename, PATHINFO_FILENAME);
            $extension = pathinfo($filename, PATHINFO_EXTENSION);

            // Generate a safe and clean filename
            $basename = Str::slug(substr($basename, 0, 20));

            // Optionally prepend the current timestamp
            if ($timename) {
                $basename = time() . '-' . $basename;
            }

            // Generate the full file path
            $fullfile = $basename . '.' . $extension;

            // Check if the file exists and generate a new name if necessary
            if (Storage::disk($disk)->exists($location . $fullfile)) {
                $imageToken = substr(sha1(mt_rand()), 0, 5);
                $fullfile = $basename . '-' . $imageToken . '.' . $extension;
            }

            // Return the unique filename with path
            return $fullfile;
        } catch (\Exception $e) {
            // Log error details
            Log::error("Error generating a unique filename", [
                'class' => __CLASS__,
                'function' => __FUNCTION__,
                'line' => __LINE__,
                'location' => $location,
                'filename' => $filename,
                'exception' => $e->getMessage(),
                'user' => auth()->user() ? auth()->user()->toArray() : 'N/A'
            ]);

            return null;
        }
    }

    function fullPath(int $size = null, bool $check_exist = false): string
    {
        $baseLocation = $this->location;
        $thumbnailLocation = 'cache/thumbnails/';

        // Correctly format the location for public disk
        if ($this->disk == 'public') {
            $baseLocation = "/storage/" . $baseLocation;
        }

        $finalPath = $baseLocation . $this->filename;
        if ($size !== null) {
            $thumbnailPath = $baseLocation . $thumbnailLocation . $size . '/' . $this->filename;
            $fullUrl = $this->url . $thumbnailPath;

            // Check if the specific thumbnail exists
            if ($check_exist && !file_exists($fullUrl)) {
                // Resize only if the specified size thumbnail doesn't exist
                $this->resize($size);
            }
            $finalPath = $thumbnailPath;
        }

        return $this->url . $finalPath;
    }

    public function resize(int $width = 400, int $height = null): void
    {
        // Check if both width and height are null and throw exception if needed
        if ($width === null && $height === null) {
            throw new \InvalidArgumentException("Both width and height cannot be null.");
        }

        // Use Null-coalescing-operator to set default width
        $width = $width ?? 400;

        // Fetch the image from storage
        try {
            $imagePath = $this->location . $this->filename;
            $imageContent = Storage::disk($this->disk)->get($imagePath);
            $image = ImageManager::imagick()->read($imageContent)->resize($width, $height, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Construct the thumbnail directory path
            $folderSize = $width ?: $height;
            $thumbnailDirectory = $this->location . 'cache/thumbnails/' . $folderSize . '/';
            $thumbnailPath = $thumbnailDirectory . $this->filename;

            // dd($this->extension, (string)$image->encode());

            // Make directory if it doesn't exist
            if (!Storage::disk($this->disk)->exists($thumbnailPath)) {
                Storage::disk($this->disk)->makeDirectory($thumbnailDirectory, 0755, true);
                Storage::disk($this->disk)->put($thumbnailPath, (string)$image->encode());
            }
        } catch (\Exception $e) {
            Log::error('Error resizing image: ' . $e->getMessage());
            // Optionally: throw $e; // Rethrow the exception if needed
        }
    }

    public function pdfToPages(): void
    {
        if ($this->pdfLock == 0 && $this->extension == 'pdf' && Storage::disk($this->disk)->exists($this->location . $this->filename)) {
            $this->pdfLock = 1;
            $this->save();
            Storage::disk('local')->put("/pdf_temp/" . $this->id . "/" . $this->filename, Storage::disk($this->disk)->get($this->location . $this->filename));
            $temp_location = Storage::disk('local')->path("/pdf_temp/" . $this->id . "/" . $this->filename);
            /**
             * * Try to read PDF
             */
            try {
                $pdfi = new Fpdi();
                $pdfi->setSourceFile($temp_location);
            } catch (\Exception $e) {
                $this->error = $e->getMessage();
                $this->save();
            }
            /**
             * * Try to create thumbnails
             */
            try {
                $pdf = new PdfToImage($temp_location);
                $pdf->setCompressionQuality(60);
                $pdf->setOutputFormat('jpg');
                $pdf->setColorspace(1);
                $this->pages = $pdf->getNumberOfPages();
                foreach (range(1, $this->pages) as $pageNumber) {
                    $tempPath = storage_path("app/pdf_temp/" . $this->id . "/") . "page" . $pageNumber . ".jpg";
                    $pdf->setPage($pageNumber)
                        // ->setOutputFormat('jpg')
                        ->saveImage($tempPath);
                    Storage::disk($this->disk)->put($this->location . "/cache/pdf/" . $this->id . "/page{$pageNumber}.jpg", file_get_contents($tempPath), 'public');
                }
                Storage::disk('local')->deleteDirectory("pdf_temp/" . $this->id . "/");
                Storage::disk('local')->deleteDirectory("pdf_temp" . $this->location . "../");
                $this->pages = $pdf->getNumberOfPages();
                $this->save();
            } catch (\Exception $e) {
                $this->error = $e->getMessage();
                $this->save();
            }
        }
    }

    public function pdfGetImages()
    {
        return Storage::disk($this->disk)->allFiles($this->location . "/cache/pdf/" . $this->id);
    }

    /**
     * @param string $size
     * @return int
     */
    public function parseSize(string $size): int
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }

    /**
     * @param int $decimals
     * @return string
     */
    function convertFilesize(int $decimals = 2): string
    {
        $bytes = $this->size;
        $size = array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public function getImage($size = null): array
    {
        $return     = ['src' => false, 'url' => false];
        $location   = $this->location;

        if ($this->filename && in_array($this->extension, ['jpg', 'jpeg', 'png', 'svg'])) {
            if (!Storage::disk($this->disk)->exists($location . $this->filename) && $size == null) {
                return $return; // Ensure an array is returned
            } elseif (!Storage::disk($this->disk)->exists($location . "cache/thumbnails/{$size}/" . $this->filename) && $size != null) {
                $this->resize($size);
            }
            if ($size != null) {
                $location = $location . "cache/thumbnails/{$size}/";
            }
            if ($this->disk == 'public') {
                $location = "/storage/" . $location;
            }

            $return['src'] = env('APP_URL') .  $location . $this->filename;
            $return['url'] = env('APP_URL')  .  $location . $this->filename;
        } elseif (count($this->pdfGetImages()) > 0) {
            $location = '';
            if ($this->disk == 'public') {
                $location = "/storage/";
            }
            $return['src'] = $location . $this->pdfGetImages()[0];
            $return['url'] = $location . $this->pdfGetImages()[0];
        } else {
            $return['src'] = null;
            $return['url'] = null;
        }
        return $return;
    }


    /** @return string  */
    public function getIcon(): string
    {
        if (in_array($this->extension, ['xls', 'xlsx'])) {
            return 'fa-solid fa-file-excel';
        } elseif (in_array($this->extension, ['doc', 'docx'])) {
            return 'fa-solid fa-file-word';
        } elseif (in_array($this->extension, ['jpg', 'jpeg', 'png', 'svg', 'gif', 'tiff', 'bmp'])) {
            return 'fa-solid fa-image';
        } elseif ($this->extension == 'pdf') {
            return 'fa-solid fa-file-pdf';
        } else {
            return 'fa-solid fa-file';
        }
    }

    public function file_upload_max_size(): float
    {
        static $max_size = -1;

        if ($max_size >= 0) {
            // Return cached max size if it's already calculated
            return $max_size;
        }

        // Fetch the maximum sizes from ini settings
        $post_max_size = $this->parseSize(ini_get('post_max_size'));
        $upload_max_filesize = $this->parseSize(ini_get('upload_max_filesize'));

        // Determine the effective max size
        if ($post_max_size == 0) {
            $max_size = $upload_max_filesize;  // post_max_size is unlimited
        } elseif ($upload_max_filesize == 0) {
            $max_size = $post_max_size;  // upload_max_filesize is unlimited
        } else {
            $max_size = min($post_max_size, $upload_max_filesize);  // Neither is unlimited, take the smaller
        }

        return $max_size;
    }
}
