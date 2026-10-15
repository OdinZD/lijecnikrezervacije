<?php //62c49783ba3775cc9f9cbe3bd1cbd862faae8853c1d7b88dbb8da37baed42c3a
/** @noinspection all */

namespace LaravelIdea\Helper\App\Models {

    use App\Models\User;
    use Illuminate\Contracts\Database\Query\Expression;
    use Illuminate\Contracts\Support\Arrayable;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Pagination\Paginator;
    use Illuminate\Support\Collection;
    use LaravelIdea\Helper\_BaseBuilder;
    use LaravelIdea\Helper\_BaseCollection;

    /**
     * @method User getOrPut($key, \Closure $value)
     * @method User|$this shift(int $count = 1)
     * @method User firstOrFail(callable|string $key = null, $operator = null, $value = null)
     * @method User|$this pop(int $count = 1)
     * @method User|null pull(int|string $key, \Closure $default = null)
     * @method User|null last(callable|null $callback = null, \Closure $default = null)
     * @method User|$this random(callable|int|null $number = null, bool $preserveKeys = false)
     * @method User sole(callable|null|string $key = null, $operator = null, $value = null)
     * @method User|null get(int|null|string $key, \Closure $default = null)
     * @method User|null first(callable|null $callback = null, \Closure $default = null)
     * @method User|null firstWhere(callable|string $key, $operator = null, $value = null)
     * @method User|null find($key, $default = null)
     * @method User[] all()
     */
    class _IH_User_C extends _BaseCollection {
        /**
         * @param int $size
         * @param bool $preserveKeys
         * @return User[][]|Collection<_IH_User_C>
         */
        public function chunk($size, $preserveKeys = true)
        {
            return [];
        }
    }

    /**
     * @method _IH_User_QB whereId($value)
     * @method _IH_User_QB whereName($value)
     * @method _IH_User_QB whereEmail($value)
     * @method _IH_User_QB whereEmailVerifiedAt($value)
     * @method _IH_User_QB wherePassword($value)
     * @method _IH_User_QB whereRememberToken($value)
     * @method _IH_User_QB whereCreatedAt($value)
     * @method _IH_User_QB whereUpdatedAt($value)
     * @method User create(array $attributes = [])
     * @method User createOrFirst(array $attributes = [], array|\Closure $values = [])
     * @method User createQuietly(array $attributes = [])
     * @method _IH_User_C|User[] cursor()
     * @method User[] eagerLoadRelations(Model[]|array $models)
     * @method User|null|_IH_User_C|User[] find($id, array|string $columns = ['*'])
     * @method _IH_User_C|User[] findMany(array|Arrayable $ids, array|string $columns = ['*'])
     * @method User|_IH_User_C|User[] findOr($id, \Closure|string|string[] $columns = ['*'], \Closure|null $callback = null)
     * @method User|_IH_User_C|User[] findOrFail($id, array|string $columns = ['*'])
     * @method User|_IH_User_C|User[] findOrNew($id, array|string $columns = ['*'])
     * @method User findSole($id, array|string $columns = ['*'])
     * @method User|null first(array|string $columns = ['*'])
     * @method User firstOr(\Closure|string[] $columns = ['*'], \Closure|null $callback = null)
     * @method User firstOrCreate(array $attributes = [], array|\Closure $values = [])
     * @method User firstOrFail(array|string $columns = ['*'])
     * @method User firstOrNew(array $attributes = [], array|\Closure $values = [])
     * @method User|null firstWhere(array|\Closure|Expression|string $column, $operator = null, $value = null, string $boolean = 'and')
     * @method User forceCreate(array $attributes)
     * @method User forceCreateQuietly(array $attributes = [])
     * @method _IH_User_C|User[] fromQuery(string $query, array $bindings = [])
     * @method _IH_User_C|User[] get(array|string $columns = ['*'])
     * @method User getModel()
     * @method User[] getModels(array|string $columns = ['*'])
     * @method _IH_User_C|User[] hydrate(array $items)
     * @method User incrementOrCreate(array $attributes, string $column = 'count', float|int $default = 1, float|int $step = 1, array $extra = [])
     * @method _IH_User_C|User[] lazy(int $chunkSize = 1000)
     * @method _IH_User_C|User[] lazyById(int $chunkSize = 1000, null|string $column = null, null|string $alias = null)
     * @method _IH_User_C|User[] lazyByIdDesc(int $chunkSize = 1000, null|string $column = null, null|string $alias = null)
     * @method User make(array $attributes = [])
     * @method User newModelInstance(array $attributes = [])
     * @method LengthAwarePaginator|User[]|_IH_User_C paginate(\Closure|int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null, \Closure|int|null $total = null)
     * @method Paginator|User[]|_IH_User_C simplePaginate(int|null $perPage = null, array|string $columns = ['*'], string $pageName = 'page', int|null $page = null)
     * @method User sole(array|string $columns = ['*'])
     * @method User updateOrCreate(array $attributes, array|\Closure $values = [])
     */
    class _IH_User_QB extends _BaseBuilder {}
}
