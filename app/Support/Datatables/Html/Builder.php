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

    /**
     * Change table style to striped.
     *
     * @return $this
     */
    public function stripedTable()
    {
        if (! str_contains($this->tableAttributes['class'], 'table-striped')) {
            $this->tableAttributes['class'] = str_replace(
                'table-hover', '',
                $this->tableAttributes['class'].' table-striped'
            );
        }

        return $this;
    }

    /**
     * Change table style to hovered.
     *
     * @return $this
     */
    public function hoveredTable()
    {
        if (! str_contains($this->tableAttributes['class'], 'table-hover')) {
            $this->tableAttributes['class'] = str_replace(
                'table-striped', '',
                $this->tableAttributes['class'].' table-hover'
            );
        }

        return $this;
    }
}
