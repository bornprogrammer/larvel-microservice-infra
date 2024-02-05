<?php


namespace Laravel\Infrastructure\Grants;


interface Grant
{
    /**
     * Returns the grant type
     *
     * @return string type of the grant
     */
    public function getGrantKey(): string;

    /**
     * Returns the grant data
     *
     * @return array data of the grant
     */
    public function getPayload(): array;
}
