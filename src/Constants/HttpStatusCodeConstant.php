<?php

namespace Laravel\Infrastructure\Constants;

class HttpStatusCodeConstant
{
    public const SUCCESS = 200; // WHEN RESOURCE FETCHED
    public const RESOURCE_CREATED = 201; // WHEN RESOURCE CREATED SUCCESSFULLY
    public const NO_CONTENT = 204; // WHEN RESOURCE DELETED SUCCESSFULLY AND RETURNED NO CONTENT
    public const BAD_REQUEST = 400; // when post body,query string validation failed
    public const UNAUTHORIZED = 401; // when not logged in
    public const FORBIDDEN = 403; // when user is forbidden to access the resource
    public const UNPROCESSABLE_ENTITY = 422; //
    public const INTERNAL_SERVER_ERROR = 500; // when there is any internal error
    public const NOT_IMPLEMENTED = 501; //
    public const CONFLICT = 409; // in case of duplication
    public const RESOURCE_NOT_FOUND = 404; // when resource not found
    public const METHOD_NOT_ALLOWED = 405; // when resource not found
    public const TOO_MANY_REQUEST = 429; // when resource not found
}
