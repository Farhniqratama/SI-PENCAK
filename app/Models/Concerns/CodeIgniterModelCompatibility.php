<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

trait CodeIgniterModelCompatibility
{
    protected ?Builder $ciBuilder = null;
    protected array $ciSetData = [];
    protected ?int $ciLastInsertId = null;
    public $pager = null;

    protected function ciQuery(): Builder
    {
        return $this->ciBuilder ??= $this->newQuery();
    }

    protected function ciConsumeQuery(): Builder
    {
        $query = $this->ciBuilder ?? $this->newQuery();
        $this->ciBuilder = null;

        return $query;
    }

    public function __get($key)
    {
        if ($key === 'table') {
            return $this->getTable();
        }

        if ($key === 'allowedFields') {
            return $this->getFillable();
        }

        if ($key === 'pager') {
            return null;
        }

        return parent::__get($key);
    }

    public function select($columns = ['*'])
    {
        if (is_string($columns) && str_contains($columns, ',')) {
            $columns = array_map('trim', explode(',', $columns));
        }

        $this->ciQuery()->select($columns);

        return $this;
    }

    public function join($table, $first, $operator = null, $second = null, $type = 'inner', $where = false)
    {
        if ($second === null && is_string($operator) && in_array(strtolower($operator), ['left', 'right', 'inner'], true)) {
            $type = strtolower($operator);
            $operator = null;
        }

        if ($second === null && is_string($first) && preg_match('/^(.+?)\s*=\s*(.+)$/', $first, $matches)) {
            $first = trim($matches[1]);
            $operator = '=';
            $second = trim($matches[2]);
        }

        $this->ciQuery()->join($table, $first, $operator, $second, $type, $where);

        return $this;
    }

    public function distinct()
    {
        $this->ciQuery()->distinct();

        return $this;
    }

    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if (is_string($column) && preg_match('/^(.+)\s+(<=|>=|<>|!=|=|<|>|like)$/i', trim($column), $matches)) {
            $column = trim($matches[1]);
            $value = $operator;
            $operator = $matches[2];
        } elseif (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        if (is_string($column) && preg_match('/^YEAR\((.+)\)$/i', trim($column), $matches)) {
            $this->ciQuery()->whereYear(trim($matches[1]), $value);
        } else {
            $this->ciQuery()->where($column, $operator, $value, $boolean);
        }

        return $this;
    }

    public function orWhere($column, $operator = null, $value = null)
    {
        if (func_num_args() === 2) {
            $value = $operator;
            $operator = '=';
        }

        $this->ciQuery()->orWhere($column, $operator, $value);

        return $this;
    }

    public function whereIn($column, $values, $boolean = 'and', $not = false)
    {
        $this->ciQuery()->whereIn($column, $values, $boolean, $not);

        return $this;
    }

    public function orderBy($column, $direction = 'asc')
    {
        $this->ciQuery()->orderBy($column, $direction);

        return $this;
    }

    public function take($value)
    {
        $this->ciQuery()->take($value);

        return $this;
    }

    public function limit($value)
    {
        return $this->take($value);
    }

    public function set(array $data)
    {
        $this->ciSetData = $data;

        return $this;
    }

    public function get($columns = ['*'])
    {
        return $this->ciConsumeQuery()->get($columns);
    }

    public function first($columns = ['*'])
    {
        return $this->ciConsumeQuery()->first($columns);
    }

    public function find($id, $columns = ['*'])
    {
        if ($this->ciBuilder) {
            return $this->ciConsumeQuery()->whereKey($id)->first($columns);
        }

        return parent::find($id, $columns);
    }

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null)
    {
        return $this->withPager(
            $this->ciConsumeQuery()->paginate($perPage, $columns, $pageName, $page)
        );
    }

    protected function withPager($paginator)
    {
        $this->pager = new class($paginator) {
            public function __construct(private $paginator)
            {
            }

            public function getCurrentPage($group = null)
            {
                return $this->paginator->currentPage();
            }

            public function getTotal($group = null)
            {
                return $this->paginator->total();
            }

            public function getPageCount($group = null)
            {
                return $this->paginator->lastPage();
            }
        };

        return $paginator;
    }

    public function countAllResults()
    {
        return $this->ciConsumeQuery()->count();
    }

    public function countAll()
    {
        return $this->newQuery()->count();
    }

    public function insert(array $attributes)
    {
        if ($this->usesTimestamps()) {
            $now = $this->freshTimestampString();
            $attributes += ['created_at' => $now, 'updated_at' => $now];
        }

        return $this->newQuery()->insert($attributes);
    }

    public function save(array $options = []): bool
    {
        if ($this->looksLikeAttributes($options)) {
            $this->fill($options);
            $saved = parent::save();
            $this->ciLastInsertId = $this->getKey() ? (int) $this->getKey() : null;

            return $saved;
        }

        $saved = parent::save($options);
        $this->ciLastInsertId = $this->getKey() ? (int) $this->getKey() : null;

        return $saved;
    }

    public function update($attributes = [], $options = []): bool
    {
        if (!is_array($attributes) && is_array($options)) {
            return (bool) $this->newQuery()->whereKey($attributes)->update($options);
        }

        if ($this->ciBuilder) {
            $data = $this->ciSetData ?: $attributes;
            $this->ciSetData = [];

            return (bool) $this->ciConsumeQuery()->update($data);
        }

        return parent::update($attributes, is_array($options) ? $options : []);
    }

    public function delete()
    {
        if ($this->ciBuilder) {
            return $this->ciConsumeQuery()->delete();
        }

        return parent::delete();
    }

    public function getInsertID(): ?int
    {
        return $this->ciLastInsertId;
    }

    protected function looksLikeAttributes(array $data): bool
    {
        if ($data === []) {
            return false;
        }

        $optionKeys = ['timestamps', 'touch'];

        return collect(array_keys($data))->contains(
            fn ($key) => is_string($key) && ! in_array($key, $optionKeys, true)
        );
    }

    protected function randomUploadName(string $extension = ''): string
    {
        return Str::random(40) . ($extension ? '.' . ltrim($extension, '.') : '');
    }
}
