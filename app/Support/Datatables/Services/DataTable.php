<?php

namespace App\Support\Datatables\Services;

use Yajra\Datatables\Services\DataTable as BaseDataTable;

abstract class DataTable extends BaseDataTable
{
    /**
     * Get Datatables Html Builder instance.
     *
     * @return \App\Support\Datatables\Html\Builder
     */
    public function builder()
    {
        return app('App\Support\Datatables\Html\Builder')
            ->setTableAttribute('id', preg_replace('#datatable$#i', 'Table', camel_case(class_basename($this))));
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
