<?php

// namespace Laravel\Infrastructure\Events;

// use Laravel\Infrastructure\Contracts\Audit;
// use Laravel\Infrastructure\Contracts\Auditable;
// use Laravel\Infrastructure\Contracts\AuditDriver;

// class Audited
// {
//     /**
//      * The Auditable model.
//      *
//      * @var \Laravel\Infrastructure\Contracts\Auditable
//      */
//     public $model;

//     /**
//      * Audit driver.
//      *
//      * @var \Laravel\Infrastructure\Contracts\AuditDriver
//      */
//     public $driver;

//     /**
//      * The Audit model.
//      *
//      * @var \Laravel\Infrastructure\Contracts\Audit|null
//      */
//     public $audit;

//     /**
//      * Create a new Audited event instance.
//      *
//      * @param \Laravel\Infrastructure\Contracts\Auditable   $model
//      * @param \Laravel\Infrastructure\Contracts\AuditDriver $driver
//      * @param \Laravel\Infrastructure\Contracts\Audit       $audit
//      */
//     public function __construct(Auditable $model, AuditDriver $driver, Audit $audit = null)
//     {
//         $this->model = $model;
//         $this->driver = $driver;
//         $this->audit = $audit;
//     }
// }
