<?php
/**
 * Created by IntelliJ IDEA.
 * User: memeo
 * Date: 31/01/19
 * Time: 07:20 PM
 */
class Car {
    public $id_table;
    public $empty;
    public $id ;
    public $model;
    public $position;
    public $dateIngress;
    public $departureDate;
    public $time;
    public $numberAccess;
    public $positionPrevious;

    public function __construct(
        $id_table,
        $empty,
        $id,
        $model,
        $position,
        $dateIngress,
        $departureDate,
        $time,
        $numberAccess,
        $positionPrevious
    )
    {
        $this->id_table = $id_table;
        $this->empty = $empty;
        $this->id = $id;
        $this->model = $model;
        $this->position = $position;
        $this->dateIngress = $dateIngress;
        $this->departureDate = $departureDate;
        $this->time = $time;
        $this->numberAccess = $numberAccess;
        $this->positionPrevious = $positionPrevious;
    }
}
