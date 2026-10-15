<?php //95040336e1a0bbc181fb181d5445aa12d4b551fdfb01c7674e8857a50372ce67
/** @noinspection all */

namespace Illuminate\Notifications {

    use Illuminate\Support\Collection;
    use LaravelIdea\Helper\_BaseCollection;

    /**
     * @method DatabaseNotification getOrPut($key, \Closure $value)
     * @method DatabaseNotification|$this shift(int $count = 1)
     * @method DatabaseNotification firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method DatabaseNotification|$this pop(int $count = 1)
     * @method DatabaseNotification|null pull(int|string $key, \Closure $default = null)
     * @method DatabaseNotification|null last(callable|null $callback = null, \Closure $default = null)
     * @method DatabaseNotification|$this random(callable|int|null $number = null, bool $preserveKeys = false)
     * @method DatabaseNotification sole(callable|null|string $key = null, $operator = null, $value = null)
     * @method DatabaseNotification|null get(int|null|string $key, \Closure $default = null)
     * @method DatabaseNotification|null first(callable|null $callback = null, \Closure $default = null)
     * @method DatabaseNotification|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method DatabaseNotification|null find($key, $default = null)
     * @method DatabaseNotification[] all()
     */
    class DatabaseNotificationCollection extends _BaseCollection {
        /**
         * @param int $size
         * @param bool $preserveKeys
         * @return DatabaseNotification[][]|Collection<DatabaseNotificationCollection>
         */
        public function chunk($size, $preserveKeys = true)
        {
            return [];
        }
    }
}
