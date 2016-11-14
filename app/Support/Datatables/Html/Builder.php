<?php

namespace App\Support\Datatables\Html;

use Yajra\Datatables\Html\Builder as BaseBuilder;

class Builder extends BaseBuilder
{
    /**
     * Table attributes.
     *
     * @var array
     */
    protected $tableAttributes = [
        'id' => 'dataTable',
        'class' => 'table table-bordered table-striped dt-responsive',
        'width' => '100%',
    ];

    /**
     * Set table "id" attribute.
     *
     * @param  string  $id
     * @return $this
     */
    public function tableId($id)
    {
        return $this->setTableAttribute('id', $id);
    }
}
