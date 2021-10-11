<?php

namespace JaxWilko\MediaStream\Controllers;

use Flash;
use BackendMenu;
use Backend\Classes\Controller;
use ApplyNowTv\User\Models\UserProfile;

/**
 * User Groups Back-end Controller
 */
class MetaData extends Controller
{
    /**
     * @var array Extensions implemented by this controller.
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class
    ];

    /**
     * @var array `FormController` configuration.
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var array `ListController` configuration.
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var array `RelationController` configuration, by extension.
     */
    public $relationConfig;

    /**
     * @var array Permissions required to view this page.
     */
    public $requiredPermissions = ['jaxwilko.mediastream.metadata'];

    /**
     * Constructor.
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('JaxWilko.MediaStream', 'mediastream', 'metadata');
    }
}
