<?php

namespace App\Support\Datatables\Services;

use App\Support\Datatables\Html\Builder as HtmlBuilder;
use Yajra\Datatables\Services\DataTable as BaseDataTable;

abstract class DataTable extends BaseDataTable
{
    /**
     * Return attributes for a "static" column that can not be ordered, searched, nor exported.
     *
     * @param  string  $name
     * @param  array  $attributes
     * @return $this
     */
    protected function staticColumnAttributes($name, array $attributes = [])
    {
        return array_merge([
            'defaultContent' => '',
            'data' => $name,
            'name' => $name,
            'title' => $this->builder()->getQualifiedTitle($name),
            'render' => null,
            'orderable' => false,
            'searchable' => false,
            'exportable' => false,
            'printable' => true,
            'footer' => '',
        ], $attributes);
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return preg_replace('#datatable$#i', '', class_basename($this)).'-'.date('Ymdhis');
    }

    /**
     * Get default builder parameters.
     *
     * @return array
     */
    protected function getBuilderParameters()
    {
        return [
            'order' => [[0, 'desc']],
        ];
    }
}
