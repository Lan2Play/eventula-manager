<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Provides model-level caching for Eloquent models.
 *
 * To use, add to your model:
 *
 *   use Cacheable;
 *   protected static string $cacheTag    = 'my-tag';   // tag for tag-capable drivers
 *   protected static string $cacheKeyPrefix = 'my-model'; // prefix for cache keys
 *
 * This gives you:
 *   - findCached(int $id): ?static
 *   - findManyCached(array $ids): array
 *   - Automatic invalidation on saved() / deleted() via bootCacheable()
 *
 * To invalidate extra keys on save/delete (e.g. slug-based keys), override:
 *   protected static function additionalCacheKeys(self $model): array
 */
trait Cacheable
{
    // -------------------------------------------------------------------------
    // Configuration helpers — override via static properties on the model
    // -------------------------------------------------------------------------

    protected static function cacheTag(): string
    {
        return static::$cacheTag ?? 'models';
    }

    protected static function cachePrefix(): string
    {
        return static::$cacheKeyPrefix ?? 'model';
    }

    /**
     * Return additional cache keys to forget on save/delete.
     * Override in the model for slug or other secondary-key caching.
     *
     * @return string[]
     */
    protected static function additionalCacheKeys(self $model): array
    {
        return [];
    }

    // -------------------------------------------------------------------------
    // Boot hook — auto-registered by Laravel via the boot{TraitName} convention
    // -------------------------------------------------------------------------

    protected static function bootCacheable(): void
    {
        $invalidate = function (self $model) {
            static::invalidateCachedModel($model);
        };

        static::saved($invalidate);
        static::deleted($invalidate);
    }

    public static function invalidateCachedModel(self $model): void
    {
        try {
            $id     = $model->id ?? null;
            $tag    = static::cacheTag();
            $prefix = static::cachePrefix();

            $keysToForget = [];

            if ($id) {
                $keysToForget[] = "{$prefix}:{$id}";
            }

            foreach (static::additionalCacheKeys($model) as $key) {
                $keysToForget[] = $key;
            }

            if (Cache::supportsTags()) {
                foreach ($keysToForget as $key) {
                    Cache::tags([$tag])->forget($key);
                }
            } else {
                foreach ($keysToForget as $key) {
                    Cache::forget($key);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Cache invalidation for ' . static::class . ' failed: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Cached finders
    // -------------------------------------------------------------------------

    public static function findCached(int $id): ?static
    {
        $tag    = static::cacheTag();
        $prefix = static::cachePrefix();
        $key    = "{$prefix}:{$id}";

        try {
            $remember = fn() => static::query()->find($id);

            if (Cache::supportsTags()) {
                return Cache::tags([$tag])->rememberForever($key, $remember);
            }

            return Cache::rememberForever($key, $remember);
        } catch (\Throwable $e) {
            Log::warning('Cache error while loading ' . static::class . ' #' . $id . ': ' . $e->getMessage());
            return static::query()->find($id);
        }
    }

    /**
     * Fetch multiple records by IDs with per-item caching.
     * Returns array<int, static|null> — null for IDs not found in DB (not cached).
     *
     * @param  int[] $ids
     * @return array<int, static|null>
     */
    public static function findManyCached(array $ids): array
    {
        $tag    = static::cacheTag();
        $prefix = static::cachePrefix();

        try {
            $ids = array_values(array_unique(
                array_map('intval', array_filter($ids, fn($v) => (int) $v > 0))
            ));

            if (!$ids) {
                return [];
            }

            $result  = [];
            $missing = [];

            if (Cache::supportsTags()) {
                foreach ($ids as $id) {
                    $cached = Cache::tags([$tag])->get("{$prefix}:{$id}");
                    if ($cached === null) {
                        $missing[] = $id;
                    } else {
                        $result[$id] = $cached;
                    }
                }
            } else {
                foreach ($ids as $id) {
                    $cached = Cache::get("{$prefix}:{$id}");
                    if ($cached === null) {
                        $missing[] = $id;
                    } else {
                        $result[$id] = $cached;
                    }
                }
            }

            if ($missing) {
                $fromDb = static::query()->whereIn('id', $missing)->get()->keyBy('id');

                foreach ($missing as $id) {
                    if (isset($fromDb[$id])) {
                        $model = $fromDb[$id];
                        if (Cache::supportsTags()) {
                            Cache::tags([$tag])->forever("{$prefix}:{$id}", $model);
                        } else {
                            Cache::forever("{$prefix}:{$id}", $model);
                        }
                        $result[$id] = $model;
                    } else {
                        $result[$id] = null; // not found — do not cache nulls
                    }
                }
            }

            return $result;
        } catch (\Throwable $e) {
            Log::warning('Cache error while loading multiple ' . static::class . ' records: ' . $e->getMessage());
            $coll = static::query()->whereIn('id', $ids)->get()->keyBy('id');
            $out  = [];
            foreach ($ids as $id) {
                $out[$id] = $coll->get($id, null);
            }
            return $out;
        }
    }
}
