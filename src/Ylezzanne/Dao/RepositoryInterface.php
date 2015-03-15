<?php

namespace Ylezzanne\Dao;

/**
 * Repository interface.
 *
 * "The Repository pattern just means putting a façade over your persistence
 * system so that you can shield the rest of your application code from having
 * to know how persistence works."
 *
 */
interface RepositoryInterface
{

    /**
     * Returns the total number of entities.
     *
     * @return int The total number of entities.
     */
    public function getCount();

    /**
     * Returns an entity matching the supplied id.
     *
     * @param integer $id
     *
     * @return object|false An entity object if found, false otherwise.
     */
    public function find($id);

    /**
     * Returns a collection of entities.
     *
     * @return array A collection of entity objects.
     */
    public function findAll();
}
