<?php

return [
    /**
     * The Prefix to the gallery routes.
     */
    'prefix'      => 'api',

    /**
     * add more middlewares here if any.
     */
    'middlewares' => [
        'auth:api', // this filters for authenticated requests from the ministry
        'ministry.activated', // this filters only activated ministries to use
        'bindings', //used for route model binding
    ],
];
