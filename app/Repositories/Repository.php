<?php

namespace App\Repositories;

use Config;

abstract class Repository
{

    protected $model = FALSE;


    public function get($select = '*', $take = FALSE, $pagination = FALSE, $where = FALSE)
    {
        $builder = $this->model->select($select);

        if ($take) {
            $builder->take($take);
        }

        if ($where) {
            $builder->where($where[0], $where[1]);
        }

        if ($pagination) {
            return $this->check($builder->paginate(Config::get('settings.paginate')));
        }

        return $this->check($builder->get());
    }

    protected function check($result)
    {

        if ($result->isEmpty()) {
            return FALSE;
        }

        $result->transform(function ($item, $key) {

            if (is_string($item->img) && is_object(json_decode($item->img)) && (json_last_error() == JSON_ERROR_NONE)) {
                $item->img = json_decode($item->img);
            }

            return $item;

        });

        return $result;

    }

    public function one($alias, $attr = array(), $where = false)
    {
        if ($where) {
            $result = $this->model->where(['alias' => $alias, $where[0] => $where[1]])->first();
        } else {
            $result = $this->model->where(['alias' => $alias])->first();
        }

        return $result;
    }

}

?>