<?php

return [

    /*
     *
     * User settings
     *
     */
    'user' => [

        /*
         *
         * User model.
         *
         */
        'model' => 'App\User',

        /*
         *
         * Users table.
         *
         * The table to reference users from.
         *
         */
        'table' => null,
    ],

    /*
     *
     * Point settings
     *
     */
    'point' => [

        /*
         *
         * Actions Path.
         *
         * The namespace under 'app' to place created points.
         *
         */
        'actions_path' => 'Points\Actions',
    ],

    /*
     *
     * Broadcast settings
     *
     */
    'broadcast' => [

        /*
         *
         * Event broadcast name.
         *
         * The users channel to broadcast to.
         *
         */
        'name' => 'points-given',

        /*
         *
         * Broadcast channel.
         *
         * The prefix for the users channel to broadcast to.
         *
         * For example: 'users.' will be 'users.{id}'
         *
         */
        'channel' => 'users.',
    ],
];
