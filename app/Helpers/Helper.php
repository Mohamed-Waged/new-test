<?php

namespace App\Helpers;

use App\Mail\ResetMailable;
use App\Mail\ForgetMailable;
use App\Mail\VerifyMailable;
use App\Services\FCMService;
use App\Mail\WelcomeMailable;
use App\Constants\GlobalConstants;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Helper
{
    /**
     * Format the date to the specified format.
     *
     * @param string $date
     * @return string
     */
    public static function formatDate(string $date): string
    {
        return date(GlobalConstants::DEFAULT_DATE_FORMAT, strtotime($date));
    }

    /**
     * @param string $file
     * @return string
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function getFileExtensionType($file): string
    {
        if (self::getFileExtension($file) == 'mp4')
            return 'video';
        else if (self::getFileExtension($file) == 'pdf')
            return 'pdf';
        else
            return 'image';
    }

    /**
     * @param string $file
     * @return string|null
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function getFileExtension($file): ?string
    {
        return pathinfo(parse_url($file, PHP_URL_PATH), PATHINFO_EXTENSION) ?? NULL;
    }

    /**
     * @param string $status
     * @return bool
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function isActiveStatus($status): bool
    {
        return strtolower($status) === 'active';
    }

    /**
     * @param mixed $query
     * @param mixed $sortBy
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function applySorting($query, $sortBy): void
    {
        $key    = $sortBy[0]['key'];
        $value  = $sortBy[0]['order'];
        if ($key === 'title') {
            $query->orderByTranslation('title', $value);
        } else {
            $query->orderBy($key, $value);
        }
    }

    /**
     * @param object $rows
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function paginate($rows): array
    {
        $paginate = $rows->toArray();
        unset($paginate['data'], $paginate['links']);

        return $paginate;
    }

    /**
     * @param string $status
     * @return int
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function getStatusId($status): int
    {
        return (strtolower($status) == 'active')
            ? 1
            : (
                (strtolower($status) == 'pending')
                ? 2
                : 0
            );
    }

    /**
     * @param int $statusId
     * @return string
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function getStatusKey($statusId): string
    {
        return ($statusId == 1)
            ? 'Active'
            : (
                ($statusId == 2)
                ? 'Pending'
                : 'Inactive'
            );
    }

    /**
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function getAvailableAppIds(): array
    {
        return DB::table('lecturers')->whereNULL('deleted_at')->whereIsActive(true)->pluck('app_id')->toArray() ?? [];
    }

    /**
     * @param string $slug
     * @return ?int
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function getIdBySettingSlug($slug): ?int
    {
        return DB::table('settings')->where('slug', $slug)->first()->id ?? NULL;
    }

    /**
     * @return bool
     * @author Mohamed Elfeky <mohamed.elfeky@gatetechs.com>
     */
    public static function isMobileDevice(): bool
    {
        return request()->header('mobileDevice') === 'true' ?? false;
    }

    /**
     * @param object $user
     * @param string template
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function sendEmailTemplate($user, $template): void
    {
        if (env('APP_ENV') === 'production') {
            try {
                switch ($template) {
                    case 'verify':
                        Mail::to($user->email)->send(new VerifyMailable($user));
                        break;
                    case 'welcome':
                        Mail::to($user->email)->send(new WelcomeMailable($user));
                        break;
                    case 'forget':
                        Mail::to($user->email)->send(new ForgetMailable($user));
                        break;
                    case 'reset':
                        Mail::to($user->email)->send(new ResetMailable($user));
                        break;
                    default:
                        break;
                }
            } catch (Exception $e) {
                Log::warning($e);
            }
        }
    }

    /**
     * @param object $user
     * @param string $title
     * @param string|null $body
     * @return void
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function sendPushNotification($user, $title, $body = null): void
    {
        if (env('APP_ENV') === 'production') {
            try {
                if (!empty($user->fcm_token)) {
                    FCMService::send(
                        $user->fcm_token,
                        [
                            'title' => $title,
                            'body' => $body
                        ]
                    );
                }
            } catch (Exception $e) {
                Log::warning($e);
            }
        }
    }
}
