<?php

namespace Modules\Books\Repositories;

use File;
use Excel;
use Helper;
use Exception;
use Carbon\Carbon;
use App\Models\Imageable;
use Illuminate\Support\Str;
use Modules\Books\Entities\Book;
use App\Constants\GlobalConstants;
use App\Repositories\BaseRepository;
use Modules\Books\Exports\BookExport;
use Modules\Roles\Entities\Permission;
use Illuminate\Database\Eloquent\Builder;
use Modules\Books\Http\Resources\BookResource;
use Modules\Books\Http\Resources\BookMobileResource;
use Modules\Books\Http\Resources\BookKeyValueResource;
use Modules\Books\Repositories\Contracts\BooksRepositoryInterface;

class BooksRepository extends BaseRepository implements BooksRepositoryInterface
{
    protected $model;

    /**
     * @param Book $model
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function __construct(Book $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function index($request): array
    {
        $rows = $this->buildQuery($request)->paginate($request['paginate'] ?? GlobalConstants::DEFAULT_PAGINATE);

        return [
            'rows'          => BookResource::collection($rows),
            'paginate'      => Helper::paginate($rows),
            'permissions'   => Permission::getAccessPermissions('books')
        ];
    }

    /**
     * @param  mixed $request
     * @return Builder
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function buildQuery($request): Builder
    {
        return $this->model
            ->whereNull('deleted_at')
            ->whereHas('translations')
            ->when(!empty($request['search']), function ($query) use ($request) {
                $query->whereTranslationLike('title', '%' . $request['search'] . '%');
            })
            ->when(!empty($request['date']), function ($query) use ($request) {
                $query->whereDate('created_at', $request['date']);
            })
            ->when(!empty($request['status']), function ($query) use ($request) {
                $query->whereIsActive(Helper::isActiveStatus($request['status']));
            }, function($else) {
                $else->whereIsActive(true);
            })
            ->when(!empty($request['sortBy']), function ($query) use ($request) {
                Helper::applySorting($query, $request['sortBy']);
            }, function ($else) {
                $else->latest('id');
            });
    }

    /**
     * @param array $request
     * @param string|null $id
     * @return mixed
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function createOrUpdate($request, $id = NULL): mixed
    {
        try {

            $row                    = (!empty($id)) ? $this->model::find(decrypt($id)) : new $this->model;

            // translations
            foreach (['en', 'ar'] as $locale) {
                $row->translateOrNew($locale)->title = $request[$locale]['title'];
                $row->translateOrNew($locale)->body  = $request[$locale]['body'] ?? NULL;
            }

            $row->lecturer_id       = $request['lecturer']['id'];
            $row->book_type_id      = $request['book_type']['id'];
            $row->slug              = Str::slug($request['en']['title'], '-');
            $row->price             = $request['price'];
            $row->pages_no          = (int)$request['pages_no'];
            $row->published_at      = $request['published_at'];
            $row->sort              = (int)$request['sort'];
            $row->is_active         = Helper::getStatusId($request['status']);
            $row->save();

            // images
            $row->image()->delete();
            if (!empty($request['imageBase64'])) {
                if (!Str::contains($request['imageBase64'], [Imageable::contains()])) {
                    $image = Imageable::uploadImage($request['imageBase64'], 'books');
                } else {
                    $image = explode('/', $request['imageBase64']);
                    $image = end($image);
                }
                $row->image()->create(['url' => $image]);
            }

            // files
            // $row->files()->delete();
            // if (!empty($request['filesBase64']) && is_array($request['filesBase64'])) {
            //     $filesBase64 = [];
            //     foreach ($request['filesBase64'] as $fileBase64) {
            //         $filesBase64 = [
            //             'book_id'   => $row->id,
            //             'url'       => Imageable::uploadImage($fileBase64, 'books')
            //         ];
            //     }
            //     $row->files()->insert($filesBase64);
            // }

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param mixed $book
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function find($book): array
    {
        $row = $this->model
            ->whereNULL('deleted_at')
            ->whereIsActive(true)
            ->whereHas('translations')
            ->when(!empty($book), function ($query) use ($book) {
                if (is_numeric($book)) {
                    $query->whereId($book);
                } else {
                    $query->whereSlug($book);
                    $query->orWhere('id', decrypt($book));
                }
            })
            ->first();

        return [
            'row' => Helper::isMobileDevice()
                ? new BookMobileResource($row)
                : new BookResource($row)
        ];
    }

    /**
     * @param string $id
     * @return mixed
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function destroy($id): mixed
    {
        try {
            $destroy = $this->model->find(decrypt($id));

            if ($destroy) {
                // Mark the book as deleted by appending a deleted-at-timestamp to the slug
                $destroy->slug      .= '-deleted-at-' . Carbon::now()->timestamp;
                $destroy->deleted_by = auth('api')->user()->id;
                $destroy->deleted_at = Carbon::now();
                $destroy->save();
            }

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param array $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function export($request): array
    {
        $fileName   = 'books-' . now()->format('Y-m-d-H-i-s') . '.xlsx';
        $directory  = GlobalConstants::EXCEL_DIRECTORY_PATH;
        $path       = $directory . '/' . $fileName;

        try {
            // Ensure the directory exists
            if (!File::isDirectory(public_path($directory)))
                File::makeDirectory(public_path($directory), 0777, true, true);

            // Optionally delete old files or handle file versioning
            File::deleteDirectory(public_path($directory));

            // Store the export
            Excel::store(new BookExport($request), $path, 'public');

            return [
                'status'    => true,
                'path'      => request()->root() . '/' . $path,
            ];
        } catch (Exception $e) {
            return [
                'status'    => false,
                'message'   => $e->getMessage()
            ];
        }
    }

    /**
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function keyValue(): array
    {
        $rows = $this->model
            ->whereNULL('deleted_at')
            ->whereIsActive(true)
            ->whereHas('translations')
            ->latest('id')
            ->get();

        return [
            'rows' => BookKeyValueResource::collection($rows)
        ];
    }

    /**
     * @param array $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function list($request): array
    {
        $rows = $this->buildQuery($request)->paginate($request['paginate'] ?? GlobalConstants::DEFAULT_PAGINATE);

        return [
            'rows'      => BookMobileResource::collection($rows),
            'paginate'  => Helper::paginate($rows)
        ];
    }
}
