<?php
/**
 * Module options override.
 */

$options = [
    // set true if using https
    'secure'    => true,

    // The hostname for the Solr server
    'hostname'  => 'yawik.org',

    // The port number
    'port'      => 8443,

    // The username used for HTTP Authentication, if any
    'username'  => 'yawik',

    // The HTTP Authentication password
    'password'  => '3qaS2uQU86dGbMXjDds2',

    // A path for solr jobs index
    'jobsPath' => '/solr/YawikDemo',

    // List of Facet Fields. Fieldnames must exist in the solr index. Each facet field must have a 'name'.
    // 'label' is optional. It can be used as a headline of the facet result.
    'facetFields' => [
        [
            'name' => 'regionList',
            'label' => 'Region'
        ],
	[
            'name' => 'organizationTag',
            'label' => 'Firma'
	],
	[
            'name' => 'professionList',
            'label' => 'Berufsfeld'
	],
//	[
//            'name' => 'employmentTypeList',
//            'label' => 'Art der Anstellung'
//	]
	
    ],
    //
    'parameterNames' => [
        'q' => [
            'name' => 'q'
        ],
        'l' => [
            'name' => 'l'
        ],
        'd' => [
            'name' => 'd'
        ]
    ]


];

/*
 * Do not change below this line
 */
return [ 'options' => [ 'Solr/Options/Module' => [ 'options' => $options ] ] ];

