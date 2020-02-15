<?php

return [
    '__name' => 'admin-post-website',
    '__version' => '0.0.1',
    '__git' => 'git@github.com:getmim/admin-post-website.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/admin-post-website' => ['install','update','remove'],
        'theme/admin/post/website' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'admin' => NULL
            ],
            [
                'post-website' => NULL
            ],
            [
                'admin-post' => NULL
            ],
            [
                'lib-form' => NULL
            ],
            [
                'lib-formatter' => NULL
            ],
            [
                'lib-pagination' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'AdminPostWebsite\\Controller' => [
                'type' => 'file',
                'base' => 'modules/admin-post-website/controller'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'admin' => [
            'adminPostWebsite' => [
                'path' => [
                    'value' => '/post/website'
                ],
                'method' => 'GET',
                'handler' => 'AdminPostWebsite\\Controller\\Website::index'
            ],
            'adminPostWebsiteEdit' => [
                'path' => [
                    'value' => '/post/website/(:id)',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET|POST',
                'handler' => 'AdminPostWebsite\\Controller\\Website::edit'
            ],
            'adminPostWebsiteRemove' => [
                'path' => [
                    'value' => '/post/website/(:id)/remove',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminPostWebsite\\Controller\\Website::remove'
            ]
        ]
    ],
    'adminUi' => [
        'sidebarMenu' => [
            'items' => [
                'post' => [
                    'label' => 'Post',
                    'icon' => '<i class="fas fa-newspaper"></i>',
                    'priority' => 0,
                    'filterable' => false,
                    'children' => [
                        'website' => [
                            'label' => 'Website',
                            'icon'  => '<i></i>',
                            'route' => ['adminPostWebsite'],
                            'perms' => 'manage_post_website'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'libForm' => [
        'forms' => [
            'admin.post.edit' => [
                'website' => [
                    'label' => 'Website',
                    'type' => 'select',
                    'rules' => [
                        'exists' => [
                            'model' => 'PostWebsite\\Model\\PostWebsite',
                            'field' => 'id'
                        ]
                    ]
                ]
            ],
            'admin.post-website.edit' => [
                'name' => [
                    'label' => 'Name',
                    'type' => 'text',
                    'rules' => [
                        'required' => true,
                        'unique' => [
                            'model' => 'PostWebsite\\Model\\PostWebsite',
                            'field' => 'name',
                            'self' => [
                                'service' => 'req.param.id',
                                'field' => 'id'
                            ]
                        ]
                    ]
                ]
            ],
            'admin.post-website.index' => [
                'q' => [
                    'label' => 'Search',
                    'type' => 'search',
                    'nolabel' => true,
                    'rules' => []
                ]
            ]
        ]
    ]
];